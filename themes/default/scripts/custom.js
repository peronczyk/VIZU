$(function() {

	/**
	 * Simple contact form handler.
	 * Sends form asynchronously and display the results.
	 */
	$('#contact-form').on('submit', function(event) {
		event.preventDefault();

		$form = $(this);
		$infoWrapper = $('#contact-form-info');

		$.ajax({
			method: 'POST',
			url: $(this).attr('action'),
			data: $(this).serialize(),
			dataType: 'json',

			success: function(result) {
				$infoWrapper.html('');
				$form.find('.is-Error').removeClass('is-Error');

				if (result.error) {
					$infoWrapper.html(result.error);
				}
				else if (result.message) {
					$infoWrapper.html(result.message);
				}

				if (result['form-errors']) {
					for (var i = 0; i < result['form-errors'].length; i++) {
						var $input = $('[name=' + result['form-errors'][i]['input-name'] + ']');
						$input.addClass('is-Error')
					}
				}
			},

			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				$infoWrapper.html(textStatus);
			}
		});
	});


	/**
	 * reCAPTCHA v3 code
	 */
	grecaptcha.ready(function() {
		grecaptcha
			.execute('6LcKGmgUAAAAAJp1huktDQhKHsFiq0DEF1uxZTdv', {action: 'homepage'})
			.then(function(token) {
				console.log('reCAPTCHA v3 running...');
				$('input[name="recaptcha_token"]').val(token);
			});
	});


});