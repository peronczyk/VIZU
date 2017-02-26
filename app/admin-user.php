<?php

# ==================================================================================
# PASSWORD CHANGE OPERATION
# ==================================================================================


if ($router->request[count($router->request) - 1] == 'change_password') {

	// Form validation

	$error_msg = null;

	if (empty($_POST['password_actual']))							$error_msg = 'Nie podano aktualnego hasła';
	elseif (empty($_POST['password_new1']))							$error_msg = 'Nie podano nowego hasła';
	elseif ($_POST['password_new1'] != $_POST['password_new2'])		$error_msg = 'Nowe hasło nie zgadza się z jego powtórzeniem';
	elseif ($_POST['password_actual'] == $_POST['password_new1']) 	$error_msg = 'Nowe hasło musi różnić się od starego aby zostało zmienione';
	elseif (strlen($_POST['password_new1']) < 6) 					$error_msg = 'Nowe hasło powinno mieć conajmniej 5 znaków';

	if ($error_msg) {
		$ajax->set('message', $error_msg);
		return;
	}


	// Check if entered actual password is correct

	$result = $db->query("SELECT `password` FROM `users` WHERE `id` = '" . $user->get_id() . "' LIMIT 1");
	$user_data = $db->fetch($result);

	if ($user_data[0]['password'] && $user_data[0]['password'] != User::password_encode($_POST['password_actual'])) {
		$ajax->set('message', 'Podane aktualne hasło nie jest poprawne');
		return;
	}


	// Save new password

	$new_password = User::password_encode($_POST['password_new1']);
	$result = $db->query("UPDATE `users` SET `password` = '" . $new_password . "' WHERE `id` = '" . $user->get_id() . "' LIMIT 1");

	if ($result) {
		$ajax->set('message', 'Pomyślnie zmieniono hasło');
		$_SESSION['password'] = $new_password;
	}
	else $ajax->set('message', 'Nie udało się zmienić hasła');
}



# ==================================================================================
# DISPLAY PAGE
# ==================================================================================


else {
	$template_content	= $tpl->get_content('user');
	$template_fields	= $tpl->get_fields($template_content);

	$ajax->set('html', $tpl->parse($template_content, $template_fields));
}