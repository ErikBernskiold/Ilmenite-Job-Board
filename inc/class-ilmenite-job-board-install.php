<?php
/**
 * Installation Class
 *
 * These things will be setup when the plugin is installed.
 */

// Prevent file from being accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class Ilmenite_Job_Board_Install {

	/**
	 * Constructor...
	 */
	public function __construct() {

		// Run cron setup
		$this->cron_jobs;

	}

	/**
	 * Cron Job Setup
	 */
	public function cron_jobs() {

		wp_clear_scheduled_hook( 'il_job_board_check_for_expired_jobs' );
		wp_schedule_event( time(), 'hourly', 'il_job_board_check_for_expired_jobs' );

	}

}

new Ilmenite_Job_Board_Install();