<?php

class Ajax {

	// Variables that can be set

	public $log = array(); // Array of values to log in console
	public $error; // Errors
	public $message; // Popup messages
	public $html; // HTML to display
	public $loggedin = false;	// Is actual user logged in


	// Reset new value to variable

	public function set($varname, $value) {
		$this->$varname = $value;
	}


	// Add new value to array

	public function add($varname, $value) {
		array_push($this->$varname, $value);
	}


	// Echoes stored JSON data

	public function send() {
		echo json_encode($this);
	}
}