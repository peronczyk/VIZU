<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin API
 *
 * =================================================================================
 */

// Security constant. Needs to be checked in all included files
define('IN_ADMIN_API', true);

$user          = new User($db);
$rest_store    = new RestStore();
$admin_actions = new AdminActions($user);

$requested_submodule = $router->getRequestChunk(1);

switch($requested_submodule) {
	case 'status':
		require 'admin-api-status.php';
		break;

	case 'users':
		require 'admin-api-users.php';
		break;

	case 'content':
		require 'admin-api-content.php';
		break;

	case 'history':
		require 'admin-api-history.php';
		break;

	case 'backup':
		require 'admin-api-backup.php';
		break;


	// UNKNOWN REQUEST

	default:
		$rest_store->set('error', [
			'str'  => "Unknown submodule '{$requested_submodule}'",
			'file' => __FILE__,
			'line' => __LINE__
		]);
}

$rest_store->output();