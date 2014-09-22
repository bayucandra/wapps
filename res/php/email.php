<?php
session_start();
$path_relative=".";
require_once($path_relative."/functions/general.php");
require_once($path_relative."/config.php");
require_once($path_relative."/connect/db.php");
$php_mailer_path=$path_relative."/".PHP_MAILER_FOLDER."/class.phpmailer.php";
require("classes/bmail.php");
	if(isset($_REQUEST["section"])){
		$idmail_account=$_SESSION[SESSION_NM]["idmail_account"];
		$OBMail=new BMail($db,array("verbose"=>false,"msg_encoding"=>"UTF-8","mail_box_attachment_path"=>"../files/mail/mailbox"));
		switch($_REQUEST["section"]){
			case "sync_accounts":
				$OBMail->sync_accounts();
				break;
			case "send_mail":
				$att_json_str=stripslashes($_REQUEST['att_json']);
				$att_json_arr=json_decode($att_json_str,true);
//				print_r($_REQUEST);
				$OBMail->send_mail(array(
					"idmail_account"=>$idmail_account,
					"addr_to_csv" => $_REQUEST["addr_to_csv"], 
					"addr_cc_csv" => $_REQUEST["addr_cc_csv"],
					"subject" => $_REQUEST["subject"],
					"message"=>$_REQUEST["msg"],
					"att_json_arr" => $att_json_arr,
					"compose_itemId"=>$_REQUEST["compose_itemId"],
					"idmail_box" => null, 
					"use_local_account" => true, "direct_sending" => true));
				break;
			case "upload_att":/*
				print_r($_REQUEST);
				print_r($_FILES);*/
				$OBMail->att_upload($_FILES,$idmail_account,$_REQUEST["compose_itemId"]);
				break;
			case "test":
				$OBMail->send_mail();
				break;
		}
	}else{
		die("Parameter not set properly");
	}
