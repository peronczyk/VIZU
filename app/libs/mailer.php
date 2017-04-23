<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Mailer
#
# ==================================================================================

class Mailer {

	private $recipients	= array();
	private $list_data	= array();
	private $cc			= array();
	private $bcc		= array();
	private $reply_to;
	private $from;
	private $topic;


	# ==============================================================================
	# SET TOPIC
	# ==============================================================================

	public function set_topic($topic) {
		$this->topic = $topic;
	}


	# ==============================================================================
	# ADD RECIPIENT
	# ==============================================================================

	public function add_recipient($mail, $name = '') {
		$this->recipients[] = array($mail, $name);
	}


	# ==============================================================================
	# ADD BCC EMAIL
	# ==============================================================================

	public function add_bcc($mail, $name = '') {
		$this->bcc[] = array($mail, $name);
	}


	# ==============================================================================
	# ADD LIST DATA
	# Data presented as list at the beginning of message
	# ==============================================================================

	public function add_list_data($name, $data) {
		$this->list_data[] = array($name, $data);
	}


	# ==============================================================================
	# ADD REPLY TO HEADERS
	# ==============================================================================

	public function set_reply_to($mail) {
		$this->reply_to = $mail;
	}


	# ==============================================================================
	# ADD FROM TO HEADERS
	# ==============================================================================

	public function set_from($mail) {
		$this->from = $mail;
	}


	# ==============================================================================
	# ADD 'FROM TO' HEADERS
	# ==============================================================================

	private function emails2string($emails) {
		$str = '';
		if (is_array($emails)) {
			$num = count($emails);
			for($i = 0; $i < $num; $i++) {
				/*echo('<pre>');
				print_r($emails[$i]);
				echo('</pre><br>');*/
				if (empty($emails[$i][1]))	$str .= $emails[$i][0];
				else						$str .= $emails[$i][1] . '<' . $emails[$i][0] . '>';

				if (($i + 1) < $num) $str .= ', ';
			}
		}
		return $str;
	}


	# ==============================================================================
	# SEND EMAIL
	# ==============================================================================

	public function send($message) {
		if (empty($message))	return 'Pusta wiadomość';
		if (empty($this->topic)) return 'Nie ustawiono tematu';
		if (count($this->recipients) < 1) return 'Brak odbiorców';

		$topic = $this->topic;
		$recipients = $this->emails2string($this->recipients);
		$content = '';

		# --------------------------------------------------------------------------
		# CONTENT

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

		# --------------------------------------------------------------------------
		# HEADERS

		$headers  = "X-Mailer: PHP/" . phpversion() . "\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		$headers .= "MIME-Version: 1.0";

		if (!empty($this->reply_to))	$headers .= "\r\nReply-To: " . $this->reply_to;
		if (!empty($this->from))		$headers .= "\r\nFrom: " . $this->from;
		if (!empty($this->cc))			$headers .= "\r\nCc: " . $this->emails2string($this->cc);
		if (!empty($this->bcc))			$headers .= "\r\nBcc: " . $this->emails2string($this->bcc);

		# --------------------------------------------------------------------------
		# SENDING

		if (@mail($recipients, $topic, $content, $headers)) return true;
		else return 'Nie udało się wysłać wiadomości. Spróbuj ponownie lub skontaktuj się z nami telefonicznie.';
	}

}