<?php

if (IN_ADMIN !== true) die('This file can be loaded only in admin'); // Security check


// Get data from database for all fields

$result = $db->query("SELECT `id`, `modified` FROM `fields` WHERE `template` = 'home'");
$fields_data = $core->process_array($db->fetch($result), 'id');


// Get fields from home of user template

$tpl->set_theme(Config::THEME_NAME);
$content			= $tpl->get_content('home');
$template_fields	= $tpl->get_fields($content);


// Prepare arrays to sort them

foreach($fields_data as $key => $val) {
	if (isset($template_fields[$key])) {
		$fields_data_simple[$key] = $val['modified'];
	}
}

// Sort array by date

arsort($fields_data_simple);


// Prepare array ready to display

$display_str = '';

foreach($fields_data_simple as $key => $val) {
	if (!isset($template_fields[$key]['name']) || !isset($template_fields[$key]['category'])) continue;

	$date = explode(' ', $val);
	switch($template_fields[$key]['category']) {
		case 'text':	$category = 'Zawartość'; break;
		case 'setting':	$category = 'Ustawienia'; break;
		default:		$category = null; break;
	}
	$display_str .= '<tr><td>' . $category . '</td><td>' . $template_fields[$key]['name'] . '</td><td>' . $date[0] . '</td><td>' . $date[1] . '</td></tr>';
}


$tpl->assign(array(
	'history' => $display_str
));

// Display layout

$tpl->set_theme('admin');

$template_content	= $tpl->get_content('history');
$template_fields	= $tpl->get_fields($template_content);

$ajax->set('html', $tpl->parse($template_content, $template_fields));