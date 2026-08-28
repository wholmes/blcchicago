<?php
/**
 * Admin settings and summary screen.
 *
 * @package BLC_Membership_Summary
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin page controller.
 */
class BLC_MS_Admin_Page {

	/**
	 * Menu slug.
	 */
	const MENU_SLUG = 'blc-membership-summary';

	/**
	 * Instance.
	 *
	 * @var BLC_MS_Admin_Page|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return BLC_MS_Admin_Page
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
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_post_blc_ms_save_settings', array( $this, 'handle_save_settings' ) );
		add_action( 'admin_post_blc_ms_send_test', array( $this, 'handle_send_test' ) );
	}

	/**
	 * Register admin menu under PMPro when available.
	 */
	public function register_menu() {
		$capability = current_user_can( 'pmpro_memberships_menu' ) ? 'pmpro_memberships_menu' : 'manage_options';

		if ( defined( 'PMPRO_VERSION' ) ) {
			add_submenu_page(
				'pmpro-membershiplevels',
				__( 'Membership Summary', 'blc-membership-summary' ),
				__( 'Summary', 'blc-membership-summary' ),
				$capability,
				self::MENU_SLUG,
				array( $this, 'render_page' )
			);
			return;
		}

		add_menu_page(
			__( 'Membership Summary', 'blc-membership-summary' ),
			__( 'BLC Members', 'blc-membership-summary' ),
			$capability,
			self::MENU_SLUG,
			array( $this, 'render_page' ),
			'dashicons-groups',
			58
		);
	}

	/**
	 * Enqueue admin styles on plugin screen only.
	 *
	 * @param string $hook_suffix Current admin hook.
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( false === strpos( $hook_suffix, self::MENU_SLUG ) ) {
			return;
		}

		wp_enqueue_style(
			'blc-ms-admin',
			BLC_MS_PLUGIN_URL . 'admin/css/admin.css',
			array(),
			BLC_MS_VERSION
		);
	}

	/**
	 * Render summary admin page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'pmpro_memberships_menu' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'blc-membership-summary' ) );
		}

		$report      = BLC_MS_Report::get_report();
		$subscribers = BLC_MS_Mailer::get_subscribers();
		$schedule    = BLC_MS_Mailer::get_schedule();
		$last_sent   = BLC_MS_Mailer::get_last_sent_label();

		include BLC_MS_PLUGIN_DIR . 'admin/views/summary-page.php';
	}

	/**
	 * Save email settings.
	 */
	public function handle_save_settings() {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'pmpro_memberships_menu' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'blc-membership-summary' ) );
		}

		check_admin_referer( 'blc_ms_save_settings' );

		$emails = isset( $_POST['blc_ms_subscribers'] ) ? wp_unslash( $_POST['blc_ms_subscribers'] ) : array();
		BLC_MS_Mailer::save_subscribers( is_array( $emails ) ? $emails : array() );

		$schedule = isset( $_POST['blc_ms_schedule'] ) ? sanitize_key( wp_unslash( $_POST['blc_ms_schedule'] ) ) : 'weekly';
		BLC_MS_Mailer::save_schedule( $schedule );

		BLC_MS_Report::flush_cache();

		$send_test = ! empty( $_POST['blc_ms_send_after_save'] );
		if ( $send_test ) {
			BLC_MS_Mailer::send_report_to( BLC_MS_Mailer::get_subscribers() );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => self::MENU_SLUG,
					'updated' => $send_test ? 'saved-sent' : 'saved',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Send test email without saving.
	 */
	public function handle_send_test() {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'pmpro_memberships_menu' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'blc-membership-summary' ) );
		}

		check_admin_referer( 'blc_ms_send_test' );

		$sent = BLC_MS_Mailer::send_report_to( BLC_MS_Mailer::get_subscribers() );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => self::MENU_SLUG,
					'updated' => $sent ? 'test-sent' : 'test-failed',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}
}
