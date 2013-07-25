<?php
/**
 * General Functions
 */

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