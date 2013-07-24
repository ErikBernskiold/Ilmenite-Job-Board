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
		$this->cron_jobs();

		// User Role Setup
		$this->setup_user_roles();

	}

	/**
	 * Cron Job Setup
	 */
	public function cron_jobs() {

		wp_clear_scheduled_hook( 'il_job_board_check_for_expired_jobs' );
		wp_schedule_event( time(), 'hourly', 'il_job_board_check_for_expired_jobs' );

	}

	/**
	 * Set up user roles
	 */
	public function setup_user_roles() {

		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		// Add custom capability for job board
		if( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'administrator', 'manage_job_board' );
		}

		// Set up custom employer role
		$employer = add_role( 'employer', __( 'Employer', 'wpjobboard' ) array(
			'read' => true
		));

	}

}

new Ilmenite_Job_Board_Install();