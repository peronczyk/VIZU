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

if (!$core->isDev() && !Core::isAjaxRequest()) {
	die('Only API requests');
}

$user = new User($db);
$ajax = new Ajax();

$requested_submodule = $router->getRequestChunk(1);

switch($requested_submodule) {
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
		$ajax->set('error', [
			'str'  => "Unknown submodule '{$requested_submodule}'",
			'file' => __FILE__,
			'line' => __LINE__
		]);
}

$ajax->send();