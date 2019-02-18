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

	public function assignValues() {
		$this->_template->removeDuplicateTemplateFieldsByType(self::FIELD_TYPE);
	}
}