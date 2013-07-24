<?php
/**
 * Form Class
 *
 * Base form class that can be extended to create
 * submission and editing forms in own classes.
 *
 * @abstract
 */
abstract class Ilmenite_Job_Board_Form {

	protected static $fields;
	protected static $action;
	protected static $errors = array();

	/**
	 * Error Function
	 */
	public static function add_error( $error ) {
		self::$errors[] = $error;
	}

	/**
	 * Display Errors
	 */
	public static function display_errors() {

		foreach ( self::$errors as $error )
			echo '<div class="job-board-message error-message"> ' . $error . ' </div>';

	}

	/**
	 * Get Action of Form
	 */
	public static function get_form_action() {
		return self::$action;
	}

	/**
	 * Return all the fields
	 */
	public static function display_fields( $key ) {

		// If we don't have a key, quit here.
		if ( empty( self::$fields[ $key ] ) )
			return;

		$fields = self::$fields[ $key ];

		// Sort the fields according to the field priority set.
		uasort( $fields, __CLASS__ . '::priority_cmp' );

		return $fields,

	}

	/**
	 * Get the Field Priority
	 */
	public static function priority_cmp( $a, $b ) {

		if ( $a['priority'] == $b['priority'] )
			return 0;

		return ( $a['priority'] < $b['priority'] ) ? -1 : 1;

	}

}