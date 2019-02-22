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

	public function assignValues($fields_data) {
		$started = false;
		$started_key = null;
		$started_id = null;
		$child_field_ids = [];

		foreach ($this->_template->template_fields as $key => $field) {
			// Closing tag
			if ($field['type'] == '/' . self::FIELD_TYPE) {
				$this->_template->template_fields[$started_key]['groups-number'] = 1;
				if (isset($fields_data[$started_id])) {
					$this->_template->template_fields[$started_key]['values'] = json_decode($fields_data[$started_id]['subcontent']);
				}

				unset($this->_template->template_fields[$key]);
				$started = false;
				$child_field_ids = [];
			}

			// Child fields that are placed between opening and closing tags
			elseif ($started) {
				$this->_template->template_fields[$started_key]['children'][] = $field;
				unset($this->_template->template_fields[$key]);
				array_push($child_field_ids, $field['props']['id']);
			}

			// Opening tag
			elseif ($field['type'] == self::FIELD_TYPE) {
				$started = true;
				$started_key = $key;
				$started_id = $field['props']['id'];
			}
		}

		$this->_template->template_fields = array_values($this->_template->template_fields);
	}
}