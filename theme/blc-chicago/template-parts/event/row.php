<?php
/**
 * Event list row partial.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

$post_id  = get_the_ID();
$display  = blc_get_event_display_date( $post_id );
$featured = (bool) get_post_meta( $post_id, 'blc_event_featured', true );
$classes  = 'event-row' . ( $featured ? ' event-row--featured' : '' );
?>

<a class="<?php echo esc_attr( $classes ); ?>" href="<?php the_permalink(); ?>">
	<div class="event-row__date">
		<strong><?php echo esc_html( $display['day'] ); ?></strong>
		<span><?php echo esc_html( $display['month'] ); ?></span>
	</div>
	<div>
		<?php if ( $featured ) : ?>
			<span class="event-row__badge"><?php esc_html_e( 'Up next', 'blc-chicago' ); ?></span>
		<?php endif; ?>
		<h2 class="event-row__title"><?php the_title(); ?></h2>
		<p class="event-row__meta"><?php echo esc_html( $display['meta'] ?: get_post_meta( $post_id, 'blc_event_location', true ) ); ?></p>
	</div>
	<span class="event-row__action"><?php esc_html_e( 'Details', 'blc-chicago' ); ?></span>
</a>
