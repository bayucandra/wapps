<?php
/*
	$path_relative=".";
	if(isset($_SESSION["root_path"])){
		require_once($_SESSION[SESSION_NM]['root_path'].'/res/php/config.php');
		require_once($_SESSION[SESSION_NM]['root_path']."/res/php/functions/general.php");
	}else{//For CRUD and another purpose must include 'config.php' file at the relative path
		require_once($path_relative.'/functions/general.php');
		require_once($path_relative.'/config.php');
	}*/
	

	$db_config=unserialize(MYSQL_CONFIG);
	$db_dsn="mysql:host=".$db_config['host'].";dbname=".$db_config['db_name'];
	$db=false;
	try{
		$db = new PDO($db_dsn,$db_config['username'],$db_config['password']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		die("Connection failed: ".$e->getMessage());
	}
?>
