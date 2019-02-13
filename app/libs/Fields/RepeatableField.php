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
		$str = "<div class='row'><h4>{$params['name']}</h4>";
		foreach ($params[\Template::CHILD_FIELD_KEY] as $key => $val) {
			$str .= "<div class='row__inner'><div class='desc'><h4>{$val['name']}</h4></div><div class='field'></div></div>";
		}
		$str .= "<button data-add-group>Add group</button>";
		$str .= "<pre>" . $id . "<br>" . print_r($params, true) . "<br>" . $content . "</pre>";
		$str .= "</div>";
		return $str;
	}
}