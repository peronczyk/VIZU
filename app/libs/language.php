<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Language
#
# ==================================================================================

namespace libs;

class Language {

	private $lang_code;
	public $translations = array();
	private $lang_list;
	private $_db; // Handle to database controller


	/**
	 * Constructor & dependency injection
	 */

	public function __construct($db) {
		// Check if variable passed to this class is database controller
		if ($db && is_object($db) && is_a($db, __NAMESPACE__ . '\Database')) $this->_db = $db;
		else Core::error('Variable passed to class "Language" is not correct "Database" object', __FILE__, __LINE__, debug_backtrace());
	}


	/**
	 * GETTER : List of configured languages
	 *
	 * @return array
	 */

	public function get_list() {
		if (!$lang_list) {
			$result = $this->_db->query('SELECT * FROM `languages`', true);
			$this->lang_list = $this->_db->fetch($result);
		}
		return $this->lang_list;
	}


	/**
	 * GETTER : Active language code
	 *
	 * @return string{2}
	 */

	public function get() {
		return $this->lang_code;
	}


	/**
	 * SETTER : Active language
	 *
	 * @return boolean - Returns false if language was set to default
	 */

	public function set($requested = null) {
		if ($requested) $lang_code = $requested;
		else $lang_code = \Config::$DEFAULT_LANG;

		if ($this->exists($lang_code)) {
			$this->lang_code = $lang_code;
			return true;
		}

		return false;
	}


	/**
	 * Check if provided lang code matches any configured language in database
	 *
	 * @return boolean
	 */

	public function exists($lang_code) {
		$lang_list = $this->get_list();
		if (count($lang_list) > 0) {
			foreach($lang_list as $lang) {
				if ($lang['code'] == $lang_code && (bool)$lang['active'] === true) {
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * Load theme translations
	 */

	public function load_theme_translations() {
		if (!$this->lang_code) return false;

		$lang_file = \Config::$THEMES_DIR . \Config::$THEME_NAME . '/lang/' . $this->lang_code . '.php';
		if (!file_exists($lang_file)) {
			Core::error('Theme translations file not found at this location: ' . $lang_file, __FILE__, __LINE__, debug_backtrace());
		}
		$this->translations = include $lang_file;
	}


	/**
	 * Translate string
	 *
	 * @param string $key - Key name that will be returned from loaded translations
	 * @param string|array $additionals - Used in case if $key was not found
	 *	in loaded translations. If array is passed script searches in it for key.
	 *	If string is passed it will be returned as it is.
	 *
	 * @return string|false
	 */

	public function _t($key, $additionals = false) {
		if (isset($this->translations[$key])) return $this->translations[$key];
		elseif ($additionals) {
			if (is_array($additionals) && isset($additionals[$key])) return $additionals[$key];
			else return $additionals;
		}
		else return false;
	}
}