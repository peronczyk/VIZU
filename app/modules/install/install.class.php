<?php

# ==================================================================================
#
#	VIZU CMS
#	Class: Install
#
# ==================================================================================

namespace modules\install;

class Install {

	private $_db; // Handle to database controller


	/**
	 * Constructor & dependancy injection
	 */

	public function __construct($db) {

		// Check if variable passed to this class is database controller
		if ($db && is_object($db) && is_a($db, 'libs\Database')) $this->_db = $db;
		else \libs\Core::error('Variable passed to class "Install" is not correct "Database" object', __FILE__, __LINE__, debug_backtrace());
	}


	/**
	 * Ckeck if required database tables exists
	 */

	public function check_db_tables() {
		$table_fields = $this->_db->query("SELECT 1 FROM `fields` LIMIT 1");
		$table_users = $this->_db->query("SELECT 1 FROM `users` LIMIT 1");
		return $table_fields && $table_users;
	}


	/**
	 * Ckeck if there are any users in database
	 */

	public function check_db_users() {
		$result = $this->_db->query("SELECT `id` FROM `users`");
		return ($this->_db->fetch($result) > 0) ? true : false;
	}


	/**
	 * DISPLAY : Html header
	 *
	 * @return string
	 */

	public function show_html_header($title = '') {
		$code = '<!doctype html>
			<html>
				<head>
					<meta charset="utf-8">
					<title>VIZU Installer: ' . $title . '</title>
					<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700">
					<style type="text/css">
						* { box-sizing: border-box; margin: 0; padding: 0; border: none; outline: none; }
						body { background-color: #f5f5f3; font-family: Roboto, Arial, Helvetica, sans-serif; font-size: 14px; color: #1f1e38; }

						main { display: flex; align-items: center; min-height: 100vh; }
						.Inner { margin: auto; width: 100%; max-width: 600px; }

						h1 { margin-bottom: 20px; }
						h2 { margin-bottom: 16px; color: #8fa3ad; font-weight: normal; }

						form { margin-top: 20px; padding-top: 10px; }
						label { display: block; margin-top: 16px; }
						input[type="text"], input[type="password"] { display: block; margin-top: 8px; width: 100%; height: 34px; background: transparent; border-bottom: 1px solid #dcdcd9; }
						button { margin-top: 20px; padding: 8px 14px; min-width: 100px; background-color: #00a8ff; font-weight: bold; color: #fff; text-align: center; cursor: pointer; }
					</style>
				</head>
				<body>
					<main>
						<div class="Inner">
		';
		return $code;
	}


	/**
	 * DISPLAY : Html footer
	 *
	 * @return string
	 */

	public function show_html_footer() {
		$code = '</div></main></body></html>';
		return $code;
	}

}