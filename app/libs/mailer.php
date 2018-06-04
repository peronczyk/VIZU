<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Mailer
#
# ==================================================================================

namespace libs;

class Mailer {

	// Headers newline. Should be wrapped in double quotes.
	const NL = "\r\n";

	const ERR_SERVER = 1;
	const ERR_NO_TOPIC = 3;
	const ERR_NO_RECIPIENTS = 4;
	const ERR_ANTIFLOOD = 5;

	private $recipients = [];
	private $content = [];
	private $cc = [];
	private $bcc = [];
	private $reply_to;
	private $from;
	private $topic;
	private $antiflood = false;


	/**
	 * Sanitise email
	 */

	public function sanitise_email($email) {
		return filter_var($email, FILTER_SANITIZE_EMAIL);
	}


	/**
	 * Sanitise string
	 */

	public function sanitise_string($str) {
		return preg_replace('/\r|\n/', '', htmlentities(trim($str), ENT_QUOTES));
	}


	/**
	 * Sanitise block of text (message)
	 */

	public function sanitise_text($text) {
		return preg_replace('/\r|\n/', '', nl2br(htmlentities(trim($text), ENT_QUOTES)));
	}


	/**
	 * SETTER : Topic
	 *
	 * @param string $topic
	 * @param string $domain
	 */

	public function set_topic($topic, $domain = null) {
		$topic = $this->sanitise_string($topic);
		if ($domain) $topic = '[' . $domain . '] ' . $topic;
		$this->topic = $topic;
		return $this;
	}


	/**
	 * SETTER : Add recipient
	 */

	public function add_recipient($email, $name = '') {
		$this->recipients[] = [
			'email' => $this->sanitise_email($email),
			'name'  => $this->sanitise_string($name)
		];
		return $this;
	}


	/**
	 * SETTER : Add BCC email
	 */

	public function add_bcc($email, $name = '') {
		$this->bcc[] = [
			'email' => $this->sanitise_email($email),
			'name'  => $this->sanitise_string($name)
		];
		return $this;
	}


	/**
	 * SETTER : Add list data
	 * Data presented as list after the message content.
	 *
	 * @param string $name
	 * @param string $value
	 */

	public function add_content($name, $value) {
		$this->content[] = [
			'name'  => $name,
			'value' => $this->sanitise_text($value)
		];
		return $this;
	}


	/**
	 * SETTER : "reply-to" header
	 */

	public function set_reply_to($email) {
		$this->reply_to = $this->sanitise_email($email);
		return $this;
	}


	/**
	 * SETTER : "from" header
	 */

	public function set_from($email) {
		$this->from = $this->sanitise_email($email);
		return $this;
	}


	/**
	 * HELPER : Emails to string
	 * Converts array that contains email addresses in to string that can be set
	 * as header.
	 *
	 * @param array $emails
	 */

	private function emails2string($emails) {
		$str = '';
		if (is_array($emails)) {
			$num = count($emails);
			for($i = 0; $i < $num; $i++) {
				if (empty($emails[$i]['name'])) {
					$str .= $emails[$i]['email'];
				}
				else $str .= $emails[$i]['name'] . '<' . $emails[$i]['email'] . '>';

				if (($i + 1) < $num) $str .= ', ';
			}
		}
		return $str;
	}


	/**
	 * Antiflood toggle
	 *
	 * @param int|bool $delay
	 */

	public function set_antiflood($delay = 120) {
		$this->antiflood = $delay;
		return $this;
	}


	/**
	 * Prepare email
	 * This method can be used to view complete email before sending it
	 */

	public function prepare() {
		if (empty($this->topic)) {
			throw new \Exception(
				'Topic was not set',
				self::ERR_NO_TOPIC
			);
		}
		if (count($this->recipients) < 1) {
			throw new \Exception(
				'Recipients not set',
				self::ERR_NO_RECIPIENTS
			);
		}

		$topic = $this->topic;
		$recipients = $this->emails2string($this->recipients);
		$content = '';


		/**
		 * The content
		 */

		$content .= '<html><body>';

		if (count($this->content) > 0) {
			$content .= '<br><hr style="border:0;border-bottom:1px solid #e3e3e3;"><table style="border-collapse:collapse;"><tbody>';
			foreach($this->content as $entry) {
				$content .= '<tr><td style="padding:5px;"><strong>' . $entry['name'] . ':</strong></td><td style="padding:5px;">' . $entry['value'] . '</td></tr>';
			}
			$content .= '</tbody></table><hr style="border:0;border-bottom:1px solid #e3e3e3;">';
		}

		$content .= '</body></html>';


		/**
		 * Headers
		 */

		$headers  = 'X-Mailer: PHP/' . phpversion() . self::NL;
		$headers .= 'Content-Type: text/html; charset=UTF-8' . self::NL;
		$headers .= 'MIME-Version: 1.0' . self::NL;

		if (!empty($this->reply_to)) {
			$headers .= 'Reply-To: ' . $this->reply_to . self::NL;
		}
		if (!empty($this->from)) {
			$headers .= 'From: ' . $this->from . self::NL;
		}
		if (!empty($this->cc)) {
			$headers .= 'Cc: ' . $this->emails2string($this->cc) . self::NL;
		}
		if (!empty($this->bcc)) {
			$headers .= 'Bcc: ' . $this->emails2string($this->bcc) . self::NL;
		}

		return [$recipients, $topic, $content, $headers];
	}


	/**
	 * ACTION : Send email
	 */

	public function send() {
		if ($this->antiflood && isset($_SESSION['mailer_sended']) && (date('U') - $_SESSION['mailer_sended']) < $this->antiflood) {
			throw new \Exception(
				'You can not send messages so often',
				self::ERR_ANTIFLOOD
			);
		}

		list($recipients, $topic, $content, $headers) = $this->prepare();

		$last_error = error_get_last();
		if (@mail($recipients, $topic, $content, $headers)) {
			$_SESSION['mailer_sended'] = date('U');
			return true;
		}
		$actual_error = error_get_last();

		// Detect if running mail() function thrown error.
		// This is the only way to handle errors with this primitive function
		if ($actual_error['message'] && $actual_error['message'] != $last_error['message']) {
			throw new \Exception(
				str_replace('mail(): ', '', $actual_error['message']),
				self::ERR_SERVER
			);
		}

		return false;
	}

}