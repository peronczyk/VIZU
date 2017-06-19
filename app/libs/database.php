<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Database
#
# ==================================================================================

namespace libs;

class Database {

	private $host;
	private $user;
	private $pass;
	private $name;

	private $connection;
	private $queries_count = 0;


	/**
	 * Constructor
	 */

	public function __construct($host, $user, $pass, $name) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->name = $name;
	}


	/**
	 * Connect to database
	 */

	public function connect() {
		mysqli_report(MYSQLI_REPORT_STRICT);

		try {
			$this->connection = new \mysqli($this->host, $this->user, $this->pass, $this->name);
		}
		catch(\Exception $e) {
			Core::error('Unable to connect to MySQL database "' . $this->name . '". Returned error: ' . $e->getMessage() . ' [' . $e->getCode() . '].<br>Probably application is not installed propertly. Check configured database connection credentials and be sure that database exists.', __FILE__, __LINE__, debug_backtrace());
			exit;
		}

		mysqli_set_charset($this->connection, 'utf8');
	}


	/**
	 * GETTER : Database connection handle
	 */

	public function get_conn() {
		return $this->connection;
	}


	/**
	 * Query
	 *
	 * @param string $query - MySQL query
	 * @param boolean $is_silent - if true this method will not throw errors
	 *	on failure.
	 *
	 * @return object - MySQL result
	 */

	public function query($query, $is_silent = false) {
		if (!$this->connection) $this->connect();

		$result = $this->connection->query($query);
		$this->queries_count++;

		// Display critical error if queried table doesn't exist
		if (!$is_silent && !$result && mysqli_errno($this->connection) === 1146) {
			Core::error('<strong>Queried database table does not exist</strong>. Returned error: ' . mysqli_error($this->connection) . '. Probably the application is not installed. Navigate to "install/" to start installation process.', __FILE__, __LINE__, debug_backtrace());
		}

		return $result;
	}


	/**
	 * Fetch results
	 *
	 * @return array|false
	 */

	public function fetch($result) {
		if (is_object($result) && method_exists($result, 'fetch_assoc')) {
			$arr = array();
			while ($row = $result->fetch_assoc()) $arr[] = $row;
			return $arr;
		}
		else return false;
	}


	/**
	 * GETTER : Database server version
	 */

	public function get_version() {
		if (!$this->connection) $this->connect();
		return $connection->server_version;
	}


	/**
	 * Check if application is connected to database
	 */

	public function is_connected() {
		return $this->connection ? true : false;
	}


	/**
	 * GETTER : Number of performed queries
	 */

	public function get_queries_count() {
		return $this->queries_count;
	}


	/**
	 * Import file
	 */

	public function import_file($file) {
		if (!file_exists($file)) {
			Core::error('Unable to import SQL file "' . $file . '" becouse it does not exists.', __FILE__, __LINE__, debug_backtrace());
		}

		$errors = 0;

		// Temporary variable, used to store current query
		$templine = '';

		// Read in entire file
		$lines = file($file);

		foreach ($lines as $line) {

			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '') continue;

			// Add this line to the current segment
			$templine .= $line;

			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';') {
				if (!$this->query($templine)) $errors++;
				$templine = '';
			}
		}

		return ($errors > 0) ? false : true;
	}

}