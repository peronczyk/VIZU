<?php

// Tu zaczynają się totalne heble. To trzeba napisać po ludzku. Póki co jest jak jest

// Configuration

define('DB_FILE', 'db.sql');
if (!file_exists(Config::$INSTALL_DIR . DB_FILE)) {
	Core::error('Missing required default database dump file: "db.sql"', __FILE__, __LINE__, debug_backtrace());
}

require_once(Config::$INSTALL_DIR . 'install.class.php');
$install = new Install();

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
}
