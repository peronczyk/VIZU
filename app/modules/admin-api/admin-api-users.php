<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin API / User
 *
 * =================================================================================
 */

if (IN_ADMIN_API !== true) {
	die('This file can be loaded only in admin module');
}

switch ($router->getRequestChunk(2)) {
	case 'list':
		$admin_actions->requireAdminAccessRights();

		$query = $db->query('SELECT * FROM `users`');
		$result = $db->fetchAll($query);
		$users_list = [];

		foreach($result as $user_data) {
			$users_list[] = [
				'id' => $user_data['id'],
				'email' => $user_data['email']
			];
		}

		$rest_store->set('users-list', $users_list);

		break;


		/** ----------------------------------------------------------------------------
		 * Add user
		 */

		case 'add':
			$admin_actions->requireAdminAccessRights();

			$email = $_POST['email'] ?? null;
			$contact_config = $theme_config['contact'];

			// Validate entered email address
			if (empty($email) || !User::verifyUsername($email)) {
				$rest_store->set('message', 'Provided email address is missing or incorrect');
				break;
			}

			// Check if email address already exists
			$result = $db->query("SELECT * FROM `users` WHERE `email` = '{$email}'");
			$user_found = $db->fetchAll($result);
			if ($user_found) {
				$rest_store->set('message', 'Account with provided email address already exists');
				break;
			}


			// Set sender email address as theme contact form main recipient
			if ($contact_config['default_recipient']) {
				$user_id = $contact_config['default_recipient'];

				// Get email addres of contact user that was set in configuration
				$result  = $db->query("SELECT `email` FROM `users` WHERE `id` = '{$user_id}'");
				$fetched = $db->fetchAll($result);

				if (!$fetched) {
					$rest_store->set('message', "Configured default sender/receiver '{$user_id}' does not exist. Admin acount creation failed.");
					break;
				}
				$contact_user_email = $fetched[0]['email'];
			}

			$generated_password = User::generatePassword();
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
					$notifier->prepareBodyWithTable($content_fields, $lang->getActiveLangCode()), // Body
					$email // Recipient
				);
			}
			catch (\Exception $e) {
				$rest_store->set('message', "Failed to send account creation notification. Error thrown: '{$e->getMessage()}'");
				break;
			}

			// Add user to database
			$query = $db->query("INSERT INTO `users` VALUES ('', '{$email}', '{$user->password_encode($generated_password)}')");

			$rest_store->set('message', "Administrator account with email address {$email} has been created.");

			break;


	case 'password-change':
		$admin_actions->requireAdminAccessRights();

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
			$rest_store->set('message', $error_msg);
			return;
		}


		// Check if entered actual password is correct

		$result = $db->query("SELECT `password` FROM `users` WHERE `id` = '{$user->get_id()}' LIMIT 1");
		$user_data = $db->fetchAll($result);

		if (isset($user_data[0]['password']) && $user_data[0]['password'] !== User::passwordEncode($_POST['password_actual'])) {
			$rest_store->set('message', 'Provided current password is not correct');
			return;
		}


		// Save new password

		$new_password = User::passwordEncode($_POST['password_new1']);
		$result = $db->query("UPDATE `users` SET `password` = '{$new_password}' WHERE `id` = '{$user->get_id()}' LIMIT 1");

		if ($result) {
			$rest_store->set('message', 'Password changed');
			$_SESSION['password'] = $new_password;
		}
		else {
			$rest_store->set('message', 'Password change failed');
		}

		break;


	/** ----------------------------------------------------------------------------
	 * Password recovery
	 */

	case 'password-recovery':
		$admin_actions->requireAdminAccessRights();

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
					$rest_store->set('error', [
						'str'  => 'Password recvery process failed - could not send email. Returned error: ' . $e->getMessage(),
						'file' => __FILE__,
						'line' => __LINE__
					]);
				}

				if ($user_notified) {
					$result = $db->query("UPDATE `users` SET `password` = '{$new_password}' WHERE `id` = '{$user_data[0]['email']}' LIMIT 1");
				}
			}
			$rest_store->set('message', 'Password recovery process started. We have sent you further informations to your email box.');
		}
		else {
			$rest_store->set('error', [
				'str'  => 'Provided email address is not valid.',
				'file' => __FILE__,
				'line' => __LINE__
			]);
		}
		break;
}