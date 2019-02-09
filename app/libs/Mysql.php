<?php

/**
 * =================================================================================
 *
 * Simple MySQL abstraction class
 *
 * ---------------------------------------------------------------------------------
 *
 * @category  Database Access
 * @author    Bartosz Perończyk <bartosz@peronczyk.com>
 *
 * =================================================================================
 */

declare(strict_types=1);

class Mysql implements SqlDb {

	private $host;
	private $user;
	private $pass;
	private $name;

	private $connection;
	private $queries = [];


	/** ----------------------------------------------------------------------------
	 * Constructor
	 */

	public function __construct(string $host, string $user, string $pass, string $name) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->name = $name;
	}


	/** ----------------------------------------------------------------------------
	 * Connect to database
	 */

	public function connect() {
		mysqli_report(MYSQLI_REPORT_STRICT);

		try {
			$this->connection = new \mysqli($this->host, $this->user, $this->pass, $this->name);
		}
		catch(\Exception $e) {
			Core::error('Unable to connect to MySQL database "' . $this->name . '". Returned error: ' . $e->getMessage() . ' [' . $e->getCode() . '].<br>Probably application is not installed propertly. Check configured database connection credentials and be sure that database exists.', __FILE__, __LINE__, debug_backtrace());
		}

		mysqli_set_charset($this->connection, 'utf8');
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : Database connection handle
	 */

	public function getConnection() {
		return $this->connection;
	}


	/** ----------------------------------------------------------------------------
	 * Query
	 *
	 * @param String $query - MySQL query
	 * @param Boolean $is_silent - if true this method will not throw errors
	 *	on failure.
	 *
	 * @return object - MySQL result
	 */

	public function query(string $query) {
		if (!$this->connection) {
			$this->connect();
		}

		array_push($this->queries, $query);
		$result = $this->connection->query($query);

		return $result;
	}


	/** ----------------------------------------------------------------------------
	 * Fetch results
	 *
	 * @return array|false
	 */

	public function fetchAll($result = null) {
		$arr = [];
		while ($row = $result->fetch_assoc()) {
			$arr[] = $row;
		}
		return $arr;
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : Database server version
	 */

	public function getVersion() {
		if (!$this->connection) {
			$this->connect();
		}
		return $connection->server_version;
	}


	/** ----------------------------------------------------------------------------
	 * Check if application is connected to database
	 */

	public function isConnected() : bool {
		return ($this->connection) ? true : false;
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : Number of performed queries
	 */

	public function getQueriesNumber() {
		return count($this->queries);
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : Database name
	 */

	public function getDbName() {
		return $this->name;
	}
}