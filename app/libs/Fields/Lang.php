<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Field: Lang
 *
 * =================================================================================
 */

namespace Fields;

class Lang {
	const FIELD_TYPE = 'lang';

	private $_template;
	private $_language;

	public function __construct(\Template $template, \DependencyContainer $container) {
		$this->_template = $template;
		$this->_language = $container->get('Language');
	}


	/** ----------------------------------------------------------------------------
	 * Assign values taken from theme translations
	 */

	public function assignValues() {
		$this->_template->removeDuplicateTemplateFieldsByType(self::FIELD_TYPE);

		$translations = $this->_language->getTranslations();
		if (!$translations) {
			return;
		}

		$this->_template->iterateTemplateFieldsType(self::FIELD_TYPE, function($key, $field) use ($translations) {
			$field_id = $field['props']['id'] ?? null;
			if (isset($translations[$field_id])) {
				$this->_template->template_fields[$key]['value'] = $translations[$field_id];
			}
		});
	}


	/** ----------------------------------------------------------------------------
	 * Lang fields are not editable in CMS so this method can remove them from
	 * Template object.
	 */

	public function removeNotEditableFields() {
		$this->_template->removeFieldType(self::FIELD_TYPE);
	}
}