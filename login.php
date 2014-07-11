<?php
// print_r($_POST);
// print_r($_SESSION);
	session_start();
	require("res/php/functions/general.php");
	require("res/php/config.php");
	require("res/php/connect/db.php");
	require("res/php/classes/badmin.php");
	$OBAdmin=new BAdmin($db);
	$OBAdmin->logged_in_protect("index.php");
	$OBAdmin->display_login();
?>
