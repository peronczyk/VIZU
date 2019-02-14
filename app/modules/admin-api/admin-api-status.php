<?php

$ajax->set('user-access', $user->getAccess());

if ($user->getAccess() > 0) {
	$ajax->set('app-version', VIZU_VERSION);
	$ajax->set('php-version', phpversion());
	$ajax->set('site-name', Config::$SITE_NAME);
}