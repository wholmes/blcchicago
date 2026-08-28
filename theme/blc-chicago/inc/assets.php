<?php
/**
 * Enqueue styles and scripts.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Front-end assets.
 */
function blc_enqueue_assets() {
	wp_enqueue_style(
		'blc-fonts',
		'https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'blc-main',
		BLC_THEME_URI . '/assets/css/main.css',
		array( 'blc-fonts' ),
		BLC_THEME_VERSION
	);

	wp_enqueue_script(
		'blc-main',
		BLC_THEME_URI . '/assets/js/main.js',
		array(),
		BLC_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'blc_enqueue_assets' );
