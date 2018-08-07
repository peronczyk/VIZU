<?php

namespace libs;

class Recaptcha3 {
	const MIN_SCORE = 0.5;
	const VERIFY_API_URL = 'https://www.google.com/recaptcha/api/siteverify';

	private $curl;
	private $mailer;
	private $secret;

	public function __construct(Curl $curl, Mailer $mailer, string $secret) {
		$this->curl = $curl;
		$this->mailer = $mailer;
		$this->secret = $secret;
	}

	public function validate(string $token, int $min_score = self::MIN_SCORE) {
		$validation = $this->curl->call(self::VERIFY_API_URL, 'POST', [
			// POST params required by reCAPTCHA verifier
			'secret'   => $this->secret,
			'response' => $this->mailer->sanitiseString($_POST['recaptcha_token'] ?? ''),
			'remoteip' => $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
		]);

		if ($validation->getErrno()) {
			throw new \Exception($validation->getError());
		}

		$result = json_decode($validation->getBody(), true);

		if (empty ($result['score']) || !is_numeric($result['score'])) {
			if (isset($result['error-codes']) && is_array($result['error-codes'])) {
				throw new \Exception(implode(',', $result['error-codes']));
			}
			else {
				throw new \Exception('Unknown reCAPTCHA error occured');
			}
		}

		return ($result['score'] > $min_score);
	}
}