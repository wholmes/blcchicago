<?php
/**
 * BLC Chicago theme bootstrap.
 *
 * @package BLC_Chicago
 */

defined( 'ABSPATH' ) || exit;

define( 'BLC_THEME_VERSION', '0.3.8' );
define( 'BLC_THEME_DIR', get_template_directory() );
define( 'BLC_THEME_URI', get_template_directory_uri() );

require_once BLC_THEME_DIR . '/inc/setup.php';
require_once BLC_THEME_DIR . '/inc/assets.php';
require_once BLC_THEME_DIR . '/inc/template-tags.php';
require_once BLC_THEME_DIR . '/inc/nav.php';
require_once BLC_THEME_DIR . '/inc/taxonomies.php';
require_once BLC_THEME_DIR . '/inc/member-meta.php';
require_once BLC_THEME_DIR . '/inc/seed-tiers.php';
require_once BLC_THEME_DIR . '/inc/cpt-member.php';
require_once BLC_THEME_DIR . '/inc/cpt-leader.php';
require_once BLC_THEME_DIR . '/inc/cpt-event.php';
require_once BLC_THEME_DIR . '/inc/pmpro-integration.php';
require_once BLC_THEME_DIR . '/inc/capabilities.php';
require_once BLC_THEME_DIR . '/inc/formidable.php';
require_once BLC_THEME_DIR . '/inc/formidable-sync.php';
require_once BLC_THEME_DIR . '/inc/import-members.php';
require_once BLC_THEME_DIR . '/inc/member-query.php';
require_once BLC_THEME_DIR . '/inc/activate.php';
require_once BLC_THEME_DIR . '/inc/admin-setup.php';
