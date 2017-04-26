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

	// This option disables all error notifications if set to 'true'
	private $silent_mode = false;


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
		$connection = new \mysqli($this->host, $this->user, $this->pass, $this->name);
		$mysqli_errno = mysqli_connect_errno();

		// If connection was established
		if ($mysqli_errno === 0) {
			$this->connection = $connection;
			$this->connected = true;

			mysqli_set_charset($this->connection, 'utf8');
		}

		// If there was problem with connection
		else {
			$err_numbers = array(
				1044 => 'Access danied for provided user',
				1045 => 'Access danied for provided password',
				1049 => 'Unknown database',
				2002 => 'Unable to connect database - provided host didn\'t answare',
			);

			if (isset($err_numbers[$mysqli_errno])) {
				$mysqli_err_txt = $err_numbers[$mysqli_errno];
			}
			else $mysqli_err_txt = 'Unknown error';

			Core::error('Unable to connect to MySQL database. Returned error:<br>' . $mysqli_err_txt . ' [' . mysqli_connect_errno() . '].<br>Check if application is installed propertly by going to "install/" address in your browser.', __FILE__, __LINE__, debug_backtrace());
		}
	}


	/**
	 * Enable silent mode
	 */

	public function enable_silent_mode() {
		return $this->silent_mode = true;
	}


	/**
	 * GETTER : Database connection handle
	 */

	public function get_conn() {
		return $this->connection;
	}


	/**
	 * Query
	 */

	public function query($query) {
		if (!$this->connection) $this->connect();

		$result = $this->connection->query($query);
		$this->queries_count++;

		if ($result === false && !$this->silent_mode) {
			Core::error('Database query failed. Returned error:<br>' . mysqli_error($this->connection), __FILE__, __LINE__, debug_backtrace());
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
		return $this->connection ? 1 : 0;
	}


	/**
	 * GETTER : Number of performed queries
	 */
	
	public function get_queries_count() {
		return $this->queries_count;
	}

}