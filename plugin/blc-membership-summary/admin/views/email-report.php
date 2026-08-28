<?php
/**
 * HTML email template for membership summary.
 *
 * @package BLC_Membership_Summary
 *
 * @var array<string, mixed> $report
 */

defined( 'ABSPATH' ) || exit;

$site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
$when      = wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $report['generated_at'] );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo esc_html( $site_name ); ?> — <?php esc_html_e( 'Membership summary', 'blc-membership-summary' ); ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #0a0a0a; line-height: 1.5;">
	<h1 style="font-size: 20px; margin: 0 0 8px;"><?php echo esc_html( $site_name ); ?></h1>
	<p style="margin: 0 0 20px; color: #5c5c5c;"><?php esc_html_e( 'Membership summary', 'blc-membership-summary' ); ?> · <?php echo esc_html( $when ); ?></p>

	<table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; font-size: 13px; min-width: 640px;">
		<thead style="background: #306ca8; color: #fff;">
			<tr>
				<th rowspan="2"><?php esc_html_e( 'Level', 'blc-membership-summary' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Total', 'blc-membership-summary' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Active', 'blc-membership-summary' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Renewal overdue', 'blc-membership-summary' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Lapsed', 'blc-membership-summary' ); ?></th>
				<th colspan="3"><?php esc_html_e( 'Pending', 'blc-membership-summary' ); ?></th>
				<th colspan="2"><?php esc_html_e( 'New in last', 'blc-membership-summary' ); ?></th>
			</tr>
			<tr>
				<th><?php esc_html_e( 'New', 'blc-membership-summary' ); ?></th>
				<th><?php esc_html_e( 'Renewal', 'blc-membership-summary' ); ?></th>
				<th><?php esc_html_e( 'Level change', 'blc-membership-summary' ); ?></th>
				<th><?php esc_html_e( '7 days', 'blc-membership-summary' ); ?></th>
				<th><?php esc_html_e( '30 days', 'blc-membership-summary' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $report['rows'] as $row ) : ?>
				<tr>
					<th scope="row" style="text-align: left;"><?php echo esc_html( $row['level_name'] ); ?></th>
					<td style="text-align: center;"><?php echo esc_html( (string) $row['total'] ); ?></td>
					<td style="text-align: center;"><?php echo esc_html( $row['active'] ? (string) $row['active'] : '—' ); ?></td>
					<td style="text-align: center;"><?php echo esc_html( $row['renewal_overdue'] ? (string) $row['renewal_overdue'] : '—' ); ?></td>
					<td style="text-align: center;"><?php echo esc_html( $row['lapsed'] ? (string) $row['lapsed'] : '—' ); ?></td>
					<td style="text-align: center;"><?php echo esc_html( $row['pending_new'] ? (string) $row['pending_new'] : '—' ); ?></td>
					<td style="text-align: center;"><?php echo esc_html( $row['pending_renewal'] ? (string) $row['pending_renewal'] : '—' ); ?></td>
					<td style="text-align: center;"><?php echo esc_html( $row['pending_level'] ? (string) $row['pending_level'] : '—' ); ?></td>
					<td style="text-align: center;"><?php echo esc_html( $row['new_7_days'] ? (string) $row['new_7_days'] : '—' ); ?></td>
					<td style="text-align: center;"><?php echo esc_html( $row['new_30_days'] ? (string) $row['new_30_days'] : '—' ); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr style="font-weight: bold; background: #eef3f8;">
				<th scope="row" style="text-align: left;"><?php esc_html_e( 'Total', 'blc-membership-summary' ); ?></th>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['total'] ); ?></td>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['active'] ); ?></td>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['renewal_overdue'] ); ?></td>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['lapsed'] ); ?></td>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['pending_new'] ); ?></td>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['pending_renewal'] ); ?></td>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['pending_level'] ); ?></td>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['new_7_days'] ); ?></td>
				<td style="text-align: center;"><?php echo esc_html( (string) $report['totals']['new_30_days'] ); ?></td>
			</tr>
		</tbody>
	</table>

	<p style="margin-top: 24px; font-size: 12px; color: #5c5c5c;">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=blc-membership-summary' ) ); ?>"><?php esc_html_e( 'View in WordPress admin', 'blc-membership-summary' ); ?></a>
	</p>
</body>
</html>
