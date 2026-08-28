<?php
/**
 * Primary navigation.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default nav items.
 *
 * @return array<int, array{label: string, url: string, current: bool}>
 */
function blc_default_nav_items() {
	return array(
		array(
			'label'   => __( 'Home', 'blc-chicago' ),
			'url'     => home_url( '/' ),
			'current' => is_front_page(),
		),
		array(
			'label'   => __( 'Leadership', 'blc-chicago' ),
			'url'     => home_url( '/leadership/' ),
			'current' => is_page( 'leadership' ),
		),
		array(
			'label'   => __( 'Sponsorship', 'blc-chicago' ),
			'url'     => home_url( '/corporate-sponsorship/' ),
			'current' => is_page( 'corporate-sponsorship' ),
		),
		array(
			'label'   => __( 'Events', 'blc-chicago' ),
			'url'     => home_url( '/events/' ),
			'current' => is_page( 'events' ),
		),
		array(
			'label'   => __( 'Directory', 'blc-chicago' ),
			'url'     => blc_get_public_directory_url(),
			'current' => is_page_template( 'page-templates/public-directory.php' ),
		),
		array(
			'label'   => __( 'Members', 'blc-chicago' ),
			'url'     => blc_get_members_directory_url(),
			'current' => is_page_template( 'page-templates/members-directory.php' ),
		),
		array(
			'label'   => __( 'Articles', 'blc-chicago' ),
			'url'     => get_post_type_archive_link( 'post' ) ?: home_url( '/articles/' ),
			'current' => is_home() || is_singular( 'post' ) || is_category(),
		),
		array(
			'label'   => __( 'Contact', 'blc-chicago' ),
			'url'     => home_url( '/contact/' ),
			'current' => is_page( 'contact' ) || is_page_template( 'page-templates/contact.php' ),
		),
	);
}

/**
 * Render primary navigation (matches static prototype structure).
 */
function blc_render_primary_nav() {
	echo '<nav class="nav" id="site-nav">';

	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'fallback_cb'    => false,
				'depth'          => 1,
			)
		);
	} else {
		foreach ( blc_default_nav_items() as $item ) {
			blc_nav_link( $item['url'], $item['label'], $item['current'] );
		}
	}

	echo '<div class="header-actions">';
	printf(
		'<a class="nav__login" href="%s">%s</a>',
		esc_url( blc_get_login_url() ),
		esc_html__( 'Log in', 'blc-chicago' )
	);
	printf(
		'<a class="nav__cta" href="%s">%s</a>',
		esc_url( blc_get_join_url() ),
		esc_html__( 'Become a Member', 'blc-chicago' )
	);
	echo '</div></nav>';
}
