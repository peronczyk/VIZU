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

	public function __construct(\Template $template) {
		$this->_template = $template;
	}

	public function assignValues($lol) {
	}
}