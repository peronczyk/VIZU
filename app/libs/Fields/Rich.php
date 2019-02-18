<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Field: Rich
 *
 * =================================================================================
 */

namespace Fields;

class Rich {
	const FIELD_TYPE = 'rich';

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

	public function assignValues() {
		$this->_template->removeDuplicateTemplateFieldsByType(self::FIELD_TYPE);
	}
}