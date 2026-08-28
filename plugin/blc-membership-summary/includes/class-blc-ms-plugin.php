<?php
/**
 * Main plugin orchestrator.
 *
 * @package BLC_Membership_Summary
 */

defined( 'ABSPATH' ) || exit;

require_once BLC_MS_PLUGIN_DIR . 'includes/class-blc-ms-report.php';
require_once BLC_MS_PLUGIN_DIR . 'includes/class-blc-ms-mailer.php';

if ( is_admin() ) {
	require_once BLC_MS_PLUGIN_DIR . 'admin/class-blc-ms-admin-page.php';
}

/**
 * Plugin singleton.
 */
final class BLC_MS_Plugin {

	/**
	 * Instance.
	 *
	 * @var BLC_MS_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return BLC_MS_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( BLC_MS_Mailer::CRON_HOOK, array( 'BLC_MS_Mailer', 'send_scheduled_report' ) );

		if ( is_admin() ) {
			BLC_MS_Admin_Page::instance();
		}
	}

	/**
	 * Load translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'blc-membership-summary',
			false,
			dirname( plugin_basename( BLC_MS_PLUGIN_FILE ) ) . '/languages'
		);
	}

	/**
	 * Activation: schedule cron and seed options.
	 */
	public static function activate() {
		if ( ! get_option( BLC_MS_Mailer::OPTION_SUBSCRIBERS ) ) {
			add_option( BLC_MS_Mailer::OPTION_SUBSCRIBERS, array() );
		}

		if ( ! get_option( BLC_MS_Mailer::OPTION_SCHEDULE ) ) {
			add_option( BLC_MS_Mailer::OPTION_SCHEDULE, 'weekly' );
		}

		BLC_MS_Mailer::reschedule_cron();
	}

	/**
	 * Deactivation: clear cron.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( BLC_MS_Mailer::CRON_HOOK );
	}
}
