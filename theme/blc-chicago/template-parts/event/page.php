<?php
/**
 * Events page body (hero + list + join band).
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;
?>

<main>
	<header class="page-hero">
		<div class="container">
			<p class="section__eyebrow"><?php esc_html_e( 'Calendar', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'Events', 'blc-chicago' ); ?></h1>
			<p><?php esc_html_e( 'Public event listings for the Chicago business community. Ticket purchases are available to BLC members in good standing.', 'blc-chicago' ); ?></p>
		</div>
	</header>

	<section class="section section--surface">
		<div class="container">
			<?php get_template_part( 'template-parts/event/list' ); ?>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>
