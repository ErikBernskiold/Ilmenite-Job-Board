<?php
/**
 * Forms Class
 */

class Ilmenite_Job_Board_Forms {

	protected static $job_id;
	protected static $fields;
	protected static $action;
	protected static $errors = array();

	public function __construct() {

		add_action( 'init', array( $this, 'form_fields' ) );

	}

	public function submit_form() {

		// Stop the display here if the user isn't logged in.
		if ( ! is_user_logged_in() )
			return iljb_get_message( 'error', __( 'You must be logged in to submit a new job.', 'iljobboard' ) );

		ob_start();

		if ( isset( $_POST['submitted'] ) ) {

			$values = $this->get_form_post_data();

			// Check that our nonce verifies
			if ( empty($_POST) || !wp_verify_nonce($_POST['submit_job_nonce'],'submit_job_nonce') ) {
				echo iljb_get_message( 'error', __( 'There has been an error in the submission: The nonce did not verify.', 'iljobboard' ) );
				exit;

			// Check if any form field doesn't validate
			} elseif ( is_wp_error( ( $return = self::validate_form_fields( $values ) ) ) ) {
				echo iljb_get_message( 'error', $return->get_error_message() );

			// If we don't have any validation or nonce errors, let's submit the form.
			} else {
				self::save_job( $values['job']['job_title'], $values['job']['job_description'] );
				self::update_job_meta( $values );

				echo iljb_get_message( 'success', __( 'Your job listing has been successfully submitted to us.', 'iljobboard' ) );

				// Stop outputting the form and redirect...
				return;
			}

		}

?>

		<form action="<?php the_permalink(); ?>" method="post">

			<?php $this->display_fields( 'job' ); ?>

			<div class="form-actions">
				<?php wp_nonce_field( 'submit_job_nonce', 'submit_job_nonce' ); ?>
				<input type="hidden" name="submitted" id="submitted" value="true">
				<input type="hidden" name="company_id" id="company_id" value="<?php echo get_current_user_id(); ?>">
				<input type="submit" value="<?php _e('Submit', 'iljobboard'); ?>" class="button success" name="submit_job" id="submit_job">
			</div>

		</form>

<?php
		return ob_get_clean();

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
					'description' => '',
					'options'	  => false,
				),
				'expiry_date' => array(
					'label'       => __( 'Expiry Date', 'iljobboard' ),
					'type'        => 'date',
					'required'    => true,
					'placeholder' => date( 'Y-m-d' ),
					'priority'    => 5,
					'description' => __( 'Enter the date that this job listing should expire', 'iljobboard' ),
					'options'	  => false,
				),
				'start_date' => array(
					'label'       => __( 'Start Date', 'iljobboard' ),
					'type'        => 'text',
					'required'    => false,
					'placeholder' => __( 'Right away', 'iljobboard' ),
					'priority'    => 10,
					'description' => __( 'When can the applicant start?', 'iljobboard' ),
					'options'	  => false,
				),
				'salary' => array(
					'label'       => __( 'Salary/Hourly Rate', 'iljobboard' ),
					'type'        => 'text',
					'required'    => false,
					'placeholder' => __( '$30/hr', 'iljobboard' ),
					'priority'    => 15,
					'description' => __( 'What salary/hourly rate can the employee expect?', 'iljobboard' ),
					'options'	  => false,
				),
				'location' => array(
					'label'       => __( 'Job Site Location', 'iljobboard' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => __( 'Muskoka', 'iljobboard' ),
					'priority'    => 20,
					'description' => __( 'Where is this position located?', 'iljobboard' ),
					'options'	  => false,
				),
				'job_type' => array(
					'label'       => __( 'Job Type', 'iljobboard' ),
					'type'        => 'select',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 25,
					'description' => '',
					'options'	  => self::job_taxonomy_options( 'iljb_job_type' ),
				),
				'job_status' => array(
					'label'       => __( 'Job Status', 'iljobboard' ),
					'type'        => 'select',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 30,
					'description' => '',
					'options'	  => self::job_taxonomy_options( 'iljb_job_status' ),
				),
				'job_hours' => array(
					'label'       => __( 'Hours of Work', 'iljobboard' ),
					'type'        => 'select',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 35,
					'description' => '',
					'options'	  => self::job_taxonomy_options( 'iljb_job_hours' ),
				),
				'qualifications' => array(
					'label'       => __( 'Qualifications', 'iljobboard' ),
					'type'        => 'textarea',
					'required'    => false,
					'placeholder' => '',
					'priority'    => 40,
					'description' => '',
					'options'	  => false,
				),
				'job_description' => array(
					'label'       => __( 'Job Description', 'iljobboard' ),
					'type'        => 'wysiwyg',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 45,
					'description' => '',
					'options'	  => false,
				),
				'how_to_apply' => array(
					'label'       => __( 'How to Apply', 'iljobboard' ),
					'type'        => 'checkboxes',
					'required'    => false,
					'placeholder' => '',
					'priority'    => 50,
					'description' => '',
					'options'	  => array(
						'Resume',
						'Cover letter',
						'Other',
						'Email',
						'Fax',
						'Online',
						'In person'
					),
				),
				'share_listing' => array(
					'label'       => __( 'Would you like the posting shared with other employment services in the region?', 'iljobboard' ),
					'type'        => 'truefalse',
					'required'    => false,
					'placeholder' => '',
					'priority'    => 55,
					'description' => '',
					'options'	  => false,
				),
			),
		) );

	}

	/**
	 * Job Taxonomy Options
	 */
	private function job_taxonomy_options( $taxonomy ) {

		$options = array();
		$terms = get_job_taxonomy( $taxonomy );

		foreach ( $terms as $term )
			$options[ $term->slug ] = $term->name;

		return $options;

	}

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
			echo iljb_get_message( 'error', $error );

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
	public static function get_fields( $key ) {

		// If we don't have a key, quit here.
		if ( empty( self::$fields[ $key ] ) )
			return;

		$fields = self::$fields[ $key ];

		// Sort the fields according to the field priority set.
		uasort( $fields, __CLASS__ . '::priority_cmp' );

		return $fields;

	}

	/**
	 * Get the Field Priority
	 */
	public static function priority_cmp( $a, $b ) {

		if ( $a['priority'] == $b['priority'] )
			return 0;

		return ( $a['priority'] < $b['priority'] ) ? -1 : 1;

	}

	/**
	 * Display Fields
	 */
	protected function display_fields( $key, $edit = false ) {

		include( 'forms/class-ilmenite-job-board-form-fields.php' );
		$form_field = new Ilmenite_Job_Board_Form_Fields();

		self::form_fields();

		foreach ( self::$fields as $group_key => $fields ) {
			foreach ( $fields as $key => $field ) {
				echo $form_field->the_field( $field['type'], $key, $field['label'], $field['description'], $field['placeholder'], $field['required'], $field['options'], $edit );
			}
		}

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

					case 'wysiwyg':
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
	protected function save_job( $post_title, $post_content, $status = 'pending' ) {

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
			wp_update_post( $post_data );
		} else {
			self::$job_id = wp_insert_post( $post_data );
		}

	}

	/**
	 * Update and set the post meta terms
	 */
	protected function update_job_meta( $values ) {

		// Go through the taxonomy fields
		wp_set_object_terms( self::$job_id, array( $values['job']['job_type'] ), 'iljb_job_type', false );
		wp_set_object_terms( self::$job_id, array( $values['job']['job_status'] ), 'iljb_job_status', false );
		wp_set_object_terms( self::$job_id, array( $values['job']['job_hours'] ), 'iljb_job_hours', false );

		// Go through all of the fields here...
		update_post_meta( self::$job_id, 'iljb_company_id', $_POST['company_id'] );
		update_post_meta( self::$job_id, 'iljb_company_id', $values['job']['company_id'] );
		update_post_meta( self::$job_id, 'iljb_expiry_date', $values['job']['expiry_date'] );
		update_post_meta( self::$job_id, 'iljb_start_date', $values['job']['start_date'] );
		update_post_meta( self::$job_id, 'iljb_salary', $values['job']['salary'] );
		update_post_meta( self::$job_id, 'iljb_location', $values['job']['location'] );
		update_post_meta( self::$job_id, 'iljb_qualifications', $values['job']['qualifications'] );
		update_post_meta( self::$job_id, 'iljb_how_to_apply', $values['job']['how_to_apply'] );
		update_post_meta( self::$job_id, 'iljb_share_listing', $values['job']['share_listing'] );

	}


}