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
	private $_template;

	public function __construct(\Template $template) {
		$this->_template = $template;
	}

	public function assignValues() {
	}
}