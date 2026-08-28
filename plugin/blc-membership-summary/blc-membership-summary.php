<?php
/**
 * Plugin Name:       BLC Membership Summary
 * Plugin URI:        https://blcchicago.com/
 * Description:       Wild Apricot-style membership summary by tier (active, lapsed, renewal overdue, pending) with scheduled email delivery for admins.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Business Leadership Council
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       blc-membership-summary
 *
 * @package BLC_Membership_Summary
 */

defined( 'ABSPATH' ) || exit;

define( 'BLC_MS_VERSION', '1.0.0' );
define( 'BLC_MS_PLUGIN_FILE', __FILE__ );
define( 'BLC_MS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BLC_MS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once BLC_MS_PLUGIN_DIR . 'includes/class-blc-ms-plugin.php';

/**
 * Plugin bootstrap.
 */
function blc_ms_init() {
	BLC_MS_Plugin::instance();
}
add_action( 'plugins_loaded', 'blc_ms_init' );

register_activation_hook( __FILE__, array( 'BLC_MS_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BLC_MS_Plugin', 'deactivate' ) );
