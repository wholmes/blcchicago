<?php
/**
 * Fallback index template.
 *
 * @package BLC_Chicago
 */

get_header();
?>

<main>
	<section class="section">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<article <?php post_class( 'reveal' ); ?>>
						<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
				<?php the_posts_navigation(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No posts found.', 'blc-chicago' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
