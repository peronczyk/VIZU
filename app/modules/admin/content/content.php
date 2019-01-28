<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / Content
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module');
}


/**
 * Common operations
 */

// Check if field types or group was queried
// If yes set up allowed_field_types to view them

$selected_field_category = $router->getQuery('field_category');

if ($selected_field_category) {
	if (in_array($selected_field_category, Config::$FIELD_CATEGORIES['content'])) {
		$allowed_field_category = [$selected_field_category];
		$tpl->assign(['field_type' => $selected_field_category]);
	}
	else {
		$ajax->set('error', [
			'str'  => 'Selected field type could not be edited from the admin panel or does not exist in the settings.',
			'file' => __FILE__,
			'line' => __LINE__
		]);
		return;
	}
}
else {
	$ajax->set('error', [
		'str'  => 'The type of field you want to edit is not selected.',
		'file' => __FILE__,
		'line' => __LINE__
	]);
	return;
}


// Check wchich language is selected in this form

$active_lang = (strlen($router->getQuery('language')) == 2)
	? $router->getQuery('language')
	: $lang->get();

$ajax->add('log', 'Active language: ' . $active_lang);


// Get data from database for all fields

$result = $db->query("SELECT * FROM `fields` WHERE `template` = 'home' AND `language` = '" . $active_lang . "'");
$fields_data = Core::processArray($db->fetch($result), 'id');


// Get fields from home of user template

$tpl->setTheme(Config::$THEME_NAME);
$content         = $tpl->getContent('home');
$template_fields = $tpl->getFields($content);

switch ($router->getLastRequest()) {
	/**
	 * Save data
	 */
	case 'save':
		require_once __DIR__ . '/content-save.php';
		break;

	/**
	 * Display form
	 */
	default:
		require_once __DIR__ . '/content-display.php';
}