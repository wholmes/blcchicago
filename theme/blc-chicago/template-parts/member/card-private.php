<?php
/**
 * Members-only directory row.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

$post_id       = get_the_ID();
$org           = blc_get_member_organization( $post_id );
$first         = blc_get_member_meta( $post_id, 'blc_first_name' );
$last          = blc_get_member_meta( $post_id, 'blc_last_name' );
$position      = blc_get_member_meta( $post_id, 'blc_position' );
$tiers         = get_the_terms( $post_id, 'blc_membership_tier' );
$tier_slug     = ( $tiers && ! is_wp_error( $tiers ) ) ? $tiers[0]->slug : '';
$tier_name     = ( $tiers && ! is_wp_error( $tiers ) ) ? $tiers[0]->name : '';
$industries    = get_the_terms( $post_id, 'blc_industry' );
$industry_slug = ( $industries && ! is_wp_error( $industries ) ) ? $industries[0]->slug : '';
$industry_name = ( $industries && ! is_wp_error( $industries ) ) ? $industries[0]->name : '';
$email         = blc_get_member_meta( $post_id, 'blc_company_email' );
$phone         = blc_get_member_meta( $post_id, 'blc_company_phone' );
$website       = blc_get_member_meta( $post_id, 'blc_website' );
$glimpse       = blc_get_member_meta( $post_id, 'blc_collaboration' );
$person        = trim( $first . ' ' . $last );
$search_name   = strtolower( trim( $org . ' ' . $person ) );
$logo_label    = blc_member_logo_label( $org );
?>
<div class="member-row member-row--private" data-name="<?php echo esc_attr( $search_name ); ?>" data-tier="<?php echo esc_attr( $tier_slug ); ?>" data-industry="<?php echo esc_attr( $industry_slug ); ?>">
	<a class="member-row__stretch" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s profile', 'blc-chicago' ), $org ) ); ?>"></a>
	<div class="member-row__logo">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'blc-member-logo' ); ?>
		<?php else : ?>
			<span><?php echo esc_html( $logo_label ); ?></span>
		<?php endif; ?>
	</div>
	<div class="member-row__main">
		<h2 class="member-row__name"><?php echo esc_html( $org ); ?></h2>
		<?php if ( $tier_name || $industry_name ) : ?>
			<div class="member-row__meta">
				<?php if ( $tier_name ) : ?>
					<span class="tier-badge"><?php echo esc_html( $tier_name ); ?></span>
				<?php endif; ?>
				<?php if ( $industry_name ) : ?>
					<span><?php echo esc_html( $industry_name ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( $person || $position ) : ?>
			<p class="member-row__person">
				<?php echo esc_html( $person ); ?>
				<?php if ( $position ) : ?>
					<?php echo $person ? ' · ' : ''; ?><?php echo esc_html( $position ); ?>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
	<div class="member-row__contact">
		<ul class="contact-lines">
			<?php if ( $phone ) : ?>
				<li>
					<span class="contact-lines__label"><?php esc_html_e( 'Tel', 'blc-chicago' ); ?></span>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
				</li>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<li>
					<span class="contact-lines__label"><?php esc_html_e( 'Email', 'blc-chicago' ); ?></span>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</li>
			<?php endif; ?>
			<?php if ( $website ) : ?>
				<li>
					<span class="contact-lines__label"><?php esc_html_e( 'Web', 'blc-chicago' ); ?></span>
					<a href="<?php echo esc_url( $website ); ?>" rel="noopener noreferrer"><?php echo esc_html( wp_parse_url( $website, PHP_URL_HOST ) ?: $website ); ?></a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<?php if ( $glimpse ) : ?>
		<div class="member-row__glimpse">
			<ul class="contact-lines">
				<li>
					<span class="contact-lines__label contact-lines__label--recent"><?php esc_html_e( 'Recently', 'blc-chicago' ); ?></span>
					<?php echo esc_html( $glimpse ); ?>
				</li>
			</ul>
		</div>
	<?php endif; ?>
</div>
