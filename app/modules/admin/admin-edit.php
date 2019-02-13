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

/** --------------------------------------------------------------------------------
 * Common operations
 */

// Check wchich language is selected in this form

$active_lang = (!empty($router->query['language']) && strlen($router->query['language']) == 2)
	? $router->query['language']
	: $lang->getActiveLangCode();

	$ajax->add('log', 'Active language: ' . $active_lang);


// Get data from database for all fields

$result = $db->query("SELECT * FROM `fields` WHERE `template` = 'home' AND `language` = '{$active_lang}'");
$fields_data = $core->processArray($db->fetchAll($result), 'id');


// Get fields from home of user template

$tpl->setTheme(Config::$THEME_NAME);
$content         = $tpl->getContent('home');
$template_fields = $tpl->getFields($content);


/** --------------------------------------------------------------------------------
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
			$ajax->add('log', 'Try to modify field: ' . $post_key . '. Result: ' . ($result ? '0' : '1'));
			if ($result) {
				$num_changes++;
			}
		}
	}

	$ajax->set(
		'message',
		"Changes saved: {$num_changes}"
	);
}


/** --------------------------------------------------------------------------------
 * Display form
 */

else {

	// Get languages
	$languages = $lang->getList();
	$lang_str  = '';

	// Loop over languages received from DB and set the active
	foreach($languages as $lang) {
		$active_class = ($active_lang == $lang['code']) ? ' class="active"' : '';
		$lang_str .= "<li{$active_class}><a href='admin/edit?language={$lang['code']}'>{$lang['short_name']}</a></li>";
	}

	// Stores array of loaded field's classes
	$fields_container = new Fields\Container();

	// Stores html of form elements - fields
	$form_fields = null;

	// Stores numbers of each field types
	$field_num = [];

	// Stores number of skiped fields (not editable fields or with errors)
	$skiped_fields = [];

	/**
	 * Loop over fields that was found in the template file
	 */
	foreach($template_fields as $field_id => $field) {

		// Skip if field don't have 'type' or it's 'type' is not editable
		if (!isset($field['type']) || !in_array($field['type'], Config::$EDITABLE_FIELD_TYPES)) {
			$ajax->add('log', "Skipped: {$field_id} / {$field['type']}");
			$skiped_fields++;
			continue;
		}

		// Count how many fields of this type occured in parsed document
		if (!isset($field_num[$field['type']])) {
			$field_num[$field['type']] = 1;
		}
		else {
			$field_num[$field['type']]++;
		}

		// If field don't have name in template skip it
		if (empty($field['name'])) {
			$ajax->add('log', "There is no name for field {$field_id} [{$field_num[$field['type']]}]");
			$skiped_fields++;
			continue;
		}

		// If any data of this field was in DB set it to display
		$content = null;
		if (isset($fields_data[$field_id])) {
			$content = $fields_data[$field_id]['content'];

			// Unset field data to check which of them don't exist in the template
			unset($fields_data[$field_id]);
		}

		// Add to form data about this field
		$form_fields .= $fields_container->getFieldClass($field['type'])->fieldHtml($field_id, $field, $content);
	}

	if ($skiped_fields > 0) {
		$ajax->add('log', 'Skipped fields: ' . count($skiped_fields));
	}

	// Display other fields, that was in the database but don't exists in template
	$not_used_fields_num = 0;
	foreach($fields_data as $data_key => $data) {
		$not_used_fields_num++;
	}

	$tpl->assign([
		'languages'      => $lang_str,
		'active_lang'    => $active_lang,
		'fields'         => $form_fields,
		'skipped_fields' => $not_used_fields_num,
	]);


	/**
	 * Display layout
	 */

	$tpl->setTheme('admin');

	$template_content = $tpl->getContent('edit');
	$template_fields  = $tpl->getFields($template_content);
	$parsed_html      = $tpl->parse($template_content, $template_fields);

	if (empty($parsed_html)) {
		$json->set('error', [
			'str'  => 'Parsing function does not return any value.',
			'file' => __FILE__,
			'line' => __LINE__
		]);
	}
	else {
		$ajax->set('html', $parsed_html);
	}
}