<?php
	session_start();
	require("res/php/functions/general.php");
	require("res/php/config.php");
	$_SESSION[SESSION_NM]["root_path"]=getcwd();
	require("res/php/connect/db.php");
	require("res/php/classes/badmin.php");
	$OBAdmin=new BAdmin($db);
	$OBAdmin->logged_out_protect("login.php");
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">

	<title>WApps</title>
	<link rel="stylesheet" type="text/css" href="res/css/main.css"/>
	<script type="text/javascript" src="res/ckeditor/ckeditor.js"></script>

	<!-- The line below must be kept intact for Sencha Cmd to build your application -->
	<script id="microloader" type="text/javascript" src="bootstrap.js"></script>
<!-- 	BEGIN JS GLOBAL VARS -->
	<script type="text/javascript">
		var app_detail=<?php echo json_encode(unserialize(APP_DETAIL));?>;
		var compose_arr=[{"subject":"--NO SUBJECT--","id":0,"content":""}];
	</script>
<!-- 	END JS GLOBAL VARS -->
	<script type="text/javascript" src="res/js/bfunctions.js"></script>

</head>
<body style="min-height:100%;">
	<div style="margin-top:-70px;position:absolute;top:50%;width:95%;text-align:center;z-index:-999999;">
		<img src="res/images/loading.gif" alt="Loading..." />
		<h3 style="color:#999999" class="farial">Loading...</h3>
	</div>
</body>
</html>
