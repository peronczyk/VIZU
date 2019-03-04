<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Field: Simple
 *
 * =================================================================================
 */

namespace Fields;

class Simple {
	const FIELD_TYPE = 'simple';

	private $_template;


	/** ----------------------------------------------------------------------------
	 * Constructor
	 */

	public function __construct(\Template $template, \DependencyContainer $container) {
		$this->_template = $template;
	}


	/** ----------------------------------------------------------------------------
	 * Assign values taken from the database
	 */

	public function assignValues($fields_data) {
		$this->_template->removeDuplicateTemplateFieldsByType(self::FIELD_TYPE);

		$this->_template->iterateTemplateFieldsType(self::FIELD_TYPE, function($key, $field) use ($fields_data) {
			$field_id = $field['props']['id'] ?? null;
			if (isset($fields_data[$field_id])) {
				$this->_template->template_fields[$key]['value'] = $fields_data[$field_id]['content'];
			}
		});
	}
}