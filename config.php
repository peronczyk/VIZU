<?php

/**
 * =================================================================================
 *
 * DEFAULT CONFIGURATION
 *
 * ---------------------------------------------------------------------------------
 *
 * This configuration can be overwritten by file 'config-override.php'
 * placed in main directory.
 * It is strongly recommended to avoid modyfying this file.
 *
 * =================================================================================
 */

class Config {

	/**
	 * Site name displayed in administration panel and login screen.
	 * @var String
	 */
	static $SITE_NAME = 'VIZU';

	/**
	 * VIZU module that will be loaded by default (if there is no parameters in URL)
	 * @var String
	 */
	static $DEFAULT_MODULE = 'page';

	/**
	 * Language that will be set by default when entering the website.
	 * @var String{2}
	 */
	static $DEFAULT_LANG = 'en';

	/**
	 * Decide whether the app should detect user system language or not.
	 * If set to true user will be redirected to url with language that matches his
	 * system/browser language - only if this language exists and is active.
	 * @var Boolean
	 */
	static $DETECT_LANG = true;

	/**
	 * This is the name of direcory of your theme. Default: "default".
	 * @var String
	 */
	static $THEME_NAME = 'default';

	/**
	 * Turn true only if you are sure that you will use HTTPS.
	 * When turned on you will not be able to log into admin panel
	 * on non-https connections.
	 * @var Boolean
	 */
	static $FORCE_HTTPS = false;

	/**
	 * Application directory location
	 * @var String
	 */
	static $APP_DIR = 'app/';

	/**
	 * Themes directory location
	 * @var String
	 */
	static $THEMES_DIR = 'themes/';

	/**
	 * Storage directory location
	 * @var String
	 */
	static $STORAGE_DIR = 'storage/';

	/**
	 * IP Address of development enviroment. After setting this debug mode will be
	 * set on this enviroment. By default localhost IPs are treated as DEV_IP
	 * so you don't need to add here IPs like 127.0.0.1.
	 * @var Array
	 */
	static $DEV_IP = ['127.0.0.1', '0.0.0.0', '::1'];

	/**
	 * Forces debug mode for everybody.
	 * @var Boolean
	 */
	static $DEBUG = false;

	/**
	 * Auto redirecting from http://domain.com to http://www.domain.com
	 * @var Boolean
	 */
	static $REDIRECT_TO_WWW = false;

	/**
	 * Block requests to admin script if they are not made with AJAX.
	 * @var Boolean
	 */
	static $BLOCK_AJAX = true;

	/**
	 * Password salt.
	 * @var String
	 */
	static $PASSWORD_SALT = 'SomeRand0mString';

	/**
	 * Field types that are allowed to be edited in admin panel.
	 * @example {{ fieldtype id='foo' }}
	 * @var Array
	 */
	static $EDITABLE_FIELD_TYPES = ['simple', 'rich', 'repeatable'];

	/**
	 * Field types that has its start and end tag.
	 * @example {{ paired }}Foo{{ /paired }}
	 */
	static $PAIRED_FIELD_TYPES = ['repeatable'];

	/**
	 * Other acceptable field types that can be used in template files.
	 * @var Array
	 */
	static $OTHER_FIELD_TYPES = ['lang'];

	/**
	 * Name of the library class name that will handle SQL queries
	 * @var String - SQLite | MySQL
	 */
	static $DB_TYPE = 'SQLite';

	/**
	 * MySQLI database file name. You can change this file name to something more
	 * complex if you want to be more sure no one will access it from browser.
	 * @var String
	 */
	static $SQLITE_FILE_NAME = 'db.sqlite';

	/**
	 * MySQL database host name
	 */
	static $MYSQL_HOST = 'localhost';

	/**
	 * MySQL database name
	 */
	static $MYSQL_NAME = 'vizu';

	/**
	 * MySQL database user name
	 */
	static $MYSQL_USER = 'root';

	/**
	 * MySQL database password
	 */
	static $MYSQL_PASS = 'root';
}