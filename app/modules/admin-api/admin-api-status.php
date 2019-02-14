<?php

$rest_store->set('user-access', $user->getAccess());

if ($user->getAccess() > 0) {
	$rest_store->merge([
		'app-version'   => VIZU_VERSION,
		'php-version'   => phpversion(),
		'site-name'     => Config::$SITE_NAME,
		'language-code' => $lang->getActiveLangCode()
	]);
}