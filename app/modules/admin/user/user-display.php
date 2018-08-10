<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / User / Home
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module');
}


$query = $db->query('SELECT * FROM `users`');
$result = $db->fetch($query);
$users_list = '';

foreach($result as $user_data) {
	$users_list .= '<tr><td>' . $user_data['id'] . '</td><td>' . $user_data['email'] . '</td></tr>';
}

$tpl->assign([
	'user_email' => $user->getEmail(),
	'users_list' => $users_list,
]);

$template_content = $tpl->getContent('user');
$template_fields  = $tpl->getFields($template_content);

$ajax->set('html', $tpl->parse($template_content, $template_fields));