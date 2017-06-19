
/*	================================================================================
 *
 *	JQ: AJAXFORMS
 *
 *	Author : Bartosz Perończyk
 *
 *	--------------------------------------------------------------------------------
 *	DESCRIPTION:
 *
 *
 *	--------------------------------------------------------------------------------
 *	INSTALATION:
 *
 *
 *	================================================================================
 */

(function($) {

	'use strict';


	/*	----------------------------------------------------------------------------
	 *	PLUGIN DEFAULT CONFIGURATION
	 */

	var defaults = {
			debug: 0,
			messageBoxElem: '.message',
			formSuccessClassName: 'is-success',
			inputErrorClassName: 'is-error',
			inputErrorTooltipClassName: 'error-tooltip'
		};


	/*	----------------------------------------------------------------------------
	 *	BEFORE FORM SUBMISSION
	 */

	var formSendBefore = function($form, config) {
		if (config.debug) console.info('ajaxForms: Start sending');

		$form
			// Remove 'error' class from form and add 'loading'
			.removeClass('error').addClass('loading')

			// Remove 'error' classes from inputs
			.find('.error').removeClass(config.inputErrorClassName);

		// Remove error tooltips
		$form.find('.' + config.inputErrorTooltipClassName).remove();
	}


	/*	----------------------------------------------------------------------------
	 *	AFTER RECEIVING DATA
	 */

	var formSendSuccess = function($form, config, dataType, data) {
		$form.removeClass('loading');
		if (config.debug) console.info('ajaxForms: Form sended');

		if (!data) {
			if (config.debug) console.log('ajaxForm: No data received');
			return;
		}

		var $messageBox = $form.find(config.messageBoxElem);

		// If received data is JSON object

		if (typeof data === 'object') {
			if (config.debug) console.log(data);

			// If errors are detected

			if (data.formErrors && typeof data.formErrors === 'object' && data.formErrors.length > 0) {
				for (var i = 0; i < data.formErrors.length; i++) {
					if (data.formErrors[i].inputName) {
						var $input = $form.find('[name=' + data.formErrors[i].inputName + ']');
						$input.addClass(config.inputErrorClassname).focus();

						if (data.formErrors[i].errorMessage) {
							var $errorTooltip = $('<div/>', {'class': config.inputErrorTooltipClassName}).html(data.formErrors[i].errorMessage);
							$input.parent().append($errorTooltip);
							$input.on('change input', function() {
								monitorErrorTooltip($(this), config);
							});
						}
					}
				};
				if (config.debug) console.error('ajaxForms: ' + i + ' errors found in sended form');
			}

			// If there is no errors and form has message box

			else if ($messageBox.length > 0) {
				var $message = $messageBox.find('p');
				$form.addClass(config.formSuccessClassName);
				if (data.message) {
					$message.html(data.message);
				}

				// Handle close button
				$messageBox.find('.close').on('click', function() {
					return closeMessage($form, $message);
				});
			}
		}
	}


	/*	----------------------------------------------------------------------------
	 *	IF AJAX DIDN'T WORK PROPERTLY
	 */

	var formSendError = function($form, config, jqXHR, textStatus, errorThrown, action) {

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

		if (config.debug) { console.error('ajaxForms: Error'); console.error('errorThrown: ' + errorThrown); console.error('textStatus: ' + textStatus); console.log(jqXHR); }
	}


	/*	----------------------------------------------------------------------------
	 *	MONITORS INPUT WITH ERROR TOOLTIP FOR CHANGES
	 */

	var monitorErrorTooltip = function($input, config) {
		if ($input.val().length > 0) {
			$input.off('change input').removeClass('error');
			$input.siblings('.' + config.inputErrorTooltipClassname).remove();
		}
	}


	/*	----------------------------------------------------------------------------
	 *	HANDLE CLOSE BUTTON FOR POPUP MESSAGE
	 */

	var closeMessage = function($form, config, $message) {
		if (config.debug) console.log('ajaxForm: Close button clicked');
		$message.empty();
		$form.removeClass('loading success error');
		return false;
	}


	/*	----------------------------------------------------------------------------
	 *	SET UP JQUERY PLUGIN
	 */


	$.fn.ajaxForms = function(options) {

		var
			// Setup configuration
			config = $.extend({}, defaults, options);


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
				if (config.debug) console.log('Form does not have proper attributes: method, action');
				return;
			}

			// Set default method if it wasn't defined in <form>
			if (['post', 'get'].indexOf(method) < 0) method = 'post';

			// Form submit action

			$form.on('submit', function(event) {
				event.preventDefault();

				$.ajax({
					type		: method,
					url			: action,
					data		: $form.serialize(),
					dataType	: dataType,

					beforeSend: function() {
						formSendBefore($form, config);
					},
					success: function(data) {
						formSendSuccess($form, config, dataType, data);
					},
					error: function(jqXHR, textStatus, errorThrown) {
						formSendError($form, config, jqXHR, textStatus, errorThrown, action);
					}
				});
			});
		});
	}

})(jQuery);