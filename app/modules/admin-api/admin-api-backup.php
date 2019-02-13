<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Admin / Backup
 *
 * =================================================================================
 */

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module'); // Security check
}

// Display layout

if (empty($router->request[2])) {
	$tpl->setTheme(Config::$APP_DIR . 'admin-panel');

	$template_content = $tpl->getContent('backup');
	$template_fields  = $tpl->getFields($template_content);

	$ajax->set('html', $tpl->parse($template_content, $template_fields));

}

else {
	$ajax->set('message', 'Opcja wykonywania oraz wczytywania kopii bezpieczeństwa nie jest aktywna w tej wersji aplikacji.');
}