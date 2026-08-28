<?php
/**
 * Template Name: Corporate Sponsorship
 * Template Post Type: page
 *
 * @package BLC_Chicago
 */

get_header();
?>

<main>
	<header class="page-hero page-hero--brand page-hero--photo">
		<div class="page-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( blc_get_hero_image_url() ); ?>" alt="">
		</div>
		<div class="container">
			<p class="section__eyebrow"><?php esc_html_e( 'Lead Partner Program', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'Corporate sponsorship', 'blc-chicago' ); ?></h1>
			<p><?php esc_html_e( 'Partner with BLC to meet supplier diversity goals, mentor protégé firms, and invest in Chicago’s Black business ecosystem—from prime contracts to community impact.', 'blc-chicago' ); ?></p>
			<hr class="page-hero__rule">
		</div>
	</header>

	<section class="section section--surface">
		<div class="container sponsor-intro reveal">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'The program', 'blc-chicago' ); ?></p>
				<h2><?php esc_html_e( 'Black entrepreneurs as prime contractors', 'blc-chicago' ); ?></h2>
				<p><?php esc_html_e( 'Black entrepreneurs bring value to their customers when engaged as prime contractors. They subcontract and mentor smaller protégé companies—and all companies invest in micro finance and neighborhood initiatives to hire and inspire people from the Black community.', 'blc-chicago' ); ?></p>
				<p><?php esc_html_e( 'Corporate, government, and institutional buyers should examine categories of spend and establish meaningful business relationships with at least one Black firm as a prime vendor in several categories.', 'blc-chicago' ); ?></p>
			</div>
			<aside class="sponsor-intro__panel">
				<h2><?php esc_html_e( 'The opportunity', 'blc-chicago' ); ?></h2>
				<p><?php esc_html_e( 'Black businesses can help corporations and governments meet their need for goods and services with high-quality deliverables.', 'blc-chicago' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'Prime vendor relationships across key spend categories', 'blc-chicago' ); ?></li>
					<li><?php esc_html_e( 'Mentorship for protégé subcontractors', 'blc-chicago' ); ?></li>
					<li><?php esc_html_e( 'Micro-finance and neighborhood hiring initiatives', 'blc-chicago' ); ?></li>
					<li><?php esc_html_e( 'Access to 120+ premier Black vendors in the BLC network', 'blc-chicago' ); ?></li>
				</ul>
			</aside>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="section__head reveal">
				<div>
					<p class="section__eyebrow"><?php esc_html_e( 'Sponsorship tiers', 'blc-chicago' ); ?></p>
					<h2><?php esc_html_e( 'Choose your level of impact', 'blc-chicago' ); ?></h2>
				</div>
			</div>
			<div class="sponsor-tier-grid reveal">
				<article class="sponsor-tier sponsor-tier--lead">
					<span class="sponsor-tier__label"><?php esc_html_e( 'Premier', 'blc-chicago' ); ?></span>
					<h3><?php esc_html_e( 'Lead Partner', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'For organizations investing deeply in supplier diversity and BLC’s mission—prime contractor pathways, protégé mentorship, and flagship visibility.', 'blc-chicago' ); ?></p>
					<ul>
						<li><?php esc_html_e( 'Lead Partner recognition across BLC channels', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Curated introductions to member primes', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Roundtable seat with BLC leadership', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Signature event naming rights', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Featured placement in directory & newsletter', 'blc-chicago' ); ?></li>
					</ul>
					<a class="btn btn--coral" href="mailto:info@blcchicago.com"><?php esc_html_e( 'Inquire · Lead Partner', 'blc-chicago' ); ?></a>
				</article>
				<article class="sponsor-tier">
					<span class="sponsor-tier__label"><?php esc_html_e( 'Strategic', 'blc-chicago' ); ?></span>
					<h3><?php esc_html_e( 'Strategic Partner', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'Sustained partnership for corporations building long-term relationships with Black-owned primes and rising firms.', 'blc-chicago' ); ?></p>
					<ul>
						<li><?php esc_html_e( 'Quarterly BLC Beacon feature', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Invitation to member mixers & roundtables', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Directory partner badge', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Co-branded programming opportunities', 'blc-chicago' ); ?></li>
					</ul>
					<a class="btn btn--blue" href="mailto:info@blcchicago.com"><?php esc_html_e( 'Inquire · Strategic', 'blc-chicago' ); ?></a>
				</article>
				<article class="sponsor-tier">
					<span class="sponsor-tier__label"><?php esc_html_e( 'Events', 'blc-chicago' ); ?></span>
					<h3><?php esc_html_e( 'Event Sponsor', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'Support a single BLC gathering—mixers, spotlights, or civic convenings—with visible community impact.', 'blc-chicago' ); ?></p>
					<ul>
						<li><?php esc_html_e( 'Event program recognition', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'On-site branding & speaking slot', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Member attendee introductions', 'blc-chicago' ); ?></li>
						<li><?php esc_html_e( 'Post-event recap in BLC Beacon', 'blc-chicago' ); ?></li>
					</ul>
					<a class="btn btn--outline" href="mailto:info@blcchicago.com"><?php esc_html_e( 'Inquire · Events', 'blc-chicago' ); ?></a>
				</article>
			</div>
		</div>
	</section>

	<section class="section section--ink">
		<div class="container">
			<div class="section__head reveal">
				<div>
					<p class="section__eyebrow"><?php esc_html_e( 'What you can do', 'blc-chicago' ); ?></p>
					<h2><?php esc_html_e( 'Three ways to engage', 'blc-chicago' ); ?></h2>
				</div>
			</div>
			<div class="goals-grid reveal">
				<article class="goal-card">
					<h3><?php esc_html_e( 'Lead', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'If you lead a public or private organization, engage our Lead Partner Program with true supplier diversity. BLC is comprised of premier Black vendors who can help you meet your needs for high-quality goods and services.', 'blc-chicago' ); ?></p>
				</article>
				<article class="goal-card goal-card--coral">
					<h3><?php esc_html_e( 'Partner', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'Join forces with BLC members and external organizations to enhance Black business capacity as prime contractors—and forge the relationships that open more doors of opportunity.', 'blc-chicago' ); ?></p>
				</article>
				<article class="goal-card">
					<h3><?php esc_html_e( 'Share', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'Bring your knowledge to the table—whether it’s marketing, research, or opportunities. BLC wants to work with sponsors who share expertise and open networks.', 'blc-chicago' ); ?></p>
				</article>
				<article class="goal-card goal-card--coral">
					<h3><?php esc_html_e( 'Advocate · Bridge · Curate', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'BLC advocates for Black-owned business growth, bridges corporations and governments with our talent, and curates collaboration that builds generational wealth.', 'blc-chicago' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="container sponsor-actions reveal">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Get started', 'blc-chicago' ); ?></p>
				<h2><?php esc_html_e( 'Pull others forward with BLC.', 'blc-chicago' ); ?></h2>
				<p><?php esc_html_e( 'Join us in efforts to significantly impact business development across Chicago. Connect with our team to design a sponsorship aligned with your procurement and community goals.', 'blc-chicago' ); ?></p>
				<p><strong><?php esc_html_e( '150 N. Michigan Avenue, Suite 2400 · Chicago, IL 60601', 'blc-chicago' ); ?></strong><br>
				<a href="mailto:info@blcchicago.com">info@BLCchicago.com</a></p>
			</div>
			<div class="btn-row" style="flex-direction:column;align-items:stretch">
				<a class="btn btn--coral" href="mailto:info@blcchicago.com"><?php esc_html_e( 'Contact sponsorship team', 'blc-chicago' ); ?></a>
				<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/leadership/' ) ); ?>"><?php esc_html_e( 'Meet BLC leadership', 'blc-chicago' ); ?></a>
				<a class="btn btn--outline" href="<?php echo esc_url( blc_get_public_directory_url() ); ?>"><?php esc_html_e( 'Browse member directory', 'blc-chicago' ); ?></a>
			</div>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
