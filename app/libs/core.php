<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Core
#
# ==================================================================================

namespace libs;

class Core {

	// Check if script is running as AJAX request
	public static $ajax_loaded = false;


	/**
	 * Constructor
	 */

	public function __construct() {
		error_reporting(E_ERROR | E_WARNING | E_PARSE);

		if (!function_exists('session_status') || session_status() == PHP_SESSION_NONE) session_start();

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			self::$ajax_loaded = true;
		}
	}


	/**
	 * Display critical errors
	 *
	 * @param string $msg
	 * @param string $file - Pass here __FILE__
	 * @param string $line - Pass here __LINE__
	 * @param array $debug - Pass here debug_backtrace()
	 */

	public static function error($msg, $file = null, $line = null, $debug = null) {

		// Hide server document root from file path
		$document_root = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']);
		$file = str_replace($document_root, '', $file);

		if (self::$ajax_loaded === true) {
			header('Content-type: application/json');
			echo json_encode(array(
				'error' => array(
					'str'  => $msg,
					'file' => $file,
					'line' => $line
				)
			));
		}
		else {
			echo self::common_html_header('Critical error');
			echo '<figure>;(</figure><h1>Something went<br>terribly wrong</h1><hr><p>' . $msg . '</p><ul>';

			if (empty($debug)) {
				echo '<li>File: <strong>' . $file . '</strong></li><li>Line: <strong>' . $line . '</strong></li>';
			}
			else {
				$debug[0]['file'] = str_replace($document_root, '', $debug[0]['file']);
				echo '<li>Invoked by: ' . $debug[0]['file'] . ' (line: <strong>' . $debug[0]['line'] . '</strong>)</li>';
				echo '<li>Occurs in: ' . $file . ' (line: <strong>' . $line . '</strong>)</li>';
			}

			echo '</ul>';
			echo self::common_html_footer();
		}
		if ($headers_sent) ob_end_flush();
		exit;
	}


	/**
	 * Check if application is in development mode
	 */

	public function is_dev() {
		if (is_array(\Config::$DEV_IP) && in_array($_SERVER['REMOTE_ADDR'], \Config::$DEV_IP)) {
			return true;
		}
		return false;
	}


	/**
	 * GETTER : Mtime
	 */

	public static function get_mtime() {
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

	public function process_array($array, $key_name) {
		if (!is_array($array)) return false;

		$processed_array = array();
		foreach($array as $val) {
			if (isset($val[$key_name])) {
				$processed_array[$val[$key_name]] = $val;
				unset($processed_array[$val[$key_name]][$key_name]);
			}
		}
		return $processed_array;
	}


	/**
	 * Aplication messages HTML header structure
	 *
	 * @return string
	 */

	public static function common_html_header($title = 'VIZU') {
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

	public static function common_html_footer() {
		return '</div></main></body></html>';
	}

}