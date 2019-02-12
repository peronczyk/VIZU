<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Field: simple
 *
 * =================================================================================
 */

namespace fields;

class Simple implements Field {

	public function fieldHtml(string $id, array $params, string $content = null) {
		$str = "<label class='row'><div class='desc'><h4>{$params['name']}</h4>";

		if (!empty($params['desc'])) {
			$str .= "<p>{$params['desc']}</p>";
		}

		$str .= "</div><div class='field'><input type='text' name='{$id}' value='{$content}'></div></label>";

		return $str;
	}

}