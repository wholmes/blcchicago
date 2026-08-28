<?php
/**
 * Default page template.
 *
 * @package BLC_Chicago
 */

get_header();
?>

<main>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<header class="page-hero page-hero--brand">
			<div class="container page-hero__inner reveal">
				<h1><?php the_title(); ?></h1>
			</div>
		</header>
		<section class="section">
			<div class="container container--narrow">
				<div class="reveal entry-content">
					<?php the_content(); ?>
				</div>
			</div>
		</section>
	<?php endwhile; ?>
	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
