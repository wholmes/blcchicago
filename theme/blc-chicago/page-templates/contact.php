<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * @package BLC_Chicago
 */

get_header();
?>

<main>
	<header class="page-hero page-hero--brand">
		<div class="container page-hero__inner reveal">
			<p class="section__eyebrow"><?php esc_html_e( 'Get in touch', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'Connect with BLC', 'blc-chicago' ); ?></h1>
			<p><?php esc_html_e( 'Questions about membership, sponsorship, events, or partnering with Chicago’s premier Black business organization.', 'blc-chicago' ); ?></p>
		</div>
	</header>

	<section class="section">
		<div class="container contact-layout reveal">
			<div class="contact-panel">
				<h2><?php esc_html_e( 'Send a message', 'blc-chicago' ); ?></h2>
				<?php blc_render_formidable_form( 'contact' ); ?>
			</div>
			<aside class="contact-aside">
				<div class="contact-aside__block">
					<h3><?php esc_html_e( 'Office', 'blc-chicago' ); ?></h3>
					<p>
						<?php esc_html_e( 'Business Leadership Council', 'blc-chicago' ); ?><br>
						150 N. Michigan Avenue, Suite 2400<br>
						Chicago, IL 60601
					</p>
				</div>
				<div class="contact-aside__block">
					<h3><?php esc_html_e( 'Email', 'blc-chicago' ); ?></h3>
					<p><a href="mailto:info@blcchicago.com">info@BLCchicago.com</a></p>
				</div>
				<div class="contact-aside__block contact-aside__block--members">
					<h3><?php esc_html_e( 'Already a member?', 'blc-chicago' ); ?></h3>
					<p><?php esc_html_e( 'Log in to connect directly with members through the private directory.', 'blc-chicago' ); ?></p>
					<a class="btn btn--outline btn--sm" href="<?php echo esc_url( blc_get_login_url() ); ?>"><?php esc_html_e( 'Member log in', 'blc-chicago' ); ?></a>
				</div>
			</aside>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
