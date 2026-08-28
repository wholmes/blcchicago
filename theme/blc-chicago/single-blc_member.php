<?php
/**
 * Single member profile.
 *
 * @package BLC_Chicago
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id        = get_the_ID();
	$is_member_view = blc_user_can_access_member_directory();
	$org            = blc_get_member_organization( $post_id );
	$tiers          = get_the_terms( $post_id, 'blc_membership_tier' );
	$industries     = get_the_terms( $post_id, 'blc_industry' );
	$first          = blc_get_member_meta( $post_id, 'blc_first_name' );
	$last           = blc_get_member_meta( $post_id, 'blc_last_name' );
	$position       = blc_get_member_meta( $post_id, 'blc_position' );
	$back_url       = $is_member_view ? blc_get_members_directory_url() : blc_get_public_directory_url();
	?>

<main>
	<header class="page-hero">
		<div class="container">
			<p class="section__eyebrow">
				<a href="<?php echo esc_url( $back_url ); ?>" style="color:inherit;text-decoration:none">
					<?php echo $is_member_view ? esc_html__( '← Member network', 'blc-chicago' ) : esc_html__( '← Directory', 'blc-chicago' ); ?>
				</a>
			</p>
			<div class="profile-hero-block" style="margin:0;color:#fff">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="profile-logo"><?php the_post_thumbnail( 'blc-member-logo' ); ?></div>
				<?php else : ?>
					<div class="profile-logo" style="background:#111;border-color:rgba(255,255,255,0.12)">
						<span style="color:rgba(255,255,255,0.5);font-size:0.7rem;letter-spacing:0.08em;text-transform:uppercase"><?php echo esc_html( $org ); ?></span>
					</div>
				<?php endif; ?>
				<div>
					<h1 style="margin-bottom:0.4rem"><?php echo esc_html( $org ); ?></h1>
					<div class="member-row__meta" style="margin-bottom:0.75rem">
						<?php if ( $tiers && ! is_wp_error( $tiers ) ) : ?>
							<span class="tier-badge"><?php echo esc_html( $tiers[0]->name ); ?></span>
						<?php endif; ?>
						<?php if ( $industries && ! is_wp_error( $industries ) ) : ?>
							<span style="color:rgba(255,255,255,0.65)"><?php echo esc_html( $industries[0]->name ); ?></span>
						<?php endif; ?>
					</div>
					<?php if ( $is_member_view && ( $first || $last || $position ) ) : ?>
						<p style="margin:0;color:rgba(255,255,255,0.78)">
							<?php echo esc_html( trim( $first . ' ' . $last ) ); ?>
							<?php if ( $position ) : ?>
								<?php echo ' · ' . esc_html( $position ); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</header>

	<section class="section">
		<div class="container profile-layout reveal">
			<div>
				<?php get_template_part( 'template-parts/member/profile', 'public' ); ?>
				<?php get_template_part( 'template-parts/member/profile', 'private' ); ?>
			</div>

			<aside>
				<div class="profile-panel">
					<h2><?php esc_html_e( 'Connect', 'blc-chicago' ); ?></h2>
					<?php if ( $is_member_view ) : ?>
						<div class="btn-row" style="flex-direction:column;align-items:stretch">
							<?php blc_render_formidable_form( 'connect' ); ?>
							<?php
							$email = blc_get_member_meta( $post_id, 'blc_email' );
							$site  = blc_get_member_meta( $post_id, 'blc_website' );
							if ( $email ) :
								?>
								<a class="btn btn--outline" href="mailto:<?php echo esc_attr( $email ); ?>">
									<?php
									printf(
										/* translators: %s: first name */
										esc_html__( 'Email %s', 'blc-chicago' ),
										esc_html( $first ?: __( 'member', 'blc-chicago' ) )
									);
									?>
								</a>
							<?php endif; ?>
							<?php if ( $site ) : ?>
								<a class="btn btn--outline" href="<?php echo esc_url( $site ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Website', 'blc-chicago' ); ?></a>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<p><?php esc_html_e( 'Become a member to request introductions and view full profiles.', 'blc-chicago' ); ?></p>
						<a class="btn btn--coral" href="<?php echo esc_url( blc_get_join_url() ); ?>"><?php esc_html_e( 'Become a Member', 'blc-chicago' ); ?></a>
					<?php endif; ?>
				</div>
			</aside>
		</div>
	</section>

	<?php blc_render_join_band(); ?>
</main>

	<?php
endwhile;

get_footer();
