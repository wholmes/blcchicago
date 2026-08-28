<?php
/**
 * Taxonomies: industry and membership tier.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register taxonomies.
 */
function blc_register_taxonomies() {
	register_taxonomy(
		'blc_industry',
		array( 'blc_member' ),
		array(
			'labels'            => array(
				'name'          => __( 'Industries', 'blc-chicago' ),
				'singular_name' => __( 'Industry', 'blc-chicago' ),
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'industry' ),
		)
	);

	register_taxonomy(
		'blc_membership_tier',
		array( 'blc_member' ),
		array(
			'labels'            => array(
				'name'          => __( 'Membership Tiers', 'blc-chicago' ),
				'singular_name' => __( 'Membership Tier', 'blc-chicago' ),
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'membership-tier' ),
		)
	);
}
add_action( 'init', 'blc_register_taxonomies' );

/**
 * Tier visibility: public, internal, provisional, join_page.
 *
 * @param WP_Term $term Tier term.
 * @return string
 */
function blc_get_tier_visibility( $term ) {
	$visibility = get_term_meta( $term->term_id, 'blc_tier_visibility', true );
	return $visibility ? (string) $visibility : 'public';
}

/**
 * Whether tier appears on public directory filters.
 *
 * @param WP_Term $term Tier term.
 * @return bool
 */
function blc_tier_is_public_directory( $term ) {
	return 'internal' !== blc_get_tier_visibility( $term ) && 'provisional' !== blc_get_tier_visibility( $term );
}
