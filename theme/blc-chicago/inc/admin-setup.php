<?php
/**
 * BLC Setup admin page (checklist + PMPro mapping).
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register setup page.
 */
function blc_register_setup_page() {
	add_options_page(
		__( 'BLC Setup', 'blc-chicago' ),
		__( 'BLC Setup', 'blc-chicago' ),
		'manage_options',
		'blc-setup',
		'blc_render_setup_page'
	);
}
add_action( 'admin_menu', 'blc_register_setup_page' );

/**
 * Save PMPro level map.
 */
function blc_save_pmpro_level_map() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_POST['blc_pmpro_map_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['blc_pmpro_map_nonce'] ) ), 'blc_save_pmpro_map' ) ) {
		return;
	}

	$raw = isset( $_POST['blc_pmpro_level_tier'] ) ? wp_unslash( $_POST['blc_pmpro_level_tier'] ) : array();
	if ( ! is_array( $raw ) ) {
		return;
	}

	$map = array();
	foreach ( $raw as $level_id => $tier_slug ) {
		$level_id  = (int) $level_id;
		$tier_slug = sanitize_title( (string) $tier_slug );
		if ( $level_id && $tier_slug ) {
			$map[ $level_id ] = $tier_slug;
		}
	}

	update_option( 'blc_pmpro_level_tier_map', $map, false );
	add_settings_error( 'blc_setup', 'blc_pmpro_saved', __( 'PMPro level mapping saved.', 'blc-chicago' ), 'success' );
}
add_action( 'admin_init', 'blc_save_pmpro_level_map' );

/**
 * Render setup page.
 */
function blc_render_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$import_url  = admin_url( 'edit.php?post_type=blc_member&page=blc-import-members' );
	$forms_url   = admin_url( 'options-general.php?page=blc-formidable' );
	$levels      = function_exists( 'pmpro_getAllLevels' ) ? pmpro_getAllLevels( true, true ) : array();
	$tier_terms  = get_terms(
		array(
			'taxonomy'   => 'blc_membership_tier',
			'hide_empty' => false,
		)
	);
	$stored_map  = blc_get_stored_pmpro_level_tier_map();
	$blueprint   = blc_pmpro_level_blueprint();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'BLC Setup', 'blc-chicago' ); ?></h1>

		<?php settings_errors( 'blc_setup' ); ?>

		<h2><?php esc_html_e( 'Checklist', 'blc-chicago' ); ?></h2>
		<ol style="list-style:decimal;padding-left:1.5rem;max-width:42rem">
			<li><?php esc_html_e( 'Install & activate Paid Memberships Pro + Formidable Pro.', 'blc-chicago' ); ?></li>
			<li><?php esc_html_e( 'Install & activate BLC Membership Summary plugin.', 'blc-chicago' ); ?></li>
			<li><a href="<?php echo esc_url( $import_url ); ?>"><?php esc_html_e( 'Import Wild Apricot members CSV', 'blc-chicago' ); ?></a></li>
			<li><?php esc_html_e( 'Create PMPro membership levels (see blueprint below).', 'blc-chicago' ); ?></li>
			<li><?php esc_html_e( 'Map each PMPro level to a membership tier (form below).', 'blc-chicago' ); ?></li>
			<li><a href="<?php echo esc_url( $forms_url ); ?>"><?php esc_html_e( 'Assign Formidable form IDs', 'blc-chicago' ); ?></a></li>
			<li><?php esc_html_e( 'Settings → Reading: confirm Articles page is set as Posts page.', 'blc-chicago' ); ?></li>
			<li><?php esc_html_e( 'Settings → Permalinks: Post name (/%postname%/) recommended.', 'blc-chicago' ); ?></li>
		</ol>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1rem">
			<?php wp_nonce_field( 'blc_dismiss_setup' ); ?>
			<input type="hidden" name="action" value="blc_dismiss_setup">
			<?php submit_button( __( 'Dismiss activation notice', 'blc-chicago' ), 'secondary', 'submit', false ); ?>
		</form>

		<hr>

		<h2><?php esc_html_e( 'PMPro level → tier mapping', 'blc-chicago' ); ?></h2>
		<?php if ( ! blc_is_pmpro_active() ) : ?>
			<p><em><?php esc_html_e( 'Activate Paid Memberships Pro to map levels here.', 'blc-chicago' ); ?></em></p>
		<?php elseif ( ! $levels ) : ?>
			<p><em><?php esc_html_e( 'No PMPro levels found. Create levels in Memberships → Membership Levels.', 'blc-chicago' ); ?></em></p>
		<?php else : ?>
			<form method="post">
				<?php wp_nonce_field( 'blc_save_pmpro_map', 'blc_pmpro_map_nonce' ); ?>
				<table class="widefat striped" style="max-width:36rem">
					<thead>
						<tr>
							<th><?php esc_html_e( 'PMPro level', 'blc-chicago' ); ?></th>
							<th><?php esc_html_e( 'BLC tier', 'blc-chicago' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $levels as $level ) : ?>
							<tr>
								<td><?php echo esc_html( $level->name ); ?> <code>#<?php echo (int) $level->id; ?></code></td>
								<td>
									<select name="blc_pmpro_level_tier[<?php echo (int) $level->id; ?>]">
										<option value=""><?php esc_html_e( '— Select tier —', 'blc-chicago' ); ?></option>
										<?php if ( $tier_terms && ! is_wp_error( $tier_terms ) ) : ?>
											<?php foreach ( $tier_terms as $term ) : ?>
												<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $stored_map[ (int) $level->id ] ?? '', $term->slug ); ?>>
													<?php echo esc_html( $term->name ); ?>
												</option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php submit_button( __( 'Save mapping', 'blc-chicago' ) ); ?>
			</form>
		<?php endif; ?>

		<hr>

		<h2><?php esc_html_e( 'Suggested PMPro levels', 'blc-chicago' ); ?></h2>
		<p><?php esc_html_e( 'Create these in PMPro admin (pricing configured there). Tier terms are seeded automatically.', 'blc-chicago' ); ?></p>
		<ul style="columns:2;max-width:42rem">
			<?php foreach ( $blueprint as $item ) : ?>
				<li><strong><?php echo esc_html( $item['name'] ); ?></strong> <code><?php echo esc_html( $item['slug'] ); ?></code></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}
