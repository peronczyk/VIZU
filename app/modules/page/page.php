<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Page
 *
 * =================================================================================
 */

$source_template_name = 'home';
$template_file = __ROOT__ . '/' . Config::$THEMES_DIR . Config::$THEME_NAME . '/templates/' . $source_template_name . '.html';
$home_page_template = new Template($template_file);
$template_fields = $home_page_template->getTemplateFields();


/**
 * If there are coded fields in template get their values from database
 */

if (count($template_fields) > 0) {

	// Get data from database for all fields
	$result = $db->query("SELECT `id`, `content` FROM `fields` WHERE `template` = 'home' AND `language` = '{$lang->getActiveLangCode()}'");
	$fields_unsorted_data = $db->fetchAll($result);
	$fields_data = Template::setArrayKeysAsIds($fields_unsorted_data);

	if (is_array($fields_data) && count($fields_data) > 0) {
		$field_handlers = new FieldHandlersWrapper($home_page_template, $dependency_container);
		$field_handlers->preParse($fields_data);

		// Loop over fields from template
		foreach ($template_fields as $field) {
			$field_id = $field['props']['id'] ?? null;
			if (isset($fields_data[$field_id]['content'])) {
				$home_page_template->assign([$field_id => $fields_data[$field_id]['content']]);
			}
		}
	}
}


/**
 * Assign common fields that will be available in template
 */

$home_page_template->assign([
	'site_path'    => $router->site_path . '/',
	'theme_path'   => Config::$THEMES_DIR . Config::$THEME_NAME . '/',
	'app_path'     => Config::$APP_DIR,
	'lang_code'    => $lang->getActiveLangCode(),
	'db_connected' => (int)$db->isConnected(),
	'db_queries'   => (int)$db->getQueriesNumber(),
]);


/**
 * Parse and display
 */

$parsed_html = $home_page_template->parse($lang->getTranslations());

if (!empty($parsed_html)) {
	echo $parsed_html;
}
else {
	Core::error('Parsing function does not return any value.', __FILE__, __LINE__, debug_backtrace());
}