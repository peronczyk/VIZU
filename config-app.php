<?php

# ==================================================================================
#
#	VIZU CMS
#	Configuration file
#	This way variables are available globally
#
# ==================================================================================

class Config {

	// VIZU module that will be loaded by default (if there is no parameters in URL)
	static $DEFAULT_MODULE = 'page';

	// Language that will be set by default when entering the website.
	static $DEFAULT_LANG = 'en';

	// Decide whether the app should detect user system language or not.
	// If set to true user will be redirected to url with language that matches his
	// system/browser language - only if this language exists and is active.
	static $DETECT_LANG = true;

	// This is the name of direcory of your theme. Default: "default".
	static $THEME_NAME = 'default';


	# ------------------------------------------------------------------------------
	# DIRECTORIES

	static $APP_DIR		= 'app/';
	static $THEMES_DIR	= 'themes/';


	# ------------------------------------------------------------------------------
	# DEV OPTIONS

	// IP Address of development enviroment. After setting this debug mode will be
	// set on this enviroment. By default localhost IPs are treated as DEV_IP
	// so you don't need to add here IPs like 127.0.0.1.
	static $DEV_IP = ['127.0.0.1', '0.0.0.0', '::1'];

	// Forces debug mode for everybody.
	static $DEBUG = false;

	// Turn off / on auto redirecting from http://domain.com to
	// http://www.domain.com
	static $REDIRECT_TO_WWW = false;

	// Block requests to admin script if they are not made with AJAX.
	static $BLOCK_AJAX = true;

	// Salt for more secure password storing.
	static $PASSWORD_SALT = 'SomeRand0mString';


	# ------------------------------------------------------------------------------
	# FIELDS

	// Categories of fields that can be used in templates.
	static $FIELD_CATEGORIES = [

		// Fields, that can be edited in 'Content' CMS.
		'content' => ['text', 'setting'],

		// Other fields.
		'other' => ['lang'],
	];

	// Determines how field can be edited in admin panel.
	static $FIELD_TYPES = ['simple', 'rich'];
}