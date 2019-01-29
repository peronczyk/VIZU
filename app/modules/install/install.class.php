<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Class: Install
 *
 * =================================================================================
 */

class Install {

	/**
	 * Database handle
	 */
	private $_db;


	/** ----------------------------------------------------------------------------
	 * Constructor & dependancy injection
	 */

	public function __construct(Database $db) {
		$this->_db = $db;
	}


	/** ----------------------------------------------------------------------------
	 * Ckeck if required database tables exists
	 */

	public function checkDbTables() {
		$table_fields = $this->_db->query('SELECT 1 FROM `fields` LIMIT 1', true);
		$table_users  = $this->_db->query('SELECT 1 FROM `users` LIMIT 1', true);
		return ($table_fields && $table_users);
	}


	/** ----------------------------------------------------------------------------
	 * Ckeck if there are any users in database
	 */

	public function checkDbUsers() {
		$result = $this->_db->query('SELECT `id` FROM `users`', true);
		return (count($this->_db->fetch($result)) > 0) ? true : false;
	}
}