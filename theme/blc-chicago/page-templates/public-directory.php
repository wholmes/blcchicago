<?php
/**
 * Public Directory
 * Template Post Type: page
 *
 * @package BLC_Chicago
 */

get_header();

$members = blc_get_public_directory_posts();
?>

<main>
	<header class="page-hero">
		<div class="container">
			<p class="section__eyebrow"><?php esc_html_e( 'Public', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'Member directory', 'blc-chicago' ); ?></h1>
			<p><?php esc_html_e( 'Discover BLC organizations across Chicago. Logos, industries, and public contact—presented with room to breathe.', 'blc-chicago' ); ?></p>
		</div>
	</header>

	<section class="section">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/directory/section',
				null,
				array(
					'context' => 'public',
					'members' => $members,
					'empty'   => __( 'Member profiles will appear here after import.', 'blc-chicago' ),
				)
			);
			?>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
