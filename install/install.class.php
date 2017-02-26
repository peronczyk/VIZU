<?php

class Install {

	public function show_html_header($title) {
		$code = '<!doctype html>
			<html>
				<head>
					<meta charset="utf-8">
					<title>Visu Instal: ' . $title . '</title>
					<link rel="stylesheet" href="../themes/admin/style/style.css">
					<style type="text/css">
						.wrapper { margin: 40px auto; width: 600px; }
					</style>
				</head>
				<body>
					<div class="wrapper">
		';
		return $code;
	}

	public function show_html_footer() {
		$code = '</div></body></html>';
		return $code;
	}

}