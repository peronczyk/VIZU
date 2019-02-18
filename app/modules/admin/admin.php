<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin
 *
 * =================================================================================
 */

$template_file = __ROOT__ . '/' . Config::$APP_DIR . 'admin-panel/index.html';
$admin_template = new Template($template_file);

$admin_template->assign([
	'site_name'      => Config::$SITE_NAME,
	'site_path'      => $router->site_path,
	'app_path'       => Config::$APP_DIR,
	'resources_path' => $router->site_path . '/' . Config::$APP_DIR . 'admin-panel',
	'script_version' => VIZU_VERSION,
	'phpversion'     => phpversion(),
]);

echo $admin_template->parse();