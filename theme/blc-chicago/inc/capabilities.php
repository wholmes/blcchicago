<?php
/**
 * Membership access helpers (Paid Memberships Pro).
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * PMPro level ID → blc_membership_tier slug map.
 *
 * Append rows as levels are created in PMPro admin.
 *
 * @return array<int, string>
 */
function blc_pmpro_level_tier_map() {
	$map = blc_get_stored_pmpro_level_tier_map();

	/**
	 * Filter PMPro level → tier slug map.
	 *
	 * @param array<int, string> $map Level ID to term slug.
	 */
	return apply_filters( 'blc_pmpro_level_tier_map', $map );
}

/**
 * Resolve tier slug for a PMPro level.
 *
 * @param int $level_id PMPro level ID.
 * @return string|null
 */
function blc_pmpro_level_to_tier_slug( $level_id ) {
	$map = blc_pmpro_level_tier_map();
	return isset( $map[ (int) $level_id ] ) ? $map[ (int) $level_id ] : null;
}

/**
 * Whether PMPro is active.
 *
 * @return bool
 */
function blc_is_pmpro_active() {
	return function_exists( 'pmpro_hasMembershipLevel' );
}

/**
 * User has any active paid membership level.
 *
 * @param int|null $user_id User ID.
 * @return bool
 */
function blc_user_has_active_membership( $user_id = null ) {
	if ( ! blc_is_pmpro_active() ) {
		return false;
	}

	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( ! $user_id ) {
		return false;
	}

	if ( user_can( $user_id, 'manage_options' ) ) {
		return true;
	}

	return (bool) pmpro_hasMembershipLevel( null, $user_id );
}

/**
 * Whether user may access the members-only directory.
 *
 * @param int|null $user_id User ID.
 * @return bool
 */
function blc_user_can_access_member_directory( $user_id = null ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();

	/**
	 * Filter members-only directory access.
	 *
	 * @param bool $allowed Access flag.
	 * @param int  $user_id User ID.
	 */
	return (bool) apply_filters( 'blc_user_can_access_member_directory', blc_user_has_active_membership( $user_id ), $user_id );
}

/**
 * Current user's primary PMPro level ID, if any.
 *
 * @param int|null $user_id User ID.
 * @return int|null
 */
function blc_user_membership_level_id( $user_id = null ) {
	if ( ! blc_is_pmpro_active() ) {
		return null;
	}

	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( ! $user_id ) {
		return null;
	}

	$level = pmpro_getMembershipLevelForUser( $user_id );
	return ( $level && isset( $level->id ) ) ? (int) $level->id : null;
}

/**
 * URL for join / membership levels page.
 *
 * @return string
 */
function blc_get_join_url() {
	$page = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-templates/join-us.php',
			'number'     => 1,
		)
	);

	if ( ! empty( $page ) ) {
		return get_permalink( $page[0]->ID );
	}

	if ( function_exists( 'pmpro_url' ) ) {
		return pmpro_url( 'levels' );
	}

	return home_url( '/join-us/' );
}

/**
 * URL for member login.
 *
 * @return string
 */
function blc_get_login_url() {
	if ( function_exists( 'pmpro_login_url' ) ) {
		return pmpro_login_url();
	}

	return wp_login_url( blc_get_members_directory_url() );
}

/**
 * Members-only directory URL.
 *
 * @return string
 */
function blc_get_members_directory_url() {
	$page = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-templates/members-directory.php',
			'number'     => 1,
		)
	);

	if ( ! empty( $page ) ) {
		return get_permalink( $page[0]->ID );
	}

	return home_url( '/members/directory/' );
}

/**
 * Public directory URL.
 *
 * @return string
 */
function blc_get_public_directory_url() {
	$page = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-templates/public-directory.php',
			'number'     => 1,
		)
	);

	if ( ! empty( $page ) ) {
		return get_permalink( $page[0]->ID );
	}

	return home_url( '/directory/' );
}

/**
 * Paywall / renew URL for limited accounts.
 *
 * @return string
 */
function blc_get_paywall_url() {
	$page = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-templates/paywall.php',
			'number'     => 1,
		)
	);

	if ( ! empty( $page ) ) {
		return get_permalink( $page[0]->ID );
	}

	if ( function_exists( 'pmpro_url' ) ) {
		return pmpro_url( 'account' );
	}

	return blc_get_join_url();
}

/**
 * Gate members-only template; redirect or show paywall.
 */
function blc_require_member_directory_access() {
	if ( blc_user_can_access_member_directory() ) {
		return;
	}

	if ( is_user_logged_in() ) {
		wp_safe_redirect( blc_get_paywall_url() );
		exit;
	}

	wp_safe_redirect( blc_get_login_url() );
	exit;
}
