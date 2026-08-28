<?php
/**
 * Builds membership summary rows from PMPro data.
 *
 * @package BLC_Membership_Summary
 */

defined( 'ABSPATH' ) || exit;

/**
 * Membership report generator.
 */
class BLC_MS_Report {

	/**
	 * Transient cache key.
	 */
	const CACHE_KEY = 'blc_ms_summary_report';

	/**
	 * Cache lifetime in seconds.
	 *
	 * @var int
	 */
	const CACHE_TTL = 300;

	/**
	 * Whether PMPro is available.
	 *
	 * @return bool
	 */
	public static function is_pmpro_active() {
		return function_exists( 'pmpro_getAllLevels' );
	}

	/**
	 * Get full report with totals row.
	 *
	 * @param bool $refresh Skip cache when true.
	 * @return array{rows: array<int, array<string, mixed>>, totals: array<string, mixed>, generated_at: int, pmpro_active: bool}
	 */
	public static function get_report( $refresh = false ) {
		if ( ! $refresh ) {
			$cached = get_transient( self::CACHE_KEY );
			if ( is_array( $cached ) ) {
				return $cached;
			}
		}

		$report = array(
			'rows'         => array(),
			'totals'       => self::empty_counts(),
			'generated_at' => time(),
			'pmpro_active' => self::is_pmpro_active(),
		);

		if ( ! self::is_pmpro_active() ) {
			set_transient( self::CACHE_KEY, $report, self::CACHE_TTL );
			return $report;
		}

		$levels = pmpro_getAllLevels( true, true );
		if ( empty( $levels ) ) {
			set_transient( self::CACHE_KEY, $report, self::CACHE_TTL );
			return $report;
		}

		foreach ( $levels as $level ) {
			$row    = self::get_level_counts( (int) $level->id, (string) $level->name );
			$report['rows'][] = $row;

			foreach ( array_keys( self::empty_counts() ) as $key ) {
				if ( isset( $row[ $key ] ) && is_numeric( $row[ $key ] ) ) {
					$report['totals'][ $key ] += (int) $row[ $key ];
				}
			}
		}

		set_transient( self::CACHE_KEY, $report, self::CACHE_TTL );

		return $report;
	}

	/**
	 * Default count shape.
	 *
	 * @return array<string, int>
	 */
	public static function empty_counts() {
		return array(
			'total'            => 0,
			'bundles'          => 0,
			'active'           => 0,
			'renewal_overdue'  => 0,
			'lapsed'           => 0,
			'pending_new'      => 0,
			'pending_renewal'  => 0,
			'pending_level'    => 0,
			'new_7_days'       => 0,
			'new_30_days'      => 0,
		);
	}

	/**
	 * Count members for one PMPro level.
	 *
	 * @param int    $level_id Level ID.
	 * @param string $level_name Display name.
	 * @return array<string, mixed>
	 */
	public static function get_level_counts( $level_id, $level_name ) {
		global $wpdb;

		$row = array_merge(
			self::empty_counts(),
			array(
				'level_id'   => $level_id,
				'level_name' => $level_name,
			)
		);

		$memberships_table = $wpdb->pmpro_memberships_users;
		$now               = current_time( 'mysql' );
		$seven_days_ago    = gmdate( 'Y-m-d H:i:s', strtotime( '-7 days', current_time( 'timestamp' ) ) );
		$thirty_days_ago   = gmdate( 'Y-m-d H:i:s', strtotime( '-30 days', current_time( 'timestamp' ) ) );

		// Latest membership row per user for this level.
		$sql = "
			SELECT mu.user_id, mu.status, mu.startdate, mu.enddate
			FROM {$memberships_table} mu
			INNER JOIN (
				SELECT user_id, MAX(id) AS latest_id
				FROM {$memberships_table}
				WHERE membership_id = %d
				GROUP BY user_id
			) latest ON latest.latest_id = mu.id
		";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepare follows.
		$members = $wpdb->get_results( $wpdb->prepare( $sql, $level_id ) );

		if ( empty( $members ) ) {
			return $row;
		}

		$user_ids = array();
		foreach ( $members as $member ) {
			$user_ids[] = (int) $member->user_id;
			++$row['total'];

			$bucket = self::classify_membership( $member, $now );
			if ( $bucket && isset( $row[ $bucket ] ) ) {
				++$row[ $bucket ];
			}

			if ( ! empty( $member->startdate ) && $member->startdate >= $seven_days_ago ) {
				++$row['new_7_days'];
			}

			if ( ! empty( $member->startdate ) && $member->startdate >= $thirty_days_ago ) {
				++$row['new_30_days'];
			}
		}

		$row['bundles'] = self::count_bundles( $user_ids );

		return $row;
	}

	/**
	 * Map PMPro row to summary bucket.
	 *
	 * @param object $member Membership row.
	 * @param string $now MySQL datetime.
	 * @return string|null
	 */
	public static function classify_membership( $member, $now ) {
		$status  = isset( $member->status ) ? (string) $member->status : '';
		$enddate = isset( $member->enddate ) ? (string) $member->enddate : '';
		$expired = ( '' !== $enddate && '0000-00-00 00:00:00' !== $enddate && $enddate < $now );

		switch ( $status ) {
			case 'active':
				return $expired ? 'renewal_overdue' : 'active';
			case 'expired':
			case 'cancelled':
				return 'lapsed';
			case 'inactive':
				return self::is_pending_renewal( (int) $member->user_id ) ? 'pending_renewal' : 'pending_new';
			default:
				return 'lapsed';
		}
	}

	/**
	 * Detect pending renewal vs first-time pending.
	 *
	 * @param int $user_id User ID.
	 * @return bool
	 */
	public static function is_pending_renewal( $user_id ) {
		global $wpdb;

		$memberships_table = $wpdb->pmpro_memberships_users;

		$count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$memberships_table}
				WHERE user_id = %d
				AND status IN ('expired', 'cancelled')",
				$user_id
			)
		);

		return $count > 0;
	}

	/**
	 * Count bundle coordinators (or distinct bundle IDs) among users.
	 *
	 * @param int[] $user_ids User IDs at this level.
	 * @return int
	 */
	public static function count_bundles( $user_ids ) {
		if ( empty( $user_ids ) ) {
			return 0;
		}

		$bundle_ids = array();

		foreach ( $user_ids as $user_id ) {
			$role = get_user_meta( $user_id, 'blc_member_role', true );
			if ( 'Bundle coordinator' === $role ) {
				$bundle_ids[ 'coord_' . $user_id ] = true;
				continue;
			}

			$bundle_key = get_user_meta( $user_id, 'blc_bundle_id', true );
			if ( $bundle_key ) {
				$bundle_ids[ (string) $bundle_key ] = true;
			}
		}

		return count( $bundle_ids );
	}

	/**
	 * Admin URL to filtered member list for a cell.
	 *
	 * @param int    $level_id Level ID.
	 * @param string $metric Metric key.
	 * @return string
	 */
	public static function get_filter_url( $level_id, $metric ) {
		$args = array(
			'page'       => 'blc-membership-summary',
			'level_id'   => (int) $level_id,
			'metric'     => sanitize_key( $metric ),
		);

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * Flush cached report.
	 */
	public static function flush_cache() {
		delete_transient( self::CACHE_KEY );
	}
}
