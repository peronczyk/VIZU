<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / User
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module');
}

define('PASSWORD_MIN_CHARS', 5);

switch($router->getLastRequest()) {

	/**
	 * Password change operation
	 */
	case 'change_password';
		require_once __DIR__ . '/user-change-password.php';
		break;


	/**
	 * Add user
	 */
	case 'user_add':
		require_once __DIR__ . '/user-add.php';
		break;


	/**
	 * Display page
	 */
	default:
		require_once __DIR__ . '/user-display.php';
}