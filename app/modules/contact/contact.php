<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Mail
 *
 * =================================================================================
 */

$contact_config = $theme_config['contact'];

if (!is_array($contact_config)) {
	$ajax
		->set('message', 'Theme configuration file not found or does not contain contact form configuration')
		->send();
}

if (!is_array($contact_config['fields'])) {
	$ajax
		->set('message', 'Theme contact configuration does not contain fields setup')
		->send();
}


$ajax = new Ajax();
$tpl  = new Template();

$tpl->setTheme(Config::$THEMES_DIR . Config::$THEME_NAME);


/**
 * Form validation
 */

$contact_fields_errors = [];

foreach ($contact_config['fields'] as $form_field) {
	if (isset($form_field['required']) && $form_field['required']) {
		switch ($form_field['type']) {

			// Email validation
			case 'email':
				if (empty($_POST[$form_field['name']]) || !filter_var($_POST[$form_field['name']], FILTER_VALIDATE_EMAIL)) {
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
		->set('error', $lang->_t('mailer-not-sent', 'Message not sent') . '<br>' . $lang->_t('mailer-form-invalid', 'One or more fields have an error.'))
		->set('form-errors', $contact_fields_errors)
		->send();
}


/**
 * reCAPTCHA validation
 */

if (!empty($contact_config['recaptcha_secret'])) {
	$curl = new libs\Curl();
	if ($core->isDev()) {
		$curl->disableSsl();
	}

	$recaptcha3 = new Recaptcha3($curl, $contact_config['recaptcha_secret']);
	$token = preg_replace('/\r|\n/', '', htmlentities(trim($_POST['recaptcha_token']), ENT_QUOTES));

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
 * Get message recipient
 */

$bcc = [];
$main_recipient = null;

$result = $db->query('SELECT `id`, `email` FROM `users`');
$users  = $db->fetchAll($result);

foreach($users as $user) {
	if ($user['id'] == $contact_config['default_recipient']) {
		$main_recipient = $user['email'];
		if ($contact_config['inform_all'] !== true) {
			break;
		}
	}
	elseif ($contact_config['inform_all'] === true) {
		$bcc[] = $user['email'];
	}
}

if (!$main_recipient) {
	$ajax
		->set('message', $lang->_t('mailer-recipient-error', 'Message recipient not configured'))
		->send();
}

if (count($contact_fields_errors) > 0) {
	$ajax
		->set('form-errors', $contact_fields_errors)
		->send();
}


/**
 * Prepare message body. There is no need to validate POST elements because
 * PHPMailer has its own validation.
 */

$content_fields = [];
foreach ($contact_config['fields'] as $form_field) {
	$content_fields[$lang->_t($form_field['label'])] = $_POST[$form_field['name']];
}


/**
 * Notify user
 */

try {
	$notifier = new Notifier($contact_config);
	$notifier->notify(
		"[{$router->domain}] Contact message", // Subject
		$notifier->prepareBodyWithTable($content_fields, $lang->getActiveLangCode()), // Body
		$main_recipient, // Recipient
		$_POST['email'], // Reply to
		$bcc // BCC
	);

	$result_message = $lang->_t('mailer-sent', 'Message sent');
}
catch (Exception $e) {
	$result_message = $lang->_t('mailer-error', 'Error while sending message:') . ' ' . $e->getMessage();
}


/**
 * Output AJAX response
 */

$ajax
	->set('message', $result_message)
	->send();