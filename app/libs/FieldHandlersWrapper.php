<?php

class FieldHandlersWrapper {
	const FIELDS_NAMESPACE = 'Fields';

	private $_template;
	private $field_handlers = [];


	/** ----------------------------------------------------------------------------
	 * Initiate
	 */

	public function __construct(Template $template) {
		$this->_template = $template;
		$this->loadAvailableFieldsHandlers();
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function loadAvailableFieldsHandlers() {
		$field_handlers_dir = __DIR__ . '/' . self::FIELDS_NAMESPACE . '/';
		$dir_contents = scandir($field_handlers_dir);

		foreach ($dir_contents as $key => $file_name) {
			if (!is_file($field_handlers_dir . $file_name)) {
				continue;
			}
			$file_name = pathinfo($file_name, PATHINFO_FILENAME);
			$class_name = self::FIELDS_NAMESPACE . '\\' . $file_name;
			array_push($this->field_handlers, new $class_name());
		}
		//var_dump($this->field_handlers);
	}
}