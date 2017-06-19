<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Mailer
#
# ==================================================================================

namespace libs;

class Mailer {

	private $recipients = array();
	private $list_data = array();
	private $cc = array();
	private $bcc = array();
	private $reply_to;
	private $from;
	private $topic;


	/**
	 * SETTER : Topic
	 */

	public function set_topic($topic) {
		$this->topic = $topic;
		return $this;
	}


	/**
	 * SETTER : Add recipient
	 */

	public function add_recipient($mail, $name = '') {
		$this->recipients[] = array($mail, $name);
		return $this;
	}


	/**
	 * SETTER : Add BCC email
	 */

	public function add_bcc($mail, $name = '') {
		$this->bcc[] = array($mail, $name);
		return $this;
	}


	/**
	 * SETTER : Add list data
	 * Data presented as list after the message content.
	 *
	 * @param string $name
	 * @param string $value
	 */

	public function add_list_data($name, $value) {
		$this->list_data[] = array($name, $value);
		return $this;
	}


	/**
	 * SETTER : "reply-to" header
	 */

	public function set_reply_to($mail) {
		$this->reply_to = $mail;
		return $this;
	}


	/**
	 * SETTER : "from" header
	 */

	public function set_from($mail) {
		$this->from = $mail;
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
				if (empty($emails[$i][1])) {
					$str .= $emails[$i][0];
				}
				else $str .= $emails[$i][1] . '<' . $emails[$i][0] . '>';

				if (($i + 1) < $num) $str .= ', ';
			}
		}
		return $str;
	}


	/**
	 * ACTION : Send email
	 *
	 * @param string $message
	 */

	public function send($message) {
		if (empty($message)) {
			throw new \Exception('Pusta wiadomość');
		}
		if (empty($this->topic)) {
			throw new \Exception('Nie ustawiono tematu');
		}
		if (count($this->recipients) < 1) {
			throw new \Exception('Brak odbiorców');
		}

		$topic = $this->topic;
		$recipients = $this->emails2string($this->recipients);
		$content = '';

		/**
		 * The content
		 */

		$content .= '<html><body><h3>Kontakt z witryny</h3>';
		$content .= '<p>' . $message . '</p>';

		if (count($this->list_data) > 0) {
			$content .= '<br><hr style="border:0;border-bottom:1px solid #e3e3e3;"><table style="border-collapse:collapse;"><tbody>';
			foreach($this->list_data as $val) {
				$content .= '<tr><td style="padding:5px;"><strong>' . $val[0] . ':</strong></td><td style="padding:5px;">' . $val[1] . '</td></tr>';
			}
			$content .= '</tbody></table><hr style="border:0;border-bottom:1px solid #e3e3e3;">';
		}

		$content .= '</body></html>';

		/**
		 * Headers
		 */

		$headers  = "X-Mailer: PHP/" . phpversion() . "\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		$headers .= "MIME-Version: 1.0";

		if (!empty($this->reply_to)) {
			$headers .= "\r\nReply-To: " . $this->reply_to;
		}
		if (!empty($this->from)) {
			$headers .= "\r\nFrom: " . $this->from;
		}
		if (!empty($this->cc)) {
			$headers .= "\r\nCc: " . $this->emails2string($this->cc);
		}
		if (!empty($this->bcc)) {
			$headers .= "\r\nBcc: " . $this->emails2string($this->bcc);
		}

		/**
		 * The action
		 */

		if (@mail($recipients, $topic, $content, $headers)) return true;
		else {
			throw new \Exception('Nie udało się wysłać wiadomości. Spróbuj ponownie lub skontaktuj się z nami telefonicznie.');
		}
	}

}