
$.fn.ajaxForms = function() {

	//	------------------------------------------------------------------------
	//	BEFORE FORM SUBMISSION

	function formSendBefore($form) {
		if (debug) console.info('ajaxForms: Start sending');
		$form.removeClass('error').addClass('loading') // Remove 'error' class from form and add 'loading'
			.find('.error').removeClass('error'); // Remove 'error' classes from inputs
	}


	//	------------------------------------------------------------------------
	//	AFTER RECEIVING DATA

	function formSendSuccess($form, dataType, data) {
		$form.removeClass('loading');
		if (debug) console.info('ajaxForms: Form sended');

		if (!data) {
			if (debug) console.log('ajaxForm: No data received');
			return;
		}

		var $messageBox = $form.find('.message');

		// If received data is JSON object

		if (typeof data === 'object') {
			if (debug) console.log(data);

			// If errors are detected

			if (data.formErrors && typeof data.formErrors === 'object' && data.formErrors.length > 0) {
				for (var i = 0; i < data.formErrors.length; i++) {
					if (data.formErrors[i].inputName) {
						var $input = $form.find('[name=' + data.formErrors[i].inputName + ']');
						$input.addClass('error').focus();

						if (data.formErrors[i].errorMessage) {
							var $errorTooltip = $('<div/>', {'class': 'error-tooltip'}).html(data.formErrors[i].errorMessage);
							$input.parent().append($errorTooltip);
							$input.on('change input', function() {
								monitorErrorTooltip($(this));
							});
						}
					}
				};
				console.error('ajaxForms: ' + i + ' errors found in sended form');
			}

			// If there is no errors and form has message box

			else if ($messageBox.length > 0) {
				var $message = $messageBox.find('p');
				$form.addClass('success');
				if (data.message) {
					$message.html(data.message);
				}

				// Handle close button
				$messageBox.find('.close').on('click', function() {
					return closeMessage($form, $message);
				});
			}
		}

		// If received data is string

		else {
			$message.html(data);
		}
	}


	//	------------------------------------------------------------------------
	//	IF AJAX DIDN'T WORK PROPERTLY

	function formSendError($form, jqXHR, textStatus, errorThrown, action) {

		$form.removeClass('loading').addClass('error');

		var $messageBox	= $form.find('.message'),
			$message	= $messageBox.find('p'),
			errorMessageValue	= 'Wiadomość nie została wysłana.<br>';

		// Display errors

		if ($message.length > 0) {
			if (jqXHR.status == '404') errorMessageValue += 'Aplikacja nie mogła nawiązać połączenia z plikiem wykonawczym';
			else if (textStatus == 'parsererror') {
				if (jqXHR.responseText === '') errorMessageValue += 'Aplikacja nie zwróciła żadnego wyniku operacji';
				else errorMessageValue += 'Aplikacja napotkała błędy składni w pliku wykonawczym.';
			}
			else errorMessageValue += 'Aplikacja napotkała nieznany błąd:<br>' + errorThrown;

			$message.html(errorMessageValue);
		}

		// Handle close button

		$messageBox.find('.close').on('click', function() {
			return closeMessage($form, $message);
		});

		if (debug) { console.error('ajaxForms: Error'); console.error('errorThrown: ' + errorThrown); console.error('textStatus: ' + textStatus); console.log(jqXHR); }
	}


	//	------------------------------------------------------------------------
	//	MONITORS INPUT WITH ERROR TOOLTIP FOR CHANGES

	function monitorErrorTooltip($input) {
		if ($input.val().length > 0) {
			$input.off('change input').removeClass('error');
			$input.siblings('.error-tooltip').remove();
		}
	}


	//	------------------------------------------------------------------------
	//	HANDLE CLOSE BUTTON FOR POPUP MESSAGE

	function closeMessage($form, $message) {
		console.log('ajaxForm: Close button clicked');
		$message.empty();
		$form.removeClass('loading success error');
		return false;
	}


	//	------------------------------------------------------------------------
	//	LOOP OVER ALL ELEMENTS FOUND

	$(this).each(function() {

		var $form		= $(this),
			method		= $form.attr('method'),
			action		= $form.attr('action'),
			dataType	= $form.data('type');

		// Check if selected element is OK

		if (!$form.is('form')) return;
		if (!action) {
			if (debug) console.log('Form does not have proper attributes: method, action');
			return;
		}

		if (['post', 'get'].indexOf(method) < 0) method = 'post'; // Set default method if it wasn't defined in <form>

		// Form submit action

		$form.on('submit', function() {
			$.ajax({
				type		: method,
				url			: action,
				data		: $form.serialize(),
				dataType	: dataType,
				beforeSend	: function() { formSendBefore($form); },
				success		: function(data) { formSendSuccess($form, dataType, data); },
				error		: function(jqXHR, textStatus, errorThrown) { formSendError($form, jqXHR, textStatus, errorThrown, action); }
			});
			return false;
		});
	});
}