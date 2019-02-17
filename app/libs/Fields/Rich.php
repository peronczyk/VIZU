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
	private $_template;

	public function __construct(\Template $template) {
		$this->_template = $template;
	}

	public function assignValues() {
	}
}