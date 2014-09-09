<?php
	session_start();
	define("IS_ROOT", true);//FOR CHECKING IF IT MUST BE AN INCLUDE AND MAY NOT ACCESSED PARTIALLY
	require("res/php/functions/general.php");
	require("res/php/config.php");
	$_SESSION[SESSION_NM]["root_path"]=getcwd();
// 	require("res/php/connect/db.php");
// 	require("res/php/classes/badmin.php");
// 	$OBAdmin=new BAdmin($db);//purge files later
// 	$OBAdmin->logged_out_protect("login.php");//purge files later
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">

	<title>WApps</title>
<!-- 	BEGIN JS GLOBAL VARS -->
	<script type="text/javascript" src="res/js/dropzone.js"></script>
	<script type="text/javascript" src="res/js/bfunctions.js"></script>
	<script type="text/javascript">
		Dropzone.autoDiscover = false;
		var app_detail=<?php echo json_encode(unserialize(APP_DETAIL));?>;
		var loading_html='<img alt="Loading..." src="res/images/loading.gif" /><h3 style="color:#999999" class="farial">Loading...</h3>';/*
		var compose_arr=[
			{"subject":"--NO SUBJECT--","id":0,"content":""},
			{"subject":"--NO SUBJECT--","id":15,"content":""}
		];*/
		var compose_idx=0;
		var extjs_conf={
			addrs_book_page_size:10
		};
	</script>
<!-- 	END JS GLOBAL VARS -->
	<link rel="stylesheet" type="text/css" href="res/css/main.css"/>
	<link rel="stylesheet" type="text/css" href="res/css/interface.css"/>
	<link rel="stylesheet" type="text/css" href="res/css/dz/dropzone.css"/>
	<script type="text/javascript" src="res/ckeditor/ckeditor.js"></script>

	<!-- The line below must be kept intact for Sencha Cmd to build your application -->
	<script id="microloader" type="text/javascript" src="bootstrap.js"></script>

</head>
<body style="min-height:100%;background-color:#f5f5f5">
	<div id="wapps_loading" style="margin-top:-70px;position:absolute;top:50%;width:95%;text-align:center;z-index:-999999;">
		<img src="res/images/loading.gif" alt="Loading..." />
		<h3 style="color:#999999" class="farial">Loading...</h3>
	</div>
	<iframe name="bsaving" frameborder="0" width="0" height="0"></iframe>
</body>
</html>
