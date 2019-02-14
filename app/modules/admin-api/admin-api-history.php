<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin / History
 *
 * =================================================================================
 */

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module');
}


// Get data from database for all fields

$result = $db->query("SELECT `id`, `modified` FROM `fields` WHERE `template` = 'home'");
$fields_data = $core->processArray($db->fetchAll($result), 'id');


// Get fields from home of user template

$tpl->setTheme(Config::$THEMES_DIR . Config::$THEME_NAME);
$content            = $tpl->getContent('home');
$template_fields    = $tpl->getFields($content);
$fields_data_simple = [];


// Prepare arrays to sort them

foreach($fields_data as $key => $val) {
	if (isset($template_fields[$key])) {
		$fields_data_simple[$key] = $val['modified'];
	}
}

// Sort array by date

arsort($fields_data_simple);

$rest_store->set('history', $fields_data_simple);