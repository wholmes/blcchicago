<?php
/**
 * Homepage pillars section.
 *
 * @package BLC_Chicago
 */
?>
<section class="section">
	<div class="container">
		<div class="section__head reveal">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Who we are', 'blc-chicago' ); ?></p>
				<h2><?php esc_html_e( 'Power. Values. Purpose.', 'blc-chicago' ); ?></h2>
			</div>
			<a class="section__link" href="<?php echo esc_url( home_url( '/leadership/' ) ); ?>"><?php esc_html_e( 'BLC leadership →', 'blc-chicago' ); ?></a>
		</div>
		<div class="pillars-grid reveal">
			<article class="pillar-card pillar-card--power">
				<h3 class="pillar-card__label"><?php esc_html_e( 'Power', 'blc-chicago' ); ?></h3>
				<p><?php esc_html_e( 'Chicago’s premier Black business organization—representing leaders and entrepreneurs in positions of influence across corporate and civic leadership.', 'blc-chicago' ); ?></p>
				<p><?php esc_html_e( 'Harnessing the power of today’s Black business leaders to build the next generation of Chicago’s business leaders.', 'blc-chicago' ); ?></p>
			</article>
			<article class="pillar-card pillar-card--values">
				<h3 class="pillar-card__label"><?php esc_html_e( 'Values', 'blc-chicago' ); ?></h3>
				<p><?php esc_html_e( 'The Business Leadership Council builds bridges in every arena as a strong foundation for business growth and development within the Black community.', 'blc-chicago' ); ?></p>
				<p><?php esc_html_e( 'We are a values-driven organization, committed to making a positive impact on our communities.', 'blc-chicago' ); ?></p>
			</article>
			<article class="pillar-card pillar-card--purpose">
				<h3 class="pillar-card__label"><?php esc_html_e( 'Purpose', 'blc-chicago' ); ?></h3>
				<p><?php esc_html_e( 'The mentor and protégé paradigm needs to be reimagined—having Black companies participate as primes and mentor emerging Black companies as protégés.', 'blc-chicago' ); ?></p>
			</article>
		</div>
	</div>
</section>
