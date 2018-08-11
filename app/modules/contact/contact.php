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

$tpl->setTheme(Config::$THEME_NAME);

if (!is_array($theme_config['contact']['fields'])) {
	$ajax
		->set('message', 'Theme configuration file not found or does not contain contact form configuration')
		->send();
}


/**
 * Form validation
 */

$contact_fields_errors = [];

foreach($theme_config['contact']['fields'] as $form_field) {
	if (isset($form_field['required']) && $form_field['required'] == true) {
		switch ($form_field['type']) {

			// Email validation
			case 'email':
				if ($mailer->sanitiseEmail($_POST[$form_field['name']] ?? '') == false) {
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


/**
 * Stop code execution and output error if validations failed
 */

if (count($contact_fields_errors) > 0) {
	$ajax
		->set('success', false)
		->set('error', $lang->_t('mailer-not-sent', 'Message not sent') . '<br>' .  $lang->_t('mailer-form-invalid', 'One or more fields have an error.'))
		->set('form-errors', $contact_fields_errors)
		->send();
}


/**
 * reCAPTCHA validation
 */

if (!empty($theme_config['contact']['recaptcha_secret'])) {
	$curl = new libs\Curl();
	if ($core->isDev()) {
		$curl->disableSsl();
	}

	$recaptcha3 = new libs\Recaptcha3($curl, $mailer, $theme_config['contact']['recaptcha_secret']);
	$token = $mailer->sanitiseString($_POST['recaptcha_token'] ?? '');

	try {
		$recaptcha_result = $recaptcha3->validate($token);
	}
	catch (Exception $e) {
		return $ajax
			->set('success', false)
			->set('error', $lang->_t('mailer-not-sent', 'Message not sent') . '<br>' . $lang->_t('mailer-captcha-error', 'Anti-spam system error.') . ' ' . $e->getMessage())
			->send();
	}

	// Stop code execution if reCAPTCHA validator recognize user as not a human
	if ($recaptcha_result === false) {
		return $ajax
			->set('success', false)
			->set('error', $lang->_t('mailer-not-sent', 'Message not sent') . '<br>' . $lang->_t('mailer-captcha-invalid', 'You have been recognized as spammer.'))
			->send();
	}
}


/**
 * Prepare mail data
 */

if (!$core->isDev()) {
	$mailer->setAntiflood();
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
			$mailer->addBcc($user['email']);
		}
	}
}

if (!$main_recipient) {
	return $ajax
		->set('success', false)
		->set('error', $lang->_t('mailer-not-sent', 'Message not sent') . '<br>' . $lang->_t('mailer-recipient-error', 'Error in form configuration'))
		->send();
}

$mailer
	->addRecipient($main_recipient)
	->setTopic('Contact message', $router->domain)
	->setReplyTo($_POST['email'])

	// Some hosting services require this e-mail to exist on the same
	// hosting account.
	->setFrom($main_recipient);


/**
 * Collect all form data
 */

foreach($theme_config['contact']['fields'] as $form_field) {
	$mailer->addContent($lang->_t($form_field['label']), $_POST[$form_field['name']]);
}


/**
 * Add some meta data
 */

$mailer
	->addContent('Time', date('j-n-Y') . ', ' . date('H:i'))
	->addContent('Host', gethostbyaddr($_SERVER['REMOTE_ADDR']) . ' (' . $_SERVER['REMOTE_ADDR'] . ')')
	->addContent('Language', $lang->get());


/**
 * Try to send prepared message
 */

$sending_error = false;

try {
	$sending_result = $mailer->send();
}
catch (\Exception $e) {
	switch($e->getCode()) {
		case 1:
			$message = $lang->_t('mailer-not-sent', 'Message not sent') . '<br>' . $lang->_t('mailer-error', 'Error occured while sending the message.') . ' ' . $e->getMessage();
			break;

		case 5:
			$message = $lang->_t('mailer-not-sent', 'Message not sent') . '<br>' . $lang->_t('mailer-flood', $e->getMessage());
			break;

		default:
			$message = $e->getMessage();
	}

	$ajax->set('success', false);
	$ajax->set('error', $message);
	$ajax->add('log', 'Error code: ' . $e->getCode());

	$sending_error = true;
}

if (!$sending_error) {
	if ($sending_result === true) {
		$ajax->set('success', true);
		$ajax->set('message', $lang->_t('mailer-sent', 'Message sent'));
	}
	else {
		$ajax->set('success', false);
		$ajax->set('error', $lang->_t('mailer-not-sent', 'Message not sent') . '<br>' . $lang->_t('mailer-error', 'Unknown error occured.'));
	}
}

$ajax->send();