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

		$exec_format = 'mysqldump --user=%s --password=%s --host=%s --log-error=mysqldump_error.log %s > %s';
		$exec_command = sprintf($exec_format, $db_config['user'], $db_config['pass'], $db_config['host'], $db_config['name'], $backup_file);

		exec(
			$exec_command,
			$exec_output,
			$exec_status
		);

		if ($exec_status === 0) {
			$ajax->set('message', 'Backup creation succes');
			// header("Content-Disposition: attachment; filename=\"" . basename($file_path) . "\"");
			// header("Content-Type: application/force-download");
			// header("Content-Length: " . filesize($file_path));
			// header("Connection: close");
			// readfile($file_path);
		}
		else {
			$ajax->set('message', 'Backup creation failed. Probably your server does not recognize "mysqldump" command. Returned output: ' . print_r($exec_output, true));
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