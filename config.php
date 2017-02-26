<?php

// Configuration file
// This way variables are available globally

class Config {

	// Language that will be set by default when entering the website.
	const DEFAULT_LANG = 'en';

	// This is the name of direcory of your theme. Default: "default".
	const THEME_NAME = 'default';


	# ------------------------------------------------------------------------------
	# DATABASE CONNECTION

	const DB_HOST		= 'localhost';
	const DB_NAME		= 'vizu';
	const DB_USER		= 'root';
	const DB_PASS		= '';


	# ------------------------------------------------------------------------------
	# DIRECTORIES

	const APP_DIR		= 'app/';
	const THEMES_DIR	= 'themes/';
	const INSTALL_DIR	= 'install/';


	# ------------------------------------------------------------------------------
	# CONTACT SETTINGS

	// ID of default user to send email via contact form.
	const CONTACT_USER = 1;

	// Set this to true if you want to send messages to all other users as BCC.
	const CONTACT_ALL = true;


	# ------------------------------------------------------------------------------
	# DEV OPTIONS

	// IP Address of development enviroment. After setting this debug mode will be
	// set on this enviroment. By default localhost IPs are treated as DEV_IP
	// so you don't need to add here IPs like 127.0.0.1.
	const DEV_IP = null;

	// Forces debug mode for everybody.
	const DEBUG = false;

	// Turn off / on auto redirecting from http://domain.com to
	// http://www.domain.com
	const REDIRECT_TO_WWW = false;

	// Block requests to admin script if they are not made with AJAX.
	const BLOCK_AJAX = true;

	// Salt for more secure password storing.
	const PASSWORD_SALT = 'SomeRand0mString';


	# ------------------------------------------------------------------------------
	# FIELDS

	// Categories of fields that can be used in templates.
	static $_FIELD_CATEGORIES = array(

		// Fields, that can be edited in 'Content' CMS.
		'content' => array('text', 'setting'),

		// Other fields.
		'other' => array('lang'),
	);

	// Determines how field can be edited in admin panel.
	static $_FIELD_TYPES = array('simple', 'rich');
}