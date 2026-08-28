<?php
/**
 * Members-only profile panels.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

if ( ! blc_user_can_access_member_directory() ) {
	return;
}

$post_id = get_the_ID();
$first   = blc_get_member_meta( $post_id, 'blc_first_name' );
$last    = blc_get_member_meta( $post_id, 'blc_last_name' );
$position = blc_get_member_meta( $post_id, 'blc_position' );
?>

<?php if ( $first || $last || $position ) : ?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'Primary contact', 'blc-chicago' ); ?></h2>
		<p>
			<?php echo esc_html( trim( $first . ' ' . $last ) ); ?>
			<?php if ( $position ) : ?>
				<br><span class="profile-meta"><?php echo esc_html( $position ); ?></span>
			<?php endif; ?>
		</p>
	</div>
<?php endif; ?>

<?php
$collab = blc_get_member_meta( $post_id, 'blc_collaboration' );
if ( $collab ) :
	?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'Open to', 'blc-chicago' ); ?></h2>
		<p><?php echo esc_html( $collab ); ?></p>
	</div>
<?php endif; ?>

<?php
$skills = blc_get_member_meta( $post_id, 'blc_skills' );
if ( $skills ) :
	?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'Skills & expertise', 'blc-chicago' ); ?></h2>
		<p><?php echo esc_html( $skills ); ?></p>
	</div>
<?php endif; ?>

<?php
$interests = blc_get_member_meta( $post_id, 'blc_interests' );
if ( $interests ) :
	?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'Areas of interest', 'blc-chicago' ); ?></h2>
		<p><?php echo esc_html( $interests ); ?></p>
	</div>
<?php endif; ?>

<?php
$intentions = blc_get_member_meta( $post_id, 'blc_intentions' );
if ( $intentions ) :
	?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'Intentions', 'blc-chicago' ); ?></h2>
		<p><?php echo esc_html( $intentions ); ?></p>
	</div>
<?php endif; ?>

<?php
$committee = blc_get_member_meta( $post_id, 'blc_committee' );
if ( $committee ) :
	?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'Committee participation', 'blc-chicago' ); ?></h2>
		<p><?php echo esc_html( $committee ); ?></p>
	</div>
<?php endif; ?>
