<?php
/**
 * Admin summary page template.
 *
 * @package BLC_Membership_Summary
 *
 * @var array<string, mixed> $report
 * @var string[]             $subscribers
 * @var string               $schedule
 * @var string               $last_sent
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render dash for zero counts.
 *
 * @param int $count Count.
 * @return string
 */
function blc_ms_format_count( $count ) {
	return $count > 0 ? (string) (int) $count : '&mdash;';
}

$updated = isset( $_GET['updated'] ) ? sanitize_key( wp_unslash( $_GET['updated'] ) ) : '';
?>
<div class="wrap blc-ms-admin">
	<h1><?php esc_html_e( 'Member list — Summary', 'blc-membership-summary' ); ?></h1>

	<?php if ( 'saved' === $updated ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Email settings saved.', 'blc-membership-summary' ); ?></p></div>
	<?php elseif ( 'saved-sent' === $updated ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved and report sent.', 'blc-membership-summary' ); ?></p></div>
	<?php elseif ( 'test-sent' === $updated ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Test report sent.', 'blc-membership-summary' ); ?></p></div>
	<?php elseif ( 'test-failed' === $updated ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Could not send test report. Check mail settings and subscribers.', 'blc-membership-summary' ); ?></p></div>
	<?php endif; ?>

	<?php if ( empty( $report['pmpro_active'] ) ) : ?>
		<div class="notice notice-warning"><p><?php esc_html_e( 'Paid Memberships Pro is required for live counts. Install and activate PMPro to populate this table.', 'blc-membership-summary' ); ?></p></div>
	<?php endif; ?>

	<div class="blc-ms-admin__grid">
		<section class="blc-ms-panel">
			<h2><?php esc_html_e( 'Membership by level', 'blc-membership-summary' ); ?></h2>
			<p class="description">
				<?php
				printf(
					/* translators: %s: generated datetime */
					esc_html__( 'Updated %s', 'blc-membership-summary' ),
					esc_html(
						wp_date(
							get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
							(int) $report['generated_at']
						)
					)
				);
				?>
			</p>

			<div class="blc-ms-table-wrap">
				<table class="blc-ms-table widefat striped">
					<thead>
						<tr>
							<th rowspan="2" scope="col"><?php esc_html_e( 'Level', 'blc-membership-summary' ); ?></th>
							<th rowspan="2" scope="col"><?php esc_html_e( 'Total (Bundles)', 'blc-membership-summary' ); ?></th>
							<th rowspan="2" scope="col"><?php esc_html_e( 'Active', 'blc-membership-summary' ); ?></th>
							<th rowspan="2" scope="col"><?php esc_html_e( 'Renewal overdue', 'blc-membership-summary' ); ?></th>
							<th rowspan="2" scope="col"><?php esc_html_e( 'Lapsed', 'blc-membership-summary' ); ?></th>
							<th colspan="3" scope="colgroup"><?php esc_html_e( 'Pending', 'blc-membership-summary' ); ?></th>
							<th colspan="2" scope="colgroup"><?php esc_html_e( 'New in last', 'blc-membership-summary' ); ?></th>
						</tr>
						<tr>
							<th scope="col"><?php esc_html_e( 'New', 'blc-membership-summary' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Renewal', 'blc-membership-summary' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Level change', 'blc-membership-summary' ); ?></th>
							<th scope="col"><?php esc_html_e( '7 days', 'blc-membership-summary' ); ?></th>
							<th scope="col"><?php esc_html_e( '30 days', 'blc-membership-summary' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $report['rows'] as $row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $row['level_name'] ); ?></th>
								<td>
									<?php echo esc_html( (string) $row['total'] ); ?>
									<?php if ( ! empty( $row['bundles'] ) ) : ?>
										<span class="blc-ms-bundle">(<?php echo esc_html( (string) $row['bundles'] ); ?>)</span>
									<?php endif; ?>
								</td>
								<?php
								$metrics = array( 'active', 'renewal_overdue', 'lapsed', 'pending_new', 'pending_renewal', 'pending_level', 'new_7_days', 'new_30_days' );
								foreach ( $metrics as $metric ) :
									$count = isset( $row[ $metric ] ) ? (int) $row[ $metric ] : 0;
									?>
									<td>
										<?php if ( $count > 0 ) : ?>
											<a href="<?php echo esc_url( BLC_MS_Report::get_filter_url( (int) $row['level_id'], $metric ) ); ?>">
												<?php echo esc_html( (string) $count ); ?>
											</a>
										<?php else : ?>
											&mdash;
										<?php endif; ?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
						<tr class="blc-ms-table__total">
							<th scope="row"><?php esc_html_e( 'Total', 'blc-membership-summary' ); ?></th>
							<td>
								<?php echo esc_html( (string) $report['totals']['total'] ); ?>
								<?php if ( ! empty( $report['totals']['bundles'] ) ) : ?>
									<span class="blc-ms-bundle">(<?php echo esc_html( (string) $report['totals']['bundles'] ); ?>)</span>
								<?php endif; ?>
							</td>
							<td><?php echo wp_kses_post( blc_ms_format_count( (int) $report['totals']['active'] ) ); ?></td>
							<td><?php echo wp_kses_post( blc_ms_format_count( (int) $report['totals']['renewal_overdue'] ) ); ?></td>
							<td><?php echo wp_kses_post( blc_ms_format_count( (int) $report['totals']['lapsed'] ) ); ?></td>
							<td><?php echo wp_kses_post( blc_ms_format_count( (int) $report['totals']['pending_new'] ) ); ?></td>
							<td><?php echo wp_kses_post( blc_ms_format_count( (int) $report['totals']['pending_renewal'] ) ); ?></td>
							<td><?php echo wp_kses_post( blc_ms_format_count( (int) $report['totals']['pending_level'] ) ); ?></td>
							<td><?php echo wp_kses_post( blc_ms_format_count( (int) $report['totals']['new_7_days'] ) ); ?></td>
							<td><?php echo wp_kses_post( blc_ms_format_count( (int) $report['totals']['new_30_days'] ) ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</section>

		<aside class="blc-ms-panel blc-ms-panel--email">
			<h2><?php esc_html_e( 'Email this summary', 'blc-membership-summary' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Add as many admin addresses as you need. Reports send automatically on the schedule you choose (7:00 AM site time).', 'blc-membership-summary' ); ?></p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="blc_ms_save_settings">
				<?php wp_nonce_field( 'blc_ms_save_settings' ); ?>

				<fieldset>
					<legend class="screen-reader-text"><?php esc_html_e( 'Schedule', 'blc-membership-summary' ); ?></legend>
					<p><strong><?php esc_html_e( 'Schedule', 'blc-membership-summary' ); ?></strong></p>
					<label><input type="radio" name="blc_ms_schedule" value="daily" <?php checked( $schedule, 'daily' ); ?>> <?php esc_html_e( 'Daily · 7:00 AM', 'blc-membership-summary' ); ?></label><br>
					<label><input type="radio" name="blc_ms_schedule" value="weekly" <?php checked( $schedule, 'weekly' ); ?>> <?php esc_html_e( 'Weekly · Monday 7:00 AM', 'blc-membership-summary' ); ?></label><br>
					<label><input type="radio" name="blc_ms_schedule" value="monthly" <?php checked( $schedule, 'monthly' ); ?>> <?php esc_html_e( 'Monthly · 1st at 7:00 AM', 'blc-membership-summary' ); ?></label>
				</fieldset>

				<p><strong><?php esc_html_e( 'Subscribers', 'blc-membership-summary' ); ?></strong></p>
				<div id="blc-ms-subscribers">
					<?php
					$index = 0;
					foreach ( $subscribers as $email ) :
						?>
						<p class="blc-ms-subscriber-row">
							<input type="email" name="blc_ms_subscribers[]" value="<?php echo esc_attr( $email ); ?>" class="regular-text">
							<button type="button" class="button blc-ms-remove-subscriber" aria-label="<?php esc_attr_e( 'Remove', 'blc-membership-summary' ); ?>">&times;</button>
						</p>
						<?php
						++$index;
					endforeach;
					?>
					<p class="blc-ms-subscriber-row blc-ms-subscriber-row--empty" <?php echo empty( $subscribers ) ? '' : 'hidden'; ?>>
						<input type="email" name="blc_ms_subscribers[]" value="" class="regular-text" placeholder="<?php esc_attr_e( 'email@example.com', 'blc-membership-summary' ); ?>">
					</p>
				</div>
				<p><button type="button" class="button" id="blc-ms-add-subscriber"><?php esc_html_e( 'Add email', 'blc-membership-summary' ); ?></button></p>

				<p>
					<label>
						<input type="checkbox" name="blc_ms_send_after_save" value="1">
						<?php esc_html_e( 'Send a test report after saving', 'blc-membership-summary' ); ?>
					</label>
				</p>

				<?php submit_button( __( 'Save subscribers', 'blc-membership-summary' ), 'primary', 'submit', false ); ?>
			</form>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="blc-ms-test-form">
				<input type="hidden" name="action" value="blc_ms_send_test">
				<?php wp_nonce_field( 'blc_ms_send_test' ); ?>
				<?php submit_button( __( 'Send test now', 'blc-membership-summary' ), 'secondary', 'submit', false ); ?>
			</form>

			<p class="description blc-ms-last-sent">
				<?php
				printf(
					/* translators: %s: last sent label */
					esc_html__( 'Last sent: %s', 'blc-membership-summary' ),
					esc_html( $last_sent )
				);
				?>
			</p>
		</aside>
	</div>
</div>

<script>
(function () {
	const list = document.getElementById('blc-ms-subscribers');
	const addBtn = document.getElementById('blc-ms-add-subscriber');
	if (!list || !addBtn) return;

	addBtn.addEventListener('click', function () {
		const row = document.createElement('p');
		row.className = 'blc-ms-subscriber-row';
		row.innerHTML = '<input type="email" name="blc_ms_subscribers[]" value="" class="regular-text" placeholder="email@example.com">' +
			'<button type="button" class="button blc-ms-remove-subscriber" aria-label="Remove">&times;</button>';
		list.appendChild(row);
	});

	list.addEventListener('click', function (event) {
		if (!event.target.classList.contains('blc-ms-remove-subscriber')) return;
		event.target.closest('.blc-ms-subscriber-row').remove();
	});
})();
</script>
