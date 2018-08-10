<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin
#
# ==================================================================================

// Security constant. Needs to be checked in all included files
define('IN_ADMIN', true);

$tpl = new libs\Template();

$tpl->setTheme('admin');
$tpl->assign([
	'app_path'       => Config::$APP_DIR,
	'site_path'      => $router->site_path,
	'theme_path'     => 'themes/admin',
	'script_version' => VIZU_VERSION,
	'phpversion'     => phpversion(),
]);

$user = new libs\User($db);

/**
 * PAGE LOADED VIA AJAX
 * Check if request was done asynchronously.
 * If true change the behavior of page to always return JSON data.
 */

if (libs\Core::isAjax()) {

	/**
	 * Start AJAX class and set it up
	 */

	$ajax = new libs\Ajax();

	$display = true; // Is there anything that needs to be shown?
	$request = (!isset($router->request[1]))
		? 'home'
		: $router->request[1];


	/**
	 * Handle post requests
	 */

	if (count($_POST) > 0) {
		switch($request) {
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
					$display = false;
				}
				break;
		}
	}

	/**
	 * Display results
	 */

	if (($user->getAccess() > 0) && ($display === true)) {
		$ajax->set('loggedin', true);

		switch($request) {

			// ADMIN HOME PAGE

			case 'home':
			case 'login':
				$template_content = $tpl->getContent('home');
				$template_fields  = $tpl->getFields($template_content);

				$ajax->set('html', $tpl->parse($template_content, $template_fields));
				break;


			// LOG OUT USER

			case 'logout':
				$user->logout();
				$ajax->set('loggedin', false);
				break;


			// CONTENT ADMINISTRATION

			case 'content':
				require_once __DIR__ . '/content/content.php';
				break;


			// CHANGES HISTORY

			case 'history':
				require_once __DIR__ . '/history/history.php';
				break;


			// USER FUNCTIONS

			case 'user':
				require_once __DIR__ . '/user/user.php';
				break;


			// BACKUP OPERATIONS

			case 'backup':
				require_once __DIR__ . '/backup/backup.php';
				break;


			// UNKNOWN REQUEST

			default:
				$ajax->set('error', [
					'str'  => 'Unknown function: ' . $request,
					'file' => __FILE__,
					'line' => __LINE__
				]);
		}
	}

	$ajax->send();
}


/**
 * PAGE LOADED NORMALLY
 * If page was not loaded asynchronously display admin template.
 */

else {
	if ($user->getAccess() > 0) {
		$template_content = $tpl->getContent('home');
		$template_fields  = $tpl->getFields($template_content);

		$tpl->assign([
			'loggedin' => 'loggedin',
			'page'     => $tpl->parse($template_content, $template_fields),
		]);
	}

	else {
		$tpl->assign([
			'loggedin' => '',
			'page'     => '',
		]);
	}

	$template_content  = $tpl->getContent('index');
	$template_fields   = $tpl->getFields($template_content);
	echo $tpl->parse($template_content, $template_fields);
}