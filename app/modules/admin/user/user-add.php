<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / User / Add
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module');
}

$mailer = new libs\Mailer();
$notify_result = false;

// Validate entered email address
if (empty($_POST['email']) || !$user->verifyUsername($_POST['email'])) {
	$ajax->set('message', 'Provided email address is missing or invalid.');
	return;
}

// Check if email address already exists
$result = $db->query('SELECT * FROM `users` WHERE `email` = "' . $_POST['email'] . '"');
$user_found = $db->fetch($result);
if ($user_found) {
	$ajax->set('message', 'Provided email address already exists in the database.');
	return;
}

$generated_password = $user->generatePassword();

// Add user to database
$registration_result = $db->query('INSERT INTO `users` VALUES ("", "' . $_POST['email'] . '", "' . $user->passwordEncode($generated_password) . '")');

// Set sender email address as theme contact form main recipient
if ($theme_config['contact']['default_recipient']) {
	$user_id = $theme_config['contact']['default_recipient'];

	// Get email addres of contact user that was set in configuration
	$result  = $db->query('SELECT `email` FROM `users` WHERE `id` = "' . $user_id . '"');
	$fetched = $db->fetch($result);

	if (!$fetched) {
		$ajax->set('message', 'Configured default sender/recipient [' . $user_id . '] does not exist in database. Administrator account has not been created');
		return;
	}
	$contact_user_email = $fetched[0]['email'];
	$mailer->setFrom($contact_user_email);
}

try {
	$notify_result = $mailer
		->setTopic('Account registration')
		->addRecipient($_POST['email'])
		->addContent('Message', 'Your administrator account has been created. You can find your login informations bellow. We strongly advice you to change your password right after you log in to administration panel.')
		->addContent('Page address', $router->site_path)
		->addContent('Login', $_POST['email'])
		->addContent('Password', $generated_password)
		->send();
}
catch (\Exception $e) {
	$notify_error = $e->getMessage();
}

if ($registration_result === true) {
	if ($notify_result === true) {
		$ajax->set('message', 'Administrator account created successfully: ' . $_POST['email']);
	}
	else {
		$ajax->set('message', 'Administrator account created successfully but the email notification could not be sent.' . (($notify_error) ? ' Returned error: ' . $notify_error : ''));
	}
}
else {
	$ajax->set('message', 'Administrator account creation failed. Query error: ' . $db->getConn()->error);
}