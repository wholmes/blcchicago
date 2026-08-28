<?php
/**
 * Template Name: Paywall
 * Template Post Type: page
 *
 * @package BLC_Chicago
 */

get_header();
?>

<main>
	<section class="section">
		<div class="container">
			<div class="login-panel reveal">
				<p class="section__eyebrow"><?php esc_html_e( 'Membership', 'blc-chicago' ); ?></p>
				<h1><?php esc_html_e( 'Renew to access member tools', 'blc-chicago' ); ?></h1>
				<p><?php esc_html_e( 'You’re signed in, but your membership isn’t active. The members-only directory and connection tools unlock after dues are current.', 'blc-chicago' ); ?></p>
				<div class="login-form__actions">
					<?php if ( function_exists( 'pmpro_url' ) ) : ?>
						<a class="btn btn--coral" href="<?php echo esc_url( pmpro_url( 'levels' ) ); ?>"><?php esc_html_e( 'Pay or renew · PMPro', 'blc-chicago' ); ?></a>
						<a class="btn btn--outline" href="<?php echo esc_url( pmpro_url( 'account' ) ); ?>"><?php esc_html_e( 'View account', 'blc-chicago' ); ?></a>
					<?php else : ?>
						<a class="btn btn--coral" href="<?php echo esc_url( blc_get_join_url() ); ?>"><?php esc_html_e( 'View membership tiers', 'blc-chicago' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
