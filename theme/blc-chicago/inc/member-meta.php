<?php
/**
 * Member post meta keys and accessors.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registered meta keys for blc_member.
 *
 * @return string[]
 */
function blc_member_meta_keys() {
	return array(
		'blc_wa_user_id',
		'blc_organization',
		'blc_first_name',
		'blc_last_name',
		'blc_position',
		'blc_website',
		'blc_email',
		'blc_phone',
		'blc_company_phone',
		'blc_company_email',
		'blc_mailing_address',
		'blc_bio',
		'blc_products_services',
		'blc_glimpse',
		'blc_committee',
		'blc_collaboration',
		'blc_interests',
		'blc_skills',
		'blc_intentions',
		'blc_membership_status',
		'blc_member_since',
		'blc_renewal_due',
		'blc_member_role',
		'blc_bundle_id',
		'blc_profile_visibility',
		'blc_privacy_rules',
		'blc_archived',
		'blc_featured_spotlight',
		'blc_spotlight_quote',
	);
}

/**
 * Register post meta for REST and sanitization.
 */
function blc_register_member_meta() {
	$string_keys = blc_member_meta_keys();
	foreach ( $string_keys as $key ) {
		register_post_meta(
			'blc_member',
			$key,
			array(
				'single'            => true,
				'type'              => 'string',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'blc_register_member_meta' );

/**
 * Whether a field is visible to the current viewer.
 *
 * @param int    $post_id Member post ID.
 * @param string $field   Logical field name.
 * @return bool
 */
function blc_member_field_is_public( $post_id, $field ) {
	if ( blc_user_can_access_member_directory() ) {
		return true;
	}

	$rules_raw = get_post_meta( $post_id, 'blc_privacy_rules', true );
	if ( $rules_raw ) {
		$rules = json_decode( $rules_raw, true );
		if ( is_array( $rules ) && isset( $rules[ $field ] ) ) {
			return 'Anybody' === $rules[ $field ];
		}
	}

	return in_array( $field, array( 'organization', 'tier', 'industry', 'company_phone', 'company_email', 'website', 'products_services' ), true );
}

/**
 * Get sanitized meta for display.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @return string
 */
function blc_get_member_meta( $post_id, $key ) {
	return (string) get_post_meta( $post_id, $key, true );
}
