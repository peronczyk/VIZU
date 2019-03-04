<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Lib: Language
 *
 * =================================================================================
 */

class Language {

	private $translations = [];

	private $lang_code;
	private $lang_list;

	private $_router; // Handle to router class


	/** ----------------------------------------------------------------------------
	 * Constructor & dependency injection
	 * @param Object $router - Router class
	 * @param Object $db - Database handling class
	 */

	public function __construct(Router $router) {
		$this->_router = $router;
	}


	/** ----------------------------------------------------------------------------
	 * SETTER : List of configured languages
	 * @return Array
	 */

	public function setList(array $lang_list) {
		$this->lang_list = $lang_list;
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : List of configured languages
	 * @return Array
	 */

	public function getList() {
		return $this->lang_list;
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : Active language code
	 * @return String{2}
	 */

	public function getActiveLangCode() {
		return $this->lang_code;
	}


	/** ----------------------------------------------------------------------------
	 * Set active language
	 * @return Boolean - Returns false if language was set to default
	 */

	public function autoSetLanguage() {
		// Check if first chunk of request is valid language definition
		$lang_requested = strlen($this->_router->getFirstRequest()) == 2;

		$user_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

		// Lang detection mechanism
		// This code checks if user language was not detected before
		// and if language was not requested in url.

		if (!$lang_requested && Config::$DETECT_LANG && (empty($_SESSION['lang_detected']) || !$_SESSION['lang_detected'])) {
			if ($user_lang != Config::$DEFAULT_LANG && $this->exists($user_lang) && count($_POST) < 1) {
				$_SESSION['lang_detected'] = $user_lang;
				$this->_router->redirect($user_lang, true, true);
			}
		}

		// Check if first request chunk is a existing and active language

		if ($lang_requested && $this->exists($this->_router->getFirstRequest())) {

			// Prevent accessing default language from two different URLs
			if ($this->_router->getFirstRequest() == Config::$DEFAULT_LANG) {
				$this->_router->requestShift();

				if (count($_POST) < 1) {
					$_SESSION['lang_detected'] = Config::$DEFAULT_LANG;
					$this->_router->redirect('', true, true);
				}
			}

			// Set requested language as active and shift requests
			else {
				$this->lang_code = $this->_router->getFirstRequest();
				$this->_router->requestShift();
				return true;
			}
		}

		if ($this->exists(\Config::$DEFAULT_LANG)) {
			$this->lang_code = Config::$DEFAULT_LANG;
			return false;
		}
		else {
			Core::error('Configured default page language does not exist in database or it is inactive.', __FILE__, __LINE__, debug_backtrace());
		}
	}


	/** ----------------------------------------------------------------------------
	 * Check if provided lang code matches any configured language in database
	 * @return Boolean
	 */

	public function exists($lang_code) {
		$lang_list = $this->getList();
		if ($lang_list && count($lang_list) > 0) {
			foreach ($lang_list as $lang) {
				if ($lang['code'] == $lang_code && (bool)$lang['active'] === true) {
					return true;
				}
			}
		}
		return false;
	}


	/** ----------------------------------------------------------------------------
	 * Load theme translations
	 * @return Boolean
	 */

	public function loadThemeTranslations() {
		if (!$this->lang_code) {
			return false;
		}

		$lang_file = Config::$THEMES_DIR . Config::$THEME_NAME . '/lang/' . $this->lang_code . '.php';

		if (file_exists($lang_file)) {
			$this->translations = include $lang_file;
			return true;
		}
		else {
			Core::error('Theme translations file not found at location: ' . $lang_file, __FILE__, __LINE__);
		}
	}


	/** ----------------------------------------------------------------------------
	 * Get stored translations
	 */

	public function getTranslations() {
		return $this->translations;
	}


	/** ----------------------------------------------------------------------------
	 * Translate string
	 *
	 * @param string $key - Key name that will be returned from loaded translations
	 * @param string|array $additionals - Used in case if $key was not found
	 *	in loaded translations. If array is passed script searches in it for key.
	 *	If string is passed it will be returned as it is.
	 *
	 * @return String|false
	 */

	public function _t($key, $additionals = false) {
		if (isset($this->translations[$key])) {
			return $this->translations[$key];
		}
		elseif ($additionals) {
			return (is_array($additionals) && isset($additionals[$key]))
				? $additionals[$key]
				: $additionals;
		}
		else {
			return $key;
		}
	}
}