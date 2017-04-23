<?php

# ==================================================================================
#
#	VIZU CMS
#	https://github.com/peronczyk/vizu
#
# ==================================================================================


define('VIZU_VERSION', '1.0.0');


// Load configuration
if (!file_exists('config.php')) die('Configuration file does not exist');
require_once('config.php');


// Start application core library
require_once(Config::APP_DIR . 'libs/core.php');
$core = new Core();

// Start router
$router = $core->load_lib('Router');


// Redirect to address with www. at the beginning if configuration requires it
if (Config::REDIRECT_TO_WWW === true) $router->redirect_to_www();


// Redirect to installation if it exists and if app is not in dev mode
if (!$core->is_dev() && file_exists(Config::INSTALL_DIR) && ((isset($router->request[0]) && $router->request[0] !== 'install') || !isset($router->request[0]))) {
	header('Location: ' . $router->site_path . '/' . Config::INSTALL_DIR . '?redirected=true');
}


// Start database library and connect to the database
$db = $core->load_lib('Database');
$db->connect(Config::DB_HOST, Config::DB_USER, Config::DB_PASS, Config::DB_NAME);


// Start templating library
$tpl = $core->load_lib('Template');


// Start language library and set active language
$lang = $core->load_lib('Language');
$lang->inject($db);

if ($lang->set((isset($router->request[0]) ? $router->request[0] : ''))) {
	$router->request_shift();
}

$lang->load_theme_translations();


// Load module
$module_to_load = $router->get_module_to_load();
if ($module_to_load) require_once($router->get_module_to_load());