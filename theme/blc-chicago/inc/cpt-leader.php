<?php
/**
 * Leadership CPT for BLC Leadership page.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register blc_leader post type.
 */
function blc_register_leader_cpt() {
	register_post_type(
		'blc_leader',
		array(
			'labels'       => array(
				'name'          => __( 'Leadership', 'blc-chicago' ),
				'singular_name' => __( 'Leader', 'blc-chicago' ),
				'add_new_item'  => __( 'Add Leader', 'blc-chicago' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => 'edit.php?post_type=blc_member',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'menu_icon'    => 'dashicons-businessperson',
		)
	);

	register_post_meta(
		'blc_leader',
		'blc_leader_role',
		array(
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		)
	);

	register_post_meta(
		'blc_leader',
		'blc_leader_org',
		array(
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		)
	);

	register_post_meta(
		'blc_leader',
		'blc_leader_featured',
		array(
			'single'       => true,
			'type'         => 'boolean',
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'blc_register_leader_cpt' );

/**
 * Seed default leaders from static prototype (run once on theme switch).
 */
function blc_seed_default_leaders() {
	if ( get_option( 'blc_leaders_seeded' ) ) {
		return;
	}

	$leaders = array(
		array(
			'name'     => 'Keiana Barrett',
			'role'     => 'CEO · BLC',
			'org'      => 'Chief executive stewarding BLC’s mission, member experience, and civic partnerships across Chicago.',
			'photo'    => 'keiana-barrett.webp',
			'featured' => true,
			'order'    => 1,
		),
		array(
			'name'     => 'Charles Smith',
			'role'     => 'Chairman · BLC',
			'org'      => 'CEO, CS Insurance Strategies, Division of MMA',
			'photo'    => 'charles-smith.webp',
			'featured' => true,
			'order'    => 2,
		),
		array( 'name' => 'Robert Blackwell Jr.', 'org' => 'CEO · Quant 16', 'photo' => 'robert-blackwell.webp', 'order' => 10 ),
		array( 'name' => 'Glenn Charles Jr.', 'org' => 'President & CEO · Show Strategy', 'photo' => 'glenn-charles.webp', 'order' => 11 ),
		array( 'name' => 'Frank Clark', 'org' => 'Chairman & CEO · ComEd (Retired)', 'photo' => 'frank-clark.webp', 'order' => 12 ),
		array( 'name' => 'Kalia M. Coleman', 'org' => 'Partner · Riley Safer Holmes & Cancila LLP', 'photo' => 'kalia-coleman.webp', 'order' => 13 ),
		array( 'name' => 'Steve Davis', 'org' => 'Chairman & CEO · The Will Group', 'photo' => 'steve-davis.webp', 'order' => 14 ),
		array( 'name' => 'Otto Nichols III', 'org' => 'Executive VP, Shareholder · Clayco', 'photo' => 'otto-nichols.webp', 'order' => 15 ),
		array( 'name' => 'Felicia Rauls', 'org' => 'Partner / Senior Compliance Manager · Creative Planning LLC', 'photo' => 'felicia-rauls.webp', 'order' => 16 ),
		array( 'name' => 'Jim Reynolds', 'org' => 'Chairman & CEO · Loop Capital Markets', 'photo' => 'jim-reynolds.webp', 'order' => 17 ),
		array( 'name' => 'John Rogers', 'org' => 'Chairman & Co-CEO · Ariel Investments', 'photo' => 'john-rogers.webp', 'order' => 18 ),
		array( 'name' => 'Melody Spann-Cooper', 'org' => 'Chairman & President · Midway Broadcasting', 'photo' => 'melody-spann-cooper.webp', 'order' => 19 ),
		array( 'name' => 'Melissa Washington', 'org' => 'SVP · ComEd Government & External Affairs', 'photo' => 'melissa-washington.webp', 'order' => 20 ),
	);

	foreach ( $leaders as $leader ) {
		$existing = get_page_by_title( $leader['name'], OBJECT, 'blc_leader' );
		if ( $existing ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'blc_leader',
				'post_status' => 'publish',
				'post_title'  => $leader['name'],
				'menu_order'  => $leader['order'],
			)
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}

		$role = $leader['role'] ?? '';
		if ( $role ) {
			update_post_meta( $post_id, 'blc_leader_role', $role );
		}
		update_post_meta( $post_id, 'blc_leader_org', $leader['org'] ?? '' );
		if ( ! empty( $leader['featured'] ) ) {
			update_post_meta( $post_id, 'blc_leader_featured', true );
		}

		$photo_path = BLC_THEME_DIR . '/assets/images/leadership/' . $leader['photo'];
		if ( file_exists( $photo_path ) ) {
			blc_attach_theme_image_to_post( $post_id, $photo_path, $leader['name'] );
		}
	}

	update_option( 'blc_leaders_seeded', 1, false );
}
add_action( 'after_switch_theme', 'blc_seed_default_leaders' );

/**
 * Attach a theme asset file as featured image.
 *
 * @param int    $post_id  Post ID.
 * @param string $path     Absolute file path.
 * @param string $alt      Alt text.
 */
function blc_attach_theme_image_to_post( $post_id, $path, $alt ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$filename = basename( $path );
	$upload   = wp_upload_bits( $filename, null, file_get_contents( $path ) );
	if ( ! empty( $upload['error'] ) ) {
		return;
	}

	$filetype   = wp_check_filetype( $filename, null );
	$attachment = array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => sanitize_file_name( $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );
	if ( ! is_wp_error( $attach_id ) ) {
		wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		set_post_thumbnail( $post_id, $attach_id );
		update_post_meta( $attach_id, '_wp_attachment_image_alt', $alt );
	}
}
