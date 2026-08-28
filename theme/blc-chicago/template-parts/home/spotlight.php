<?php
/**
 * Homepage member spotlight.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

$member = blc_get_spotlight_member();

if ( ! $member ) {
	return;
}

$post_id   = $member->ID;
$org       = blc_get_member_organization( $post_id );
$tiers     = get_the_terms( $post_id, 'blc_membership_tier' );
$tier_name = ( $tiers && ! is_wp_error( $tiers ) ) ? $tiers[0]->name : '';
$first     = blc_get_member_meta( $post_id, 'blc_first_name' );
$last      = blc_get_member_meta( $post_id, 'blc_last_name' );
$position  = blc_get_member_meta( $post_id, 'blc_position' );
$quote     = blc_get_member_meta( $post_id, 'blc_spotlight_quote' );
$who       = trim( $first . ' ' . $last );
$subtitle  = $position ? $position . ( $org ? ' · ' . $org : '' ) : $org;

if ( ! $quote ) {
	$quote = __( '“BLC puts me in rooms where peer networks become real partnerships—not just introductions.”', 'blc-chicago' );
}
?>
<section class="section section--ink">
	<div class="container">
		<div class="section__head reveal">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Members', 'blc-chicago' ); ?></p>
				<h2><?php esc_html_e( 'Member spotlight', 'blc-chicago' ); ?></h2>
			</div>
			<a class="section__link" href="<?php echo esc_url( blc_get_public_directory_url() ); ?>"><?php esc_html_e( 'Browse directory →', 'blc-chicago' ); ?></a>
		</div>
		<div class="spotlight reveal">
			<div class="spotlight__visual">
				<?php if ( has_post_thumbnail( $member ) ) : ?>
					<?php echo get_the_post_thumbnail( $member, 'large' ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( blc_get_hero_image_url() ); ?>" alt="">
				<?php endif; ?>
			</div>
			<div>
				<?php if ( $tier_name ) : ?>
					<span class="spotlight__tier"><?php echo esc_html( $tier_name ); ?></span>
				<?php endif; ?>
				<blockquote><?php echo esc_html( $quote ); ?></blockquote>
				<?php if ( $who || $subtitle ) : ?>
					<p class="spotlight__who">
						<?php if ( $who ) : ?>
							<strong><?php echo esc_html( $who ); ?></strong>
						<?php endif; ?>
						<?php if ( $subtitle ) : ?>
							<?php echo $who ? ' · ' : ''; ?><?php echo esc_html( $subtitle ); ?>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<a class="btn btn--coral" href="<?php echo esc_url( get_permalink( $member ) ); ?>"><?php esc_html_e( 'View profile', 'blc-chicago' ); ?></a>
			</div>
		</div>
	</div>
</section>
