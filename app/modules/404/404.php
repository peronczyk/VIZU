<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: 404 error page
 *
 * =================================================================================
 */

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);

$source_template_name = '404';
$template_file = __ROOT__ . '/' . Config::$THEMES_DIR . Config::$THEME_NAME . '/templates/' . $source_template_name . '.html';

try {
	$tpl = new Template($template_file);
	$tpl->assign([
		'site_path'  => $router->site_path . '/',
		'theme_path' => Config::$THEMES_DIR . Config::$THEME_NAME . '/',
		'app_path'   => Config::$APP_DIR
	]);

	echo $tpl->parse($lang->getTranslations());
}

catch (Exception $e) {
	echo '<h1>404 Not Found</h1>';
	echo '<p>The page that you have requested could not be found.</p>';
}