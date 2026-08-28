<?php
/**
 * Homepage events section.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section section--surface">
	<div class="container">
		<div class="section__head reveal">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Calendar', 'blc-chicago' ); ?></p>
				<h2><?php esc_html_e( 'Recent & upcoming events', 'blc-chicago' ); ?></h2>
			</div>
			<a class="section__link" href="<?php echo esc_url( home_url( '/events/' ) ); ?>"><?php esc_html_e( 'All events →', 'blc-chicago' ); ?></a>
		</div>
		<?php get_template_part( 'template-parts/event/list', null, array( 'posts_per_page' => 3 ) ); ?>
	</div>
</section>
