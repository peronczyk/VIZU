<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Field: Repeatable
 *
 * =================================================================================
 */

namespace Fields;

class Repeatable {
	const FIELD_TYPE = 'repeatable';

	private $_template;


	/** ----------------------------------------------------------------------------
	 * Constructor
	 */

	public function __construct(\Template $template) {
		$this->_template = $template;
	}


	/** ----------------------------------------------------------------------------
	 * Assign values taken from the database
	 */

	public function assignValues() {
		$started = false;
		$started_key = null;
		$changes_made = 0;

		foreach ($this->_template->template_fields as $key => $field) {
			if ($field['type'] == '/' . self::FIELD_TYPE) {
				unset($this->_template->template_fields[$key]);
				$started = false;
				$changes_made++;
			}
			elseif ($started) {
				$this->_template->template_fields[$started_key]['children'][] = $field;
				unset($this->_template->template_fields[$key]);
				$changes_made++;
			}
			elseif ($field['type'] == self::FIELD_TYPE) {
				$started = true;
				$started_key = $key;
			}
		}

		$this->_template->template_fields = array_values($this->_template->template_fields);
	}
}