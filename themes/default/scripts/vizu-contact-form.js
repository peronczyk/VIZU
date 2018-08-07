$(function() {

	var contactFormSending = false;

	function sendForm($form, $infoWrapper) {
		$.ajax({
			method: 'POST',
			url: $form.attr('action'),
			data: $form.serialize(),
			dataType: 'json'
		})
			.always(function(data, textStatus, jqXHR) {
				contactFormSending = false;
				$form.removeClass('is-Sending');

				if (textStatus == 'success') {
					if (data.error) {
						$infoWrapper.html(data.error);
						$form.addClass('is-Error');
					}
					else if (data.message) {
						$infoWrapper.html(data.message);
						$form.addClass('is-Success');
					}

					if (data['form-errors']) {
						for (var i = 0; i < data['form-errors'].length; i++) {
							var $input = $('[name=' + data['form-errors'][i]['input-name'] + ']');
							$input.addClass('is-Error')
						}
					}
				}

				else {
					$infoWrapper.html('Sending failed.<br>' + data.statusText);
					console.log(data);
				}
			});
	}

	/**
	 * Simple contact form handler.
	 * Sends form asynchronously and display the results.
	 */
	$('#contact-form').on('submit', function(event) {
		event.preventDefault();

		if (contactFormSending) {
			return;
		}
		contactFormSending = true;

		$form = $(this);
		$form
			.addClass('is-Sending')
			.removeClass('is-Error is-Success')
			.find('.is-Error')
			.removeClass('is-Error');

		$infoWrapper = $('#contact-form-info');
		$infoWrapper.html('');

		// Send form with reCAPTCHA token
		if (window.recaptchaSiteKey && window.recaptchaSiteKey.length > 10) {
			console.info('[ContactForm] Sending form with reCAPTCHA validation...');
			grecaptcha
				.execute(window.recaptchaSiteKey, {action: 'homepage'})
				.then(function(token) {
					$('input[name="recaptcha_token"]').val(token);

					// Post form data to VIZU contact form API
					sendForm($form, $infoWrapper);
				});
		}

		// Send form normally
		else {
			console.info('[ContactForm] Sending form...')
			sendForm($form, $infoWrapper);
		}
	});
});