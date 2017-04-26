<?php

# ==================================================================================
#
#	VIZU CMS
#	Class Autoloader
#
# ==================================================================================

spl_autoload_register(function($lib_name) {
	if (class_exists($lib_name, false)) return;

	$chunks = explode('\\', trim($lib_name));

	if (count($chunks) > 1) {
		$lib_file_name = strtolower(end($chunks));
		$lib_file_path = null;
		$lib_class_name = ucfirst($lib_file_name);

		switch($chunks[0]) {
			case 'libs': $lib_file_path = Config::$APP_DIR . 'libs/' . $lib_file_name . '.php';
		}

		if (file_exists($lib_file_path)) require_once($lib_file_path);
	}
});