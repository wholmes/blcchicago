<?php
/**
 * Leader card partial.
 *
 * @package BLC_Chicago
 *
 * @var WP_Post $leader Leader post.
 */

defined( 'ABSPATH' ) || exit;

/** @var array<string, mixed> $args */
$leader   = isset( $args['leader'] ) ? $args['leader'] : get_post();
$featured = (bool) get_post_meta( $leader->ID, 'blc_leader_featured', true );
$role     = get_post_meta( $leader->ID, 'blc_leader_role', true );
$org      = get_post_meta( $leader->ID, 'blc_leader_org', true );
$classes  = 'leader-card' . ( $featured ? ' leader-card--featured' : '' );
?>

<article class="<?php echo esc_attr( $classes ); ?>">
	<div class="leader-card__photo">
		<?php if ( has_post_thumbnail( $leader ) ) : ?>
			<?php echo get_the_post_thumbnail( $leader, 'blc-leader-photo', array( 'alt' => esc_attr( get_the_title( $leader ) ) ) ); ?>
		<?php endif; ?>
	</div>
	<div class="leader-card__body">
		<?php if ( $role ) : ?>
			<p class="leader-card__role"><?php echo esc_html( $role ); ?></p>
		<?php endif; ?>
		<h3 class="leader-card__name"><?php echo esc_html( get_the_title( $leader ) ); ?></h3>
		<?php if ( $org ) : ?>
			<p class="leader-card__org"><?php echo esc_html( $org ); ?></p>
		<?php endif; ?>
	</div>
</article>
