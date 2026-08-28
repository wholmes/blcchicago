<?php
/**
 * Events list query + markup (shared by page template and archives).
 *
 * @package BLC_Chicago
 *
 * @var array<string, mixed> $args {
 *     @type int $posts_per_page Max events to show. Default 20.
 *     @type bool $empty_message Show empty state. Default true.
 * }
 */

defined( 'ABSPATH' ) || exit;

$posts_per_page = isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : 20;
$empty_message  = $args['empty_message'] ?? true;

$events = new WP_Query(
	array(
		'post_type'      => 'blc_event',
		'posts_per_page' => $posts_per_page,
		'post_status'    => 'publish',
		'meta_key'       => 'blc_event_start',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'no_found_rows'  => $posts_per_page <= 3,
	)
);
?>

<div class="event-list reveal">
	<?php if ( $events->have_posts() ) : ?>
		<?php
		while ( $events->have_posts() ) :
			$events->the_post();
			get_template_part( 'template-parts/event/row' );
		endwhile;
		wp_reset_postdata();
		?>
	<?php elseif ( $empty_message ) : ?>
		<p><?php esc_html_e( 'No upcoming events. Check back soon.', 'blc-chicago' ); ?></p>
	<?php endif; ?>
</div>
