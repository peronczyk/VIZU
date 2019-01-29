<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Simple, dependency-free CMS system that allows for quick implementation of
 * simple one-pages without having to configure anything in the administration panel
 *
 * ---------------------------------------------------------------------------------
 *
 * @see    https://github.com/peronczyk/vizu
 * @author Bartosz Perończyk <bartosz@peronczyk.com>
 *
 * =================================================================================
 */


define('VIZU_VERSION', '1.2.2');


/**
 * Load configuration
 */

if (!file_exists('config-app.php')) {
	die('Configuration file does not exist');
}
require_once 'config-app.php';


/**
 * Class autoloader
 */

require_once Config::$APP_DIR . 'autoload.php';


/**
 * Start core libraries
 */

$core   = new Core();
$router = new Router();


/**
 * Load theme configuration
 */

$theme_configuration_file = Config::$THEMES_DIR . Config::$THEME_NAME . '/config-theme.php';
if (!file_exists($theme_configuration_file)) {
	Core::error('Theme configuration file is missing', __FILE__, __LINE__, debug_backtrace());
}
$theme_config = require_once $theme_configuration_file;


/**
 * Redirects based on configuration and enviroment
 */

if (Config::$REDIRECT_TO_WWW === true) {
	$router->redirectToWww();
}


/**
 * Load database handler library
 * The connection is performed on the occasion of the first query.
 * Connection configuration depends on enviroment - development or production.
 */

if ($core->isDev() && file_exists('config-db.dev.php')) {
	$db_config = require_once 'config-db.dev.php';
}
elseif (file_exists('config-db.php')) {
	$db_config = require_once 'config-db.php';
}
else {
	Core::error('Database configuration file (config-db.php) is missing. You can copy this file from <a href="https://raw.githubusercontent.com/peronczyk/VIZU/master/config-db.php">this</a> location. Be sure to set database connection credentials.', __FILE__, __LINE__, debug_backtrace());
}

$db = new Mysql($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['name']);
unset($db_config);


/**
 * Start language library and set active language
 * if user is not in installation process
 */

if ($router->getFirstRequest() !== 'install') {
	$lang = new Language($router, $db);
	$lang->set();
	$lang->loadThemeTranslations();
}


/**
 * Load the module based on the page address
 */

$module_to_load = $router->getModuleToLoad();
if ($module_to_load) {
	require_once $module_to_load;
}