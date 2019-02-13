<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Lib: AJAX
 *
 * =================================================================================
 */

class Ajax {
	public function set($varname, $value) {
		$this->$varname = $value;
		return $this;
	}

	public function add($varname, $value) {
		array_push($this->$varname, $value);
		return $this;
	}

	public function send() {
		header('Content-type: application/json');
		echo json_encode($this);
		exit;
	}
}