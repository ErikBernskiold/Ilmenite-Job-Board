<?php
/**
 * Form Fields
 */

class Ilmenite_Job_Board_Form_Fields {

	private static $type;
	private static $id;
	private static $label;
	private static $description;
	private static $placeholder;
	private static $required;
	private static $options;
	private static $edit;

	private static $required_content;
	private static $required_class;
	private static $required_label;

	/**
	 * Display Field
	 */
	public function the_field( $field_type = 'text', $field_id = null, $field_label = null, $field_description = null, $field_placeholder = null, $field_required = false, $field_options = null, $field_edit = false ) {

		$this->type = $field_type;
		$this->id = $field_id;
		$this->label = $field_label;
		$this->description = $field_description;
		$this->placeholder = $field_placeholder;
		$this->required = $field_required;
		$this->options = $field_options;
		$this->edit = $field_edit;

		if ( $this->required ) {
			$this->required_content = 'data-validation-engine="validate[required]"';
			$this->required_class = 'required';
			$this->required_label = '<abbr title="' . __( 'Required', 'iljobboard' ) . '" class="required-indicator">*</abbr>';
		}

		// If the field type doesn't have a function, quit here.
		if( ! method_exists( $this, 'field_' . $this->type ) )
			return;

		return '<div class="form-field-item">' . call_user_func( array( $this, 'field_' . $this->type ), $this->id, $this->label, $this->description, $this->placeholder, $this->required, $this->options, $this->edit ) . '</div>';

	}

	/**
	 * Field Label
	 */
	private function field_label() {

		$label = '<label for="' . $this->id . '" class="job-submit-label">' . $this->label . $this->required_label . '</label>';

		return $label;

	}

	/**
	 * Text Field
	 */
	private function field_text() {

		$text_field = '<input type="text" id="' . $this->id . '" name="' . $this->id . '" ' . $this->required_content . ' placeholder="' . $this->placeholder . '">';

		$output = $this->field_label() . $text_field;

		return $output;

	}

	/**
	 * Date Field
	 */
	private function field_date() {

		// Make sure we have the jQuery UI Datepicker
		wp_enqueue_script( 'jquery-ui-datepicker' );

		// Send in the style for the jQuery Datepicker
		wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		wp_enqueue_style( 'jquery-ui' );

		// Add the datepicker code
		add_action( 'wp_footer', array( $this, 'datepicker_code' ), 20 );

		$text_field = '<input type="text" class="jquery-ui-datepicker" id="' . $this->id . '" name="' . $this->id . '" ' . $this->required_content . ' placeholder="' . $this->placeholder . '">';

		$output = $this->field_label() . $text_field;

		return $output;

	}

	/**
	 * Textarea Field
	 */
	private function field_textarea() {

		$text_field = '<textarea id="' . $this->id . '" name="' . $this->id . '" ' . $this->required_content . ' placeholder="' . $this->placeholder . '"></textarea>';

		$output = $this->field_label() . $text_field;

		return $output;

	}

	/**
	 * WYSIWYG Field
	 */
	private function field_wysiwyg() {

		ob_start();

		echo $this->field_label();

		wp_editor( '', $this->id, array(
			'media_buttons' => false,
			'textarea_rows' => 8,
			'teeny' => true,
			'quicktags' => false,
			'tinymce' => array(
				'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline',
			),
		) );

		return ob_get_clean();

	}

	/**
	 * Select Field
	 */
	private function field_select() {

		$select = '<select id="' . $this->id . '" name="' . $this->id . '" ' . $this->required_content . '>';

		$select .= '<option selected="selected" disabled="disabled">' . __( 'Please select...', 'iljobboard' ) . '</option>';

		foreach( $this->options as $key => $value ) {
			$select .= '<option value="' . $key . '">' . $value . '</option>';
		}

		$select .= '</select>';

		$output = $this->field_label() . $select;

		return $output;

	}

	/**
	 * Checkboxes Field
	 */
	private function field_checkboxes() {

		$checkboxes = '';

		foreach( $this->options as $option ) {
			$checkboxes .= '<input type="checkbox" name="' . $this->id . '[]" id="' . $option . '" value="' . $option . '"> <label for="' . $option . '" class="job-checkbox-label">' . $option . '</label>';
		}

		$output = '<span class="job-checkbox-group-label">' . $this->label . '</span>' . $checkboxes;

		return $output;

	}

	/**
	 * True/False Field
	 */
	private function field_truefalse() {

		$checkbox = '<input type="checkbox" name="' . $this->id . '" id="' . $this->id . '" value="1"> <label for="' . $this->id . '" class="job-truefalse-label">' . $this->label . '</label>';

		$output = $checkbox;

		return $output;

	}

	/**
	 * Datepicker Code
	 */
	public function datepicker_code() {

		$script = '<script>
			jQuery(function() {
				jQuery( ".jquery-ui-datepicker" ).datepicker( { dateFormat: "yy-mm-dd" } );
			});
		</script>';

		echo $script;
	}

}