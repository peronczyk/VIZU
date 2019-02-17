<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Lib: VizuMailer
 *
 * ---------------------------------------------------------------------------------
 *
 * This class handles sending messages to users via email
 * by using PHPMailer library.
 *
 * =================================================================================
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Notifier {

	private $mailer;
	private $contact_config;


	/** ----------------------------------------------------------------------------
	 * Constructor
	 *
	 * @param Array $contact_config
	 */

	public function __construct($contact_config = null) {
		// Run PHPMailer with exceptions turned on
		$this->mailer = new PHPMailer(true);

		$this->mailer->CharSet = PHPMailer::CHARSET_UTF8;
		$this->mailer->isHTML(true);

		$this->contact_config = $contact_config;

		if (is_array($this->contact_config)) {
			// Try to set up SMTP connection if theme configuration has it
			if (isset($this->contact_config['smtp'])) {
				$this->useThemeSMTP($this->contact_config['smtp']);
			}

			// Set up "from"
			if (!empty($this->contact_config['from'])) {
				$this->mailer->setFrom($this->contact_config['from']);
			}
		}
	}


	/** ----------------------------------------------------------------------------
	 * Set up SMTP connection based on theme configuration
	 */

	private function useThemeSMTP(array $smtp) {
		if (!empty($smtp['hostname']) && !empty($smtp['username']) && !empty($smtp['password'])) {
			$this->mailer->isSMTP();
			$this->mailer->SMTPAuth   = true;
			$this->mailer->SMTPSecure = 'tls';
			$this->mailer->Host       = $smtp['hostname'];
			$this->mailer->Username   = $smtp['username'];
			$this->mailer->Password   = $smtp['password'];
			$this->mailer->Port       = $smtp['port'] ?? 587;
		}
	}


	/** ----------------------------------------------------------------------------
	 * Prepare HTML table message
	 *
	 * @param Array $fields
	 * @param String $lang
	 */

	public function prepareBodyWithTable(array $fields, $lang = null) {
		$style_table   = 'style="border-collapse:collapse;"';
		$style_caption = 'style="padding: 10px 0 5px 0;"';
		$style_content = 'style="padding-bottom: 10px; border-bottom: 1px solid #dedede;"';

		$fields['Time'] = date('j-n-Y') . ', ' . date('H:i');
		$fields['Host'] = gethostbyaddr($_SERVER['REMOTE_ADDR']) . ' (' . $_SERVER['REMOTE_ADDR'] . ')';
		if ($lang) {
			$fields['Language'] = $lang;
		}

		$body = "<html><body><table {$style_table}><tbody>";
		foreach ($fields as $label => $value) {
			$body .= "<tr><td {$style_caption}><strong>{$label}:</strong></td></tr><tr><td {$style_content}>{$value}</td></tr>";
		}
		$body .= '</tbody></table></body></html>';

		return $body;
	}


	/** ----------------------------------------------------------------------------
	 * Send message to user
	 *
	 * @param String $subject
	 * @param String $body
	 * @param String $recipient
	 * @param String $reply_to
	 * @param Array $bcc
	 */

	public function notify(string $subject, string $body, string $recipient, string $reply_to = null, array $bcc = []) {
		$this->mailer->addAddress($recipient);

		if ($reply_to) {
			$this->mailer->addReplyTo($reply_to);
		}

		foreach ($bcc as $bcc_email) {
			$this->mailer->addBCC($bcc_email);
		}

		// Content
		$this->mailer->Subject = $subject;
		$this->mailer->Body = $body;

		// Send
		$this->mailer->send();
	}
}