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
	private $_template;

	public function __construct(\Template $template) {
		$this->_template = $template;
	}

	public function assignValues() {
	}
}