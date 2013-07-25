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

		// If the field type doesn't have a function, quit here.
		if( ! method_exists( $this, 'field_' . $this->type ) )
			return;

		return '<p>' . call_user_func( array( $this, 'field_' . $this->type ), $this->id, $this->label, $this->description, $this->placeholder, $this->required, $this->options, $this->edit ) . '</p>';

	}

	/**
	 * Field Label
	 */
	private function field_label() {

		$label = '<label for="' . $this->id . '" class="job-submit-label">' . $this->label . '</label>';

		return $label;

	}

	/**
	 * Text Field
	 */
	private function field_text() {

		$text_field = '<input type="text" id="' . $this->id . '" name="' . $this->id . '" placeholder="' . $this->placeholder . '">';

		$output = $this->field_label() . $text_field;

		return $output;

	}

	/**
	 * Textarea Field
	 */
	private function field_textarea() {

		$text_field = '<textarea id="' . $this->id . '" name="' . $this->id . '" placeholder="' . $this->placeholder . '"></textarea>';

		$output = $this->field_label() . $text_field;

		return $output;

	}

	/**
	 * Select Field
	 */
	private function field_select() {

		$text_field = '<textarea id="' . $this->id . '" name="' . $this->id . '" placeholder="' . $this->placeholder . '"></textarea>';

		$output = $this->field_label() . $text_field;

		return $output;

	}

	/**
	 * Checkboxes Field
	 */
	private function field_checkboxes() {

		$checkboxes = '';

		foreach( $this->options as $option ) {
			$checkboxes .= '<input type="checkbox" name="' . $option . '" id="' . $option . '"> <label for="' . $option . '" class="job-checkbox-label">' . $option . '</label>';
		}

		$output = '<span class="job-checkbox-group-label">' . $this->label . '</span>' . $checkboxes;

		return $output;

	}

	/**
	 * True/False Field
	 */
	private function field_truefalse() {

		$checkbox = '<input type="checkbox" name="' . $this->id . '" id="' . $this->id . '"> <label for="' . $this->id . '" class="job-truefalse-label">' . $this->label . '</label>';

		$output = $checkbox;

		return $output;

	}

}