<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Field: repeatable
 *
 * =================================================================================
 */

namespace Fields;

class RepeatableField {

	public function fieldHtml(string $id, array $params, string $content = null) {
		$str = "<div class='row'>Repeatable</div>";
		return $str;
	}
}