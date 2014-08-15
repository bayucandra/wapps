<?php
	session_start();
	require_once('functions/general.php');
	require_once('config.php');
	$session=$_SESSION[SESSION_NM];
	$act=$_REQUEST["act"];
	switch($act){
		case "set":
			$key=$_REQUEST["key"];
			$val=$_REQUEST["val"];
			$_SESSION[SESSION_NM][$key] = $val;
			break;
		case "get":
			echo json_encode($session);
			break;
		case "destroy":
			$_SESSION=array();
			session_destroy();
// 			unset($_SESSION);
			break;
	}
?>