/*
stdClass Object ( 
	[type] => 0 
	[encoding] => 0 
	[ifsubtype] => 1 
	[subtype] => PLAIN 
	[ifdescription] => 0 
	[ifid] => 0 [lines] => 3 [bytes] => 37 
	[ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 
	[parameters] => Array ( [0] => stdClass Object ( [attribute] => CHARSET [value] => ISO-8859-1 ) [1] => stdClass Object ( [attribute] => FORMAT [value] => flowed ) ) 
) 

stdClass Object ( 
	[type] => 1 [encoding] => 0 [ifsubtype] => 1 [subtype] => MIXED [ifdescription] => 0 [ifid] => 0 [bytes] => 444966 [ifdisposition] => 0 
	[ifdparameters] => 0 [ifparameters] => 1 
	[parameters] => Array ( 
		[0] => stdClass Object ( 
			[attribute] => BOUNDARY [value] => ------------080504070103000908080405 ) 
	) 
	[parts] => Array ( 
		[0] => stdClass Object ( 
			[type] => 1 [encoding] => 0 [ifsubtype] => 1 [subtype] => ALTERNATIVE [ifdescription] => 0 [ifid] => 0 
			[bytes] => 91122 [ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 
			[parameters] => Array ( 
				[0] => stdClass Object ( 
					[attribute] => BOUNDARY [value] => ------------080408060703050400020501 ) 
			) 
			[parts] => Array ( 
				[0] => stdClass Object ( 
					[type] => 0 [encoding] => 0 [ifsubtype] => 1 [subtype] => PLAIN [ifdescription] => 0 
					[ifid] => 0 [lines] => 3 [bytes] => 59 [ifdisposition] => 0 [ifdparameters] => 0 
					[ifparameters] => 1 
					[parameters] => Array ( [0] => stdClass Object ( [attribute] => CHARSET [value] => ISO-8859-1 ) [1] => stdClass Object ( [attribute] => FORMAT [value] => flowed ) ) 
				) 
				[1] => stdClass Object ( 
					[type] => 1 [encoding] => 0 [ifsubtype] => 1 [subtype] => RELATED [ifdescription] => 0 
					[ifid] => 0 [bytes] => 90753 [ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 
					[parameters] => Array ( 
						[0] => stdClass Object ( [attribute] => BOUNDARY [value] => ------------030305050302030208080101 ) 
					) 
					[parts] => Array ( 
						[0] => stdClass Object ( 
							[type] => 0 [encoding] => 0 [ifsubtype] => 1 [subtype] => HTML [ifdescription] => 0 [ifid] => 0 [lines] => 12 [bytes] => 355 [ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 [parameters] => Array ( [0] => stdClass Object ( [attribute] => CHARSET [value] => ISO-8859-1 ) ) ) 
						[1] => stdClass Object ( 
							[type] => 5 [encoding] => 3 [ifsubtype] => 1 [subtype] => JPEG [ifdescription] => 0 [ifid] => 1 [id] => [bytes] => 89988 [ifdisposition] => 1 
							[disposition] => INLINE [ifdparameters] => 1 
							[dparameters] => Array ( [0] => stdClass Object ( [attribute] => FILENAME [value] => joging.jpg ) ) 
							[ifparameters] => 1 
							[parameters] => Array ( [0] => stdClass Object ( [attribute] => NAME [value] => joging.jpg ) )
						) 
					)
				) 
			) 
		) 
		[1] => stdClass Object ( 
			[type] => 3 [encoding] => 3 [ifsubtype] => 1 [subtype] => VND.MS-EXCEL [ifdescription] => 0 
			[ifid] => 0 [bytes] => 28768 [ifdisposition] => 1 
			[disposition] => ATTACHMENT [ifdparameters] => 1 
			[dparameters] => Array ( 
				[0] => stdClass Object ( 
					[attribute] => FILENAME [value] => Laporan Kerja Mingguan Software-WJB-1407.xls ) 
			) 
			[ifparameters] => 1 
			[parameters] => Array ( 
				[0] => stdClass Object ( [attribute] => NAME [value] => Laporan Kerja Mingguan Software-WJB-1407.xls ) 
			) 
		) 
		[2] => stdClass Object ( 
			[type] => 5 [encoding] => 3 [ifsubtype] => 1 [subtype] => JPEG [ifdescription] => 0 
			[ifid] => 0 [bytes] => 324402 [ifdisposition] => 1 
			[disposition] => ATTACHMENT [ifdparameters] => 1 
			[dparameters] => Array ( [0] => stdClass Object ( [attribute] => FILENAME [value] => outlook.jpg ) ) 
			[ifparameters] => 1 
			[parameters] => Array ( [0] => stdClass Object ( [attribute] => NAME [value] => outlook.jpg ) ) 
		) 
	)
)

stdClass Object ( 
	[type] => 1 [encoding] => 0 [ifsubtype] => 1 
	[subtype] => MIXED [ifdescription] => 0 [ifid] => 0 [bytes] => 63040 [ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 
	[parameters] => Array ( [0] => stdClass Object ( [attribute] => BOUNDARY [value] => ------------090204080309030404040402 ) ) 
	[parts] => Array ( 
		[0] => stdClass Object ( 
			[type] => 1 [encoding] => 0 [ifsubtype] => 1 
			[subtype] => ALTERNATIVE [ifdescription] => 0 [ifid] => 0 [bytes] => 33784 [ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 
			[parameters] => Array ( [0] => stdClass Object ( [attribute] => BOUNDARY [value] => ------------030703040803030309010308 ) ) 
			[parts] => Array ( 
				[0] => stdClass Object ( 
					[type] => 0 [encoding] => 0 [ifsubtype] => 1 [subtype] => PLAIN [ifdescription] => 0 
					[ifid] => 0 [lines] => 5 [bytes] => 48 [ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 
					[parameters] => Array ( [0] => stdClass Object ( [attribute] => CHARSET [value] => ISO-8859-1 ) [1] => stdClass Object ( [attribute] => FORMAT [value] => flowed ) ) ) 
				[1] => stdClass Object ( 
					[type] => 1 [encoding] => 0 [ifsubtype] => 1 [subtype] => RELATED [ifdescription] => 0 
					[ifid] => 0 [bytes] => 33426 [ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 
					[parameters] => Array ( [0] => stdClass Object ( [attribute] => BOUNDARY [value] => ------------070707070808090902040705 ) ) 
					[parts] => Array ( 
						[0] => stdClass Object ( 
							[type] => 0 [encoding] => 0 [ifsubtype] => 1 [subtype] => HTML [ifdescription] => 0 [ifid] => 0 
							[lines] => 14 [bytes] => 360 [ifdisposition] => 0 [ifdparameters] => 0 [ifparameters] => 1 
							[parameters] => Array ( [0] => stdClass Object ( [attribute] => CHARSET [value] => ISO-8859-1 ) ) ) 
						[1] => stdClass Object ( 
							[type] => 5 [encoding] => 3 [ifsubtype] => 1 [subtype] => JPEG [ifdescription] => 0 [ifid] => 1 
							[id] => [bytes] => 32642 [ifdisposition] => 1 [disposition] => INLINE [ifdparameters] => 1 
							[dparameters] => Array ( [0] => stdClass Object ( [attribute] => FILENAME [value] => bayu-profile1.jpg ) ) 
							[ifparameters] => 1 
							[parameters] => Array ( [0] => stdClass Object ( [attribute] => NAME [value] => bayu-profile1.jpg ) ) ) ) ) ) 
		) 
		[1] => stdClass Object ( 
			[type] => 3 [encoding] => 3 [ifsubtype] => 1 [subtype] => VND.MS-EXCEL [ifdescription] => 0 
			[ifid] => 0 [bytes] => 28768 [ifdisposition] => 1 [disposition] => ATTACHMENT [ifdparameters] => 1 
			[dparameters] => Array ( [0] => stdClass Object ( [attribute] => FILENAME [value] => Laporan Kerja Mingguan Software-WJB-1407.xls ) ) 
			[ifparameters] => 1 
			[parameters] => Array ( [0] => stdClass Object ( [attribute] => NAME [value] => Laporan Kerja Mingguan Software-WJB-1407.xls ) ) ) ) ) 

*/
?>
