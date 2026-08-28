<?php
/**
 * Members Directory
 * Template Post Type: page
 *
 * @package BLC_Chicago
 */

blc_require_member_directory_access();

get_header();

$members = blc_get_members_directory_posts();
?>

<main>
	<header class="page-hero">
		<div class="container">
			<p class="section__eyebrow"><?php esc_html_e( 'Members only · Active', 'blc-chicago' ); ?></p>
			<h1><?php esc_html_e( 'Member network', 'blc-chicago' ); ?></h1>
			<p><?php esc_html_e( 'Full profiles, latest updates, and ways to connect. Available to members in good standing.', 'blc-chicago' ); ?></p>
		</div>
	</header>

	<section class="section">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/directory/section',
				null,
				array(
					'context' => 'members',
					'members' => $members,
					'empty'   => __( 'No member profiles yet.', 'blc-chicago' ),
				)
			);
			?>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

<?php
get_footer();
