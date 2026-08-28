<?php
/**
 * Events custom post type.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register blc_event.
 */
function blc_register_event_cpt() {
	register_post_type(
		'blc_event',
		array(
			'labels'       => array(
				'name'          => __( 'Events', 'blc-chicago' ),
				'singular_name' => __( 'Event', 'blc-chicago' ),
				'add_new_item'  => __( 'Add Event', 'blc-chicago' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'has_archive'  => false,
			'rewrite'      => array(
				'slug'       => 'event',
				'with_front' => false,
			),
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		)
	);

	$meta_keys = array(
		'blc_event_start'    => 'string',
		'blc_event_end'      => 'string',
		'blc_event_location' => 'string',
		'blc_event_featured' => 'boolean',
	);

	foreach ( $meta_keys as $key => $type ) {
		register_post_meta(
			'blc_event',
			$key,
			array(
				'single'       => true,
				'type'         => $type,
				'show_in_rest' => true,
			)
		);
	}
}
add_action( 'init', 'blc_register_event_cpt' );

/**
 * Seed Lakeside Chat prototype event.
 */
function blc_seed_default_event() {
	if ( get_option( 'blc_events_seeded' ) ) {
		return;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'blc_event',
			'post_status'  => 'publish',
			'post_title'   => 'BLC Lakeside Chat with Mayor Brandon Johnson',
			'post_excerpt' => 'Thu · 11:30 AM – 1:00 PM · The Ivy Room, 12 E. Ohio St.',
			'post_content' => '<p>Join BLC members and civic leaders for a conversation on Chicago’s economy, Black business, and community investment.</p>',
		)
	);

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, 'blc_event_start', '2026-09-17T11:30:00' );
		update_post_meta( $post_id, 'blc_event_end', '2026-09-17T13:00:00' );
		update_post_meta( $post_id, 'blc_event_location', 'The Ivy Room, 12 E. Ohio St., Chicago, IL' );
		update_post_meta( $post_id, 'blc_event_featured', true );
	}

	update_option( 'blc_events_seeded', 1, false );
}
add_action( 'after_switch_theme', 'blc_seed_default_event' );

/**
 * Format event date for list rows.
 *
 * @param int $post_id Event post ID.
 * @return array{day: string, month: string, meta: string}
 */
function blc_get_event_display_date( $post_id ) {
	$start = get_post_meta( $post_id, 'blc_event_start', true );
	$ts    = $start ? strtotime( $start ) : get_post_time( 'U', true, $post_id );

	return array(
		'day'   => gmdate( 'j', $ts ),
		'month' => gmdate( 'M', $ts ),
		'meta'  => get_the_excerpt( $post_id ),
	);
}
