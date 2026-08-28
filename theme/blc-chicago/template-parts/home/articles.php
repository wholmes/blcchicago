<?php
/**
 * Homepage latest articles.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

$articles = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="section">
	<div class="container">
		<div class="section__head reveal">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Insights', 'blc-chicago' ); ?></p>
				<h2><?php esc_html_e( 'Latest articles', 'blc-chicago' ); ?></h2>
			</div>
			<a class="section__link" href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/articles/' ) ); ?>"><?php esc_html_e( 'All articles →', 'blc-chicago' ); ?></a>
		</div>
		<div class="article-grid reveal">
			<?php if ( $articles->have_posts() ) : ?>
				<?php
				while ( $articles->have_posts() ) :
					$articles->the_post();
					?>
					<a class="article-item" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="article-item__media"><?php the_post_thumbnail( 'blc-article-card' ); ?></div>
						<?php endif; ?>
						<span class="article-item__meta">
							<?php
							$cat = get_the_category();
							echo esc_html( $cat ? $cat[0]->name : __( 'Article', 'blc-chicago' ) );
							echo ' · ';
							echo esc_html( get_the_date( 'M j' ) );
							?>
						</span>
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
					</a>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Publish articles in WordPress to populate this section.', 'blc-chicago' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
