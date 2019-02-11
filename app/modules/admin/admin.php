<?php

/**
 * =================================================================================
 *
 * IZU CMS
 * Module: Admin
 *
 * =================================================================================
 */

// Security constant. Needs to be checked in all included files
define('IN_ADMIN', true);

$tpl = new Template();

$tpl->setTheme('admin');
$tpl->assign([
	'site_name'      => Config::$SITE_NAME,
	'site_path'      => $router->site_path,
	'app_path'       => Config::$APP_DIR,
	'theme_path'     => Config::$THEMES_DIR . 'admin',
	'script_version' => VIZU_VERSION,
	'phpversion'     => phpversion(),
]);

$user = new User($db);
$admin = new AdminActions($user, $tpl);

/**
 * PAGE LOADED VIA AJAX
 * Check if request was done asynchronously.
 * If true change the behavior of page to always return JSON data.
 */

if (!Core::isAjaxRequest()) {
	$admin->displayAdminHomePage();
	return;
}

$admin->setAjaxErrorHandler();


/**
 * Start AJAX class and set it up
 */

$ajax = new Ajax();
$show_content = true; // Is there anything that needs to be shown?
$request = $router->getRequestChunk(1) ?? 'home';


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
		case 'passrec':
			$show_content = false;
			if (User::verifyUsername($_POST['email'])) {
				$result = $db->query("SELECT `id`, `email` FROM `users` WHERE `email` = '{$_POST['email']}'");
				$user_data = $db->fetchAll($result);
				if (count($user_data) == 1) {
					$user_notified = false;
					$new_password = User::generatePassword();
					$content_fields = [
						'Message' => "You have requested password recovery to your administration panel. Here is your new password: <strong>{$new_password}</strong>. Please use it to log in and change it as soon as possible.",
						'Page address' => $router->site_path,
					];

					try {
						$notifier = new Notifier($theme_config['contact'] ?? []);
						$notifier->notify(
							'[' . Config::$SITE_NAME . '] Password recovery request', // Subject
							$notifier->prepareBodyWithTable($content_fields, $lang->getActiveLangCode()), // Body
							$user_data[0]['email'] // Recipient
						);
						$user_notified = true;
					}
					catch (Exception $e) {
						$ajax->set('error', [
							'str'  => 'Password recvery process failed - could not send email. Returned error: ' . $e->getMessage(),
							'file' => __FILE__,
							'line' => __LINE__
						]);
					}

					if ($user_notified) {
						$result = $db->query("UPDATE `users` SET `password` = '{$new_password}' WHERE `id` = '{$user_data[0]['email']}' LIMIT 1");
					}
				}
				$ajax->set('message', 'Password recovery process started. We have sent you further informations to your email box.');
			}
			else {
				$ajax->set('error', [
					'str'  => 'Provided email address is not valid.',
					'file' => __FILE__,
					'line' => __LINE__
				]);
			}
			break;
	}
}

/**
 * Display results
 */

if (($user->getAccess() > 0) && ($show_content === true)) {
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

		case 'edit':
			require_once 'admin-edit.php';
			break;


		// CHANGES HISTORY

		case 'history':
			require_once 'admin-history.php';
			break;


		// USER FUNCTIONS

		case 'user':
			require_once 'admin-user.php';
			break;


		// BACKUP OPERATIONS

		case 'backup':
			require_once 'admin-backup.php';
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