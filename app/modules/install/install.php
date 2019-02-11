<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Module: Install
 *
 * =================================================================================
 */


// Configuration

define('DB_FILE', 'db.sql');

if (!file_exists(__DIR__ . '/' . DB_FILE)) {
	Core::error('Missing required default database dump file: "db.sql"', __FILE__, __LINE__, debug_backtrace());
}

require __DIR__ . '/install.class.php';
$install = new Install($db);
$user = new User($db);


/**
 * Display error after performing action that fails
 */

if ($router->getRequestChunk(1) === 'error') {
	echo libs\Core::commonHtmlHeader('VIZU Installer: Error');
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
	echo Core::commonHtmlFooter();

	$_SESSION['vizu_installation_error'] = false;
}


/**
 * Check if required database tables exists
 */

elseif ($install->checkDbTables()) {

	/**
	 * Everything is OK
	 */

	if ($install->checkDbUsers()) {
		echo Core::commonHtmlHeader('VIZU Installer: OK');
		?>
			<h1>VIZU installed <u>correctly</u></h1>
			<h2>Enjoy the simplicity</h2>
			&mdash;&nbsp; <a href="./">Home page</a><br>
			&mdash;&nbsp; <a href="./admin">Administration panel</a>
		<?php
		echo Core::commonHtmlFooter();
	}


	/**
	 * There is no users in database
	 */

	else {

		// Action: Add user to database
		if (isset($_POST['op']) && $_POST['op'] === 'add_user') {
			$status = false;
			if (User::verifyPassword($_POST['password']) && User::verifyUsername($_POST['email'])) {
				$result = $db->query("INSERT INTO `users` (email, password) VALUES ('" . $_POST['email'] . "', '" . User::passwordEncode($_POST['password']) . "');");
				if ($result) {
					$status = true;
				}
			}

			if ($status) {
				header('location: ' . $router->site_path . '/install');
			}
			else {
				$_SESSION['vizu']['install'] = [
					'error' => 2,
					'message' => $db->getConnection()->error
				];
				header('location: ' . $router->site_path . '/install/error');
			}
		}

		// Show installation step : 2
		else {
			echo Core::commonHtmlHeader('VIZU Installer: Set up administrator');
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
			echo Core::commonHtmlFooter();
		}
	}
}


/**
 * If required tables does not exist in database provide installation option
 * or init the installation process.
 */

else {

	// Action : Put data in to database
	if (isset($_POST['op']) && $_POST['op'] === 'install') {
		$db_install_file = 'db.sql';

		try {
			$status = $install->importSqlFile(__DIR__ . '/' . $db_install_file);
		}
		catch (Exception $e) {
			Core::error($e->getMessage(), __FILE__, __LINE__, debug_backtrace());
		}

		if ($status) {
			header('location: ' . $router->site_path . '/install');
		}
		else {
			$_SESSION['vizu']['install'] = [
				'error'   => 1,
				'message' => $db->getConnection()->error
			];
			header('location: ' . $router->site_path . '/install/error');
		}
	}

	// Show installation step : 1
	else {
		echo Core::commonHtmlHeader('VIZU Installer: Begin installation');
		?>
			<h3>Step 1/2</h3>
			<hr>
			<h1>VIZU is <u>not</u> installed</h1>
			<h2>Proceed installation?</h2>
			<p>All data will be inserted into <?php echo Config::$DB_TYPE; ?> database "<?php echo $db->getDbName(); ?>".</p>

			<form action="" method="post">
				<input type="hidden" name="op" value="install">
				<button type="submit">YES, please &nbsp;&nbsp;&rsaquo;</button>
			</form>
		<?php
		echo Core::commonHtmlFooter();
	}
}