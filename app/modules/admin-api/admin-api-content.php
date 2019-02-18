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

		$field_handlers = new FieldHandlersWrapper($template, $dependency_container);

		$field_handlers->assignValues();

		$rest_store->merge([
			'fields'                  => $template->getTemplateFields(),
			'languages'               => $languages,
			'active-language'         => $active_lang,
		]);

		break;
}