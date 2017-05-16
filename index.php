<?php

# ==================================================================================
#
#	VIZU CMS
#	https://github.com/peronczyk/vizu
#
# ==================================================================================


define('VIZU_VERSION', '1.1.0');

/**
 * Load configuration
 */

if (!file_exists('config-app.php')) die('Configuration file does not exist');
require_once('config-app.php');

/**
 * Load class autoloader
 */

require_once(Config::$APP_DIR . 'autoload.php');


/**
 * Start core libraries
 */

$core = new libs\Core();
$router = new libs\Router();


/**
 * Redirects based on configuration and enviroment
 */

if (Config::$REDIRECT_TO_WWW === true) $router->redirect_to_www();


/**
 * Load database handler library
 * The connection is performed on the occasion of the first query.
 * Connection configuration depends on enviroment - development or production.
 */

if ($core->is_dev() && file_exists('config-db.dev.php')) {
	$db_config = include('config-db.dev.php');
}
elseif (file_exists('config-db.php')) {
	$db_config = include('config-db.php');
}
else Core::error('Database configuration file is missing', __FILE__, __LINE__, debug_backtrace());

$db = new libs\Database($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['name']);
unset($db_config);


/**
 * Start language library and set active language
 */

$lang = new libs\Language($db);
$lang_set_by_request = $lang->set(@$router->request[0]);
if ($lang_set_by_request) $router->request_shift();
$lang->load_theme_translations();

/**
 * Load the module based on the page address
 */

$module_to_load = $router->get_module_to_load();
if ($module_to_load) require_once($module_to_load);