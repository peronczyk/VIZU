<?php

/*
Needs to be rewriten to write queries this way:
$db
	->where(array('id' => 3, 'val' => 'test'))
	->update('table', array('name' => 'lorem'));

or this way:

$db->arrayQuery(array(
	'select' => array(
		'what'	=> '*',
		'where'	=> array('id' => 3, 'val' => 'test'),
		'limit'	=> 1
	)
));

*/

class Database {

	private $connection = false;


	# ==============================================================================
	# CONSTRUCTOR
	# ==============================================================================

	public function __construct() {
	}


	# ==============================================================================
	# CONNECT
	# ==============================================================================

	public function connect($host, $user, $pass, $database) {

		$connection = @new mysqli($host, $user, $pass, $database);
		$mysqli_errno = mysqli_connect_errno();

		// If connection was established
		if ($mysqli_errno == 0) {
			$this->connection = $connection;
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

			if (isset($err_numbers[$mysqli_errno])) $mysqli_err_txt = $err_numbers[$mysqli_errno];
			else $mysqli_err_txt = 'Unknown error';

			Core::error('Nie udało się nawiązać połaczenia z bazą danych MySQL. Zwrócony błąd: ' . $mysqli_err_txt . ' [' . mysqli_connect_errno() . ']', __FILE__, __LINE__,  debug_backtrace());
		}
	}


	# ==============================================================================
	# RETURN DATABASE CONNECTION HANDLE
	# ==============================================================================

	public function get_conn() {
		return $this->connection;
	}


	# ==============================================================================
	# QUERY
	# ==============================================================================

	public function query($query) {
		$result = $this->connection->query($query);
		if ($result === false) {
			Core::error('Zapytanie do bazy danych nie powiodło się. Zwrócony bład: ' . mysqli_error($this->connection), __FILE__, __LINE__, debug_backtrace());
		}
		else return $result;
	}


	# ==============================================================================
	# FETCH RESULTS
	# ==============================================================================

	public function fetch($result) {
		if (is_object($result) && method_exists($result, 'fetch_assoc')) {
			$arr = array();
			while ($row = $result->fetch_assoc()) $arr[] = $row;
			return $arr;
		}
		else return false;
	}

}