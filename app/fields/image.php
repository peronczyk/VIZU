<?php

class image {

	public function field_html($id, $params) {
		$str = '<div class="row"><div class="desc"><h4>' . $params['name'] . '</h4>';

		if (!empty($params['desc'])) {
			$str .= '<p>' . $params['desc'] . '</p>';
		}

		$str .= '</div><div class="field"><label class="file">Prześlij plik<input type="file" name="' . $id . '"></label></div></div>';

		return $str;
	}

}