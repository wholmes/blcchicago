<?php
/**
 * Theme activation: pages, permalinks, setup checklist.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Recommended pages and templates.
 *
 * @return array<string, array{title: string, template: string, set_front?: bool, set_posts?: bool}>
 */
function blc_recommended_pages() {
	return array(
		'directory'              => array(
			'title'    => __( 'Directory', 'blc-chicago' ),
			'template' => 'page-templates/public-directory.php',
		),
		'members/directory'      => array(
			'title'    => __( 'Member Network', 'blc-chicago' ),
			'template' => 'page-templates/members-directory.php',
		),
		'join-us'                => array(
			'title'    => __( 'Become a Member', 'blc-chicago' ),
			'template' => 'page-templates/join-us.php',
		),
		'contact'                => array(
			'title'    => __( 'Contact', 'blc-chicago' ),
			'template' => 'page-templates/contact.php',
		),
		'leadership'             => array(
			'title'    => __( 'Leadership', 'blc-chicago' ),
			'template' => 'page-templates/leadership.php',
		),
		'corporate-sponsorship'  => array(
			'title'    => __( 'Corporate Sponsorship', 'blc-chicago' ),
			'template' => 'page-templates/corporate-sponsorship.php',
		),
		'events'                 => array(
			'title'    => __( 'Events', 'blc-chicago' ),
			'template' => 'page-templates/events.php',
		),
		'account/paywall'        => array(
			'title'    => __( 'Renew Membership', 'blc-chicago' ),
			'template' => 'page-templates/paywall.php',
		),
		'articles'               => array(
			'title'      => __( 'Articles', 'blc-chicago' ),
			'template'   => '',
			'set_posts'  => true,
		),
	);
}

/**
 * Create a page if missing.
 *
 * @param string $path   Page path (may include slashes).
 * @param array  $config Page config.
 * @return int Page ID.
 */
function blc_ensure_page( $path, array $config ) {
	$existing = get_page_by_path( $path );
	if ( $existing ) {
		if ( ! empty( $config['template'] ) ) {
			$current = get_post_meta( $existing->ID, '_wp_page_template', true );
			if ( $config['template'] !== $current ) {
				update_post_meta( $existing->ID, '_wp_page_template', $config['template'] );
			}
		}
		return (int) $existing->ID;
	}

	$parts = explode( '/', $path );
	$slug  = end( $parts );
	$parent = 0;

	if ( count( $parts ) > 1 ) {
		$parent_path = implode( '/', array_slice( $parts, 0, -1 ) );
		$parent      = blc_ensure_page(
			$parent_path,
			array(
				'title'    => ucwords( str_replace( '-', ' ', $parts[ count( $parts ) - 2 ] ) ),
				'template' => '',
			)
		);
	}

	$page_id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_title'  => $config['title'],
			'post_name'   => $slug,
			'post_parent' => $parent,
		)
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	if ( ! empty( $config['template'] ) ) {
		update_post_meta( $page_id, '_wp_page_template', $config['template'] );
	}

	return (int) $page_id;
}

/**
 * Run on theme activation.
 */
function blc_on_theme_activate() {
	blc_seed_membership_tiers();
	blc_seed_default_leaders();
	blc_seed_default_event();

	$posts_page = 0;

	foreach ( blc_recommended_pages() as $path => $config ) {
		$page_id = blc_ensure_page( $path, $config );
		if ( ! empty( $config['set_posts'] ) && $page_id ) {
			$posts_page = $page_id;
		}
	}

	if ( $posts_page ) {
		update_option( 'page_for_posts', $posts_page );
	}

	update_option( 'blc_show_setup_notice', 1, false );

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'blc_on_theme_activate' );

/**
 * Ensure page templates stay assigned (e.g. after CPT slug fixes).
 */
function blc_repair_recommended_pages() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	foreach ( blc_recommended_pages() as $path => $config ) {
		blc_ensure_page( $path, $config );
	}
}
add_action( 'admin_init', 'blc_repair_recommended_pages' );

/**
 * Admin notice with setup checklist.
 */
function blc_setup_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! get_option( 'blc_show_setup_notice' ) ) {
		return;
	}

	$setup_url = admin_url( 'options-general.php?page=blc-setup' );
	?>
	<div class="notice notice-info is-dismissible" data-blc-setup-notice>
		<p>
			<strong><?php esc_html_e( 'BLC Chicago theme activated.', 'blc-chicago' ); ?></strong>
			<?php esc_html_e( 'Recommended pages were created. Next: install PMPro + Formidable, map levels, and import members.', 'blc-chicago' ); ?>
			<a href="<?php echo esc_url( $setup_url ); ?>"><?php esc_html_e( 'Open setup checklist →', 'blc-chicago' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'blc_setup_admin_notice' );

/**
 * Dismiss setup notice.
 */
function blc_dismiss_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized', 'blc-chicago' ) );
	}

	check_admin_referer( 'blc_dismiss_setup' );
	delete_option( 'blc_show_setup_notice' );
	wp_safe_redirect( wp_get_referer() ?: admin_url() );
	exit;
}
add_action( 'admin_post_blc_dismiss_setup', 'blc_dismiss_setup_notice' );
