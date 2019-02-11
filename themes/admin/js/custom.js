
'use strict';

var debug = 1;

$(function() {

	if (debug) console.info('%cLoaded: custom.js / Debug: ON', 'font-weight:bold');


	/*	============================================================================
	 *	INIT LOADED SCRIPTS
	 *	============================================================================ */

	var date = new Date(),
		refreshAfter = date.getMinutes();

	$('body').SVGIconSetLoader({
		'debug'			: debug,
		'iconsFileUrl'	: themePath + '/images/icons.svg',
		'iconsVersion'	: refreshAfter, // Refresh each minute
	});


	/**
	 * =============================================================================
	 * DEFINITIONS
	 * =============================================================================
	 */

	var $pages			= $('#pages'),
		$msgBox			= $('#msgbox'),
		$mainMenu		= $('#main-menu'),
		$loadBar		= $('#loadbar'),
		msgBoxInitial	= 0; // Store timeout for message box;


	/**
	 * =============================================================================
	 * VISIBILITY TOGGLER
	 * =============================================================================
	 */

	$(document).on('click', '[data-toggle-visibility]', function(event) {
		var dataToggleValue = $(this).data('toggle-visibility');
		$('#' + dataToggleValue).toggleClass('is-Visible');
	});



	/*	========================================================================
	 *	AJAX SUCCESS
	 *	======================================================================== */

	function ajaxSuccess(obj, json, url) {

		$loadBar.removeClass('active');

		if (debug) {
			console.info('Ajax request succed to url: ' + url);
			console.log(json);
		}


		/*	--------------------------------------------------------------------
		 *	JSON DATA CHECK / ERROR HANDLING
		 */

		// If there was no JSON data
		if (!json) {
			if (debug) {
				console.log('No JSON data received after AJAX success');
				console.log(json);
			}
			showMsg('Serwer nie przesłał żadnej odpowiedzi na zapytanie.');
			return false;
		}

		// Simple check if received data is JSON. Yes, it's lame, but fast
		else if (typeof json !== 'object') {
			if (debug) {
				console.log('Received data is not an AJAX object');
			}
			showMsg('Odpowiedź serwera jest niepoprawna - przesłane dane nie sa obiektem AJAX<br><br>' + json);
			return false;
		}

		// If JSON data contains errors
		else if ('error' in json && json.error) {
			var errormsg = json.error.str + '<br><small>File: ' + json.error.file + '<br>Line: ' + json.error.line + '</small>';
			showMsg('error', errormsg);

			if (debug) console.error('Received error: ' + JSON.stringify(json.error) + '\nFile: ' + json.error.file + '\nLine: ' + json.error.line);
			return false;
		}

		// If JSON data contains message
		else if ('message' in json && json.message) {
			showMsg('message', json.message);
			if (debug) console.info('Received message: ' + json.message);
		}


		/*	--------------------------------------------------------------------
		 *	PARSE RESULTS
		 */

		// If JSON data contains data to LOG
		if ('log' in json && json.log != null && json.log.length > 0 && debug === 1) {
			console.group('JSON.log (' + json.log.length + '):');
			if (json.log.constructor === Array) {
				for(var i = 0; i < json.log.length; i++) {
					console.log(json.log[i]);
				}
			}
			console.groupEnd();
		}

		// User auth
		if (json.message) {
			showMsg('message', json.message);
		}
		else if (json.loggedin !== true) {
			$('body').removeClass('loggedin');
			$pages.empty();
			showMsg('error', 'This operation requires you to be logged in.');
		}
		else {
			$('body').addClass('loggedin');

			if (json.html) {
				if (debug) console.info('HTML found in received JSON. Rendering new page...');
				$pages.addClass('loading').on('transitionend', function(event) {
					$(this).html(json.html).off().removeClass('loading'); // Add off() to prevent multiple firing
				});
			}
			else if (debug) console.info('No HTML found in received JSON')
		}
		return true;
	}


	/*	============================================================================
	 *	AJAX ERROR
	 *	============================================================================ */

	function ajaxError(obj, jqXHR, textStatus, errorThrown, url) {
		if (debug) {
			console.group('AJAX failed');
			console.error('Error thrown: ' + errorThrown + '\nText status: ' + textStatus + '\nURL: ' + url + '\njqXHR: ' + jqXHR);
			if (('responseText' in jqXHR) && (jqXHR.responseText)) console.log('Response: ' + jqXHR.responseText);
			console.groupEnd();
		}
		showMsg('error', '<strong>Błąd ładowania asynchronicznego.</strong><br>Prawdopodobnie funkcja, którą próbowano uruchomić posiada błędy składni');
	}


	/*	============================================================================
	 *	AJAX BEFORE SEND
	 *	============================================================================ */

	function ajaxStart() {
		$loadBar.addClass('active');
	}


	/*	========================================================================
	 *	AJAX FORMS
	 *	======================================================================== */

	$('body').on('submit', 'form', function(e) {

		var $form = $(this),
			url = $form.attr('action');

		$.ajax({
			type		: 'post',
			url 		: url,
			data		: $form.serialize(),
			dataType	: 'json',
			cache		: false,
			beforeSend	: function() { ajaxStart($form); },
			success		: function(json) { ajaxSuccess($form, json, url); },
			error		: function(jqXHR, textStatus, errorThrown) { ajaxError($form, jqXHR, textStatus, errorThrown, url); },
		});
		return false;
	});


	/*	========================================================================
	 *	AJAX LINKS
	 *	======================================================================== */

	$(document).on('click', 'a', function() {
		if (debug) console.info('Link clicked');

		var $link	= $(this),
			url		= $link.attr('href');

		// Prevent loading AJAX content on normal links
		if ($link.is('[data-noajax]') || $link.is('[data-toggle-visibility]')) {
			return true;
		}

		$.ajax({
			type		: 'get',
			url			: url,
			dataType	: 'json',
			cache		: false,
			beforeSend	: function() { ajaxStart($link); },
			success		: function(json) {

				// If HTML was loaded and clicked element is an main menu link highlight it
				if (ajaxSuccess($link, json, url) && $link.parent().parent()[0] == $mainMenu[0]) {
					$mainMenu.find('a').removeClass('active');
					$link.addClass('active');
				}
			},
			error		: function(jqXHR, textStatus, errorThrown) { ajaxError($link, jqXHR, textStatus, errorThrown, url); },
		});
		return false;
	});


	/*	========================================================================
	 *	MESSAGE BOX
	 *	======================================================================== */

	// Close message box

	$msgBox.on('click', function() {
		$(this).removeClass('active');
	});

	// Open message box

	function showMsg(type, text) {

		// Don't open if no data is provided
		if (!type || !text || text.length < 1) {
			if (debug) console.error('Script tried to open message box, but no data was provided (type, text)');
			return false;
		}

		/* DOPISAĆ OBSŁUGĘ JUŻ OTWORZONEGO OKNA */
		if ($msgBox.hasClass('active')) { }

		$msgBox
			.removeClass().addClass('active').addClass(type)
			.children('p').empty().html(text);

		// Auto close after given time
		msgBoxInitial = setTimeout(function() { $msgBox.removeClass(); }, 8000);
	}


	/*	========================================================================
	 *	OBSERVE CONTENT CHANGES AND REACT
	 *	======================================================================== */

	$pages.observe(function(mutation) {
		$pages.find('[data-richtext]').richTextEditor();
	}, 'childList');
});