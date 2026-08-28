<?php
/**
 * Single event.
 *
 * @package BLC_Chicago
 */

get_header();

while ( have_posts() ) :
	the_post();
	$start    = get_post_meta( get_the_ID(), 'blc_event_start', true );
	$end      = get_post_meta( get_the_ID(), 'blc_event_end', true );
	$location = get_post_meta( get_the_ID(), 'blc_event_location', true );
	$is_member = blc_user_has_active_membership();
	?>

<main>
	<header class="page-hero page-hero--photo">
		<div class="page-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( blc_get_hero_image_url() ); ?>" alt="">
		</div>
		<div class="container">
			<p class="section__eyebrow"><?php esc_html_e( 'Upcoming · Public', 'blc-chicago' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<section class="section section--surface">
		<div class="container event-layout">
			<div class="event-body reveal">
				<h2><?php esc_html_e( 'About this event', 'blc-chicago' ); ?></h2>
				<div class="entry-content"><?php the_content(); ?></div>
				<p><a href="<?php echo esc_url( home_url( '/events/' ) ); ?>"><?php esc_html_e( '← All events', 'blc-chicago' ); ?></a></p>
			</div>

			<aside class="event-ticket-panel reveal" data-event-tickets>
				<h2><?php esc_html_e( 'Tickets', 'blc-chicago' ); ?></h2>

				<?php if ( $is_member ) : ?>
					<p><?php esc_html_e( 'As an active BLC member you can purchase tickets for yourself and guests.', 'blc-chicago' ); ?></p>
					<a class="btn btn--coral" href="<?php echo esc_url( blc_get_join_url() ); ?>"><?php esc_html_e( 'Purchase tickets', 'blc-chicago' ); ?></a>
				<?php else : ?>
					<div data-tickets-guest>
						<p><?php esc_html_e( 'Only active BLC members can purchase tickets—including tickets for friends and colleagues.', 'blc-chicago' ); ?></p>
						<a class="btn btn--outline" href="<?php echo esc_url( blc_get_login_url() ); ?>"><?php esc_html_e( 'Member log in', 'blc-chicago' ); ?></a>
						<a class="btn btn--coral" href="<?php echo esc_url( blc_get_join_url() ); ?>"><?php esc_html_e( 'Become a member', 'blc-chicago' ); ?></a>
					</div>
				<?php endif; ?>

				<ul class="event-meta-list">
					<?php if ( $start ) : ?>
						<li>
							<strong><?php esc_html_e( 'When', 'blc-chicago' ); ?></strong>
							<?php echo esc_html( wp_date( 'l, F j, Y', strtotime( $start ) ) ); ?><br>
							<?php
							if ( $end ) {
								echo esc_html( wp_date( 'g:i A', strtotime( $start ) ) . ' – ' . wp_date( 'g:i A T', strtotime( $end ) ) );
							} else {
								echo esc_html( wp_date( 'g:i A T', strtotime( $start ) ) );
							}
							?>
						</li>
					<?php endif; ?>
					<?php if ( $location ) : ?>
						<li>
							<strong><?php esc_html_e( 'Where', 'blc-chicago' ); ?></strong>
							<?php echo nl2br( esc_html( $location ) ); ?>
						</li>
					<?php endif; ?>
				</ul>
			</aside>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

	<?php
endwhile;

get_footer();
