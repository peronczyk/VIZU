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
				$groups_number = 0;

				if (isset($fields_data[$started_id])) {
					$content = json_decode($fields_data[$started_id]['content'], true);
					if (is_array($content)) {
						$this->_template->template_fields[$started_key]['value'] = $content;
					}
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