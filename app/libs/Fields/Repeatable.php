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
	}
}