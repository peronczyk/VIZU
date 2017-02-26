<?php

class Core {

	public $project_id;
	public static $ajax_loaded = false; // Check if script is run as request by AJAX


	# ==============================================================================
	# CONSTRUCTOR
	# ==============================================================================

	public function __construct() {
		error_reporting(E_ERROR | E_WARNING | E_PARSE);

		if (!function_exists('session_status') || session_status() == PHP_SESSION_NONE) session_start();
		//ob_start();

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') self::$ajax_loaded = true;

	}


	# ==============================================================================
	# DISPLAY ERRORS
	# ==============================================================================

	public static function error($msg, $file = null, $line = null, $debug = null) {
		$headers_sent = headers_sent();
		if ($headers_sent) ob_clean();

		// Hide server document root from file path
		$document_root = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']);
		$file = str_replace($document_root, '', $file);

		if (self::$ajax_loaded === true) {
			header('Content-type: application/json');
			echo json_encode(array(
				'error' => array(
					'str'	=> $msg,
					'file'	=> $file,
					'line'	=> $line
				)
			));
		}
		else {
			echo('<!DOCTYPE html><html>
				<head>
					<meta charset="utf-8"><title>Błąd krytyczny</title>
					<style type="text/css">
						html, body { font-family: helvetica,arial,sans-serif; font-size: 16px; }
						body > div { max-width: 460px; margin: 50px auto; }
						h1 { font-size: 100px; margin: 0; line-height: 90px; }
						h2 { font-size: 30px; font-weight: normal; }
						hr { border: none; border-bottom: 1px solid #dddddd; }
						p { color: #4f4f4f; line-height: 22px; }
						ul { color: #4f4f4f; line-height: 20px; }
					</style>
				</head>
				<body>
					<div><h1>;(</h1><h2>Sorry,<br /><strong>something went wrong</strong></h2><hr><p>' . $msg . '</p><ul style="font-size: 14px;">');

			if (empty($debug)) {
				echo('<li>File: <strong>' . $file . '</strong></li><li>Line: <strong>' . $line . '</strong></li>');
			}
			else {
				$debug[0]['file'] = str_replace($document_root, '', $debug[0]['file']);
				echo('<li>Invoked by: <strong>' . $debug[0]['file'] . '</strong> ( line: <strong>' . $debug[0]['line'] . '</strong> )</li>');
				echo('<li>Occurs in: <strong>' . $file . '</strong> ( line: <strong>' . $line . '</strong> )</li>');
				//echo('<li><pre>'); print_r($debug); echo('</pre></li>');
			}

			echo('</ul></div>
				</body>
			</html>');
		}
		if ($headers_sent) ob_end_flush();
		exit;
	}


	# ==============================================================================
	# DISPLAY ERRORS
	# ==============================================================================

	public function load_field_class($field_name) {
		$class_file = 'app/fields/' . $field_name . '.php';
		if (file_exists($class_file)) {
			require_once($class_file);
			if (class_exists($field_name)) {
				return new $field_name;
			}
			else return 'Plik sterujacy polem nie zawiera odpowiedniej klasy: ' . $field_name;
		}
		else { return 'Plik klasy sterujacej polem nie istnieje: ' . $class_file; }
	}


	# ==============================================================================
	# IS DEV
	# ==============================================================================

	public function is_dev() {
		$default_dev_ip = array('127.0.0.1', '0.0.0.0', '::1');
		if ($_SERVER['REMOTE_ADDR'] === Config::DEV_IP || in_array($_SERVER['REMOTE_ADDR'], $default_dev_ip)) return true;
		return false;
	}


	# ==============================================================================
	# PRINT VIEWABLE ARRAY
	# ==============================================================================

	public static function print_arr($arr) {
		if (is_array($arr)) {
			echo('<pre>');
			print_r($arr);
			echo("</pre>");
		}
		else echo('<pre>This is not a table</pre>');
	}


	# ==============================================================================
	# GET MTIME
	# ==============================================================================

	public static function get_mtime() {
		list($usec, $sec) = explode (" ", microtime());
		return((float)$usec + (float)$sec);
	}


	# ==============================================================================
	# PROCESS ARRAY
	# Changes default keys in array to $key_name values taken from inside tahe array
	# ==============================================================================

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

}