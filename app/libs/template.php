<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Template
#
# ==================================================================================

namespace libs;

class Template {

	// Assignement storage. This values will be parsed in to the template
	// eg.: {{ text id='foo'}} will be changed with 'foo' => 'Bar'
	public $vars = [];

	// Stores theme name (folder)
	private $theme;

	// Templates direcotory name that is inside each theme
	private $tpl_dir = 'templates/';

	// Template files extension
	private $tpl_ext = '.html';


	/**
	 * SETTER : Theme direcory name
	 */

	public function setTheme(string $theme_name) {
		$this->theme = $theme_name;
	}


	/**
	 * Check if template exists
	 *
	 * @return string|false - Return template path or false if not found
	 */

	public function getTemplatePath(string $file) {
		if (empty($this->theme)) {
			Core::displayError('Theme not set', __FILE__, __LINE__, debug_backtrace());
		}

		$file_path = \Config::$THEMES_DIR . $this->theme . '/' . $this->tpl_dir . $file . $this->tpl_ext;
		if (!file_exists($file_path)) {
			return false;
		}

		return $file_path;
	}


	/**
	 * Load field class
	 */

	public function loadFieldClass(string $field_name) {
		$class_file = 'app/fields/' . $field_name . '.php';
		if (file_exists($class_file)) {
			require_once $class_file;
			$class_name = 'fields\\' . ucfirst($field_name);
			if (class_exists($class_name)) {
				return new $class_name();
			}
			else {
				throw new \Exception('Template field handling file does not have proper class: "' . $field_name . '"');
			}
		}
		else {
			throw new \Exception('Template field handling file does not exist: "' . $class_file . '"');
		}
	}


	/**
	 * Get contents of template file
	 */

	public function getContent(string $file) {
		$file_path = $this->getTemplatePath($file);

		if (!$file_path) {
			Core::displayError('Template file does not exist: ' . $file_path, __FILE__, __LINE__, debug_backtrace());
			return false;
		}

		return file_get_contents($file_path);
	}


	/**
	 * Assign vars to parse
	 */

	public function assign(array $array) {
		foreach($array as $key => $val) {
			$this->vars[$key] = $val;
		}
	}


	/**
	 * Prepare array of fields found in content
	 *
	 * @param string $content
	 * @return array
	 */

	public function getFields(string $content) {
		$num_matches = preg_match_all('/{{(.*?)}}/', $content, $matches);
		$fields = [];

		foreach($matches[1] as $key => $val) {
			$val    = trim($val); // Remove spaces from beggining and the end
			$chunks = array_filter(explode(' ', $val));
			$field  = [];

			if (in_array($chunks[0], \Config::$FIELD_CATEGORIES['content']) || in_array($chunks[0], \Config::$FIELD_CATEGORIES['other'])) {
				$field['category'] = $chunks[0];

				// Get params of the field
				$num_params = preg_match_all("/([a-z]+)='([^']*)'/", $val, $params);
				if (is_array($params)) {
					foreach($params[1] as $p => $param) {
						$field[$params[1][$p]] = $params[2][$p];
					}
				}

				// Add field to array if has ID and there is no existing entry with this ID
				if (!empty($field['id']) && !isset($fields[$field['id']])) {
					$fields[$field['id']] = $field;
					unset($fields[$field['id']]['id']); // Remove additional ID from params array
				}
			}
		}

		return $fields;
	}


	/**
	 * Parse template
	 *
	 * @param string $content - HTML code with template tags: {{ something }}
	 * @param array $fields
	 * @param array $translations
	 */

	public function parse(string $content, array $fields, array $translations = []) {

		// Prepare all fields
		// @TODO: needed to be replaced
		// http://stackoverflow.com/questions/5017582/php-looping-template-engine-from-scratch

		$patterns     = [];
		$replacements = [];
		$n = 0;

		foreach($fields as $id => $field) {

			// Match anything like {{ category something='something' id='id' somethin='something' }}
			// (*UTF8) - selves problem with non latin characters in values
			// \p{L}   - searches any character from unicode.
			// /u      - allow searching for all unicode characters
			$patterns[$n] = '/{{ ' . $field['category'] . '[\p{L}0-9\'=\-_\:\.\(\)\s]+id=\'' . $id . '\'[\p{L}0-9\'=\-_\:\.\(\)\s]+}}/u';

			switch($field['category']) {
				case 'lang':
					if (isset($translations[$id])) $replacements[$n] = $translations[$id];
					else $replacements[$n] = $id;
					break;

				default:
					if (isset($this->vars[$id])) $replacements[$n] = $this->vars[$id];
					else $replacements[$n] = strtoupper($field['category'] . ':' . $id);
					break;
			}
			$n++;
		}

		if (is_array($this->vars)) {
			foreach($this->vars as $key => $var) {
				if (!empty($key)) {
					$patterns[] = '/{{ ' . $key . ' }}/';
					$replacements[] = $var;
				}
			}
		}

		$parsed = preg_replace($patterns, $replacements, $content);
		$preg_status = preg_last_error();

		// If regular expression was performed without errors
		if (!$preg_status) {
			return $parsed;
		}

		// If error eccured show error status
		else {
			if (is_numeric($preg_status)) {
				$preg_status_list = [
					1 => 'PREG_INTERNAL_ERROR',
					2 => 'PREG_BACKTRACK_LIMIT_ERROR',
					3 => 'PREG_RECURSION_LIMIT_ERROR',
					4 => 'PREG_BAD_UTF8_ERROR',
					5 => 'PREG_BAD_UTF8_OFFSET_ERROR',
				];
				$preg_status_text = (isset($preg_status_list[$preg_status]))
					? $preg_status_list[$preg_status] . ' [' . $preg_status . ']'
					: 'Unknown error [' . $preg_status . ']';
			}
			else $preg_status_text = $preg_status;

			Core::displayError('Unable to display template because of error in parsing function. Returned error:<br>' . $preg_status_text, __FILE__, __LINE__, debug_backtrace());
		}
	}
}