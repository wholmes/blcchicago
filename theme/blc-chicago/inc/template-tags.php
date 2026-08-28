<?php
/**
 * Template tags and helpers.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Brand logo URL (PNG — footer, watermarks, customizer fallback).
 *
 * @return string
 */
function blc_get_logo_url() {
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$src = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $src ) {
			return $src;
		}
	}

	return BLC_THEME_URI . '/assets/images/blc-logo.png';
}

/**
 * Header logo URL (white SVG for dark header).
 *
 * @return string
 */
function blc_get_header_logo_url() {
	return BLC_THEME_URI . '/assets/images/blc-logo-header-white.svg';
}

/**
 * Hero skyline image URL.
 *
 * @return string
 */
function blc_get_hero_image_url() {
	return BLC_THEME_URI . '/assets/images/chicago-skyline.webp';
}

/**
 * Print join band CTA section (shared pre-footer).
 */
function blc_render_join_band() {
	get_template_part( 'template-parts/global/join-band' );
}

/**
 * Nav link with aria-current when active.
 *
 * @param string $url       Link URL.
 * @param string $label     Link text.
 * @param mixed  $condition Whether this is the current page.
 */
function blc_nav_link( $url, $label, $condition = false ) {
	printf(
		'<a href="%s"%s>%s</a>',
		esc_url( $url ),
		$condition ? ' aria-current="page"' : '',
		esc_html( $label )
	);
}

/**
 * Short label for member logo placeholder.
 *
 * @param string $org Organization name.
 * @return string
 */
function blc_member_logo_label( $org ) {
	$org = trim( $org );
	if ( ! $org ) {
		return '';
	}

	$words = preg_split( '/\s+/', $org );

	return (string) ( $words[0] ?? $org );
}
