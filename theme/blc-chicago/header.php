<?php
/**
 * Header template.
 *
 * @package BLC_Chicago
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="site-header-group">
	<div class="member-utility-bar">
		<div class="container member-utility-bar__inner">
			<p class="member-utility-bar__text">
				<?php
				printf(
					/* translators: %s: login link */
					wp_kses_post( __( 'BLC members — %s for your directory, events, and profile.', 'blc-chicago' ) ),
					'<a href="' . esc_url( blc_get_login_url() ) . '">' . esc_html__( 'Log in', 'blc-chicago' ) . '</a>'
				);
				?>
			</p>
		</div>
	</div>
	<header class="site-header">
		<div class="container site-header__inner">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Business Leadership Council home', 'blc-chicago' ); ?>">
				<img class="brand__logo brand__logo--header" src="<?php echo esc_url( blc_get_header_logo_url() ); ?>" alt="<?php esc_attr_e( 'BLC', 'blc-chicago' ); ?>" width="160" height="39">
			</a>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav"><?php esc_html_e( 'Menu', 'blc-chicago' ); ?></button>
			<?php blc_render_primary_nav(); ?>
		</div>
	</header>
</div>
