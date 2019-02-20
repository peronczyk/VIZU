<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Lib: User
 *
 * =================================================================================
 */

class User {

	/**
	 * Handle to database controller
	 */
	private $_db;

	/**
	 * User access level. 0 = no access to admin panel
	 */
	private $access = 0;

	/**
	 * Logged in user ID
	 */
	private $id;

	/**
	 * Logged in user email adress (login)
	 */
	private $email;


	/** ----------------------------------------------------------------------------
	 * Constructor & login status check
	 */

	public function __construct(SqlDb $db) {
		$this->_db = $db;

		// Set up access level by checking if user is logged in

		if (!empty($_SESSION['id']) && !empty($_SESSION['password'])) {
			try {
				$result = $this->_db->query("SELECT `email`, `password` FROM `users` WHERE `id` = '{$_SESSION['id']}' LIMIT 1");
				$user_data = $this->_db->fetchAll($result);
			}
			catch (Exception $e) {
				$user_data = [];
			}

			if (count($user_data) > 0) {
				if ($_SESSION['password'] === $user_data[0]['password']) {
					$this->setAccess(1);
					$this->id = $_SESSION['id'];
					$this->email = $user_data[0]['email'];
				}
			}
		}
	}


	/** ----------------------------------------------------------------------------
	 * SETTER : Access level
	 */

	public function setAccess(int $lvl) {
		if ($lvl > -1) {
			$this->access = $lvl;
			return true;
		}
		else {
			return false;
		}
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : User access level
	 */

	public function getAccess() {
		return $this->access;
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : Logged in user email (login)
	 */

	public function getEmail() {
		return $this->email;
	}


	/** ----------------------------------------------------------------------------
	 * GETTER : Logged in user ID
	 */

	public function getId() {
		return $this->id;
	}


	/** ----------------------------------------------------------------------------
	 * Verify login (email address)
	 */

	public static function verifyUsername($username) {
		return (filter_var($username, FILTER_VALIDATE_EMAIL) !== false);
	}


	/** ----------------------------------------------------------------------------
	 * Verify password
	 */

	public static function verifyPassword($password) {
		return strlen($password) > 6;
	}


	/**
	 * Password encode
	 *
	 * @return {string} salted password hash
	 */

	public static function passwordEncode($password) {
		return hash('sha256', $password . \Config::$PASSWORD_SALT);
	}


	/** ----------------------------------------------------------------------------
	 * Try to login user
	 *
	 * @param string $login
	 * @param string $password
	 * @return true|string - Returns true if succes or error text
	 */

	public function login($email, $password) {
		if (empty($email)) {
			throw new Exception("Account login (email) not provided");
		}
		if (empty($password)) {
			throw new Exception("Account password not provided");
		}
		if (!self::verifyUsername($email)) {
			throw new Exception("Provided email address is not correct");
		}

		$result = $this->_db->query('SELECT `id`, `password` FROM `users` WHERE `email` = "' . $email . '" LIMIT 1');
		$user_data = $this->_db->fetchAll($result);

		if (count($user_data) > 0 && self::passwordEncode($password) === $user_data[0]['password']) {
			$this->setAccess(1);
			$_SESSION['id'] = $user_data[0]['id'];
			$_SESSION['password'] = $user_data[0]['password'];
			return true;
		}
		else {
			throw new Exception("Incorrect login details were provided");
		}
	}


	/** ----------------------------------------------------------------------------
	 * Logout user
	 */

	public function logout() {
		unset($_SESSION['id'], $_SESSION['password']);
		$this->setAccess(0);
		return false;
	}


	/** ----------------------------------------------------------------------------
	 * Random password generator
	 * This code generates random "easy" to remember passwords that are built by
	 * letters and 2 digits at the end. One of the letters are uppercase.
	 */

	public static function generatePassword(int $length = 10) {
		// Length paramenter must be a multiple of 2
		if (($length % 2) !== 0) {
			$length++;
		}

		// Make room for the two-digit number on the end
		$length = $length - 2;

		$conso = ['b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z'];
		$vocal = ['a','e','i','o','u'];

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
		$password .= rand(10, 99);

		return $password;
	}
}