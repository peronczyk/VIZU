<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Mail
#
# ==================================================================================

$ajax   = new libs\Ajax();
$mailer = new libs\Mailer();
$tpl    = new libs\Template();

$tpl->set_theme(Config::$THEME_NAME);


if (!is_array($theme_config['contact']['fields'])) {
	$ajax
		->set('message', 'Theme configuration file not found or does not contain contact form configuration')
		->send();
}


/**
 * Form validation
 */

$contact_fields_errors = [];

foreach ($theme_config['contact']['fields'] as $form_field) {
	if ($form_field['required']) {
		switch ($form_field['type']) {

			// Email validation
			case 'email':
				if ($mailer->sanitise_email($_POST[$form_field['name']]) == false) {
					array_push($contact_fields_errors, [
						'input-name'    => $form_field['name'],
						'error-message' => $lang->_t('mailer-email-wrong', 'Incorrect email address')
					]);
				}
				break;

			// All kinds of text fields
			default:
				if (empty($_POST[$form_field['name']]) || strlen($_POST[$form_field['name']]) < 3) {
					array_push($contact_fields_errors, [
						'input-name'    => $form_field['name'],
						'error-message' => $lang->_t('mailer-field-required', 'This field is required')
					]);
				}
		}
	}
}

if (count($contact_fields_errors) > 0) {
	$ajax
		->set('form-errors', $contact_fields_errors)
		->send();
}


/**
 * Prepare mail data
 */


if (!$core->is_dev()) {
	$mailer->set_antiflood();
}

$main_recipient = null;

$result = $db->query('SELECT `id`, `email` FROM `users`');
$users  = $db->fetch($result);

if (count($users) > 0) {
	foreach($users as $user) {
		if ($user['id'] == $theme_config['contact']['default_recipient']) {
			$main_recipient = $user['email'];
			if ($theme_config['contact']['inform_all'] !== true) break;
		}
		elseif ($theme_config['contact']['inform_all'] === true) {
			$mailer->add_bcc($user['email']);
		}
	}
}

if (!$main_recipient) {
	$ajax
		->set('message', $lang->_t('mailer-recipient-error', 'Message recipient not configured'))
		->send();
}

$mailer
	->add_recipient($main_recipient)
	->set_topic('Contact message', $router->domain)
	->set_reply_to($_POST['email'])

	// Some hosting services require this e-mail to exist on the same
	// hosting account.
	->set_from($main_recipient);


/**
 * Collect all form data
 */

foreach ($theme_config['contact']['fields'] as $form_field) {
	$mailer->add_content($lang->_t($form_field['label']), $_POST[$form_field['name']]);
}


/**
 * Add some meta data
 */

$mailer
	->add_content('Time', date('j-n-Y') . ', ' . date('H:i'))
	->add_content('Host', gethostbyaddr($_SERVER['REMOTE_ADDR']) . ' (' . $_SERVER['REMOTE_ADDR'] . ')')
	->add_content('Language', $lang->get());


/**
 * Try to send prepared message
 */

$sending_error = false;

try {
	// Send email
	$sending_result = $mailer->send();
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

$ajax->send();