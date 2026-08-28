<?php
/**
 * Front page template.
 *
 * @package BLC_Chicago
 */

get_header();
?>

<main>
	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/pillars' );
	get_template_part( 'template-parts/home/events' );
	get_template_part( 'template-parts/home/spotlight' );
	get_template_part( 'template-parts/home/articles' );
	blc_render_join_band();
	?>
</main>

<?php
get_footer();
