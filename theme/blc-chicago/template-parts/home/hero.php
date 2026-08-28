<?php
/**
 * Homepage hero.
 *
 * @package BLC_Chicago
 */
?>
<section class="hero">
	<div class="hero__media" aria-hidden="true">
		<img src="<?php echo esc_url( blc_get_hero_image_url() ); ?>" alt="">
	</div>
	<div class="hero__scrim" aria-hidden="true"></div>
	<div class="container hero__layout">
		<div class="hero__content">
			<p class="hero__eyebrow"><?php esc_html_e( 'Business Leadership Council · Chicago', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'Building bridges for Black business in Chicago.', 'blc-chicago' ); ?></h1>
			<p class="hero__lede"><?php esc_html_e( 'The Business Leadership Council convenes executives, entrepreneurs, and civic leaders committed to equitable growth and community wealth.', 'blc-chicago' ); ?></p>
			<div class="btn-row">
				<a class="btn btn--coral" href="<?php echo esc_url( blc_get_join_url() ); ?>"><?php esc_html_e( 'Become a Member', 'blc-chicago' ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_url( blc_get_public_directory_url() ); ?>"><?php esc_html_e( 'View Directory', 'blc-chicago' ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/leadership/' ) ); ?>"><?php esc_html_e( 'BLC Leadership', 'blc-chicago' ); ?></a>
			</div>
		</div>
	</div>
</section>
