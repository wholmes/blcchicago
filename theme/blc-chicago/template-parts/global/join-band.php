<?php
/**
 * Join band — pre-footer CTA (shared across pages).
 *
 * @package BLC_Chicago
 */
?>
<section class="section section--ink">
	<div class="container join-band reveal">
		<div>
			<p class="section__eyebrow"><?php esc_html_e( 'Membership', 'blc-chicago' ); ?></p>
			<h2><?php esc_html_e( 'Celebrate strengths. Share insights. Plan for future success.', 'blc-chicago' ); ?></h2>
			<p><?php esc_html_e( 'As a member of the BLC you gain directory visibility, member connections, and a seat at Chicago’s premier Black business table.', 'blc-chicago' ); ?></p>
		</div>
		<div class="btn-row">
			<a class="btn btn--coral" href="<?php echo esc_url( blc_get_join_url() ); ?>"><?php esc_html_e( 'View membership tiers', 'blc-chicago' ); ?></a>
		</div>
	</div>
</section>
