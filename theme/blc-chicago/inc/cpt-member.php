<?php
/**
 * Member custom post type.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register blc_member CPT.
 */
function blc_register_member_cpt() {
	register_post_type(
		'blc_member',
		array(
			'labels'              => array(
				'name'          => __( 'Members', 'blc-chicago' ),
				'singular_name' => __( 'Member', 'blc-chicago' ),
				'add_new_item'  => __( 'Add Member Profile', 'blc-chicago' ),
				'edit_item'     => __( 'Edit Member Profile', 'blc-chicago' ),
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'rewrite'             => array(
				'slug'       => 'directory',
				'with_front' => false,
			),
			'menu_icon'           => 'dashicons-groups',
			'menu_position'       => 25,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'capability_type'     => 'post',
		)
	);
}
add_action( 'init', 'blc_register_member_cpt' );

/**
 * Organization display name meta key.
 */
function blc_member_org_meta_key() {
	return 'blc_organization';
}

/**
 * Get organization name for a member post.
 *
 * @param int $post_id Member post ID.
 * @return string
 */
function blc_get_member_organization( $post_id ) {
	$org = get_post_meta( $post_id, blc_member_org_meta_key(), true );
	if ( $org ) {
		return (string) $org;
	}

	return get_the_title( $post_id );
}
