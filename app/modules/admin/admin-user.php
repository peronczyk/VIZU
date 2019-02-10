<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin / User
 *
 * =================================================================================
 */

if (IN_ADMIN !== true) die('This file can be loaded only in admin module');


switch($router->request[count($router->request) - 1]) {

	/** ----------------------------------------------------------------------------
	 * Password change operation
	 */

	case 'change_password';

		// Form validation

		$error_msg = null;

		if (empty($_POST['password_actual'])) {
			$error_msg = 'Current password not provided';
		}
		elseif (empty($_POST['password_new1'])) {
			$error_msg = 'New password not provided';
		}
		elseif ($_POST['password_new1'] !== $_POST['password_new2']) {
			$error_msg = 'New passwords does not match';
		}
		elseif ($_POST['password_actual'] === $_POST['password_new1']) {
			$error_msg = 'New password should be different than previous one';
		}
		elseif (strlen($_POST['password_new1']) < 5) {
			$error_msg = 'New password should have at least 5 characters';
		}

		if ($error_msg) {
			$ajax->set('message', $error_msg);
			return;
		}


		// Check if entered actual password is correct

		$result = $db->query("SELECT `password` FROM `users` WHERE `id` = '{$user->get_id()}' LIMIT 1");
		$user_data = $db->fetchAll($result);

		if ($user_data[0]['password'] && $user_data[0]['password'] !== $user->passwordEncode($_POST['password_actual'])) {
			$ajax->set('message', 'Provided current password is not correct');
			return;
		}


		// Save new password

		$new_password = $user->passwordEncode($_POST['password_new1']);
		$result = $db->query("UPDATE `users` SET `password` = '{$new_password}' WHERE `id` = '{$user->get_id()}' LIMIT 1");

		if ($result) {
			$ajax->set('message', 'Password changed');
			$_SESSION['password'] = $new_password;
		}
		else {
			$ajax->set('message', 'Password change failed');
		}

		break;


	/** ----------------------------------------------------------------------------
	 * Add user
	 */

	case 'user_add':
		$email = $_POST['email'] ?? null;
		$contact_config = $theme_config['contact'];

		// Validate entered email address
		if (empty($email) || !$user->verifyUsername($email)) {
			$ajax->set('message', 'Provided email address is missing or incorrect');
			break;
		}

		// Check if email address already exists
		$result = $db->query("SELECT * FROM `users` WHERE `email` = '{$email}'");
		$user_found = $db->fetchAll($result);
		if ($user_found) {
			$ajax->set('message', 'Account with provided email address already exists');
			break;
		}


		// Set sender email address as theme contact form main recipient
		if ($contact_config['default_recipient']) {
			$user_id = $contact_config['default_recipient'];

			// Get email addres of contact user that was set in configuration
			$result  = $db->query("SELECT `email` FROM `users` WHERE `id` = '{$user_id}'");
			$fetched = $db->fetchAll($result);

			if (!$fetched) {
				$ajax->set('message', "Configured default sender/receiver '{$user_id}' does not exist. Admin acount creation failed.");
				break;
			}
			$contact_user_email = $fetched[0]['email'];
		}

		$generated_password = $user->generatePassword();
		$content_fields = [
			'Message'      => 'Administrator account created. It is strongly recomended to change your password now.',
			'Page address' => $router->site_path,
			'Login'        => $email,
			'Password'     => $generated_password
		];

		// Send notification to user about account creation
		try {
			$notifier = new Notifier($contact_config);
			$notifier->notify(
				"[{$router->domain}] Account registration", // Subject
				$notifier->prepareBodyWithTable($content_fields, $lang->get()), // Body
				$email // Recipient
			);
		}
		catch (\Exception $e) {
			$ajax->set('message', "Failed to send account creation notification. Error thrown: '{$e->getMessage()}'");
			break;
		}

		// Add user to database
		$query = $db->query("INSERT INTO `users` VALUES ('', '{$email}', '{$user->password_encode($generated_password)}')");

		$ajax->set('message', "Administrator account with email address {$email} has been created.");
		break;


	/** ----------------------------------------------------------------------------
	 * Display page
	 */

	default:
		$query  = $db->query('SELECT * FROM `users`');
		$result = $db->fetchAll($query);
		$users_list = '';

		foreach($result as $user_data) {
			$users_list .= "<li>{$user_data['email']}</li>";
		}

		$tpl->assign([
			'user_email' => $user->getEmail(),
			'users_list' => $users_list,
		]);

		$template_content = $tpl->getContent('user');
		$template_fields  = $tpl->getFields($template_content);

		$ajax->set('html', $tpl->parse($template_content, $template_fields));
}