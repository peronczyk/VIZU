<?php

# ==================================================================================
#
#	VIZU CMS
#	Class: Install
#
# ==================================================================================



// Configuration

define('DB_FILE', 'db.sql');

if (!file_exists(Config::$APP_DIR . '/modules/install/' . DB_FILE)) {
	libs\Core::error('Missing required default database dump file: "db.sql"', __FILE__, __LINE__, debug_backtrace());
}

$install = new modules\install\Install($db);

/**
 * Check if required database tables exists
 */

if ($install->check_db_tables()) {

	/**
	 * Everything is OK
	 */

	if ($install->check_db_users()) {
		echo $install->show_html_header('OK');
		echo '<h1>VIZU installed correctly</h1>';
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

			<form action="?op=add_user" method="post">
				<label>Login (email): <input type="text" name="email"></label>
				<label>Password: <input type="password" name="password"></label>
				<button type="submit">Add</button>
			</form>
		<?php
		echo $install->show_html_footer();
	}
}

else {
	echo 'Shieeet';
}

/*switch($_GET['op']) {
	case 'install':
		break;
}*/
/*
if (empty($_GET['op'])) {
	echo $install->show_html_header('Start');
	echo '<ul>
		<li><a href="?op=install">Install database</a></li>
		<li><a href="?op=password_generate">Generate password hash</a></li>
	</ul>';
	echo $install->show_html_footer();
}

elseif ($_GET['op'] == 'install') {
}

elseif ($_GET['op'] == 'password_generate') {
	echo $install->show_html_header('Start');
	echo '<form method="post">
		<input type="password" name="password">
	</form>';

	if (!empty($_POST['password'])) {
		$user = new User($db);
		echo '<p>' . $user->password_encode($_POST['password']) . '</p>';
	}
	echo $install->show_html_footer();
}*/
