<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / Backup
#
# ==================================================================================

if (IN_ADMIN !== true) die('This file can be loaded only in admin module'); // Security check


// Display layout

if (empty($router->request[2])) {

	$tpl->set_theme('admin');

	$template_content	= $tpl->get_content('backup');
	$template_fields	= $tpl->get_fields($template_content);

	$ajax->set('html', $tpl->parse($template_content, $template_fields));

}

else {
	$ajax->set('message', 'Opcja wykonywania oraz wczytywania kopii bezpieczeństwa nie jest aktywna w tej wersji aplikacji.');
}