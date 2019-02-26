<?php

/**
 * =================================================================================
 *
 * Simple SQLite abstraction class
 *
 * ---------------------------------------------------------------------------------
 *
 * @category  Database Access
 * @author    Bartosz Perończyk <bartosz@peronczyk.com>
 *
 * =================================================================================
 */

declare(strict_types=1);

class SQLite implements SqlDb {
	protected $connection = false;
	protected $file;
	protected $debug = false;
	protected $result;
	protected $queries = [];


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function __construct(string $file, $debug = false) {
		$this->file = $file;
		$this->debug = $debug;
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function connect() {
		$this->connection = new \PDO('sqlite:./' . $this->file);
		$this->connection->setAttribute(
			\PDO::ATTR_ERRMODE,
			$this->debug
				? \PDO::ERRMODE_EXCEPTION
				: \PDO::ERRMODE_SILENT
		);
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function getConnection() {
		return $this->connection;
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function isConnected() : bool {
		return ($this->connection) ? true : false;
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function query(string $query) {
		if (!$this->connection) {
			$this->connect();
		}

		$query = $this->correctQuery($query);

		array_push($this->queries, $query);
		$this->result = $this->connection->query($query);
		return $this;
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function fetchAll() {
		if ($this->result) {
			$result = $this->result->fetchAll(\PDO::FETCH_ASSOC);
			$this->result = null;
			return $result;
		}
		else {
			$last_error_msg = $this->connection->errorInfo()[2] ?? 'no error';
			throw new \Exception("Previous query was not performed or resulted in error. Returned error: {$last_error_msg}");
		}
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function getQueriesNumber() : int {
		return count($this->queries);
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function getVersion() {
		return $this->query('SELECT sqlite_version()')->fetchAll()[0];
	}


	/** ----------------------------------------------------------------------------
	 *
	 */

	public function getDbName() {
		return basename($this->file);
	}


	/** ----------------------------------------------------------------------------
	 * Correct query if it comes from MySQL
	 */

	public function correctQuery(string $query) : string {
		return str_replace([
			'AUTO_INCREMENT',
		], [
			'AUTOINCREMENT',
		], $query);
	}
}