<?php
/**
 * Template Name: Join Us
 * Template Post Type: page
 *
 * @package BLC_Chicago
 */

get_header();

$tiers = get_terms(
	array(
		'taxonomy'   => 'blc_membership_tier',
		'hide_empty' => false,
	)
);
?>

<main>
	<header class="page-hero page-hero--photo">
		<div class="page-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( blc_get_hero_image_url() ); ?>" alt="">
		</div>
		<div class="container">
			<p class="section__eyebrow"><?php esc_html_e( 'Membership', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'Choose your seat at the table', 'blc-chicago' ); ?></h1>
			<p><?php esc_html_e( 'Dues unlock the members-only directory, connection tools, and future member perks.', 'blc-chicago' ); ?></p>
		</div>
	</header>

	<section class="section">
		<div class="container">
			<div class="tier-grid reveal">
				<?php if ( $tiers && ! is_wp_error( $tiers ) ) : ?>
					<?php foreach ( $tiers as $tier ) : ?>
						<?php if ( 'internal' === blc_get_tier_visibility( $tier ) ) { continue; } ?>
						<article class="tier-card">
							<h3><?php echo esc_html( $tier->name ); ?></h3>
							<p><?php echo esc_html( $tier->description ?: __( 'Membership tier benefits configured in PMPro.', 'blc-chicago' ) ); ?></p>
							<p class="tier-card__price"><?php esc_html_e( 'See PMPro for pricing', 'blc-chicago' ); ?></p>
							<a class="btn btn--coral" href="<?php echo esc_url( function_exists( 'pmpro_url' ) ? pmpro_url( 'levels' ) : blc_get_join_url() ); ?>"><?php esc_html_e( 'Select · PMPro', 'blc-chicago' ); ?></a>
						</article>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php esc_html_e( 'Add membership tiers in WordPress admin, then map PMPro levels.', 'blc-chicago' ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( function_exists( 'pmpro_levels_shortcode' ) ) : ?>
				<div class="reveal">
					<?php echo do_shortcode( '[pmpro_levels]' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
