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
	private $email;			// Logged in user email adress (login)


	/**
	 * Constructor & login status check
	 */

	public function __construct($db) {
		// Check if variable passed to this class is database controller
		if ($db && is_object($db) && is_a($db, __NAMESPACE__ . '\Database')) $this->_db = $db;
		else Core::error('Variable passed to class "User" is not correct "Database" object', __FILE__, __LINE__, debug_backtrace());

		// Set up access level by checking if user is logged in

		if (!empty($_SESSION['id']) && !empty($_SESSION['password'])) {
			$result = $this->_db->query('SELECT `email`, `password` FROM `users` WHERE `id` = "' . (int)$_SESSION['id'] . '" LIMIT 1', true);
			$user_data = $this->_db->fetch($result);

			if (count($user_data) > 0) {
				if ($_SESSION['password'] === $user_data[0]['password']) {
					$this->set_access(1);
					$this->id = $_SESSION['id'];
					$this->email = $user_data[0]['email'];
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
	 * GETTER : Logged in user email (login)
	 */

	public function get_email() {
		return $this->email;
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

	public function login($email, $password) {
		if (empty($email)) return 'Account login (email) not provided';
		if (empty($password)) return 'Account password not provided';
		if (!$this->verify_username($email)) return 'Provided email address is not correct';

		$result = $this->_db->query('SELECT `id`, `password` FROM `users` WHERE `email` = "' . $email . '" LIMIT 1');
		$user_data = $this->_db->fetch($result);

		if (count($user_data) > 0 && $this->password_encode($password) === $user_data[0]['password']) {
			$this->set_access(1);
			$_SESSION['id'] = $user_data[0]['id'];
			$_SESSION['password'] = $user_data[0]['password'];
			return true;
		}
		else return 'Incorrect login details were provided';
	}


	/**
	 * Logout user
	 */

	public function logout() {
		unset($_SESSION['id'], $_SESSION['password']);
		$this->set_access(0);
		return false;
	}


	/**
	 * Password generator
	 * This code generates random "easy" to remember passwords that are built by
	 * letters and 2 digits at the end. One of the letters are uppercase.
	 */

	public function generate_password($length = 10) {
		// Length paramenter must be a multiple of 2
		if (($length % 2) !== 0) $length++;

		// Make room for the two-digit number on the end
		$length = $length - 2;

		$conso = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z');
		$vocal = array('a','e','i','o','u');

		$password = '';
		srand((double)microtime() * 1000000);
		$max = $length / 2;

		for ($i = 1; $i <= $max; $i++){
			$password .= $conso[rand(0,19)];
			$password .= $vocal[rand(0,4)];
		}

		// Uppercase one random letter
		$uppercase_letter = rand(0, $length - 1);
		$password[$uppercase_letter] = strtoupper($password[$uppercase_letter]);

		// Add two digits at the end
		$password .= rand(10,99);

		return $password;
	}
}
