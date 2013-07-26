<?php
/**
 * General Functions
 */

if ( ! function_exists( 'iljb_get_message' ) ) :
/**
 * Message Function
 */
function iljb_get_message( $type = '', $content = '' ) {

	// Set classes based on the message type
	switch ( $type ) {
		case 'error':
			$class = 'error-message';
			break;

		case 'success':
			$class = 'success-message';
			break;

		default:
			$class = 'info-message';
			break;
	}

	return '<div class="job-board-message ' . $class . '">' . $content . '</div>';

}
endif;

if ( ! function_exists( 'get_job_taxonomy' ) ) :
/**
 * Retrieve Job Taxonomies
 *
 * Helper class to simply get the taxonomy terms from a
 * taxonomy with pre-set options for the fitlering/submission.
 */
function get_job_taxonomy( $taxonomy = null ) {

	$terms = get_terms( $taxonomy, array(
		'orderby'    => 'name',
		'order'      => 'asc',
		'hide_empty' => false,
	));

	return $terms;

}
endif;

if ( ! function_exists( 'get_job_board_login' ) ) :
/**
 * Job Board Login Form
 *
 * Creates a login form/page to display when login is required.
 */
function get_job_board_login() {

	// Define output variable
	$output = '';

	$login_form = wp_login_form(array(
		'echo' => false,
	));

	$output .= '<h3>' . __( 'Login', 'iljobboard' ) . '</h3>';
	$output .= '<p class="job-login-message">' . __( 'To access this section, you need to be logged in.', 'iljobboard' ) . '</p>';
	$output .= $login_form;

	return $output;

}
endif;