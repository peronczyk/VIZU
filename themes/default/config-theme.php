<?php return [
	// Contact form settings
	'contact' => [
		// ID of default user to send email via contact form.
		'default_recipient' => 1,

		// Set this to true if you want to send messages to all other users as BCC.
		'inform_all' => true,

		// Place reCAPTCHA v3 'secret' code here. It is available in your
		// recaptcha admin: https://www.google.com/recaptcha/admin
		// If you don't want to use reCAPTCHA please leave this option empty.
		'recaptcha_secret' => '6LcKGmgUAAAAAKB5KwE0ePVmbPwvGe0VP20R5Gvn',

		// Decide how big should be the score for form to be sent (0.0 - 1.0)
		'recaptcha_min_score' => '0.5',

		// Form fields
		'fields' => [
			[
				'type'     => 'email',
				'name'     => 'email',
				'label'    => 'form-label-email',
				'required' => true
			],
			[
				'type'     => 'textarea',
				'name'     => 'message',
				'label'    => 'form-label-message',
				'required' => true
			],
			[
				'type'     => 'checkbox',
				'name'     => 'agreement',
				'label'    => 'form-label-agreement',
				'required' => true
			],
		]
	],
];