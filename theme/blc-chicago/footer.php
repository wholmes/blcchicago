<?php
/**
 * Footer template.
 *
 * @package BLC_Chicago
 */
?>
<footer class="site-footer">
	<div class="container site-footer__inner">
		<img src="<?php echo esc_url( blc_get_logo_url() ); ?>" alt="<?php esc_attr_e( 'BLC', 'blc-chicago' ); ?>">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'Business Leadership Council · Chicago', 'blc-chicago' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'blc-chicago' ); ?></a>
		<a href="<?php echo esc_url( blc_get_login_url() ); ?>"><?php esc_html_e( 'Member log in', 'blc-chicago' ); ?></a>
		<a href="<?php echo esc_url( blc_get_join_url() ); ?>"><?php esc_html_e( 'Become a Member', 'blc-chicago' ); ?></a>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
