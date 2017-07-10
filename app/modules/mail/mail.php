<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Mail
#
# ==================================================================================

if ($_POST['op'] !== 'send') die(); // Security check

$ajax = new libs\Ajax();

// Validate sended form

$inputs_required = Config::$CONTACT_REQUIRED_INPUTS;
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

	$mail = new libs\Mailer();

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

		try {
			$result = $mail
				->add_recipient($main_recipient)
				->set_topic('[' . $router->domain . '] ' . ucfirst($_POST['topic']))
				->set_reply_to($_POST['email'])

				// Need to be an e-mail that exists on hosting account becouse some
				// services only send it like that
				->set_from($main_recipient)

				// Add sender data
				->add_list_data('Temat', trim(strip_tags($_POST['topic'])))
				->add_list_data('Firma', trim(strip_tags($_POST['company'])))
				->add_list_data('Autor', trim(strip_tags($_POST['name'])))
				->add_list_data('Email', trim(strip_tags($_POST['email'])))
				->add_list_data('Kiedy', date('j-n-Y') . " o godzinie " . date('H:i'))
				->add_list_data('Host', gethostbyaddr($_SERVER['REMOTE_ADDR']) . ' (' . $_SERVER['REMOTE_ADDR'] . ')')
				->add_list_data('Język', LANG_CODE)

				// Send email
				->send($_POST['message']);
		}
		catch (\Exception $e) {
			$ajax->set('message', $result);
		}

		if ($result === true) {
			$ajax->set('message', $lang->_t('mailer-sent', 'Message sent'));
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