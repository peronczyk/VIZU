<?php

class User {

	private $_db;			// Handle to database controller
	private $access = 0;	// User access level. 0 = no access to admin panel
	private $id;			// Logged in user ID


	# ==============================================================================
	# CHECK IF LOGGED IN
	# ==============================================================================

	public function __construct($db) {
		// Check if variable passed to this class is database controller
		if ($db && is_object($db) && is_a($db, 'Database')) $this->_db = $db;
		else Core::error('Variable passed to class "User" is not correct "Database" object', __FILE__, __LINE__, debug_backtrace());

		// Set up access level by checking if user is logged in

		if (!empty($_SESSION['login']) && !empty($_SESSION['password'])) {

			if (!self::verify_username($_SESSION['login'])) return; // If username stored in session is invalid

			$result = $this->_db->query('SELECT `id`, `password` FROM `users` WHERE `email` = "' . $_SESSION['login'] . '" LIMIT 1');
			$user_data = $this->_db->fetch($result);

			if (count($user_data) > 0) {
				if ($_SESSION['password'] == $user_data[0]['password']) {
					$this->set_access(1);
					$this->id = $user_data[0]['id'];
				}
			}
		}
	}


	# ==============================================================================
	# VERIFY LOGIN FOR SECURITY REASONS
	# ==============================================================================

	public static function verify_username($username) {

		// @TODO : Trzeba dopisać kod weryfikujący czy ktoś nie próbuje wstrzyknąć złośliwego kodu jako swój login w sesji
		// Najlepiej przez preg

		return true;
	}


	# ==============================================================================
	# SET LEVEL OF ACCESS
	# ==============================================================================

	public function set_access($lvl) {
		if (is_int($lvl) && $lvl > -1) {
			$this->access = $lvl;
			return true;
		}
		else return false;
	}


	# ==============================================================================
	# GET LEVEL OF ACCESS
	# ==============================================================================

	public function get_access() {
		return $this->access;
	}


	# ==============================================================================
	# GET LOGGED IN USER ID
	# ==============================================================================

	public function get_id() {
		return $this->id;
	}


	# ==============================================================================
	# PASSWORD CODE
	# ==============================================================================

	public static function password_encode($password) {
		return hash('sha256', $password . Config::PASSWORD_SALT);
	}


	# ==============================================================================
	# USER LOGIN
	# ==============================================================================

	public function login($login, $password) {
		if (empty($login))					return 'Nie podałeś swojego loginu';
		if (empty($password))				return 'Nie podałeś swojego hasła';
		if (!self::verify_username($login))	return 'Podany login zawiera niedozwolone znaki';

		if ($this->_db) {
			$result = $this->_db->query('SELECT `password` FROM `users` WHERE `email` = "' . $login . '" LIMIT 1');
			$user_data = $this->_db->fetch($result);

			if (count($user_data) > 0 && self::password_encode($password) == $user_data[0]['password']) {
				$this->access = 1;
				$_SESSION['login']		= $login;
				$_SESSION['password']	= $user_data[0]['password'];
				return true;
			}
			else return 'Podałeś niepoprawne dane logowania';
		}
		else return 'Brak możliwości pobrania danych z bazy danych';
	}


	# ==============================================================================
	# USER LOGOUT
	# ==============================================================================

	public function logout() {
		unset($_SESSION['login'], $_SESSION['pass']);
		$this->access = 0;
		return false;
	}
}
