<?php
/**
 * Single post (articles).
 *
 * @package BLC_Chicago
 */

get_header();
?>

<main>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<header class="page-hero">
			<div class="container container--narrow">
				<p class="section__eyebrow">
					<?php
					$cat = get_the_category();
					echo esc_html( $cat ? $cat[0]->name : __( 'Article', 'blc-chicago' ) );
					echo ' · ' . esc_html( get_the_date() );
					?>
				</p>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</header>
		<article class="section section--surface">
			<div class="container container--narrow entry-content">
				<?php the_content(); ?>
				<p><a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/articles/' ) ); ?>"><?php esc_html_e( '← All articles', 'blc-chicago' ); ?></a></p>
			</div>
		</article>
	<?php endwhile; ?>
	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
