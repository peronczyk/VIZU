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
 * Display error after performing action that fails
 */

if ($router->request[1] === 'error') {
	echo libs\Core::common_html_header('VIZU Installer: Error');
	echo '<h3>Error occured</h3><hr>';

	switch (@$_SESSION['vizu']['install']['error']) {
		case 1:
			echo '<h1>Installation process error</h1>';
			echo '<h2>Creation of database tables and their content failed.</h2>';
			break;

		case 2:
			echo '<h1>Administrator creation error</h1>';
			echo '<h2>Application culdn\'t insert user to database table.</h2>';
			echo '<p>Probably provided e-mail or password didn\' match the requirements.</p>';
			break;

		default:
			echo '<h1>Unknown error occured</h1>';
			echo '<p>Error code: <strong>' . $_SESSION['vizu_installation_error'] . '</strong></p>';
	}

	if (!empty ($_SESSION['vizu']['install']['message'])) {
		echo '<p>Returned error message:<br>' . $_SESSION['vizu']['install']['message'] . '</p>';
	}
	echo '<p><a href="./">&lsaquo; &nbsp; Back</a></p>';
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
				$result = $db->query("INSERT INTO `users` (email, password) VALUES ('" . $_POST['email'] . "', '" . $user->password_encode($_POST['password']) . "');");
				if ($result) $status = true;
			}

			if ($status) header('location: ' . $router->site_path . '/install');
			else {
				$_SESSION['vizu']['install'] = array(
					'error' => 2,
					'message' => $db->get_conn()->error
				);
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
			$_SESSION['vizu']['install'] = array(
				'error' => 1,
				'message' => $db->get_conn()->error
			);
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
			<p>All data will be inserted to database "<?php echo $db->get_db_name(); ?>".</p>

			<form action="" method="post">
				<input type="hidden" name="op" value="install">
				<button type="submit">YES, please &nbsp;&nbsp;&rsaquo;</button>
			</form>
		<?php
		echo libs\Core::common_html_footer();
	}
}
