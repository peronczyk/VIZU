<?php

 #	================================================================================
 #
 #	VIZU CMS
 #	https://github.com/peronczyk/vizu
 #
 #	================================================================================


define('VERSION', 0.1); // VIZU version


// Load configuration

if (!file_exists('config.php')) die('Configuration file does not exist');
require_once('config.php');


// Start application core libraries

require_once(Config::APP_DIR . 'libs/core.php');
require_once(Config::APP_DIR . 'libs/router.php');

$core	= new Core();
$router	= new Router();


// Redirect to address with www. at the beginning

if (Config::REDIRECT_TO_WWW === true) $router->redirect_to_www();


// Redirect to installation if it exists and if app is not in dev mode

if (!$core->is_dev() && file_exists(Config::INSTALL_DIR) && ((isset($router->request[0]) && $router->request[0] !== 'install') || !isset($router->request[0]))) {
	header('Location: ' . $router->site_path . '/' . Config::INSTALL_DIR . '?redirected=true');
}


// Start database library and connect to the database

require_once(Config::APP_DIR . 'libs/db.php');
$db = new Database();
$db->connect(Config::DB_HOST, Config::DB_USER, Config::DB_PASS, Config::DB_NAME);


// Start templating library

require_once(Config::APP_DIR . 'libs/tpl.php');
$tpl = new Template();


// Start language library and set active language

require_once(Config::APP_DIR . 'libs/language.php');
$lang = new Language($db);
if ($lang->set((isset($router->request[0]) ? $router->request[0] : ''))) {
	$router->request_shift();
}
$lang->load_theme_translations();


// Detect what kind of content is requested. If none listed display 404

if (isset($router->request[0])) {
	switch($router->request[0]) {
		case 'admin':
			require_once(Config::APP_DIR . 'admin.php');
			break;

		case 'mailer':
			require_once(Config::APP_DIR . 'mailer.php');
			break;

		case 'install':
			require_once(Config::INSTALL_DIR . 'index.php');
			break;

		default:
			require_once(Config::APP_DIR . '404.php');
	}
}
else require_once(Config::APP_DIR . 'page.php');