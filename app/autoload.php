<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Class Autoloader
 *
 * =================================================================================
 */

spl_autoload_register(function($class) {
	if (class_exists($class, false)) {
		return;
	}

	$class = str_replace('\\', '/', $class);
	$class_file = __DIR__ . '/libs/' . $class . '.php';

	if (file_exists($class_file)) {
		require $class_file;
		return true;
	}
	else {
		return false;
	}
});