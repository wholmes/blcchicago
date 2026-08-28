<?php
/**
 * Public member profile panels (unauthenticated or limited view).
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

$post_id = get_the_ID();
?>

<?php if ( get_the_content() && blc_member_field_is_public( $post_id, 'bio' ) ) : ?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'About', 'blc-chicago' ); ?></h2>
		<div class="entry-content"><?php the_content(); ?></div>
	</div>
<?php endif; ?>

<?php
$products = blc_get_member_meta( $post_id, 'blc_products_services' );
if ( $products && blc_member_field_is_public( $post_id, 'products_services' ) ) :
	$items = array_filter( array_map( 'trim', preg_split( '/[\n,;]/', $products ) ) );
	?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'Products & services', 'blc-chicago' ); ?></h2>
		<?php if ( $items ) : ?>
			<ul class="tag-list">
				<?php foreach ( $items as $item ) : ?>
					<li><?php echo esc_html( $item ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php echo esc_html( $products ); ?></p>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php
$website = blc_get_member_meta( $post_id, 'blc_website' );
$phone   = blc_get_member_meta( $post_id, 'blc_company_phone' );
$email   = blc_get_member_meta( $post_id, 'blc_company_email' );
if ( ( $website || $phone || $email ) && ! blc_user_can_access_member_directory() ) :
	?>
	<div class="profile-panel">
		<h2><?php esc_html_e( 'Company', 'blc-chicago' ); ?></h2>
		<ul class="profile-meta-list">
			<?php if ( $website && blc_member_field_is_public( $post_id, 'website' ) ) : ?>
				<li><a href="<?php echo esc_url( $website ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Website', 'blc-chicago' ); ?></a></li>
			<?php endif; ?>
			<?php if ( $phone && blc_member_field_is_public( $post_id, 'company_phone' ) ) : ?>
				<li><?php echo esc_html( $phone ); ?></li>
			<?php endif; ?>
			<?php if ( $email && blc_member_field_is_public( $post_id, 'company_email' ) ) : ?>
				<li><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
<?php endif; ?>
