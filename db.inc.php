<?php
$db_options = array(
	'host' => 'localhost',
	'login' => '',
	'password' => '',
	'database' => 'bride',
);

function db_connect($options) {
	$link = mysql_connect($options['host'], $options['login'], $options['password']);
	if (!$link) {
		die('Not connected : ' . mysql_error());
	}

	$db_selected = mysql_select_db($options['database'], $link);
	if (!$db_selected) {
		die ('Can\'t use : ' . mysql_error());
	}
	
	mysql_query("SET NAMES utf8", $link);
	return $link;
}

?>