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

		// Try to set up SMTP connection if theme configuration has it
		if ($this->contact_config) {
			$this->useThemeSMTP();
		}
	}


	/** ----------------------------------------------------------------------------
	 * Set up SMTP connection based on theme configuration
	 */

	private function useThemeSMTP() {
		if (
			is_array($this->contact_config) &&
			is_array($this->contact_config['smtp'])
		) {
			$smtp = $this->contact_config['smtp'];

			if (!empty($smtp['hostname']) && !empty($smtp['username']) && !empty($smtp['password'])) {
				$this->mailer->isSMTP();
				$this->mailer->SMTPAuth   = true;
				$this->mailer->SMTPSecure = 'tls';
				$this->mailer->Host       = $smtp['hostname'];
				$this->mailer->Username   = $smtp['username'];
				$this->mailer->Password   = $smtp['password'];
				$this->mailer->Port       = $smtp['port'] ?? 587;
			}

			if (!empty($smtp['from'])) {
				$this->mailer->setFrom($smtp['from']);
			}
		}
	}


	/** ----------------------------------------------------------------------------
	 * Prepare HTML table message
	 *
	 * @param Array $fields
	 * @param String $lang
	 */

	public function prepareBodyWithTable($fields, $lang = null) {
		$style_table = 'style="border-collapse:collapse;"';
		$style_td    = 'style="padding: 5px;"';

		$fields['Time'] = date('j-n-Y') . ', ' . date('H:i');
		$fields['Host'] = gethostbyaddr($_SERVER['REMOTE_ADDR']) . ' (' . $_SERVER['REMOTE_ADDR'] . ')';
		if ($lang) {
			$fields['Language'] = $lang;
		}

		$body = "<html><body><table {$style_table}><tbody>";
		foreach($fields as $label => $value) {
			$body .= "<tr><td {$style_td}><strong>{$label}:</strong></td><td {$style_td}>{$value}</td></tr>";
		}
		$body .= '</tbody></table></body></html>';

		return $body;
	}


	/** ----------------------------------------------------------------------------
	 * Send message to user
	 *
	 * @param String $subject
	 * @param String $body
	 * @param String $main_recipient
	 * @param String $reply_to
	 * @param Array $bcc
	 */

	public function notify($subject, $body, $recipient, $reply_to = null, $bcc = []) {
		$this->mailer->addAddress($recipient);

		if ($reply_to) {
			$this->mailer->addReplyTo($reply_to);
		}

		foreach($bcc as $bcc_email) {
			$this->mailer->addBCC($bcc_email);
		}

		// Content
		$this->mailer->Subject = $subject;
		$this->mailer->Body = $body;

		// Send
		$this->mailer->send();
	}
}