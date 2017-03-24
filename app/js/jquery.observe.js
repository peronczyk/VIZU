
/*	========================================================================
 *
 *	JQ: OBSERVER
 *
 *	Script author: Bartosz Perończyk (peronczyk.com)
 *	------------------------------------------------------------------------
 *	Description:
 *	Watches for changes in specified dom element
 *	------------------------------------------------------------------------
 *	Instalation:
 *	Example: $('div#content').observe(function(mutation) { <code> });
 *
 *	======================================================================== */


$.fn.observe = function(init, params) {

	// Skip if function was initiated bad way

	if (init && typeof init !== 'function') {
		if (debug) console.error('Observer: variable passed as first argument was not a function');
		return $(this);
	}


	// Skip if no dom elements was passed to watch

	else if ($(this).length && $(this).length < 1) {
		if (debug) console.error('Observer: there is no elements to observe');
		return $(this);
	}


	// Set up basic variables

	window.MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

	var myObserver		= new MutationObserver(mutationHandler), // Mutation observer object
		allowedParams	= ['childList', 'characterData', 'attributes', 'subtree'],
		i 				= allowedParams.length,
		config			= {}; // What params needs to be observed


	// Set up configuration

	if (!params) {
		while(i--) config[allowedParams[i]] = true;
	}
	else if (typeof params === 'object') {
		config = params;
	}
	else if (typeof params === 'string') {
		params = params.split(' ');
		while(i--) {
			if (params.indexOf(allowedParams[i]) > -1) config[allowedParams[i]] = true;
			else config[allowedParams[i]] = false;
		}
	}


	// Add observer to all elements found in jQ

	this.each(function() {
		if (debug) console.info('Observer: set to watch');
		myObserver.observe(this, config);
	});


	// Mutation handler

	function mutationHandler(mutationRecords) {
		mutationRecords.forEach(function(mutation) {
			if (debug) console.info('Observer: change in observed area detected');
			if (init) var initObj = init.call(initObj, mutation); // Call function passed as value of init object
		});
	}

	return $(this);
}