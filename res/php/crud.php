<?php

// 	ob_start();
	require_once('functions/general.php');
	require_once('config.php');
	require_once("connect/db.php");
	require_once("classes/bcrud.php");

	$OBCrud=new BCrud($db);
	$section=$_REQUEST['section'];
	$crud=$_REQUEST['crud'];
	
	switch($section){
		case "mailbox":
			if($crud=="read"){
				echo $OBCrud->mailbox_list();
			}
			break;
	}/*
	dump_data(ob_get_clean(),"dump.txt");
	ob_end_clean();*/
?>
