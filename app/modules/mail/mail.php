<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Mail
#
# ==================================================================================

if ($_POST['op'] !== "send") die(); // Security check

$mail = new libs\Mailer();
$ajax = new libs\Ajax();

// Validate sended form

$inputs_required = Config::$CF_REQUIRED_INPUTS;
$inputs_with_errors = array();

foreach($inputs_required as $input_name) {
	if (!isset($_POST[$input_name]) || strlen($_POST[$input_name]) < 3) {
		array_push($inputs_with_errors, array(
			'inputName' => $input_name,
			'errorMessage' => $lang->_t('mailer-field-required', 'This field is required')
		));
	}
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	array_push($inputs_with_errors, array(
		'inputName' => 'email',
		'errorMessage' => $lang->_t('mailer-email-wrong', 'Incorrect email address')
	));
}


// If errors occurred

if (count($inputs_with_errors) > 0) {
	$ajax->set('formErrors', $inputs_with_errors);
}


// Flood blockade

elseif (isset($_SESSION['email_sended']) && (date('U') - $_SESSION['email_sended']) < 3600) {
	$ajax->set('message',
		$lang->_t('mailer-flood', 'You can not send messages so often')
	);
}


// Try to send email

else {

	$main_recipient = false;

	$result = $db->query("SELECT `id`, `email` FROM `users`");
	$result = $db->fetch($result);

	if (count($result) > 0) {
		foreach($result as $user) {
			if ($user['id'] == Config::$CONTACT_USER) {
				$main_recipient = $user['email'];
				if (Config::$CONTACT_ALL !== true) break;
			}
			elseif (Config::$CONTACT_ALL === true) $mail->add_bcc($user['email']);
		}
	}

	if ($main_recipient) {

		$mail->add_recipient($main_recipient);
		$mail->set_topic('[' . $router->domain . '] ' . ucfirst($_POST['topic']));
		$mail->set_reply_to($_POST['email']);

		// Need to be an e-mail that exists on hosting account becouse some services only send it like that
		$mail->set_from($main_recipient);

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
			$ajax->set('message',
				$lang->_t('mailer-sent', 'Message sent')
			);
			$_SESSION['email_sended'] = date('U');
		}
		else $ajax->set('message', $result);
	}

	else {
		$ajax->set('message',
			$lang->_t('mailer-recipient-error', 'Message recipient not configured')
		);
	}

}

$ajax->send();