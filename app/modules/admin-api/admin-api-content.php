<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin / Edit
 *
 * =================================================================================
 */

if (IN_ADMIN_API !== true) {
	die('This file can be loaded only in admin module');
}

$source_template_name = 'home';

// Check wchich language is selected in this form
$active_lang = (!empty($router->query['language']) && strlen($router->query['language']) == 2)
	? $router->query['language']
	: $lang->getActiveLangCode();

// Get data from database for all fields
$result = $db->query("SELECT * FROM `fields` WHERE `template` = '{$source_template_name}' AND `language` = '{$active_lang}'");
$fields_data = $core->processArray($db->fetchAll($result), 'id');

// Run template handler
$template_file = __ROOT__ . '/' . Config::$THEMES_DIR . Config::$THEME_NAME . '/templates/' . $source_template_name . '.html';
$template = new Template($template_file);

// Select action
switch($router->getRequestChunk(2)) {

	/** --------------------------------------------------------------------------------
	 * Return list of content fields
	 */

	case 'list':
		$admin_actions->requireAdminAccessRights();

		// Get languages
		$languages = $lang->getList();

		$field_handlers = new FieldHandlersWrapper($template, $dependency_container);

		$field_handlers
			->removeNotEditableFields()
			->assignValues($fields_data);

		$fields_to_return = [];
		foreach ($template->getTemplateFields() as $field) {
			if (isset($field['props'])) {
				array_push($fields_to_return, $field);
			}
		}

		$rest_store->merge([
			'fields'          => $fields_to_return,
			'languages'       => $languages,
			'active-language' => $active_lang,
		]);

		break;


	/** ----------------------------------------------------------------------------
	 * Save data
	 */

	case 'save':
		$admin_actions->requireAdminAccessRights();

		$query_common_where = "`template` = '{$source_template_name}' AND `language` = '{$active_lang}'"; // String used almost in all queries as WHERE
		$changed_field_ids = []; // Count changes that was made

		if (count($_POST) == 0) {
			$rest_store->set('message', "There was no data passed by the form.");
			break;
		}

		foreach ($_POST as $post_key => $post_val) {
			// Add new row to database if field does not exists
			if (!isset($fields_data[$post_key])) {
				$result = $db->query("INSERT INTO `fields` (template, language, id, content, created, modified, version)
					VALUES ('{$source_template_name}', '{$active_lang}', '{$post_key}', '{$post_val}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1');");

				array_push($changed_field_ids, $post_key);
			}

			// Modify existing field's row
			elseif ($post_val != $fields_data[$post_key]['content']) {
				$result = $db->query("UPDATE `fields`
					SET
						`content`  = '{$post_val}',
						`modified` = CURRENT_TIMESTAMP,
						`version`  = '" . ($fields_data[$post_key]['version'] + 1) . "'
					WHERE {$query_common_where} AND `id` = '{$post_key}'");

				array_push($changed_field_ids, $post_key);
			}
		}

		$rest_store->merge([
			'message' => "Changes saved: " . count($changed_field_ids),
			'language' => $active_lang,
		]);
		// $rest_store->set('post', $_POST);
		// $rest_store->set('data', $fields_data);
		// $rest_store->set('message', "Changes saved: " . count($changed_field_ids));

		break;
}