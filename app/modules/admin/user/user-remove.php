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


$remove_user_id = (int) $router->getRequestPart(3);

if ($user->getId() == $remove_user_id) {
	$ajax->set('error', 'Could not remove user that is actually logged in');
	return;
}

$result = $db->query("DELETE FROM `users` WHERE `id` = '" . $remove_user_id . "' LIMIT 1");

if ($result) {
	$ajax->set('message', 'User with ID ' . $remove_user_id . ' removed');
}
else {
	$ajax->set('error', 'Error occured while trying to remove user with ID ' . $remove_user_id);
}