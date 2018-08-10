<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / User / Change Password
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module');
}


/**
 * Validate form
 */

$error_msg = null;
$result = $db->query("SELECT `password` FROM `users` WHERE `id` = '" . $user->getId() . "' LIMIT 1");
$user_data = $db->fetch($result);

if (empty($_POST['password_actual'])) {
	$error_msg = 'No current password is provided.';
}
elseif (empty($_POST['password_new1'])) {
	$error_msg = 'No new password is provided.';
}
elseif ($_POST['password_new1'] !== $_POST['password_new2']) {
	$error_msg = 'The new password does not match its repetition.';
}
elseif ($_POST['password_actual'] === $_POST['password_new1']) {
	$error_msg = 'The new password must be different from the old one to be changed.';
}
elseif (strlen($_POST['password_new1']) < (PASSWORD_MIN_CHARS + 1)) {
	$error_msg = 'New password should be at least ' . PASSWORD_MIN_CHARS . ' characters long.';
}
elseif ($user_data[0]['password'] && $user_data[0]['password'] !== $user->passwordEncode($_POST['password_actual'])) {
	$error_msg = 'Provided current password is not correct.';
}

if ($error_msg) {
	$ajax->set('message', $error_msg);
	return;
}


/**
 * Save new password
 */

$new_password = $user->passwordEncode($_POST['password_new1']);
$result = $db->query("UPDATE `users` SET `password` = '" . $new_password . "' WHERE `id` = '" . $user->getId() . "' LIMIT 1");

if ($result) {
	$ajax->set('message', 'Password changes successfully.');
	$_SESSION['password'] = $new_password;
}
else $ajax->set('message', 'Password change process failed.');