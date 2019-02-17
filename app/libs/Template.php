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
	 * Stores path to templates directory
	 */
	private $templates_dir;


	/** ----------------------------------------------------------------------------
	 * SETTER : Theme templates directory
	 */

	public function setTemplatesDir(string $templates_dir) {
		$this->templates_dir = $templates_dir;
	}


	/** ----------------------------------------------------------------------------
	 * Get contents of template file
	 */

	public function getTemplateFileContent(string $file_path) {
		if (empty($this->templates_dir)) {
			throw new Exception('Templates directory not set.');
		}

		$file_path = $this->templates_dir . '/' . $file_path;

		if (!file_exists($file_path)) {
			throw new Exception("Template file does not exist: {$file_path}");
			return false;
		}

		return file_get_contents($file_path);
	}


	/** ----------------------------------------------------------------------------
	 * Assign vars to parse
	 */

	public function assign(array $array) {
		foreach ($array as $key => $val) {
			$this->vars[$key] = $val;
		}
	}


	/** ----------------------------------------------------------------------------
	 * Prepare array of fields found in content
	 *
	 * @param String $content
	 * @return Array
	 */

	public function getFieldsFromString(string $content) {
		$num_matches = preg_match_all('/{{(.*?)}}/', $content, $matches);
		list($full_tags, $field_contents) = $matches;

		$fields = [];

		foreach ($field_contents as $key => $val) {
			$field_type = explode(' ', trim($val), 2)[0];
			$field      = [];

			$field['type'] = $field_type;
			$field['tag'] = $full_tags[$key];

			/**
			 * Get params of the field
			 * @example foo='bar' becomes array ['foo' => 'bar']
			 */
			$num_params = preg_match_all("/([a-z]+)='([^']*)'/", $val, $params);
			if (is_array($params)) {
				foreach ($params[1] as $p => $param) {
					$field['params'][$params[1][$p]] = $params[2][$p];
				}
			}

			$fields[] = $field;
		}

		return $fields;
	}


	/** ----------------------------------------------------------------------------
	 * Parse file
	 */

	public function parseFile(string $file, array $translations = []) {
		$template_content = $this->getTemplateFileContent($file);
		$template_fields  = $this->getFieldsFromString($template_content);
		return $this->parse($template_content, $template_fields, $translations);
	}


	/** ----------------------------------------------------------------------------
	 * Parse template
	 *
	 * @param String $content - HTML code with template tags: {{ something }}
	 * @param Array $fields
	 * @param Array $translations
	 */

	public function parse(string $content, array $fields, array $translations = []) {

		/**
		 * Prepare all fields
		 * @todo needed to be replaced
		 * @link http://stackoverflow.com/questions/5017582/php-looping-template-engine-from-scratch
		 */

		$patterns     = [];
		$replacements = [];
		$n = 0;

		foreach ($fields as $id => $field) {

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
			foreach ($this->vars as $key => $var) {
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
			else {
				$preg_status_text = $preg_status;
			}

			throw new Exception('Unable to display template because of error in parsing function. Returned error:<br>' . $preg_status_text);
		}
	}
}