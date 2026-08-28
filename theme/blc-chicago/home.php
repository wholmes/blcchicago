<?php
/**
 * Blog / articles index (posts page).
 *
 * @package BLC_Chicago
 */

get_header();
?>

<main>
	<header class="page-hero">
		<div class="container">
			<p class="section__eyebrow"><?php esc_html_e( 'Insights', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'Articles', 'blc-chicago' ); ?></h1>
			<p><?php esc_html_e( 'Leadership stories, community updates, and perspectives from the BLC network.', 'blc-chicago' ); ?></p>
		</div>
	</header>

	<section class="section section--surface">
		<div class="container article-grid">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<a class="article-item reveal" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="article-item__media"><?php the_post_thumbnail( 'blc-article-card' ); ?></div>
						<?php endif; ?>
						<span class="article-item__meta">
							<?php
							$cat = get_the_category();
							echo esc_html( $cat ? $cat[0]->name : __( 'Article', 'blc-chicago' ) );
							echo ' · ' . esc_html( get_the_date( 'M j' ) );
							?>
						</span>
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
					</a>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No articles yet.', 'blc-chicago' ); ?></p>
			<?php endif; ?>
		</div>
		<div class="container">
			<?php the_posts_navigation(); ?>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
