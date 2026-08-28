<?php
/**
 * Formidable Forms Pro integration placeholders.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option keys for Formidable form IDs (set in WP admin after forms are built).
 */
function blc_formidable_option_keys() {
	return array(
		'contact' => 'blc_formidable_contact_id',
		'connect' => 'blc_formidable_connect_id',
		'profile' => 'blc_formidable_profile_id',
	);
}

/**
 * Render a Formidable form by stored option key, or admin placeholder.
 *
 * @param string $key contact|connect|profile.
 */
function blc_render_formidable_form( $key ) {
	$options = blc_formidable_option_keys();
	if ( ! isset( $options[ $key ] ) ) {
		return;
	}

	$form_id = (int) get_option( $options[ $key ], 0 );

	if ( $form_id && shortcode_exists( 'formidable' ) ) {
		echo do_shortcode( '[formidable id="' . absint( $form_id ) . '"]' );
		return;
	}

	if ( current_user_can( 'manage_options' ) ) {
		$settings_url = admin_url( 'options-general.php?page=blc-formidable' );
		$forms_url    = admin_url( 'admin.php?page=formidable' );
		printf(
			'<p class="blc-form-placeholder"><em>%s</em> <a href="%s">%s</a> · <a href="%s">%s</a></p>',
			esc_html__( 'No Formidable form linked yet.', 'blc-chicago' ),
			esc_url( $forms_url ),
			esc_html__( 'Create a form', 'blc-chicago' ),
			esc_url( $settings_url ),
			esc_html__( 'Paste the form ID in Settings → BLC Forms', 'blc-chicago' )
		);
	}
}

/**
 * Register theme settings for Formidable form IDs.
 */
function blc_register_formidable_settings() {
	register_setting(
		'blc_formidable',
		'blc_formidable_contact_id',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		)
	);
	register_setting( 'blc_formidable', 'blc_formidable_connect_id', array( 'type' => 'integer', 'sanitize_callback' => 'absint', 'default' => 0 ) );
	register_setting( 'blc_formidable', 'blc_formidable_profile_id', array( 'type' => 'integer', 'sanitize_callback' => 'absint', 'default' => 0 ) );
}
add_action( 'admin_init', 'blc_register_formidable_settings' );

/**
 * Add simple settings page under Settings → BLC Forms.
 */
function blc_formidable_settings_page() {
	add_options_page(
		__( 'BLC Forms', 'blc-chicago' ),
		__( 'BLC Forms', 'blc-chicago' ),
		'manage_options',
		'blc-formidable',
		'blc_render_formidable_settings_page'
	);
}
add_action( 'admin_menu', 'blc_formidable_settings_page' );

/**
 * Settings page markup.
 */
function blc_render_formidable_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$formidable_active = shortcode_exists( 'formidable' ) || class_exists( 'FrmAppHelper' );
	$forms_url         = admin_url( 'admin.php?page=formidable' );
	$contact_id        = (int) get_option( 'blc_formidable_contact_id', 0 );
	$connect_id        = (int) get_option( 'blc_formidable_connect_id', 0 );
	$profile_id        = (int) get_option( 'blc_formidable_profile_id', 0 );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'BLC Formidable Form IDs', 'blc-chicago' ); ?></h1>

		<?php if ( ! $formidable_active ) : ?>
			<div class="notice notice-warning">
				<p>
					<?php esc_html_e( 'Formidable Forms is not active. Install and activate Formidable (Lite or Pro), create forms, then return here.', 'blc-chicago' ); ?>
					<a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=formidable%20forms&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Install Formidable Forms', 'blc-chicago' ); ?></a>
				</p>
			</div>
		<?php else : ?>
			<div class="notice notice-info inline">
				<p>
					<strong><?php esc_html_e( 'How to get a form ID:', 'blc-chicago' ); ?></strong>
					<?php esc_html_e( 'Formidable → Forms → open a form. The ID is in the URL (?form=3) or in the forms list under the form name.', 'blc-chicago' ); ?>
					<a href="<?php echo esc_url( $forms_url ); ?>"><?php esc_html_e( 'Open Formidable Forms →', 'blc-chicago' ); ?></a>
				</p>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Leave an ID at 0 to skip that form. Contact is used on /contact/; Connect on member profiles; Profile for member self-edit (later).', 'blc-chicago' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'blc_formidable' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="blc_formidable_contact_id"><?php esc_html_e( 'Contact form', 'blc-chicago' ); ?></label></th>
					<td>
						<input name="blc_formidable_contact_id" id="blc_formidable_contact_id" type="number" min="0" value="<?php echo esc_attr( (string) $contact_id ); ?>" class="small-text">
						<?php if ( $contact_id ) : ?>
							<span class="description"><?php esc_html_e( 'Linked', 'blc-chicago' ); ?></span>
						<?php else : ?>
							<span class="description"><?php esc_html_e( 'Not linked (shows placeholder on /contact/ for admins only)', 'blc-chicago' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="blc_formidable_connect_id"><?php esc_html_e( 'Connect / introduction form', 'blc-chicago' ); ?></label></th>
					<td>
						<input name="blc_formidable_connect_id" id="blc_formidable_connect_id" type="number" min="0" value="<?php echo esc_attr( (string) $connect_id ); ?>" class="small-text">
						<span class="description"><?php esc_html_e( 'Member profile “request introduction”', 'blc-chicago' ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="blc_formidable_profile_id"><?php esc_html_e( 'Profile edit form', 'blc-chicago' ); ?></label></th>
					<td>
						<input name="blc_formidable_profile_id" id="blc_formidable_profile_id" type="number" min="0" value="<?php echo esc_attr( (string) $profile_id ); ?>" class="small-text">
						<span class="description"><?php esc_html_e( 'Optional — syncs to member meta when saved', 'blc-chicago' ); ?></span>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
