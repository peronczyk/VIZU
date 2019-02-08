<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Simple, dependency-free CMS system that allows for quick implementation of
 * simple one-pages without having to configure anything in the administration panel.
 *
 * ---------------------------------------------------------------------------------
 *
 * @see    https://github.com/peronczyk/vizu
 * @author Bartosz Perończyk <bartosz@peronczyk.com>
 *
 * =================================================================================
 */


define('VIZU_VERSION', '1.3.0');


/**
 * Load application base configuration
 */

(file_exists('config.php'))
	? require_once 'config.php'
	: die('Configuration file does not exist');

if (file_exists('config-override.php')) {
	require_once 'config-override.php';
}


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
 * Load configured database handler library.
 */

switch (Config::$DB_TYPE) {
	case 'SQLite':
		$db = new SQLite();
		break;

	case 'MySQL':
		$db = new Mysql(Config::$MYSQL_HOST, Config::$MYSQL_USER, Config::$MYSQL_PASS, Config::$MYSQL_NAME);
		break;

	default:
		Core::error('Unknown database handler: ' . Config::$DB_NAME);
}


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