<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin / History
 *
 * =================================================================================
 */

if (IN_ADMIN_API !== true) {
	die('This file can be loaded only in admin module');
}

// Select action
switch($router->getRequestChunk(2)) {
	case 'list':
		$admin_actions->requireAdminAccessRights();

		// Get data from database for all fields
		$result = $db->query("SELECT `id`, `modified` FROM `fields` WHERE `template` = 'home'");
		$fields_data = Template::setArrayKeysAsIds($db->fetchAll($result), 'id');

		$history_data = [];

		// Prepare arrays to sort them
		foreach ($fields_data as $key => $val) {
			array_push($history_data, [
				'id' => $key,
				'modified' => $val['modified'],
			]);
		}

		// Sort array by date
		arsort($history_data);

		$rest_store->set('history', $history_data);

		break;
}