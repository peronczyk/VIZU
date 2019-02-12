<?php

namespace Fields;

class Container {
	private $field_classes_instances = [];

	public function getFieldClass(string $field_type) {
		if (!isset($this->field_classes_instances[$field_type])) {
			$class_name = __NAMESPACE__ . '\\' . ucfirst($field_type) . 'Field';
			$this->field_classes_instances[$field_type] = new $class_name();
		}

		return $this->field_classes_instances[$field_type];
	}
}