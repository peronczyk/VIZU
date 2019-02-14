<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin
 *
 * =================================================================================
 */

$tpl = new Template();

$tpl->setTheme(Config::$APP_DIR . 'admin-panel');
$tpl->assign([
	'site_name'      => Config::$SITE_NAME,
	'site_path'      => $router->site_path,
	'app_path'       => Config::$APP_DIR,
	'resources_path' => $router->site_path . '/' . Config::$APP_DIR . 'admin-panel',
	'script_version' => VIZU_VERSION,
	'phpversion'     => phpversion(),
]);

$template_content = $tpl->getContent('index.html');
$template_fields  = $tpl->getFields($template_content);

echo $tpl->parse($template_content, $template_fields);
