<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Page
#
# ==================================================================================

$tpl = $core->load_lib('Template');
$tpl->set_theme(Config::THEME_NAME);

$template_content	= $tpl->get_content('home');
$template_fields	= $tpl->get_fields($template_content);


if (count($template_fields) > 0) {

	// Get data from database for all fields
	$result = $db->query("SELECT `id`, `content` FROM `fields` WHERE `template` = 'home' AND `language` = '" . $lang->get() . "'");
	$fields_data = $core->process_array($db->fetch($result), 'id');

	if (is_array($fields_data) && count($fields_data) > 0) {

		// Loop over fields from template
		foreach($template_fields as $field_id => $field) {
			if (isset($fields_data[$field_id]['content'])) {
				$tpl->assign(array($field_id => $fields_data[$field_id]['content']));
			}
		}
	}
}


$tpl->assign(array(
	'site_path'		=> $router->site_path . '/',
	'theme_path'	=> 'themes/' . Config::THEME_NAME . '/',
	'app_path'		=> Config::APP_DIR,
	'fields'		=> print_r($template_fields, true), // For debug purposes
));

$parsed_html = $tpl->parse($template_content, $template_fields, $lang->translations);

if (!empty($parsed_html)) echo $parsed_html;
else Core::error('Parsing function does not return any value.', __FILE__, __LINE__, debug_backtrace());