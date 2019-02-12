<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Field: rich
 *
 * =================================================================================
 */

namespace Fields;

class RichField {

	public function fieldHtml(string $id, array $params, string $content = null) {
		$content = $content ?? $params['content'] ?? '';

		$str = "<div class='row'><div class='desc'><h4>{$params['name']}</h4>";

		if (!empty($params['desc'])) {
			$str .= "<p>{$params['desc']}</p>";
		}

		$str .= "</div><div class='field'><textarea name='{$id}'";

		if (!empty($params['size'])) {
			$str .= " class='size-{$params['size']}'";
		}

		$str .= " data-richtext>{$content}</textarea></div></div>";

		return $str;
	}
}