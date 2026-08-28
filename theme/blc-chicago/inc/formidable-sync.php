<?php
/**
 * Sync Formidable profile form entries to blc_member meta.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default Formidable field key → member meta map.
 *
 * @return array<string, string>
 */
function blc_formidable_profile_field_map() {
	$map = array(
		'organization'      => 'blc_organization',
		'position'          => 'blc_position',
		'website'           => 'blc_website',
		'phone'             => 'blc_phone',
		'company_phone'     => 'blc_company_phone',
		'company_email'     => 'blc_company_email',
		'bio'               => 'blc_bio',
		'products_services' => 'blc_products_services',
		'collaboration'     => 'blc_collaboration',
		'interests'         => 'blc_interests',
		'skills'            => 'blc_skills',
		'intentions'        => 'blc_intentions',
	);

	return apply_filters( 'blc_formidable_profile_field_map', $map );
}

/**
 * Find member post linked to a user.
 *
 * @param int $user_id WordPress user ID.
 * @return int Post ID or 0.
 */
function blc_find_member_by_user_id( $user_id ) {
	$posts = get_posts(
		array(
			'post_type'      => 'blc_member',
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'meta_key'       => 'blc_wp_user_id',
			'meta_value'     => (string) $user_id,
			'fields'         => 'ids',
		)
	);

	return $posts ? (int) $posts[0] : 0;
}

/**
 * Sync a Formidable entry to member meta when profile form is saved.
 *
 * @param int $entry_id Formidable entry ID.
 */
function blc_sync_formidable_entry_to_member( $entry_id ) {
	if ( ! class_exists( 'FrmEntry' ) ) {
		return;
	}

	$profile_form_id = (int) get_option( 'blc_formidable_profile_id', 0 );
	if ( ! $profile_form_id ) {
		return;
	}

	$entry = FrmEntry::getOne( $entry_id, true );
	if ( ! $entry || (int) $entry->form_id !== $profile_form_id ) {
		return;
	}

	$user_id = (int) ( $entry->user_id ?? 0 );
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	if ( ! $user_id ) {
		return;
	}

	$post_id = blc_find_member_by_user_id( $user_id );
	if ( ! $post_id ) {
		return;
	}

	$map = blc_formidable_profile_field_map();
	foreach ( $map as $field_key => $meta_key ) {
		$value = FrmEntryMeta::get_entry_meta_by_field( $entry, $field_key );
		if ( null === $value || '' === $value ) {
			continue;
		}
		update_post_meta( $post_id, $meta_key, sanitize_text_field( (string) $value ) );
	}

	if ( ! empty( $map['bio'] ) ) {
		$bio = FrmEntryMeta::get_entry_meta_by_field( $entry, 'bio' );
		if ( $bio ) {
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => wp_kses_post( (string) $bio ),
				)
			);
		}
	}

	$org = FrmEntryMeta::get_entry_meta_by_field( $entry, 'organization' );
	if ( $org ) {
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => sanitize_text_field( (string) $org ),
			)
		);
	}

	do_action( 'blc_after_member_profile_sync', $post_id, $entry_id );
}
add_action( 'frm_after_create_entry', 'blc_sync_formidable_entry_to_member', 20, 1 );
add_action( 'frm_after_update_entry', 'blc_sync_formidable_entry_to_member', 20, 1 );
