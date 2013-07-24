<?php
/**
 * Job Submission Form
 *
 * Used to submit a new job to the system through the frontend.
 */

/**
 * Ilmenite_Job_Board_Submit_Job class
 */
class Ilmenite_Job_Board_Submit_job extends Ilmenite_Job_Board_Form {

	public static $form_name = 'submit-job';
	protected static $job_id;
	protected static $preview_job;
	protected static $steps;
	protected static $step;

	/**
	 * Set up the form
	 */
	public static function init() {

	}

	/**
	 * Fields Function
	 *
	 * Creates all the fields.
	 */
	public static function form_fields() {

		if ( self::$fields )
			return;

		self::$fields = apply_filters( 'job_submit_form_fields', array(
			'job' => array(
				'job_title' => array(
					'label'       => __( 'Job Title', 'iljobboard' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 1,
				),
				'job_type' => array(
					'label'       => __( 'Job Type', 'iljobboard' ),
					'type'        => 'select',
					'required'    => true,
					//'options'	  => self::job_types(),
					'placeholder' => '',
					'priority'    => 1,
				),
				'job_description' => array(
					'label'       => __( 'Job Description', 'iljobboard' ),
					'type'        => 'textarea',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 5,
				),
			),
		) );

	}

	/**
	 * Get Submitted Data from Fields
	 */
	protected function get_form_post_data() {

		self::form_fields();

		$values = array();

		foreach ( self::$fields as $group_key => $fields ) {
			foreach ( $fields as $key => $field ) {
				$values[ $group_key ][ $key ] = isset( $_POST[ $key ] ) ? stripslashes( $_POST[ $key ] ) : '';

				switch ( $key ) {
					case 'textarea':
							$values[ $group_key ][ $key ] = wp_kses_post( trim( $values[ $group_key ][ $key ] ) );
						break;

					default:
							$values[ $group_key ][ $key ] = sanitize_text_field( $values[ $group_key ][ $key ] );
						break;
				}

				// Set the field value
				self::$fields[ $group_key ][ $key ]['value'] = $values[ $group_key ][ $key ];

			}
		}

		return $values;

	}

	/**
	 * Validate the input of the fields
	 */
	protected function validate_form_fields( $values ) {

		foreach ( self::$fields as $group_key => $fields ) {
			foreach ( $fields as $key => $field ) {
				if( $field['required'] && empty( $values[ $group_key ][ $key ] ) )
					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'iljobboard' ), $field['label'] ) );
			}
		}

		return true;

	}

	/**
	 * Submit The Form
	 */
	public static function form_submission() {

		try {

			// Get the post data
			$values = self::get_form_post_data();

			// If the form is empty or nonce isn't verified, don't submit.
			if ( empty ( $_POST['submit_job'] ) || ! wp_verify_nonce( $_POST['submit_job_nonce'], 'submit_form_posted' ) )
				return;

			// Validate the fields.
			if ( is_wp_error( ( $return = self::validate_form_fields( $values ) ) ) )
				throw new Exception( $return->get_error_message() );

			// Check that the user is logged in.
			if ( ! is_user_logged_in() )
				throw new Exception( __( 'You need to be logged in to post a new job.', 'iljobboard' ) );

			// Update the job post
			self::save_job( $values['job']['job_title'], $values['job']['job_description'] );
			self::update_job_meta( $values );

		} catch ( Exception $e ) {
			self::add_error( $e->getMessage() );
			return;
		}

	}

	/**
	 * Update or Create a New Job
	 *
	 * Post title and the description. Other fields are inserted with the meta function.
	 */
	protected function save_job( $post_title, $post_content, $status = 'preview' ) {

		// Set up the post data array
		$post_data = array(
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'post_status'    => $status,
			'post_type'      => 'il_job_board',
			'comment_status' => 'closed',
		);

		// If we have an ID, pass this too into the array.
		if ( self::$job_id ) {
			$post_data['ID'] = self::$job_id;
			wp_update_post( $post_data )
		} else {
			self::$job_id = wp_insert_post( $post_data );
		}

	}

	/**
	 * Update and set the post meta terms
	 */
	protected function update_job_meta( $values ) {

		// Set the job type taxonomy
		wp_set_object_terms( self::$job_id, array( $values['job']['job_type'] ), 'iljb_job_type', false );

		// Go through all of the fields here...
		update_post_meta( self::$job_id, 'iljb_expiry_date', $values['job']['iljb_expiry_date'] );

	}



}