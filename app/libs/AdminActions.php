<?php

class AdminActions {

	/** ----------------------------------------------------------------------------
	 * Bypass default PHP errors by custom error handler.
	 * This allows to display errors as JSON.
	 */

	public function setAjaxErrorHandler() {
		set_error_handler(function($errno, $errstr, $errfile, $errline) {
			header('Content-type: application/json');
			echo json_encode(
				['error' => [
					'number' => $errno,
					'str'    => $errstr,
					'file'   => $errfile,
					'line'   => $errline
				]]
			);
			die();
		});
	}
}