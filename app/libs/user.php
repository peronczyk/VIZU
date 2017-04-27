<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: User
#
# ==================================================================================

namespace libs;

class User {

	private $_db;			// Handle to database controller
	private $access = 0;	// User access level. 0 = no access to admin panel
	private $id;			// Logged in user ID


	/**
	 * Constructor & login status check
	 */

	public function __construct($db) {
		// Check if variable passed to this class is database controller
		if ($db && is_object($db) && is_a($db, __NAMESPACE__ . '\Database')) $this->_db = $db;
		else Core::error('Variable passed to class "User" is not correct "Database" object', __FILE__, __LINE__, debug_backtrace());

		// Set up access level by checking if user is logged in

		if (!empty($_SESSION['login']) && !empty($_SESSION['password'])) {

			if (!$this->verify_username($_SESSION['login'])) return; // If username stored in session is invalid

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


	/**
	 * SETTER : Access level
	 */

	public function set_access($lvl) {
		if (is_int($lvl) && $lvl > -1) {
			$this->access = $lvl;
			return true;
		}
		else return false;
	}


	/**
	 * GETTER : User access level
	 */

	public function get_access() {
		return $this->access;
	}


	/**
	 * GETTER : Logged in user ID
	 */

	public function get_id() {
		return $this->id;
	}


	/**
	 * Verify login (email address)
	 */

	public function verify_username($username) {
		return filter_var($username, FILTER_VALIDATE_EMAIL);
	}


	/**
	 * Verify password
	 */

	public function verify_password($password) {
		return strlen($password) > 6;
	}


	/**
	 * Password encode
	 *
	 * @return {string} salted password hash
	 */

	public function password_encode($password) {
		return hash('sha256', $password . \Config::$PASSWORD_SALT);
	}


	/**
	 * Try to login user
	 *
	 * @param string $login
	 * @param string $password
	 * @return true|string - Returns true if succes or error text
	 */

	public function login($login, $password) {
		if (empty($login)) return 'Account login (email) not provided';
		if (empty($password)) return 'Account password not provided';
		if (!$this->verify_username($login)) return 'Provided email address is not correct';

		$result = $this->_db->query('SELECT `password` FROM `users` WHERE `email` = "' . $login . '" LIMIT 1');
		$user_data = $this->_db->fetch($result);

		if (count($user_data) > 0 && $this->password_encode($password) == $user_data[0]['password']) {
			$this->access = 1;
			$_SESSION['login']		= $login;
			$_SESSION['password']	= $user_data[0]['password'];
			return true;
		}
		else return 'Incorrect login details were provided';
	}


	/**
	 * Logout user
	 */

	public function logout() {
		unset($_SESSION['login'], $_SESSION['pass']);
		$this->access = 0;
		return false;
	}
}
