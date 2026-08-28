<?php
/**
 * Paid Memberships Pro integration.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read stored PMPro level → tier slug map.
 *
 * @return array<int, string>
 */
function blc_get_stored_pmpro_level_tier_map() {
	$stored = get_option( 'blc_pmpro_level_tier_map', array() );

	if ( ! is_array( $stored ) ) {
		return array();
	}

	$map = array();
	foreach ( $stored as $level_id => $tier_slug ) {
		$level_id   = (int) $level_id;
		$tier_slug  = sanitize_title( (string) $tier_slug );
		if ( $level_id && $tier_slug ) {
			$map[ $level_id ] = $tier_slug;
		}
	}

	return $map;
}

/**
 * Sync membership tier term onto a member post from PMPro level.
 *
 * @param int $user_id WordPress user ID.
 */
function blc_sync_member_tier_from_pmpro( $user_id ) {
	$user_id = (int) $user_id;
	if ( ! $user_id || ! blc_is_pmpro_active() ) {
		return;
	}

	$level_id = blc_user_membership_level_id( $user_id );
	if ( ! $level_id ) {
		return;
	}

	$tier_slug = blc_pmpro_level_to_tier_slug( $level_id );
	if ( ! $tier_slug ) {
		return;
	}

	$post_id = blc_find_member_by_user_id( $user_id );
	if ( ! $post_id ) {
		return;
	}

	wp_set_object_terms( $post_id, array( $tier_slug ), 'blc_membership_tier', false );
}
add_action( 'pmpro_after_checkout', 'blc_sync_member_tier_from_pmpro', 20, 1 );
add_action( 'pmpro_after_change_membership_level', 'blc_sync_member_tier_from_pmpro', 20, 1 );

/**
 * After import creates a user, attempt tier sync if they already have PMPro level.
 *
 * @param int $post_id Member post ID.
 */
function blc_maybe_sync_imported_member_pmpro( $post_id ) {
	$user_id = (int) get_post_meta( $post_id, 'blc_wp_user_id', true );
	if ( $user_id ) {
		blc_sync_member_tier_from_pmpro( $user_id );
	}
}

/**
 * Suggested PMPro level definitions (for admin reference / manual creation).
 *
 * @return array<string, array{name: string, slug: string, visibility: string}>
 */
function blc_pmpro_level_blueprint() {
	$blueprint = array();
	foreach ( blc_default_tier_definitions() as $name => $visibility ) {
		if ( 'internal' === $visibility ) {
			continue;
		}
		$blueprint[ sanitize_title( $name ) ] = array(
			'name'       => $name,
			'slug'       => sanitize_title( $name ),
			'visibility' => $visibility,
		);
	}

	return $blueprint;
}
