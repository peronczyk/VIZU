<?php

declare(strict_types=1);

class DependencyContainer {

	protected $dependencies = [];
	protected $prefix = '_';


	/** ----------------------------------------------------------------------------
	 * Add
	 */

	public function add(Object $dependency, string $name = null) : object {
		$this->dependencies[$name ?? get_class($dependency)] = $dependency;
		return $this;
	}


	/** ----------------------------------------------------------------------------
	 * Get (PSR-11 requirement)
	 */

	public function get(string $id) {
		if (!$this->has($id)) {
			throw new Exception("DependencyContainer does not contain instance of '{$id}'");
		}
		return $this->dependencies[$id];
	}


	/** ----------------------------------------------------------------------------
	 * Has (PSR-11 requirement)
	 */

	public function has(string $id) {
		return isset($this->dependencies[$id]);
	}


	/** ----------------------------------------------------------------------------
	 * Dependencies registrator
	 * Adds all stored dependencies to an existing object.
	 *
	 * @param object $object
	 */

	public function register(object $object) {
		foreach ($this->dependencies as $name => $dependency) {
			$name = $this->prefix . $name;

			if (isset($object->{$name})) {
				throw new Exception("Could not create dependency shortcut `{$name}` because property `{$this->prefix}{$name}` is already used.");
			}
			else {
				$object->{$name} = $dependency;
			}
		}
	}
}