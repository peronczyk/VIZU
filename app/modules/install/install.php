<?php

# ==================================================================================
#
#	VIZU CMS
#	Class: Install
#
# ==================================================================================



// Configuration

define('DB_FILE', 'db.sql');

$module_path = Config::$APP_DIR . '/modules/install/';

if (!file_exists($module_path . DB_FILE)) {
	libs\Core::error('Missing required default database dump file: "db.sql"', __FILE__, __LINE__, debug_backtrace());
}

$install = new modules\install\Install($db);


/**
 * Display errors after actions are made
 */

if ($router->request[1] === 'error') {
	echo $install->show_html_header('Error');

	switch (@$_SESSION['vizu_installation_error']) {
		case 1:
			echo '<h1>Installation process error</h1>';
			echo '<h2>Try again. If problem will repeat contact your administrator.</h2>';
			break;
		
		default:
			echo '<h1>Unknown error occured</h1>';
	}

	echo $install->show_html_footer();

	$_SESSION['vizu_installation_error'] = false;
}


/**
 * Check if required database tables exists
 */

elseif ($install->check_db_tables()) {

	/**
	 * Everything is OK
	 */

	if ($install->check_db_users()) {
		echo $install->show_html_header('OK');
		echo '<h1>VIZU installed <u>correctly</u></h1>';
		echo $install->show_html_footer();
	}


	/**
	 * There is no users in database
	 */

	else {
		echo $install->show_html_header('Add first user');
		?>
			<h1>Add first user</h1>
			<h2>There is no users in database. Please add first administrator.</h2>

			<form action="" method="post">
				<input type="hidden" name="op" value="add_user">
				<label>Login (email): <input type="text" name="email"></label>
				<label>Password: <input type="password" name="password"></label>
				<button type="submit">Add</button>
			</form>
		<?php
		echo $install->show_html_footer();
	}
}


/**
 * If there is no required database tables provide installation option
 * or handle the installation.
 */

else {
	if ($_POST['op'] === 'install') {
		$status = $db->import_file($module_path . 'db.sql');

		if ($status) header('location: ' . $router->site_path . '/install');
		else {
			$_SESSION['vizu_installation_error'] = 1;
			header('location: ' . $router->site_path . '/install/error');
		}
	}
	else {
		echo $install->show_html_header('OK');
		?>
			<h1>VIZU <u>not</u> installed correctly</h1>
			<h2>Proceed installation?</h2>
			<form action="" method="post">
				<input type="hidden" name="op" value="install">
				<button type="submit">YES, please</button>
			</form>
		<?php
		echo $install->show_html_footer();
	}
}
