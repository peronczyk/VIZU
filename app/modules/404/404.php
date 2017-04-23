<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: 404 error page
#
# ==================================================================================

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$tpl = $core->load_lib('Template');
$tpl->set_theme(Config::THEME_NAME);

if ($tpl->get_template_path('404')) {
	$template_content	= $tpl->get_content('404');
	$template_fields	= $tpl->get_fields($template_content);

	$tpl->assign(array(
		'site_path'		=> $router->site_path . '/',
		'theme_path'	=> 'themes/' . Config::THEME_NAME . '/',
		'app_path'		=> Config::APP_DIR
	));

	echo $tpl->parse($template_content, $template_fields, $lang->translations);
}

else {
	echo '<h1>404 Not Found</h1>';
	echo '<p>The page that you have requested could not be found.</p>';
}