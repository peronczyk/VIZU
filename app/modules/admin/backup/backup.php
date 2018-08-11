<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / Backup
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module'); // Security check
}


switch ($router->getLastRequest()) {

	/**
	 * Save & upload
	 */
	case 'save':
	case 'upload':
		$ajax->set('message', 'This option is not available in this application version.');
		break;

	/**
	 * Display layout
	 */
	default:
		require_once __DIR__ . '/backup-display.php';
}