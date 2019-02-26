<?php

$rest_store->merge([
	'user-access' => $user->getAccess(),
	'site-name'   => Config::$SITE_NAME,
]);

if ($user->getAccess() > 0) {
	$rest_store->merge([
		'app-version'   => VIZU_VERSION,
		'php-version'   => phpversion(),
		'language-code' => $lang->getActiveLangCode()
	]);
}