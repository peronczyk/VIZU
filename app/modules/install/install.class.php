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
		return (count($this->_db->fetch($result)) > 0) ? true : false;
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

						main { display: flex; align-items: center; min-height: 100vh; border-top: 2px solid #00a8ff }
						.Inner { margin: auto; padding: 40px; width: 100%; max-width: 640px; }

						h1 { margin-bottom: 20px; font-size: 40px; font-weight: 400; }
						h2 { margin-bottom: 16px; font-size: 24px; font-weight: 500; color: #8fa3ad; }
						h3 { font-size: 14px; font-weight: 700; }

						p { margin-top: 20px; line-height: 1.2em; }
						small { font-size: .9em; color: #8fa3ad; }

						hr { margin: 20px 0; border-top: 1px solid #dcdcd9; }

						a { text-decoration: none; color: #00a8ff; transition: .2s; }
						a:hover { color: #0199e7; text-decoration: underline; }

						form { margin-top: 20px; padding-top: 10px; }
						label { display: block; margin-top: 16px; }
						input[type="text"], input[type="password"], input[type="email"] { display: block; margin-top: 8px; width: 100%; height: 34px; background: transparent; border-bottom: 1px solid #c8c8c5; }
						input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus { border-color: #00a8ff; }
						button { margin-top: 20px; padding: 12px 20px; min-width: 100px; background-color: #00a8ff; font-weight: bold; color: #fff; text-align: center; cursor: pointer; transition: .2s; }
						button:hover { background-color: #0199e7; }

						button:active, a { transform: translateY(2px); }
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