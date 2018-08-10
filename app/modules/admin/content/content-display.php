<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / Edit / Display
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module'); // Security check
}


// Get languages
$result    = $db->query("SELECT * FROM `languages`");
$languages = $db->fetch($result);
$lang_str  = '';

if (count($languages) < 1) {
	$ajax->set('error', [
		'str'  => 'There is no languages set in the database.',
		'file' => __FILE__,
		'line' => __LINE__
	]);
	return;
}

// Loop over languages received from DB and set the active
foreach($languages as $lang) {
	$lang_str .= '<li';
	if ($active_lang == $lang['code']) $lang_str .= ' class="active"';
	$lang_str .= '><a href="admin/edit?language=' . $lang['code'] . '&amp;field_category=' . $router->getQuery('field_category') . '">' . $lang['short_name'] . '</a></li>';
}

$form_fields   = null; // Stores html of form elements - fields
$field_class   = [];   // Stores array of loaded field's classes
$field_num     = [];   // Stores numbers of each field types
$skiped_fields = 0;    // Stores number of skiped fields (not editable fields or with errors)

/**
 * Loop over fields
 */

foreach($template_fields as $field_id => $field) {

	// Skip if field don't have 'category' or it's 'category' is not editable
	if (!isset($field['category']) || !in_array($field['category'], $allowed_field_category)) {
		$skiped_fields++;
		continue;
	}

	// Skip if field don't have 'type' or it's 'type' is not editable
	if (!isset($field['type']) || !in_array($field['type'], Config::$FIELD_TYPES)) {
		$ajax->add('log', 'Skipped: ' . $field_id . ' / ' . $field['category']);
		$skiped_fields++;
		continue;
	}

	// Counting how many fields of this type occured in parsed document
	if (!isset($field_num[$field['type']])) $field_num[$field['type']] = 1;
	else $field_num[$field['type']]++;

	// If field don't have name in template skip it
	if (empty($field['name'])) {
		$ajax->add('log', 'There is no name for field ' . $field_id . ' [' . $field_num[$field['type']] . ']');
		$skiped_fields++;
		continue;
	}

	// Setup field type class if i wasn't started before
	if (!isset($field_class[$field['type']])) {
		$field_class[$field['type']] = $tpl->loadFieldClass($field['type']);

		// If class of field failed to start
		if (!is_object($field_class[$field['type']])) {
			$ajax->set('error', [
				'str'	=> $field_class[$field['type']],
				'file'	=> __FILE__,
				'line'	=> __LINE__
			]);
			continue;
		}
	}

	// If any data of this field was in DB set it to display
	$content = null;
	if (isset($fields_data[$field_id])) {
		$content = $fields_data[$field_id]['content'];

		// Unset field data to check which of them don't exist in the template
		unset($fields_data[$field_id]);
	}

	// Add to form data about this field
	$form_fields .= $field_class[$field['type']]->field_html($field_id, $field, $content);
}

if ($skiped_fields > 0) $ajax->add('log', 'Skiped fields: ' . $skiped_fields);

// Display other fields, that was in database but don't exists in template
$not_used_fields_num = 0;
foreach($fields_data as $data_key => $data) {
	$not_used_fields_num++;
}

if ($not_used_fields_num > 0) {
	$tpl->assign(['other_fields' => 'Pola nie użyte: ' . $not_used_fields_num]);
}
else {
	$tpl->assign(['other_fields' => '']);
}

$tpl->assign([
	'languages'   => $lang_str,
	'active_lang' => $active_lang,
	'fields'      => $form_fields,
]);


/**
 * Display layout
 */

$tpl->setTheme('admin');

$template_content = $tpl->getContent('content');
$template_fields  = $tpl->getFields($template_content);
$parsed_html      = $tpl->parse($template_content, $template_fields);

if (empty($parsed_html)) $json->set('error', [
	'str'  => 'Parsing function does not return any value.',
	'file' => __FILE__,
	'line' => __LINE__
]);
else {
	$ajax->set('html', $parsed_html);
}