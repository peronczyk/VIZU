<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Mail
#
# ==================================================================================

if ($_POST['op'] !== 'send') {
	die('Module unavailable the way you accessed it'); // Security check
}

$ajax = new libs\Ajax();

$tpl = new libs\Template();
$tpl->set_theme(Config::$THEME_NAME);
$theme_config = $tpl->get_theme_config();


/**
 * Form validation
 */

if ($theme_config['contact']['fields']) {
	foreach ($theme_config['contact']['fields'] as $form_field) {
		if ($form_field['required']) && () {

		}
	}
}

$inputs_required = Config::$CONTACT_REQUIRED_INPUTS;
$inputs_with_errors = [];

foreach($inputs_required as $input_name) {
	if (!isset($_POST[$input_name]) || strlen($_POST[$input_name]) < 3) {
		array_push($inputs_with_errors, [
			'inputName' => $input_name,
			'errorMessage' => $lang->_t('mailer-field-required', 'This field is required')
		]);
	}
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	array_push($inputs_with_errors, [
		'inputName' => 'email',
		'errorMessage' => $lang->_t('mailer-email-wrong', 'Incorrect email address')
	]);
}

// If errors occurred
if (count($inputs_with_errors) > 0) {
	$ajax->set('formErrors', $inputs_with_errors);
}


/**
 * Message sending
 */

else {
	$mail = new libs\Mailer();

	if (!$core->is_dev()) $mail->antiflood();

	$sending_error = false;
	$main_recipient = null;
	$topic = $_POST['topic'] ? ucfirst(trim(preg_replace('/\s\s+/', ' ', strip_tags($_POST['topic'])))) : 'Contact message';

	$result = $db->query('SELECT `id`, `email` FROM `users`');
	$users = $db->fetch($result);

	if (count($users) > 0) {
		foreach($users as $user) {
			if ($user['id'] == Config::$CONTACT_USER) {
				$main_recipient = $user['email'];
				if (Config::$CONTACT_ALL !== true) break;
			}
			elseif (Config::$CONTACT_ALL === true) {
				$mail->add_bcc($user['email']);
			}
		}
	}

	if ($main_recipient) {
		try {
			$sending_result = $mail
				->add_recipient($main_recipient)
				->set_topic(($_POST['topic'] ? $_POST['topic'] : 'Contact message'), $router->domain)
				->set_reply_to($_POST['email'])

				// Some hosting services require this e-mail to exist on the same
				// hosting account.
				->set_from($main_recipient)

				// Add sender data
				->add_list_data('Company', $_POST['company'])
				->add_list_data('Name', $_POST['name'])
				->add_list_data('Email', $_POST['email'])
				->add_list_data('Time', date('j-n-Y') . ', ' . date('H:i'))
				->add_list_data('Host', gethostbyaddr($_SERVER['REMOTE_ADDR']) . ' (' . $_SERVER['REMOTE_ADDR'] . ')')
				->add_list_data('Language', $lang->get())

				// Send email
				->send($_POST['message']);
		}
		catch (\Exception $e) {
			switch($e->getCode()) {
				case 1:
					$message = $lang->_t('mailer-error', 'Error while sending message:') . ' ' . $e->getMessage();
					break;

				case 5:
					$message = $lang->_t('mailer-flood', $e->getMessage());
					break;

				default:
					$message = $e->getMessage();
			}
			$ajax->set('message', $message);
			$ajax->add('log', 'Error code: ' . $e->getCode());

			$sending_error = true;
		}

		if (!$sending_error) {
			if ($sending_result === true) {
				$ajax->set('message', $lang->_t('mailer-sent', 'Message sent'));
			}
			else {
				$ajax->set('message', $lang->_t('mailer-not-sent', 'Unknown error'));
			}
		}
	}

	else {
		$ajax->set('message', $lang->_t('mailer-recipient-error', 'Message recipient not configured'));
	}
}

$ajax->send();