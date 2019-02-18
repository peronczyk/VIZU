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
	private $template_file_path;
	private $template_file_content;
	public $template_fields;

	/**
	 * Basic assignement storage. This keys and values will be parsed at the
	 * beginning of parsing process.
	 * @example {{ some_name }} will be changed to 'Lorem ipsum' if $vars will
	 *   contain 'some_name' => 'Lorem ipsum' array element.
	 */
	public $vars = [];


	/** ----------------------------------------------------------------------------
	 * Constructor
	 */

	public function __construct(string $template_file_path) {
		if (!file_exists($template_file_path)) {
			throw new Exception("Template file does not exist: {$template_file_path}");
		}
		$this->template_file_path = $template_file_path;
		$this->template_file_content = file_get_contents($this->template_file_path);
		$this->template_fields = self::getFieldsFromString($this->template_file_content);
	}


	/** ----------------------------------------------------------------------------
	 * Get contents of template file
	 */

	public function getTemplateFileContent() {
		return $this->template_file_content;
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

	public static function getFieldsFromString(string $content) {
		$num_matches = preg_match_all('/{{(.*?)}}/', $content, $matches);
		list($full_tags, $field_contents) = $matches;

		$fields = [];

		foreach ($field_contents as $key => $val) {
			$field = [];

			$field['type'] = explode(' ', trim($val), 2)[0];
			$field['tag'] = $full_tags[$key];

			/**
			 * Get properties of the field
			 * @example foo='bar' becomes array ['foo' => 'bar']
			 */
			$num_props = preg_match_all("/([a-z]+)='([^']*)'/", $val, $props);
			if ($num_props) {
				$field['props'] = array_combine($props[1], $props[2]);
			}

			array_push($fields, $field);
		}

		return $fields;
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function getTemplateFields() {
		return $this->template_fields;
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function iterateTemplateFieldsType(string $type, callable $callback) {
		foreach ($this->template_fields as $key => $field) {
			if ($field['type'] == $type) {
				$callback($key, $field);
			}
		}
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function removeDuplicateTemplateFieldsByType(string $type) {
		$ids_found = [];
		$remove_count = 0;

		$this->iterateTemplateFieldsType($type, function($key, $field) use (&$ids_found, &$remove_count) {
			$field_id = $field['props']['id'] ?? null;

			if ($field_id && !in_array($field_id, $ids_found)) {
				array_push($ids_found, $field_id);
			}
			else {
				unset($this->template_fields[$key]);
				$remove_count++;
			}
		});

		if ($remove_count) {
			$this->template_fields = array_values($this->template_fields);
		}
	}


	/** ----------------------------------------------------------------------------
	 * Parse template
	 */

	public function parse() {
		$content = $this->getTemplateFileContent();
		$fields  = $this->getTemplateFields();

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