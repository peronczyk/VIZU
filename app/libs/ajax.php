<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: AJAX
#
# ==================================================================================

namespace libs;

class Ajax {

	public $log = []; // Array of values to log in console
	public $error; // Errors
	public $message; // Popup messages
	public $html; // HTML to display
	public $loggedin = false; // Is actual user logged in


	/**
	 * Reset new value to variable
	 */

	public function set($varname, $value) {
		$this->$varname = $value;
		return $this;
	}


	/**
	 * Add new value to array
	 */

	public function add($varname, $value) {
		array_push($this->$varname, $value);
		return $this;
	}


	/**
	 * Echoes stored JSON data
	 */

	public function send() {
		header('Content-type: application/json');
		echo json_encode($this);
		exit;
	}
}