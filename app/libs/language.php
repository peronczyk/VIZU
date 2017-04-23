<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Language
#
# ==================================================================================

class Language {

	private $lang_code;
	public $translations = array();
	private $_db; // Handle to database controller


	# ==============================================================================
	# INJECT DEPENDENCIES
	# ==============================================================================

	public function inject($db) {
		// Check if variable passed to this class is database controller
		if ($db && is_object($db) && is_a($db, 'Database')) $this->_db = $db;
		else Core::error('Variable passed to class "Language" is not correct "Database" object', __FILE__, __LINE__, debug_backtrace());
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


	# ==============================================================================
	# TRANSLATE STRING
	#
	# @param string $key
	# 	Key name that will be returned from loaded translations
	#
	# @param string or array $additionals
	# 	Used in case if $key was not found in loaded translations.
	# 	If array is passed script searches in it for key.
	# 	If string is passed it will be returned as it is.
	# ==============================================================================

	public function _t($key, $additionals = false) {
		if (isset($this->translations[$key])) return $this->translations[$key];
		elseif ($additionals) {
			if (is_array($additionals) && isset($additionals[$key])) return $additionals[$key];
			else return $additionals;
		}
		else return false;
	}
}