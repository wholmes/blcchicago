<?php
/**
 * Theme setup.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and menus.
 */
function blc_theme_setup() {
	load_theme_textdomain( 'blc-chicago', BLC_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'blc-chicago' ),
			'footer'  => __( 'Footer Navigation', 'blc-chicago' ),
		)
	);

	add_image_size( 'blc-member-logo', 160, 160, false );
	add_image_size( 'blc-leader-photo', 480, 560, true );
	add_image_size( 'blc-hero', 1920, 1080, true );
	add_image_size( 'blc-article-card', 900, 560, true );
}
add_action( 'after_setup_theme', 'blc_theme_setup' );

/**
 * Register page templates programmatically (slug hints for editors).
 */
function blc_register_recommended_pages() {
	// Documented in theme README — pages created manually in WP admin.
}
add_action( 'init', 'blc_register_recommended_pages' );

/**
 * Flush rewrite rules when theme version changes (e.g. CPT slug updates).
 */
function blc_maybe_flush_rewrites() {
	if ( get_option( 'blc_theme_version_flushed' ) === BLC_THEME_VERSION ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'blc_theme_version_flushed', BLC_THEME_VERSION, false );
}
add_action( 'init', 'blc_maybe_flush_rewrites', 99 );
