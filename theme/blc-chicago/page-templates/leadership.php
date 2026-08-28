<?php
/**
 * Template Name: Leadership
 * Template Post Type: page
 *
 * @package BLC_Chicago
 */

get_header();

$featured = get_posts(
	array(
		'post_type'      => 'blc_leader',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'meta_key'       => 'blc_leader_featured',
		'meta_value'     => '1',
	)
);

$board = get_posts(
	array(
		'post_type'      => 'blc_leader',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'     => 'blc_leader_featured',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'blc_leader_featured',
				'value'   => '1',
				'compare' => '!=',
			),
		),
	)
);
?>

<main>
	<header class="page-hero page-hero--brand page-hero--photo">
		<div class="page-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( blc_get_hero_image_url() ); ?>" alt="">
		</div>
		<div class="container">
			<p class="section__eyebrow"><?php esc_html_e( 'Who We Are', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'BLC leadership', 'blc-chicago' ); ?></h1>
			<p><?php esc_html_e( 'BLC is Chicago’s premier Black business organization of more than 120 of Chicago’s Black business leaders and entrepreneurs representing over 25 industries. We are harnessing the power of today’s Black business leaders to build the next generation of leaders. We are Black business leaders and entrepreneurs in positions of influence across the spectrum of business and civic leadership.', 'blc-chicago' ); ?></p>
			<hr class="page-hero__rule">
		</div>
	</header>

	<?php if ( $featured ) : ?>
		<section class="section section--surface">
			<div class="container">
				<div class="section__head">
					<div>
						<p class="section__eyebrow"><?php esc_html_e( 'Executive', 'blc-chicago' ); ?></p>
						<h2><?php esc_html_e( 'Leading the council', 'blc-chicago' ); ?></h2>
					</div>
				</div>
				<div class="leader-feature">
					<?php foreach ( $featured as $leader ) : ?>
						<?php
						global $post;
						$post = $leader;
						setup_postdata( $leader );
						get_template_part( 'template-parts/leader/card', null, array( 'leader' => $leader ) );
						?>
					<?php endforeach; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $board ) : ?>
		<section class="section">
			<div class="container">
				<div class="section__head reveal">
					<div>
						<p class="section__eyebrow"><?php esc_html_e( 'Board', 'blc-chicago' ); ?></p>
						<h2><?php esc_html_e( 'BLC leadership team', 'blc-chicago' ); ?></h2>
					</div>
				</div>
				<div class="leader-grid reveal">
					<?php foreach ( $board as $leader ) : ?>
						<?php
						global $post;
						$post = $leader;
						setup_postdata( $leader );
						get_template_part( 'template-parts/leader/card', null, array( 'leader' => $leader ) );
						?>
					<?php endforeach; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section section--surface">
		<div class="container">
			<div class="section__head reveal">
				<div>
					<p class="section__eyebrow"><?php esc_html_e( 'Our goals', 'blc-chicago' ); ?></p>
					<h2><?php esc_html_e( 'Why we lead', 'blc-chicago' ); ?></h2>
				</div>
			</div>
			<div class="goals-grid reveal">
				<article class="goal-card">
					<h3><?php esc_html_e( 'Sustainability', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'To improve the well-being and long-term sustainability of the Black community, our businesses must participate in the modern economy. We aim to create generational wealth from the ground up.', 'blc-chicago' ); ?></p>
				</article>
				<article class="goal-card goal-card--coral">
					<h3><?php esc_html_e( 'Value', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'To increase appreciation of the capacity of Black businesses, we must demonstrate our ability to deliver value to governments, institutions, and corporations.', 'blc-chicago' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
