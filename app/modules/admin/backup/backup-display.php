<?php

# ==================================================================================
#
#	VIZU CMS
#	Module: Admin / Backup / Display
#
# ==================================================================================

if (IN_ADMIN !== true) {
	die('This file can be loaded only in admin module'); // Security check
}


$tpl->setTheme('admin');

$template_content = $tpl->getContent('backup');
$template_fields  = $tpl->getFields($template_content);

$ajax->set('html', $tpl->parse($template_content, $template_fields));