<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: 404 error page
 *
 * =================================================================================
 */

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$tpl = new Template();
$tpl->setTemplatesDir(__ROOT__ . '/' . Config::$THEMES_DIR . Config::$THEME_NAME);

try {
	$tpl->assign([
		'site_path'  => $router->site_path . '/',
		'theme_path' => Config::$THEMES_DIR . Config::$THEME_NAME . '/',
		'app_path'   => Config::$APP_DIR
	]);

	echo $tpl->parseFile('templates/404.html', $lang->getTranslations());
}

catch (Exception $e) {
	echo '<h1>404 Not Found</h1>';
	echo '<p>The page that you have requested could not be found.</p>';
}