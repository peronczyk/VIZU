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
	'theme_path'     => Config::$THEMES_DIR . 'admin',
	'script_version' => VIZU_VERSION,
	'phpversion'     => phpversion(),
]);

$template_content = $tpl->getContent('index.html');
$template_fields  = $tpl->getFields($template_content);

echo $tpl->parse($template_content, $template_fields);




die();


/**
 * Start AJAX class and set it up
 */


/**
 * Handle post requests
 */

if (count($_POST) > 0) {
	switch($request) {

		/**
		 * User login operation
		 */
		case 'login':
			$auth = $user->login($_POST['email'], $_POST['pass']);
			if ($auth === true) {
				$ajax->set('loggedin', true);
			}
			else {
				$ajax->set('error', [
					'str'  => $auth,
					'file' => __FILE__,
					'line' => __LINE__
				]);
				$show_content = false;
			}
			break;

		/**
		 * Password recovery operation
		 */

	}
}
