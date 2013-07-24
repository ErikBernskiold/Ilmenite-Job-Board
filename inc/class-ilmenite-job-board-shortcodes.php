<?php
/**
 * Shortcodes
 *
 * Adds shortcodes for various important displays:
 * - Job Board Listing
 * - Job Submission
 */

class Ilmenite_Job_Board_Shortcodes {

	public function __construct() {

		// Add jobs listing shortcode
		add_shortcode( 'ilmenite_jobs', array( $this, 'jobs_listing' ) );

	}

	public function jobs_listing() {

		$output = 'Hej!';

		return $output;

	}

}

new Ilmenite_Job_Board_Shortcodes();