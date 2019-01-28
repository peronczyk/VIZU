<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / Content / Save
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module'); // Security check
}


$ajax->add('log', 'Saving started');

$query_common_where = "`template` = 'home' AND `language` = '" . $active_lang . "'"; // String used almost in all queries as WHERE
$num_changes = 0; // Count changes that was made

// Loop aver all POST fields that was sent by form
foreach($_POST as $post_key => $post_val) {

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
		$result = $db->query("INSERT INTO `fields` (template, language, id, content, created, modified, version) VALUES ('home', '" . $active_lang . "', '" . $post_key . "', '" . $post_val . "', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1');");

		if ($result) {
			$ajax->add('log', 'Field "' . $post_key . '" created');
			$num_changes++;
		}
		else {
			$ajax->add('log', 'Field "' . $post_key . '" creation failed. Query error: ' . $db->getConn()->error);
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
	$ajax->set('message', 'No changes have been made in the form.');
}
else {
	$ajax->add('log', 'Changes made: ' . $num_changes);
	$ajax->set('message', 'Changes saved.');
}