
/*	========================================================================
 *
 *	JQ: SVG ICONSET LOADER
 *
 *	Author		: Bartosz Perończyk (peronczyk.com)
 *	Inspired by	: http://osvaldas.info/caching-svg-sprite-in-localstorage
 *
 *	------------------------------------------------------------------------
 *	DESCRIPTION:
 *
 *	Loads to specific element in dom set of SVG icons and store it for
 *	caching purpose in Local Storage.
 *
 *	------------------------------------------------------------------------
 *	INSTALLATION:
 *
 *	$('body').SVGIconSetLoader({
 *		iconsFileUrl: 'path/to/icons.svg',
 *		iconsVersion: 1,
 *		debug: 1
 *	});
 *
 *	If you will not set iconVersion icons set wouldn't becached at all
 *
 *	======================================================================== */


(function($) {

	'use strict';


	/*	--------------------------------------------------------------------
	 *	PLUGIN DEFAULT CONFIGURATION
	 */

	var defaults = {
			debug			: 0,
			iconsFileUrl	: null,
			iconsVersion	: false,
		},
		data,
		isLocalStorage = 'localStorage' in window && window['localStorage'] !== null;


	/*	--------------------------------------------------------------------
	 *	SET UP JQUERY PLUGIN
	 */

	$.fn.SVGIconSetLoader = function(options) {

		var
			// Setup configuration
			config	= $.extend({}, defaults, options),

			// Definitions
			_self = this;

		if (config.debug) console.info('Plugin loaded: SVGIconSetLoader');

		if (!config.iconsFileUrl) {
			if (config.debug) console.error('SVGIconSetLoader: path to icons file not set');
			return _self;
		}


		// If SVG icons found in Local Storage and their version equals requested version

		if (config.iconsVersion && isLocalStorage && localStorage.getItem('SVGIconSetVer') == config.iconsVersion) {
			data = localStorage.getItem('SVGIconSet');
			if (data) {
				_self.append(data);
				if (config.debug) console.info('SVGIconSetLoader: Icons loaded from Local Storage (ver: ' + config.iconsVersion + ')');
				return _self;
			}
		}

		// If there was no data in Local Storage or data has wrong version load data asynchronously

		_self.ajaxPromise = $.ajax({ // Add promise to returned object
			url			: config.iconsFileUrl,
			cache		: false,
			dataType	: 'html',

			success		: function(data) {
				if (data) {
					_self.append(data);
					if (config.debug) console.info('SVGIconSetLoader: Icons loaded asynchronously from file');

					if (isLocalStorage && config.iconsVersion) {
						localStorage.setItem('SVGIconSet', data);
						localStorage.setItem('SVGIconSetVer', config.iconsVersion);
						if (config.debug) console.info('SVGIconSetLoader: Icons saved to Local Storage (ver: ' + config.iconsVersion + ')');
					}
				}
				else if (config.debug) console.error('SVGIconSetLoader: No data received from icons file. Probably file is empty or damaged.');
			},

			error		: function(jqXHR, textStatus, errorThrown) {
				if (config.debug) console.error('SVGIconSetLoader: Error "' + errorThrown + '" occured while accessing icons file "' + config.iconsFileUrl + '"');
			}
		});

		return _self;
	}

})(jQuery);