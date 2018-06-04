<?php return [
	// Contact form settings
	'contact' => [
		// ID of default user to send email via contact form.
		'default_recipient' => 1,

		// Set this to true if you want to send messages to all other users as BCC.
		'inform_all' => true,

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