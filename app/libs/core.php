<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Core
#
# ==================================================================================

namespace libs;

class Core {

	/**
	 * Constructor
	 */

	public function __construct() {
		if (self::isDev()) {
			self::forceDisplayPhpErrors();
		}

		if (!function_exists('session_status') || session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}


	/**
	 * Force display PHP errors
	 */

	public static function forceDisplayPhpErrors() {
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
	}


	/**
	 * Display critical errors
	 *
	 * @param string $msg
	 * @param string $file - Pass here __FILE__
	 * @param string $line - Pass here __LINE__
	 * @param array $debug - Pass here debug_backtrace()
	 */

	public static function displayError(string $msg, $file = null, $line = null, $trace = null) {
		$error_page = self::commonHtmlHeader('Critical error');
		$error_page .= '
			<figure>;(</figure><h1>Something went<br>terribly wrong</h1><hr>
			<p>' . $msg . '</p>
			<ul>
				<li>Occurs in: <strong>' . self::processSystemFilePath($file) . '</strong> (line: <strong>' . $line . '</strong>)</li>
		';

		if ($trace) {
			$error_page .= '<li><strong>Trace</strong>:<ol>';
			foreach ($trace as $key => $entry) {
				$error_page .= '<li>' . self::processSystemFilePath($entry['file']) . ' (line: ' . $entry['line'] . ')</li>';
			}
			$error_page .= '</ol></li>';
		}

		$error_page .= '</ul>';
		$error_page .= self::commonHtmlFooter();

		die($error_page);
	}


	/**
	 * Check if application is in development mode
	 */

	public static function isDev() : bool {
		return (\Config::$DEBUG === true || (is_array(\Config::$DEV_IP) && in_array($_SERVER['REMOTE_ADDR'], \Config::$DEV_IP)));
	}


	/**
	 * Check if page was loaded as a result of AJAX request
	 */

	public static function isAjax() : bool {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}


	/**
	 * GETTER : Mtime
	 */

	public static function getMtime() {
		list($usec, $sec) = explode (' ', microtime());
		return (float)$usec + (float)$sec;
	}


	/**
	 * Change default keys in array to $key_name values taken from inside the array
	 *
	 * @param array $array
	 * @param string $key_name
	 *
	 * @return array
	 */

	public static function processArray(array $array, string $key_name) : array {
		$processed_array = [];
		foreach($array as $val) {
			if (isset($val[$key_name])) {
				$processed_array[$val[$key_name]] = $val;
				unset($processed_array[$val[$key_name]][$key_name]);
			}
		}
		return $processed_array;
	}


	/**
	 * Load database configuration
	 */

	public function loadDatabaseConfig() {
		if ($this->isDev() && file_exists('config-db.dev.php')) {
			return require_once 'config-db.dev.php';
		}
		elseif (file_exists('config-db.php')) {
			return require_once 'config-db.php';
		}
		else {
			self::error('Database configuration file (config-db.php) is missing. You can copy this file from <a href="https://raw.githubusercontent.com/peronczyk/VIZU/master/config-db.php">this</a> location. Be sure to set database connection credentials.', __FILE__, __LINE__, debug_backtrace());
		}
	}


	/**
	 * Aplication messages HTML header structure
	 *
	 * @return string
	 */

	public static function commonHtmlHeader(string $title = 'VIZU') {
		return '<!DOCTYPE html><html>
			<head>
				<meta charset="utf-8">
				<title>' . $title . '</title>
				<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700">
				<style type="text/css">
					* { box-sizing: border-box; margin: 0; padding: 0; border: none; outline: none; }
					body { background-color: #f5f5f3; font-family: Roboto, Arial, Helvetica, sans-serif; font-size: 14px; color: #1f1e38; }

					main { display: flex; align-items: center; min-height: 100vh; border-top: 2px solid #00a8ff; }
					.Inner { margin: auto; padding: 40px; width: 100%; max-width: 640px; }

					h1 { margin-bottom: 20px; font-size: 40px; line-height: 1.1em; font-weight: 400; }
					h2 { margin-bottom: 16px; font-size: 24px; font-weight: 500; color: #8fa3ad; }
					h3 { font-size: 14px; font-weight: 700; }

					figure { margin-bottom: 10px; font-size: 100px; font-weight: 700; color: #00a8ff; }
					hr { margin: 20px 0; border-top: 1px solid #dcdcd9; }

					p { margin-top: 20px; line-height: 1.6em; }
					small { font-size: .9em; color: #8fa3ad; }
					ul { margin-top: 20px; padding-left: 20px; color: #4f4f4f; font-size: 12px; line-height: 1.6em; }
					ol { margin-left: 20px; }
					li { margin-bottom: 4px; }

					a { text-decoration: none; color: #00a8ff; transition: .2s; }
					a:hover { color: #0199e7; text-decoration: underline; }

					form { margin-top: 20px; padding-top: 10px; }
					label { display: block; margin-top: 16px; }
					input[type="text"], input[type="password"], input[type="email"] { display: block; margin-top: 8px; width: 100%; height: 34px; background: transparent; border-bottom: 1px solid #c8c8c5; }
					input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus { border-color: #00a8ff; }
					button { margin-top: 20px; padding: 12px 20px; min-width: 100px; background-color: #00a8ff; font-weight: bold; color: #fff; text-align: center; cursor: pointer; transition: .2s; }
					button:hover { background-color: #0199e7; }

					button:active, a:active { transform: translateY(2px); }
				</style>
			</head>
			<body>
				<main>
					<div class="Inner">';
	}


	/**
	 * Aplication messages HTML footer structure
	 *
	 * @return string
	 */

	public static function commonHtmlFooter() {
		return '</div></main></body></html>';
	}


	/**
	 * Process file path to prevent FPD (Full Path Disclolsure) attack
	 */

	public static function processSystemFilePath(string $file_path) : string {
		return basename($file_path);
	}

}