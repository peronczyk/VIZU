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
$tpl->setTheme(Config::$THEMES_DIR . Config::$THEME_NAME);

try {
	$template_content = $tpl->getContent('404');
	$template_fields  = $tpl->getFields($template_content);

	$tpl->assign([
		'site_path'   => $router->site_path . '/',
		'theme_path'  => Config::$THEMES_DIR . Config::$THEME_NAME . '/',
		'app_path'    => Config::$APP_DIR
	]);

	echo $tpl->parse($template_content, $template_fields, $lang->translations);
}

catch (Exception $e) {
	echo '<h1>404 Not Found</h1>';
	echo '<p>The page that you have requested could not be found.</p>';
}