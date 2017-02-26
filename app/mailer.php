<?php

if ($_POST['op'] != "send") die('This file can be loaded only in admin'); // Security check

require_once(Config::APP_DIR . 'libs/mail.php');
require_once(Config::APP_DIR . 'libs/ajax.php');

$mail = new Mail();
$ajax = new Ajax();

// Validate sended form

$inputs_required = array('topic', 'message');
$inputs_with_errors = array();

foreach($inputs_required as $input_name) {
	if (empty($_POST[$input_name])) {
		array_push($inputs_with_errors, array('inputName' => $input_name, 'errorMessage' => 'To pole jest wymagane'));
	}
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) array_push($inputs_with_errors, array('inputName' => 'email', 'errorMessage' => 'Podano niepoprawny adres email'));


// If errors occurred

if (count($inputs_with_errors) > 0) {
	$ajax->set('formErrors', $inputs_with_errors);
}


// Flood blockade

elseif (isset($_SESSION['email_sended']) && (date('U') - $_SESSION['email_sended']) < 3600) {
	$ajax->set('message', '<strong>Wiadomość nie została wysłana</strong><br>Nie możesz tak często przesyłać wiadomości');
}


// Try to send email

else {

	$main_recipient = false;

	$result = $db->query("SELECT `id`, `email` FROM `users`");
	$result = $db->fetch($result);

	if (count($result) > 0) {
		foreach($result as $user) {
			if ($user['id'] == Config::CONTACT_USER) {
				$main_recipient = $user['email'];
				if (Config::CONTACT_ALL !== true) break;
			}
			elseif (Config::CONTACT_ALL === true) $mail->add_bcc($user['email']);
		}
	}

	if ($main_recipient) {

		$mail->add_recipient($main_recipient);
		$mail->set_topic('[' . $router->domain . '] ' . ucfirst($_POST['topic']));
		$mail->set_reply_to($_POST['email']);
		$mail->set_from($main_recipient); // Need to be an e-mail that exists on hosting account becouse some services only send it like that

		// Prepare mail

		$list_data_arr = array(
			'Temat'	=> trim(strip_tags($_POST['topic'])),
			'Firma'	=> trim(strip_tags($_POST['company'])),
			'Autor'	=> trim(strip_tags($_POST['name'])),
			'Email'	=> trim(strip_tags($_POST['email'])),
			'Kiedy'	=> date("j-n-Y") . " o godzinie " . date ("H:i"),
			'Host'	=> gethostbyaddr($_SERVER['REMOTE_ADDR']) . ' (' . $_SERVER['REMOTE_ADDR'] . ')',
			'Język'	=> LANG_CODE
		);
		foreach($list_data_arr as $name => $val) {
			$mail->add_list_data($name, $val);
		}

		// Send mail

		$result = $mail->send($_POST['message']);

		if ($result === true) {
			$ajax->set('message', '<strong>Wiadomość została wysłana</strong><br>Dziękujemy za kontakt!<br />Postaramy się odpowiedzieć na nią jak najszybciej.');
			$_SESSION['email_sended'] = date('U');
		}
		else $ajax->set('message', $result);
	}

	else $ajax->set('message', '<strong>Wiadomość nie została wysłana</strong><br>Brak skonfigurowanego poprawnie odbiorcy.');

}

$ajax->send();