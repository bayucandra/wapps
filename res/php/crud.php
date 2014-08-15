<?php

// 	ob_start();
	session_start();
	require_once('functions/general.php');
	require_once('config.php');
	require_once("connect/db.php");
	require_once("classes/bcrud.php");

	$OBCrud=new BCrud($db);
	$section=$_REQUEST['section'];
	$crud=$_REQUEST['crud'];
	
	switch($section){
		case "mail":
			$idmail_account=$_SESSION[SESSION_NM]["idmail_account"];
			$subsection=$_REQUEST['subsection'];
			switch($subsection){
				case "inbox":
					$OBCrud->mail_inbox_list($idmail_account);
					break;
			}
			break;
		case "mailbox":
			if($crud=="read"){
				echo $OBCrud->mailbox_list();
			}
			break;
	}/*
	dump_data(ob_get_clean(),"dump.txt");
	ob_end_clean();*/
?>
