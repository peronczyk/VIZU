<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Lib: Template
 *
 * =================================================================================
 */

class Template {

	/**
	 * Assignement storage. This values will be parsed in to the template
	 * eg.: {{ text id='foo'}} will be changed with 'foo' => 'Bar'
	 */
	public $vars = array();

	/**
	 * Stores theme name (folder)
	 */
	private $theme;

	/**
	 * Templates direcotory name that is inside each theme
	 */
	private $tpl_dir = 'templates/';

	/**
	 * Template files extension
	 */
	private $tpl_ext = '.html';


	/** ----------------------------------------------------------------------------
	 * SETTER : Theme direcory name
	 */

	public function setTheme(string $theme_name) {
		$this->theme = $theme_name;
	}


	/** ----------------------------------------------------------------------------
	 * Check if template exists
	 *
	 * @return String|false - Return template path or false if not found
	 */

	public function getTemplatePath(string $file) {
		if (empty($this->theme)) {
			Core::error('Theme not set', __FILE__, __LINE__, debug_backtrace());
		}

		$file_path = Config::$THEMES_DIR . $this->theme . '/' . $this->tpl_dir . $file . $this->tpl_ext;
		if (!file_exists($file_path)) {
			return false;
		}

		return $file_path;
	}


	/** ----------------------------------------------------------------------------
	 * Get contents of template file
	 */

	public function getContent(string $file) {
		$file_path = $this->getTemplatePath($file);

		if (!$file_path) {
			Core::error('Template file does not exist: ' . $file_path, __FILE__, __LINE__, debug_backtrace());
			return false;
		}

		return file_get_contents($file_path);
	}


	/** ----------------------------------------------------------------------------
	 * Assign vars to parse
	 */

	public function assign(array $array) {
		foreach($array as $key => $val) {
			$this->vars[$key] = $val;
		}
	}


	/** ----------------------------------------------------------------------------
	 * Prepare array of fields found in content
	 *
	 * @param String $content
	 * @return Array
	 */

	public function getFields(string $content) {
		$num_matches = preg_match_all('/{{(.*?)}}/', $content, $matches);
		$fields = [];

		foreach($matches[1] as $key => $val) {
			$val        = trim($val);
			$chunks     = array_filter(explode(' ', $val));
			$field_type = $chunks[0];
			$field      = [];

			// Skip fields with types t
			if (
				!in_array($field_type, Config::$EDITABLE_FIELD_TYPES) &&
				!in_array($field_type, Config::$OTHER_FIELD_TYPES))
			{
				continue;
			}

			$field['type'] = $field_type;

			// Get params of the field
			$num_params = preg_match_all("/([a-z]+)='([^']*)'/", $val, $params);
			if (is_array($params)) {
				foreach ($params[1] as $p => $param) {
					$field[$params[1][$p]] = $params[2][$p];
				}
			}

			// Add field to array if has ID and there is no existing entry with this ID
			if (!empty($field['id']) && !isset($fields[$field['id']])) {
				$fields[$field['id']] = $field;
				unset($fields[$field['id']]['id']); // Remove additional ID from params array
			}
		}

		return $fields;
	}


	/** ----------------------------------------------------------------------------
	 * Parse template
	 *
	 * @param String $content - HTML code with template tags: {{ something }}
	 * @param Array $fields
	 * @param Array $translations
	 */

	public function parse(string $content, array $fields, array $translations = []) {

		// Prepare all fields
		// @TODO: needed to be replaced
		// http://stackoverflow.com/questions/5017582/php-looping-template-engine-from-scratch

		$patterns     = [];
		$replacements = [];
		$n = 0;

		foreach($fields as $id => $field) {

			/**
			 * Match anything like {{ type something='something' id='id' somethin='something' }}
			 * (*UTF8) - selves problem with non latin characters in values
			 * \p{L}   - searches any character from unicode.
			 * /u      - allow searching for all unicode characters
			 */
			$patterns[$n] = '/{{ ' . $field['type'] . '[\p{L}0-9\'=\-_\:\.\(\)\s]+id=\'' . $id . '\'[\p{L}0-9\'=\-_\:\.\(\)\s]+}}/u';

			switch($field['type']) {
				case 'lang':
					$replacements[$n] = $translations[$id] ?? $id;
					break;

				default:
					$replacements[$n] = $this->vars[$id] ?? strtoupper($field['type'] . ':' . $id);
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

			Core::error('Unable to display template becouse of error in parsing function. Returned error:<br>' . $preg_status_text, __FILE__, __LINE__, debug_backtrace());
		}
	}
}