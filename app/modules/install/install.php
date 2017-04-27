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
$user = new libs\User($db);


/**
 * Display errors after actions are made
 */

if ($router->request[1] === 'error') {
	echo libs\Core::common_html_header('VIZU Installer: Error');
	echo '<h3>Error occured</h3><hr>';

	switch (@$_SESSION['vizu_installation_error']) {
		case 1:
			echo '<h1>Installation process error</h1>';
			echo '<h2>Try again. If problem will repeat contact your administrator.</h2>';
			break;

		default:
			echo '<h1>Unknown error occured</h1>';
	}

	echo libs\Core::common_html_footer();

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
		echo libs\Core::common_html_header('VIZU Installer: OK');
		?>
			<h1>VIZU installed <u>correctly</u></h1>
			<h2>Enjoy the simplicity</h2>
			&mdash;&nbsp; <a href="./">Home page</a><br>
			&mdash;&nbsp; <a href="./admin">Administration panel</a>
		<?php
		echo libs\Core::common_html_footer();
	}


	/**
	 * There is no users in database
	 */

	else {

		// Action: Add user to database
		if ($_POST['op'] === 'add_user') {
			$status = false;
			if ($user->verify_password($_POST['password']) && $user->verify_username($_POST['email'])) {
				$result = $db->query("INSERT INTO `users` VALUES ('', '" . $_POST['email'] . "', '" . $user->password_encode($_POST['password']) . "');");
				if ($result) $status = true;
			}

			if ($status) header('location: ' . $router->site_path . '/install');
			else {
				$_SESSION['vizu_installation_error'] = 2;
				header('location: ' . $router->site_path . '/install/error');
			}
		}

		// Show installation step : 2
		else {
			echo libs\Core::common_html_header('VIZU Installer: Set up administrator');
			?>
				<h3>Step 2/2</h3>
				<hr>
				<h1>Set up administrator account</h1>
				<h2>There is no users in database. Add first one.</h2>

				<form action="" method="post">
					<input type="hidden" name="op" value="add_user">
					<label>Login <small>(email address)</small> <input type="email" name="email" required></label>
					<label>Password <small>(min 6 characters)</small> <input type="password" name="password" pattern=".{6,30}" required></label>
					<button type="submit">Add &nbsp;&nbsp;&rsaquo;</button>
				</form>
			<?php
			echo libs\Core::common_html_footer();
		}
	}
}


/**
 * If required tables does not exist in database provide installation option
 * or init the installation process.
 */

else {

	// Action : Put data in to database
	if ($_POST['op'] === 'install') {
		$status = $db->import_file($module_path . 'db.sql');

		if ($status) header('location: ' . $router->site_path . '/install');
		else {
			$_SESSION['vizu_installation_error'] = 1;
			header('location: ' . $router->site_path . '/install/error');
		}
	}

	// Show installation step : 1
	else {
		echo libs\Core::common_html_header('VIZU Installer: Begin installation');
		?>
			<h3>Step 1/2</h3>
			<hr>
			<h1>VIZU is <u>not</u> installed</h1>
			<h2>Proceed installation?</h2>
			<p>All data will be inserted to database "<?php echo Config::$DB_NAME; ?>".</p>

			<form action="" method="post">
				<input type="hidden" name="op" value="install">
				<button type="submit">YES, please &nbsp;&nbsp;&rsaquo;</button>
			</form>
		<?php
		echo libs\Core::common_html_footer();
	}
}
