<?php
	b_set_time_zone("Asia/Jakarta");

	define("APP_DETAIL",serialize(array(
		"app_name"=>"Wisanka Apps",
		"app_version"=>"0.1",
		"app_title"=>"Wisanka Newsletter",
		"app_description"=>"Wisanka Applications",
		"company_name"=>"PT.Wisanka",
		"admin_contact"=>"bayucandra@gmail.com"
	)));

	define("DEV_MODE",0);
	define("MYSQL_CONFIG",serialize(
		array(
			"host" => "localhost",
			"username" =>"k1176435_wnews",
			"password" => "J3p4r4_G",
			"db_name" => "k1176435_wnews"
			)
	));
	define("ADMIN_UID",0);
// 	define("PATH_SMARTY","res/php/smarty/libs/Smarty.class.php");
// 	define("PATH_TEMPLATE","res/php/tpl");
	define("ENC_PASSWORD","plain");
	define("SESSION_NM","WNEWS_ADMIN");
	define("APP_DIR","wapps");
?>
