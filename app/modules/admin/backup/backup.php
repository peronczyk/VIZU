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
	 * Create backup
	 */
	case 'create':
		$backup_file = __ROOT__ . '/backup.sql';
		$error_log_file = __ROOT__ . '/mysqldump_error.log';

		try {
			$db->exportDb($backup_file, $error_log_file);
		}
		catch (\Exception $e) {
			$ajax->set('message', $e->getMessage());
		}

		if (file_exists($backup_file)) {
			$ajax->set('message', 'Backup file created: ' . $backup_file);
		}

		break;

	/**
	 * Load backup
	 */
	case 'load':
		$ajax->set('message', 'This option is not available in this application version.');
		break;

	/**
	 * Display layout
	 */
	default:
		require_once __DIR__ . '/backup-display.php';
}