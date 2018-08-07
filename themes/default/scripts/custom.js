$(function() {

	var contactFormSending = false;

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
			.removeClass('is-Error')
			.find('.is-Error')
			.removeClass('is-Error');

		$infoWrapper = $('#contact-form-info');
		$infoWrapper.html('');

		// Verify user with reCAPTCHA v3 front-end mechanism
		grecaptcha
			.execute('6LcKGmgUAAAAAJp1huktDQhKHsFiq0DEF1uxZTdv', {action: 'homepage'})
			.then(function(token) {
				$('input[name="recaptcha_token"]').val(token);

				// Post form data to VIZU contact form API
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
			});
	});
});