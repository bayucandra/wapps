<?php
	session_start();
	$path_relative="..";
	require_once($path_relative.'/functions/general.php');
	require_once($path_relative.'/config.php');
	
	$idmail_account=$_SESSION[SESSION_NM]["idmail_account"];
	$order_no=$_REQUEST["order_no"];
	$idmail_box=$_REQUEST["idmail_box"];
	$basename=$_REQUEST["basename"];
	$disposition=$_REQUEST["disposition"];
	$type=$_REQUEST["type"];
	$file_path="../../files/mail/mailbox/".$idmail_account."/".$idmail_box."/".$order_no."-".$basename;
	if(file_exists($file_path)){
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$basename.'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($file_path));
		readfile($file_path);
		exit;
	}
?>
