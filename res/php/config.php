<?php
	b_set_time_zone("Asia/Jakarta");

	define("APP_DETAIL",serialize(array(
		"app_name"=>"Wisanka Apps",
		"app_version"=>"0.1",
		"app_title"=>"Wisanka Applications",
		"app_description"=>"Wisanka Applications",
		"company_name"=>"PT.Wisanka",
		"admin_contact"=>"bayucandra@gmail.com"
	)));

	define("DEV_MODE",1);
	define("MYSQL_CONFIG",serialize(
		array(
			"host" => "localhost",
			"username" =>"root",
			"password" => "Th3_k1nG",
			"db_name" => "wapps"
			)
	));
	define("ADMIN_UID",0);
	define("PATH_SMARTY","res/php/smarty/libs/Smarty.class.php");
	define("PHP_MAILER_FOLDER","PHPMailer-5.2.8");
	define("PATH_TEMPLATE","res/php/tpl");
	define("ENC_PASSWORD","plain");
	define("SESSION_NM","WAPPS_ADMIN");
	define("APP_DIR","wapps");
?>
