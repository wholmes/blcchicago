<?php
/**
 * Wild Apricot CSV member import (admin).
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register import admin page.
 */
function blc_register_member_import_page() {
	add_submenu_page(
		'edit.php?post_type=blc_member',
		__( 'Import Members', 'blc-chicago' ),
		__( 'Import', 'blc-chicago' ),
		'manage_options',
		'blc-import-members',
		'blc_render_member_import_page'
	);
}
add_action( 'admin_menu', 'blc_register_member_import_page' );

/**
 * Parse WA "Details to show" into JSON privacy map.
 *
 * @param string $raw Raw CSV value.
 * @return string JSON.
 */
function blc_parse_wa_privacy_rules( $raw ) {
	$rules = array();
	if ( ! $raw ) {
		return wp_json_encode( $rules );
	}

	foreach ( explode( '|', $raw ) as $segment ) {
		$parts = explode( '&&', $segment, 2 );
		if ( 2 !== count( $parts ) ) {
			continue;
		}
		$field = trim( $parts[0] );
		$level = trim( $parts[1] );
		if ( $field && $level ) {
			$rules[ sanitize_key( str_replace( ' ', '_', strtolower( $field ) ) ) ] = $level;
		}
	}

	return wp_json_encode( $rules );
}

/**
 * Find existing member post by WA user ID.
 *
 * @param string $wa_id Wild Apricot user ID.
 * @return int Post ID or 0.
 */
function blc_find_member_by_wa_id( $wa_id ) {
	$posts = get_posts(
		array(
			'post_type'      => 'blc_member',
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'meta_key'       => 'blc_wa_user_id',
			'meta_value'     => $wa_id,
			'fields'         => 'ids',
		)
	);

	return $posts ? (int) $posts[0] : 0;
}

/**
 * Import one CSV row.
 *
 * @param array<string, string> $row CSV row keyed by column header.
 * @return array{action: string, post_id: int}
 */
function blc_import_member_row( $row ) {
	$wa_id = trim( $row['User ID'] ?? '' );
	if ( ! $wa_id ) {
		return array( 'action' => 'skipped', 'post_id' => 0 );
	}

	if ( 'Yes' === ( $row['Archived'] ?? '' ) ) {
		return array( 'action' => 'archived', 'post_id' => 0 );
	}

	$org       = trim( $row['Organization'] ?? '' );
	$first     = trim( $row['First name'] ?? '' );
	$last      = trim( $row['Last name'] ?? '' );
	$title     = $org ?: trim( $first . ' ' . $last );
	$post_id   = blc_find_member_by_wa_id( $wa_id );
	$is_update = (bool) $post_id;

	if ( ! $post_id ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'blc_member',
				'post_status' => 'publish',
				'post_title'  => $title,
			)
		);
	} else {
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $title,
			)
		);
	}

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return array( 'action' => 'error', 'post_id' => 0 );
	}

	$meta_map = array(
		'blc_wa_user_id'         => $wa_id,
		'blc_organization'       => $org,
		'blc_first_name'         => $first,
		'blc_last_name'          => $last,
		'blc_position'           => $row['Position/Title'] ?? '',
		'blc_website'            => $row['Website'] ?? '',
		'blc_email'              => $row['Email'] ?? '',
		'blc_phone'              => $row['Phone'] ?? '',
		'blc_company_phone'      => $row['Company Phone'] ?? '',
		'blc_company_email'      => $row['Company Email'] ?? '',
		'blc_mailing_address'    => $row['Mailing Address'] ?? '',
		'blc_bio'                => $row['Bio'] ?? '',
		'blc_products_services'  => $row['Products or Services'] ?? '',
		'blc_committee'          => $row['Committee Participation'] ?? '',
		'blc_collaboration'      => $row['Curated Collaboration'] ?? '',
		'blc_interests'          => $row['Area of Interest'] ?? '',
		'blc_skills'             => $row['Skills and Expertise'] ?? '',
		'blc_intentions'         => $row['Intentions'] ?? '',
		'blc_membership_status'  => $row['Membership status'] ?? '',
		'blc_member_since'       => $row['Member since'] ?? '',
		'blc_renewal_due'        => $row['Renewal due'] ?? '',
		'blc_member_role'        => $row['Member role'] ?? '',
		'blc_bundle_id'          => $row['Member bundle ID or email'] ?? '',
		'blc_profile_visibility' => $row['Access to profile by others'] ?? '',
		'blc_archived'           => $row['Archived'] ?? '',
	);

	foreach ( $meta_map as $key => $value ) {
		update_post_meta( $post_id, $key, sanitize_text_field( (string) $value ) );
	}

	update_post_meta( $post_id, 'blc_privacy_rules', blc_parse_wa_privacy_rules( $row['Details to show'] ?? '' ) );

	$bio = trim( $row['Bio'] ?? '' );
	if ( $bio ) {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => wp_kses_post( $bio ),
			)
		);
	}

	$tier_name = trim( $row['Membership level'] ?? '' );
	if ( $tier_name ) {
		wp_set_object_terms( $post_id, array( $tier_name ), 'blc_membership_tier', false );
	}

	$industry_raw = trim( $row['Industry'] ?? '' );
	if ( $industry_raw ) {
		$industries = array_map( 'trim', preg_split( '/[,;]/', $industry_raw ) );
		$industries = array_filter( $industries );
		if ( $industries ) {
			wp_set_object_terms( $post_id, $industries, 'blc_industry', false );
		}
	}

	$email = sanitize_email( $row['Email'] ?? '' );
	if ( $email && ! email_exists( $email ) ) {
		$user_id = wp_insert_user(
			array(
				'user_login'   => $email,
				'user_email'   => $email,
				'user_pass'    => wp_generate_password( 24 ),
				'first_name'   => $first,
				'last_name'    => $last,
				'display_name' => trim( $first . ' ' . $last ),
				'role'         => 'subscriber',
			)
		);
		if ( ! is_wp_error( $user_id ) ) {
			update_post_meta( $post_id, 'blc_wp_user_id', (string) $user_id );
		}
	} else	if ( $email ) {
		$user = get_user_by( 'email', $email );
		if ( $user ) {
			update_post_meta( $post_id, 'blc_wp_user_id', (string) $user->ID );
		}
	}

	blc_maybe_sync_imported_member_pmpro( $post_id );

	return array(
		'action'  => $is_update ? 'updated' : 'created',
		'post_id' => (int) $post_id,
	);
}

