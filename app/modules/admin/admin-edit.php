<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin / Edit
 *
 * =================================================================================
 */

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module');
}

/**
 * Common operations
 */

// Check if field types or group was queried
// If yes set up allowed_field_types to view them

if (!empty($router->query['field_category'])) {
	if (in_array($router->query['field_category'], Config::$FIELD_CATEGORIES['content'])) {
		$allowed_field_category = [$router->query['field_category']];
		$tpl->assign(['field_type' => $router->query['field_category']]);
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

$active_lang = (!empty($router->query['language']) && strlen($router->query['language']) == 2)
	? $router->query['language']
	: $lang->getActiveLangCode();

	$ajax->add('log', 'Active language: ' . $active_lang);


// Get data from database for all fields

$result = $db->query("SELECT * FROM `fields` WHERE `template` = 'home' AND `language` = '" . $active_lang . "'");
$fields_data = $core->processArray($db->fetchAll($result), 'id');


// Get fields from home of user template

$tpl->setTheme(Config::$THEME_NAME);
$content         = $tpl->getContent('home');
$template_fields = $tpl->getFields($content);


/**
 * Save data
 */

if ($router->request[count($router->request) - 1] == 'save') {
	$ajax->add('log', 'Saving started');

	$query_common_where = "`template` = 'home' AND `language` = '" . $active_lang . "'"; // String used almost in all queries as WHERE
	$num_changes = 0; // Count changes that was made

	// Loop aver all POST fields that was sent by form
	foreach($_POST as $post_key => $post_val) {

		// Stop if post value is empty
		if (empty($post_val)) continue;

		// Stop if post field does not exists in template
		if (!is_array($template_fields[$post_key])) continue;

		// If field doesn't exist create it
		if (!isset($fields_data[$post_key])) {
			$result = $db->query("INSERT INTO `fields` (template, language, id, content, created, modified, version) VALUES ('home', '" . $active_lang . "', '" . $post_key . "', '" . $post_val . "', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1');");

			if ($result) {
				$ajax->add('log', 'Field "' . $post_key . '" created');
				$num_changes++;
			}
			else {
				$ajax->add('log', 'Field "' . $post_key . '" creation failed. Query error: ' . $db->getConnection()->error);
			}
		}

		// If field exists in database
		elseif ($fields_data[$post_key]['content'] != $post_val) {
			$result = $db->query("UPDATE `fields` SET
				`content` = '" . $post_val . "',
				`modified` = CURRENT_TIMESTAMP
				WHERE " . $query_common_where . " AND `id` = '" . $post_key . "'");
			$ajax->add('log', 'Try to modify field: ' . $post_key . '. Result: ' . $result);
			if ($result) {
				$num_changes++;
			}
		}
	}

	if ($num_changes == 0) {
		$ajax->add('log', 'No changes have been made');
		$ajax->set('message', 'No changes have been made in the form.');
	}
	else {
		$ajax->add('log', 'Changes made: ' . $num_changes);
		$ajax->set('message', 'Changes saved.');
	}
}


/**
 * Display form
 */

else {

	// Get languages
	$result    = $db->query("SELECT * FROM `languages`");
	$languages = $db->fetchAll($result);
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
		$lang_str .= '><a href="admin/edit?language=' . $lang['code'] . '&amp;field_category=' . $router->query['field_category'] . '">' . $lang['short_name'] . '</a></li>';
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
					'str'  => $field_class[$field['type']],
					'file' => __FILE__,
					'line' => __LINE__
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
		$form_fields .= $field_class[$field['type']]->fieldHtml($field_id, $field, $content);
	}

	if ($skiped_fields > 0) $ajax->add('log', 'Skiped fields: ' . $skiped_fields);

	// Display other fields, that was in database but don't exists in template
	$not_used_fields_num = 0;
	foreach($fields_data as $data_key => $data) {
		$not_used_fields_num++;
	}

	if ($not_used_fields_num > 0) {
		$tpl->assign(['other_fields' => 'Fields not used: ' . $not_used_fields_num]);
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

	$template_content = $tpl->getContent('edit');
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
}