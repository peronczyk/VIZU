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

	/** ----------------------------------------------------------------------------
	 * Save data
	 */

	case 'save':
		$admin_actions->requireAdminAccessRights();

		$rest_store->set('post', $_POST);

		$query_common_where = "`template` = '{$source_template_name}' AND `language` = '{$active_lang}'"; // String used almost in all queries as WHERE
		$num_changes = 0; // Count changes that was made

		// Loop aver all POST fields that was sent by form
		foreach ($_POST as $post_key => $post_val) {

			// Stop if post value is empty
			if (empty($post_val)) {
				continue;
			}

			// Stop if post field does not exists in template
			if (!is_array($template_fields[$post_key])) {
				continue;
			}

			// If field doesn't exist create it
			if (!isset($fields_data[$post_key])) {
				/** @todo line below should be put outside the loop */
				$result = $db->query("INSERT INTO `fields` (template, language, id, content, created, modified, version) VALUES ('home', '{$active_lang}', '{$post_key}', '{$post_val}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1');");

				if ($result) {
					$num_changes++;
				}
				else {
					$rest_store->merge(['errors' => ['message' => "Field '{$post_key}' creation failed. Query error: {$db->getConnection()->error}"]]);
				}
			}

			// If field exists in database
			elseif ($fields_data[$post_key]['content'] != $post_val) {
				/** @todo line below should be put outside the loop */
				$result = $db->query("UPDATE `fields` SET
					`content` = '" . $post_val . "',
					`modified` = CURRENT_TIMESTAMP
					WHERE " . $query_common_where . " AND `id` = '" . $post_key . "'");

				if ($result) {
					$num_changes++;
				}
				else {
					$rest_store->merge(['errors' => ['message' => "Field '{$post_key}' modification failed. Query error: {$db->getConnection()->error}"]]);
				}
			}
		}

		$rest_store->merge([
			'message' => "Changes saved: {$num_changes}"
		]);

		break;


	/** --------------------------------------------------------------------------------
	 * Return list of content fields
	 */

	case 'list':
		$admin_actions->requireAdminAccessRights();

		// Get languages
		$languages = $lang->getList();

		// Stores array of loaded field's classes
		$field_handlers = new FieldHandlersWrapper($template);

		//$field_handlers->


		echo '<pre>';
		print_r($template->getTemplateFields());

		die();

		// Stores html of form elements - fields
		$form_fields = [];

		// Stores number of skiped fields (not editable fields or with errors)
		$skipped_fields = [];

		/**
		 * Loop over fields that was found in the template file
		 */
		foreach ($template_fields as $field_id => $field) {

			/**
			 * Skip if field:
			 * 1. Don't have 'type' or it's 'type' is not editable
			 * 2. Don't have 'name'
			 */
			if (!isset($field['type']) || !in_array($field['type'], Config::$EDITABLE_FIELD_TYPES) || empty($field['name'])) {
				$skipped_fields[] = $field;
				continue;
			}

			// Add value of the field taken from the database
			$field['value'] = (isset($fields_data[$field_id]))
				? $fields_data[$field_id]['content']
				: null;

			// Unset field data to check which of them don't exist in the template
			unset($fields_data[$field_id]);

			// Add to form data about this field
			$form_fields[] = $field;
		}

		$rest_store->merge([
			'fields'                  => $form_fields,
			'languages'               => $languages,
			'active-language'         => $active_lang,
			'template-missing-fields' => $fields_data,
			'skipped-fields-num'      => count($skipped_fields),
		]);

		break;
}