/**
 * Run CSV import from uploaded file path.
 *
 * @param string $path Absolute path to CSV.
 * @return array{created: int, updated: int, skipped: int, archived: int, errors: int}
 */
function blc_run_member_csv_import( $path ) {
	$stats = array(
		'created'  => 0,
		'updated'  => 0,
		'skipped'  => 0,
		'archived' => 0,
		'errors'   => 0,
	);

	if ( ! is_readable( $path ) ) {
		return $stats;
	}

	$handle = fopen( $path, 'r' );
	if ( ! $handle ) {
		return $stats;
	}

	$headers = fgetcsv( $handle );
	if ( ! $headers ) {
		fclose( $handle );
		return $stats;
	}

	$headers[0] = preg_replace( '/^\xEF\xBB\xBF/', '', $headers[0] );

	while ( ( $data = fgetcsv( $handle ) ) !== false ) {
		if ( count( $data ) < count( $headers ) ) {
			$data = array_pad( $data, count( $headers ), '' );
		}
		$row    = array_combine( $headers, $data );
		$result = blc_import_member_row( $row );
		$action = $result['action'];

		if ( isset( $stats[ $action ] ) ) {
			++$stats[ $action ];
		} else {
			++$stats['errors'];
		}
	}

	fclose( $handle );
	blc_seed_membership_tiers();

	return $stats;
}

/**
 * Handle import form POST.
 */
function blc_handle_member_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized', 'blc-chicago' ) );
	}

	check_admin_referer( 'blc_import_members' );

	if ( empty( $_FILES['blc_csv']['tmp_name'] ) ) {
		wp_safe_redirect( add_query_arg( 'blc_import', 'no_file', admin_url( 'edit.php?post_type=blc_member&page=blc-import-members' ) ) );
		exit;
	}

	$tmp = sanitize_text_field( wp_unslash( $_FILES['blc_csv']['tmp_name'] ) );
	$stats = blc_run_member_csv_import( $tmp );

	set_transient( 'blc_import_stats', $stats, MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'edit.php?post_type=blc_member&page=blc-import-members&blc_import=done' ) );
	exit;
}
add_action( 'admin_post_blc_import_members', 'blc_handle_member_import' );

/**
 * Import admin page.
 */
function blc_render_member_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$stats = get_transient( 'blc_import_stats' );
	if ( $stats ) {
		delete_transient( 'blc_import_stats' );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Import Wild Apricot Members', 'blc-chicago' ); ?></h1>
		<p><?php esc_html_e( 'Upload the Wild Apricot members CSV export. Rows are keyed by User ID; archived contacts are skipped.', 'blc-chicago' ); ?></p>

		<?php if ( $stats ) : ?>
			<div class="notice notice-success">
				<p>
					<?php
					printf(
						/* translators: 1: created 2: updated 3: skipped 4: archived */
						esc_html__( 'Import complete — %1$d created, %2$d updated, %3$d skipped, %4$d archived.', 'blc-chicago' ),
						(int) $stats['created'],
						(int) $stats['updated'],
						(int) $stats['skipped'],
						(int) $stats['archived']
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
			<?php wp_nonce_field( 'blc_import_members' ); ?>
			<input type="hidden" name="action" value="blc_import_members">
			<p>
				<input type="file" name="blc_csv" accept=".csv,text/csv" required>
			</p>
			<?php submit_button( __( 'Import CSV', 'blc-chicago' ) ); ?>
		</form>

		<hr>
		<h2><?php esc_html_e( 'Tier seed', 'blc-chicago' ); ?></h2>
		<p><?php esc_html_e( 'Re-create membership tier taxonomy terms from the Wild Apricot level list.', 'blc-chicago' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'blc_seed_tiers' ); ?>
			<input type="hidden" name="action" value="blc_seed_tiers">
			<?php submit_button( __( 'Seed tiers', 'blc-chicago' ), 'secondary' ); ?>
		</form>
	</div>
	<?php
}
