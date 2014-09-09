<?php

// 	ob_start();
	session_start();
	require_once('functions/general.php');
	require_once('config.php');
	require_once("connect/db.php");
	require_once("classes/bcrud.php");

	$OBCrud=new BCrud($db);
	$section=$_REQUEST["section"];
	$crud=null;
	if(isset($_REQUEST["crud"]))
		$crud=$_REQUEST["crud"];
	
	switch($section){
		case "mail":
			if(isset($_SESSION[SESSION_NM]["idmail_account"])){
				$idmail_account=$_SESSION[SESSION_NM]["idmail_account"];
				$subsection=$_REQUEST["subsection"];
				switch($subsection){
					case "inbox":
						$OBCrud->mail_inbox_list($idmail_account);
						break;
					case "attachment":
						$idmail_box=$_REQUEST["idmail_box"];
						$OBCrud->mail_attachment_list($idmail_box);
						break;
					case "addr_list":
						$idmail_box=$_REQUEST["idmail_box"];
						$OBCrud->mail_address_list($idmail_box);
						break;
					case "addr_book":
						$qry_cond="";
						if(isset($_REQUEST["query"]))$qry_cond=$_REQUEST["query"];
						$OBCrud->mail_address_book($idmail_account,$qry_cond);
						break;
				}
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
