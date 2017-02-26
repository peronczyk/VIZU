<?php

class Language {
	private $lang_code;
	public $translations = array();
	private $_db; // Handle to database controller


	# ==============================================================================
	# CONSTRUCT / LOAD DEPENDENCIES
	# ==============================================================================

	public function __construct($db) {
		// Check if variable passed to this class is database controller
		if ($db && is_object($db) && is_a($db, 'Database')) $this->_db = $db;
		else Core::error('Variable passed to class "User" is not correct "Database" object', __FILE__, __LINE__, debug_backtrace());
	}


	# ==============================================================================
	# SET ACTIVE LANGUAGE
	# This function returns false if language was set to default
	# ==============================================================================

	public function set($requested = null) {
		if (!empty($requested)) {
			$result = $this->_db->query('SELECT * FROM `languages`');
			$languages = $this->_db->fetch($result);
			if (!is_array($languages)) {
				Core::error('There is no configured languages in database.', __FILE__, __LINE__, debug_backtrace());
			}

			foreach($languages as $lang) {
				if ($lang['code'] == $requested && $lang['active'] == true) {
					$this->lang_code = $lang['code'];
					$lang_file = Config::THEME_NAME . '/lang/' . LANG_CODE . '.php';
					return true;
				}
			}
		}

		$this->lang_code = Config::DEFAULT_LANG;
		return false;
	}


	# ==============================================================================
	# GET LANGUAGE CODE
	# ==============================================================================

	public function get() {
		return $this->lang_code;
	}


	# ==============================================================================
	# LOAD THEME TRANSLATIONS
	# ==============================================================================

	public function load_theme_translations() {
		if (!$this->lang_code) return false;

		$lang_file = Config::THEMES_DIR . Config::THEME_NAME . '/lang/' . $this->lang_code . '.php';
		if (!file_exists($lang_file)) {
			Core::error('Theme translations file not found at this location: ' . $lang_file, __FILE__, __LINE__, debug_backtrace());
		}
		$this->translations = include $lang_file;
	}
}