<?php
/**
 * Directory filters, list shell, and rows.
 *
 * @package BLC_Chicago
 *
 * @var array<string, mixed> $args {
 *     @type string   $context   public|members
 *     @type WP_Post[] $members  Directory posts.
 *     @type string   $empty     Empty-state message.
 * }
 */

defined( 'ABSPATH' ) || exit;

$context = isset( $args['context'] ) ? (string) $args['context'] : 'public';
$members = isset( $args['members'] ) && is_array( $args['members'] ) ? $args['members'] : array();
$empty   = $args['empty'] ?? __( 'No members match your filters.', 'blc-chicago' );
$count   = count( $members );
$is_private = 'members' === $context;

$tiers = get_terms(
	array(
		'taxonomy'   => 'blc_membership_tier',
		'hide_empty' => false,
	)
);

$industries = get_terms(
	array(
		'taxonomy'   => 'blc_industry',
		'hide_empty' => true,
	)
);

if ( $is_private ) {
	$count_label = _n( '%d member', '%d members', $count, 'blc-chicago' );
	$search_placeholder = __( 'Search people or organizations', 'blc-chicago' );
} else {
	$count_label = _n( '%d organization', '%d organizations', $count, 'blc-chicago' );
	$search_placeholder = __( 'Search organizations', 'blc-chicago' );
}

$list_classes = 'directory-list';
if ( $is_private ) {
	$list_classes .= ' directory-list--private';
}
?>

<div class="directory-toolbar-row reveal">
	<form class="directory-toolbar" role="search" data-directory-filters>
		<input type="search" name="q" placeholder="<?php echo esc_attr( $search_placeholder ); ?>" aria-label="<?php echo esc_attr( $search_placeholder ); ?>">
		<select name="tier" aria-label="<?php esc_attr_e( 'Filter by membership tier', 'blc-chicago' ); ?>">
			<option value=""><?php esc_html_e( 'All tiers', 'blc-chicago' ); ?></option>
			<?php if ( $tiers && ! is_wp_error( $tiers ) ) : ?>
				<?php foreach ( $tiers as $tier ) : ?>
					<?php if ( ! $is_private && ! blc_tier_is_public_directory( $tier ) ) { continue; } ?>
					<option value="<?php echo esc_attr( $tier->slug ); ?>"><?php echo esc_html( $tier->name ); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<select name="industry" aria-label="<?php esc_attr_e( 'Filter by industry', 'blc-chicago' ); ?>">
			<option value=""><?php esc_html_e( 'All industries', 'blc-chicago' ); ?></option>
			<?php if ( $industries && ! is_wp_error( $industries ) ) : ?>
				<?php foreach ( $industries as $industry ) : ?>
					<option value="<?php echo esc_attr( $industry->slug ); ?>"><?php echo esc_html( $industry->name ); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</form>
	<div class="directory-view-toggle" data-directory-view-toggle role="group" aria-label="<?php esc_attr_e( 'Directory view', 'blc-chicago' ); ?>">
		<button type="button" class="view-toggle is-active" data-view="list" aria-pressed="true"><?php esc_html_e( 'List', 'blc-chicago' ); ?></button>
		<button type="button" class="view-toggle" data-view="cards" aria-pressed="false"><?php esc_html_e( 'Cards', 'blc-chicago' ); ?></button>
	</div>
</div>

<p class="directory-list__count" data-directory-count>
	<?php printf( esc_html( $count_label ), (int) $count ); ?>
</p>

<?php if ( $members ) : ?>
	<div class="<?php echo esc_attr( $list_classes ); ?>" data-directory-list>
		<div class="directory-list__head<?php echo $is_private ? ' directory-list__head--private' : ''; ?>" aria-hidden="true">
			<span></span>
			<span><?php esc_html_e( 'Organization', 'blc-chicago' ); ?></span>
			<span><?php echo $is_private ? esc_html__( 'Contact', 'blc-chicago' ) : esc_html__( 'Company info', 'blc-chicago' ); ?></span>
			<?php if ( $is_private ) : ?>
				<span><?php esc_html_e( 'Latest', 'blc-chicago' ); ?></span>
			<?php endif; ?>
		</div>
		<?php
		global $post;
		foreach ( $members as $post ) {
			setup_postdata( $post );
			get_template_part( 'template-parts/member/card', $is_private ? 'private' : 'public' );
		}
		wp_reset_postdata();
		?>
	</div>
<?php else : ?>
	<div class="directory-empty reveal">
		<p><?php echo esc_html( $empty ); ?></p>
		<?php if ( current_user_can( 'manage_options' ) ) : ?>
			<p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=blc_member&page=blc-import-members' ) ); ?>"><?php esc_html_e( 'Import members →', 'blc-chicago' ); ?></a></p>
		<?php endif; ?>
	</div>
<?php endif; ?>
