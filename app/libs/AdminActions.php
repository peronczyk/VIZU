<?php

class AdminActions {
	private $_user;
	private $_tpl;

	public function __construct(User $user, Template $tpl) {
		$this->_user = $user;
		$this->_tpl = $tpl;
	}

	public function displayAdminHomePage() {
		if ($this->_user->getAccess() > 0) {
			$template_content = $this->_tpl->getContent('home');
			$template_fields  = $this->_tpl->getFields($template_content);

			$this->_tpl->assign([
				'loggedin' => 'loggedin',
				'page'     => $this->_tpl->parse($template_content, $template_fields),
			]);
		}

		else {
			$this->_tpl->assign([
				'loggedin' => '',
				'page'     => '',
			]);
		}

		$template_content = $this->_tpl->getContent('index');
		$template_fields  = $this->_tpl->getFields($template_content);
		echo $this->_tpl->parse($template_content, $template_fields);
	}


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