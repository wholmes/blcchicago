<?php
/**
 * Scheduled email delivery for membership summary.
 *
 * @package BLC_Membership_Summary
 */

defined( 'ABSPATH' ) || exit;

/**
 * Report mailer.
 */
class BLC_MS_Mailer {

	const CRON_HOOK          = 'blc_ms_send_summary_email';
	const OPTION_SUBSCRIBERS = 'blc_ms_email_subscribers';
	const OPTION_SCHEDULE    = 'blc_ms_email_schedule';
	const OPTION_LAST_SENT   = 'blc_ms_email_last_sent';

	/**
	 * Valid schedules.
	 *
	 * @return string[]
	 */
	public static function get_schedules() {
		return array( 'daily', 'weekly', 'monthly' );
	}

	/**
	 * Get subscriber emails.
	 *
	 * @return string[]
	 */
	public static function get_subscribers() {
		$raw = get_option( self::OPTION_SUBSCRIBERS, array() );
		if ( ! is_array( $raw ) ) {
			return array();
		}

		$emails = array();
		foreach ( $raw as $email ) {
			$email = sanitize_email( $email );
			if ( $email && is_email( $email ) ) {
				$emails[] = $email;
			}
		}

		return array_values( array_unique( $emails ) );
	}

	/**
	 * Save subscribers.
	 *
	 * @param string[] $emails Email list.
	 * @return string[]
	 */
	public static function save_subscribers( $emails ) {
		$clean = self::get_subscribers_from_input( $emails );
		update_option( self::OPTION_SUBSCRIBERS, $clean, false );
		return $clean;
	}

	/**
	 * Sanitize email list input.
	 *
	 * @param mixed $emails Raw input.
	 * @return string[]
	 */
	public static function get_subscribers_from_input( $emails ) {
		if ( ! is_array( $emails ) ) {
			return array();
		}

		$clean = array();
		foreach ( $emails as $email ) {
			$email = sanitize_email( wp_unslash( $email ) );
			if ( $email && is_email( $email ) ) {
				$clean[] = $email;
			}
		}

		return array_values( array_unique( $clean ) );
	}

	/**
	 * Get saved schedule slug.
	 *
	 * @return string
	 */
	public static function get_schedule() {
		$schedule = get_option( self::OPTION_SCHEDULE, 'weekly' );
		return in_array( $schedule, self::get_schedules(), true ) ? $schedule : 'weekly';
	}

	/**
	 * Save schedule and reschedule cron.
	 *
	 * @param string $schedule Schedule slug.
	 */
	public static function save_schedule( $schedule ) {
		$schedule = sanitize_key( $schedule );
		if ( ! in_array( $schedule, self::get_schedules(), true ) ) {
			$schedule = 'weekly';
		}

		update_option( self::OPTION_SCHEDULE, $schedule, false );
		self::reschedule_cron();
	}

	/**
	 * Reschedule WP-Cron based on saved schedule.
	 */
	public static function reschedule_cron() {
		wp_clear_scheduled_hook( self::CRON_HOOK );

		$schedule  = self::get_schedule();
		$timestamp = self::next_run_timestamp( $schedule );
		wp_schedule_event( $timestamp, self::cron_recurrence( $schedule ), self::CRON_HOOK );
	}

	/**
	 * Map schedule slug to WP cron recurrence.
	 *
	 * @param string $schedule Schedule slug.
	 * @return string
	 */
	public static function cron_recurrence( $schedule ) {
		switch ( $schedule ) {
			case 'daily':
				return 'daily';
			case 'monthly':
				return 'monthly';
			case 'weekly':
			default:
				return 'weekly';
		}
	}

	/**
	 * Compute next 7:00 AM site-timezone run.
	 *
	 * @param string $schedule Schedule slug.
	 * @return int Unix timestamp (UTC).
	 */
	public static function next_run_timestamp( $schedule ) {
		$timezone = wp_timezone();
		$now      = new DateTime( 'now', $timezone );
		$target   = clone $now;
		$target->setTime( 7, 0, 0 );

		if ( 'weekly' === $schedule ) {
			if ( (int) $now->format( 'N' ) === 1 && $now < $target ) {
				// Monday before 7 AM — use today.
			} else {
				$target->modify( 'next monday' );
			}
		} elseif ( 'monthly' === $schedule ) {
			if ( (int) $now->format( 'j' ) === 1 && $now < $target ) {
				// First of month before 7 AM — use today.
			} else {
				$target->modify( 'first day of next month' );
			}
		} elseif ( $now >= $target ) {
			$target->modify( '+1 day' );
		}

		return $target->getTimestamp();
	}

	/**
	 * Cron callback: email all subscribers.
	 */
	public static function send_scheduled_report() {
		$subscribers = self::get_subscribers();
		if ( empty( $subscribers ) ) {
			return;
		}

		self::send_report_to( $subscribers );
	}

	/**
	 * Send report to given addresses.
	 *
	 * @param string[] $recipients Email addresses.
	 * @return bool
	 */
	public static function send_report_to( $recipients ) {
		$recipients = self::get_subscribers_from_input( $recipients );
		if ( empty( $recipients ) ) {
			return false;
		}

		$report  = BLC_MS_Report::get_report( true );
		$subject = sprintf(
			/* translators: %s: site name */
			__( '[%s] Membership summary', 'blc-membership-summary' ),
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
		);

		$body    = self::build_html_email( $report );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$sent = false;
		foreach ( $recipients as $email ) {
			if ( wp_mail( $email, $subject, $body, $headers ) ) {
				$sent = true;
			}
		}

		if ( $sent ) {
			update_option(
				self::OPTION_LAST_SENT,
				array(
					'time'       => time(),
					'recipients' => count( $recipients ),
				),
				false
			);
		}

		return $sent;
	}

	/**
	 * Build HTML email body.
	 *
	 * @param array<string, mixed> $report Report data.
	 * @return string
	 */
	public static function build_html_email( $report ) {
		ob_start();
		include BLC_MS_PLUGIN_DIR . 'admin/views/email-report.php';
		return (string) ob_get_clean();
	}

	/**
	 * Human-readable last sent string.
	 *
	 * @return string
	 */
	public static function get_last_sent_label() {
		$data = get_option( self::OPTION_LAST_SENT );
		if ( ! is_array( $data ) || empty( $data['time'] ) ) {
			return __( 'Never sent', 'blc-membership-summary' );
		}

		$date = wp_date(
			get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
			(int) $data['time']
		);

		$count = isset( $data['recipients'] ) ? (int) $data['recipients'] : 0;

		return sprintf(
			/* translators: 1: datetime, 2: recipient count */
			__( '%1$s to %2$d recipients', 'blc-membership-summary' ),
			$date,
			$count
		);
	}
}
