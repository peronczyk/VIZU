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

	public function __construct(SqlDb $db) {
		$this->_db = $db;
	}


	/** ----------------------------------------------------------------------------
	 * Ckeck if required database tables exists
	 */

	public function checkDbTables() {
		try {
			$table_fields = $this->_db->query('SELECT 1 FROM `fields` LIMIT 1');
			$table_users  = $this->_db->query('SELECT 1 FROM `users` LIMIT 1');
		}
		catch (Exception $e) {
			return false;
		}
		return ($table_fields && $table_users);
	}


	/** ----------------------------------------------------------------------------
	 * Ckeck if there are any users in database
	 */

	public function checkDbUsers() {
		$result = $this->_db->query('SELECT `id` FROM `users`', true);
		return (count($this->_db->fetchAll($result)) > 0);
	}


	/** ----------------------------------------------------------------------------
	 * Import SQL file contents into active database
	 */

	public function importSqlFile(string $file) {
		if (!file_exists($file)) {
			throw new Exception('Unable to import SQL file "' . $file . '" because it does not exists.');
		}

		$errors = 0;

		// Temporary variable, used to store current query
		$templine = '';

		// Read in entire file
		$lines = file($file);

		foreach ($lines as $line) {

			// Skip comments
			if (substr($line, 0, 2) == '--' || $line == '') {
				continue;
			}

			// Add this line to the current segment
			$templine .= $line;

			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';') {
				if (!$this->_db->query($templine)) {
					$errors++;
				}
				$templine = '';
			}
		}

		return ($errors > 0) ? false : true;
	}
}