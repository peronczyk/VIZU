<?php

class FieldHandlersWrapper {
	const FIELDS_NAMESPACE = 'Fields';

	private $_template;
	private $_container;
	private $field_handlers = [];


	/** ----------------------------------------------------------------------------
	 * Initiate
	 */

	public function __construct(Template $template, DependencyContainer $container) {
		$this->_template = $template;
		$this->_container = $container;
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
			array_push($this->field_handlers, new $class_name($this->_template, $this->_container));
		}
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function runHandlersMethod(string $method_name, array $method_arguments = []) {
		$results = [];
		foreach ($this->field_handlers as $handler) {
			$results[] = $handler->$method_name(...$method_arguments);
		}
		return $results;
	}

	/** ----------------------------------------------------------------------------
	 *
	 */

	public function assignValues(array $fields_data) {
		$this->runHandlersMethod('assignValues', [$fields_data]);
	}
}