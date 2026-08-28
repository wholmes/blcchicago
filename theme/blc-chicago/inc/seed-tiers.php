<?php
/**
 * Seed membership tier terms from Wild Apricot snapshot.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tier definitions: name => visibility.
 *
 * @return array<string, string>
 */
function blc_default_tier_definitions() {
	return array(
		'Established Leader'           => 'public',
		'Vanguard Leader'              => 'public',
		'Civic & Community Leader'     => 'public',
		'Corporate Leader'             => 'public',
		'Sustained Leader'             => 'public',
		'Strategic Partner'            => 'public',
		'Lead Partner Pioneers'        => 'public',
		'Board Member'                 => 'internal',
		'Internal Office'              => 'internal',
		'Provisional Event Invitee'    => 'provisional',
	);
}

/**
 * Seed tiers if missing.
 */
function blc_seed_membership_tiers() {
	if ( ! taxonomy_exists( 'blc_membership_tier' ) ) {
		return;
	}

	foreach ( blc_default_tier_definitions() as $name => $visibility ) {
		$term = term_exists( $name, 'blc_membership_tier' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'blc_membership_tier' );
		}

		if ( is_wp_error( $term ) ) {
			continue;
		}

		$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
		update_term_meta( $term_id, 'blc_tier_visibility', $visibility );
	}
}
add_action( 'after_switch_theme', 'blc_seed_membership_tiers' );

/**
 * Admin action to re-seed tiers.
 */
function blc_handle_seed_tiers() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized', 'blc-chicago' ) );
	}

	check_admin_referer( 'blc_seed_tiers' );
	blc_seed_membership_tiers();
	wp_safe_redirect( add_query_arg( 'blc_seeded', '1', wp_get_referer() ?: admin_url() ) );
	exit;
}
add_action( 'admin_post_blc_seed_tiers', 'blc_handle_seed_tiers' );
