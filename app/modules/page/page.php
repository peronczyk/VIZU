<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Page
#
# ==================================================================================

$tpl = new libs\Template();
$tpl->setTheme(Config::$THEME_NAME);

$template_content = $tpl->getContent('home');
$template_fields  = $tpl->getFields($template_content);


/**
 * If there are codded fields in template get their values from database
 */

if (count($template_fields) > 0) {

	// Get data from database for all fields
	$result = $db->query("SELECT `id`, `content` FROM `fields` WHERE `template` = 'home' AND `language` = '" . $lang->get() . "'");
	$fields_data = Core::processArray($db->fetch($result), 'id');

	if (is_array($fields_data) && count($fields_data) > 0) {

		// Loop over fields from template
		foreach($template_fields as $field_id => $field) {
			if (isset($fields_data[$field_id]['content'])) {
				$tpl->assign([$field_id => $fields_data[$field_id]['content']]);
			}
		}
	}
}


/**
 * Assign common fields that will be available in template
 */

$tpl->assign([
	'site_path'    => $router->site_path . '/',
	'theme_path'   => 'themes/' . Config::$THEME_NAME . '/',
	'app_path'     => Config::$APP_DIR,
	'lang_code'    => $lang->get(),
	'db_connected' => (int)$db->isConnected(),
	'db_queries'   => (int)$db->getQueriesNumber(),
	'fields'       => print_r($template_fields, true), // For debug purposes
]);


/**
 * Parse and display
 */

$parsed_html = $tpl->parse($template_content, $template_fields, $lang->translations);

if (!empty($parsed_html)) {
	echo $parsed_html;
}
else {
	Core::displayError('Parsing function does not return any value.', __FILE__, __LINE__, debug_backtrace());
}