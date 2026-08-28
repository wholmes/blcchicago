<?php
/**
 * Member directory queries and organization deduplication.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Membership statuses treated as directory-visible.
 *
 * @return string[]
 */
function blc_member_active_statuses() {
	$statuses = array( 'Active' );

	/**
	 * Filter membership statuses shown in directories.
	 *
	 * @param string[] $statuses Status labels from import meta.
	 */
	return apply_filters( 'blc_member_active_statuses', $statuses );
}

/**
 * Meta query excluding archived members.
 *
 * @return array<int, array<string, mixed>>
 */
function blc_member_not_archived_meta_query() {
	return array(
		'relation' => 'OR',
		array(
			'key'     => 'blc_archived',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => 'blc_archived',
			'value'   => 'Yes',
			'compare' => '!=',
		),
		array(
			'key'     => 'blc_archived',
			'value'   => '',
		),
	);
}

/**
 * Meta query for active membership status (includes legacy empty).
 *
 * @return array<int, array<string, mixed>>
 */
function blc_member_active_status_meta_query() {
	$statuses = blc_member_active_statuses();

	return array(
		'relation' => 'OR',
		array(
			'key'     => 'blc_membership_status',
			'value'   => $statuses,
			'compare' => 'IN',
		),
		array(
			'key'     => 'blc_membership_status',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => 'blc_membership_status',
			'value'   => '',
		),
	);
}

/**
 * Term IDs for tiers hidden from the public directory.
 *
 * @return int[]
 */
function blc_hidden_public_tier_term_ids() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'blc_membership_tier',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}

	$hidden = array();
	foreach ( $terms as $term ) {
		if ( ! blc_tier_is_public_directory( $term ) ) {
			$hidden[] = (int) $term->term_id;
		}
	}

	return $hidden;
}

/**
 * Base query args for member directories.
 *
 * @param string $context public|members.
 * @return array<string, mixed>
 */
function blc_member_directory_query_args( $context = 'public' ) {
	$meta_query = array(
		blc_member_not_archived_meta_query(),
		blc_member_active_status_meta_query(),
	);

	$tax_query = array();

	if ( 'public' === $context ) {
		$hidden_tiers = blc_hidden_public_tier_term_ids();
		if ( $hidden_tiers ) {
			$tax_query[] = array(
				'taxonomy' => 'blc_membership_tier',
				'field'    => 'term_id',
				'terms'    => $hidden_tiers,
				'operator' => 'NOT IN',
			);
		}
	}

	$args = array(
		'post_type'              => 'blc_member',
		'post_status'            => 'publish',
		'posts_per_page'         => 500,
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'meta_query'             => $meta_query,
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);

	if ( $tax_query ) {
		$args['tax_query'] = $tax_query;
	}

	return $args;
}

/**
 * Rank a member post for org deduplication (higher wins).
 *
 * @param WP_Post $post Member post.
 * @return int
 */
function blc_member_directory_rank( WP_Post $post ) {
	$status = blc_get_member_meta( $post->ID, 'blc_membership_status' );
	$rank   = in_array( $status, blc_member_active_statuses(), true ) ? 100 : 10;

	$since = blc_get_member_meta( $post->ID, 'blc_member_since' );
	if ( $since ) {
		$ts = strtotime( $since );
		if ( $ts ) {
			$rank += (int) min( 50, ( time() - $ts ) / YEAR_IN_SECONDS );
		}
	}

	return $rank;
}

/**
 * Deduplicate member posts by organization name (one card per org).
 *
 * @param WP_Post[] $posts Member posts.
 * @return WP_Post[]
 */
function blc_deduplicate_members_by_organization( array $posts ) {
	$by_org = array();

	foreach ( $posts as $post ) {
		$org = blc_get_member_organization( $post->ID );
		$key = sanitize_title( $org ?: 'member-' . $post->ID );

		if ( ! isset( $by_org[ $key ] ) ) {
			$by_org[ $key ] = $post;
			continue;
		}

		if ( blc_member_directory_rank( $post ) > blc_member_directory_rank( $by_org[ $key ] ) ) {
			$by_org[ $key ] = $post;
		}
	}

	$deduped = array_values( $by_org );

	usort(
		$deduped,
		static function ( WP_Post $a, WP_Post $b ) {
			return strcasecmp( blc_get_member_organization( $a->ID ), blc_get_member_organization( $b->ID ) );
		}
	);

	return $deduped;
}

/**
 * Public directory posts (deduplicated by organization).
 *
 * @return WP_Post[]
 */
function blc_get_public_directory_posts() {
	$query = new WP_Query( blc_member_directory_query_args( 'public' ) );
	$posts = $query->posts;

	return blc_deduplicate_members_by_organization( $posts );
}

/**
 * Members-only directory posts (all active profiles).
 *
 * @return WP_Post[]
 */
function blc_get_members_directory_posts() {
	$query = new WP_Query( blc_member_directory_query_args( 'members' ) );

	return $query->posts;
}

/**
 * Featured spotlight member for homepage.
 *
 * @return WP_Post|null
 */
function blc_get_spotlight_member() {
	$query = new WP_Query(
		array(
			'post_type'      => 'blc_member',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				blc_member_not_archived_meta_query(),
				array(
					'key'   => 'blc_featured_spotlight',
					'value' => '1',
				),
			),
		)
	);

	if ( $query->have_posts() ) {
		return $query->posts[0];
	}

	$public = blc_get_public_directory_posts();

	return $public ? $public[0] : null;
}
