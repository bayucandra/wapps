<?php
	session_start();
	$debug=1;
	if($debug==0){
		require("res/php/functions/general.php");
		require("res/php/config.php");
		require("res/php/connect/db.php");
	}else{
		require("functions/general.php");
		require("config.php");
		require("connect/db.php");
	}

	$json_result=array("success"=>false,"idmail_account"=>-1,"error_msg"=>"");
	if(isset($_REQUEST["login"])){
		$login=user_login($db,$_REQUEST["email"],$_REQUEST["password"]);
		if($login["idmail_account"]==-1){
			$json_result["success"]=false;
			$json_result["error_msg"]=$login["error_msg"];
		}else{
			$json_result["success"]=true;
			$json_result["idmail_account"]=$login["idmail_account"];
		}
		echo json_encode($json_result);
	}
	function user_login($p_db,$p_email,$p_password){
		$ret_arr=array("idmail_account"=>-1,"error_msg"=>"");
		if(empty($p_email)||empty($p_password)){
			$ret_arr["idmail_account"]="Please do not left empty Email/password";
			return $ret_arr;
		}
		$qry_count_str="SELECT COUNT(`idmail_account`) FROM `mail_account` WHERE `email`=:p_email OR `email_remote`=:p_email";
		$qry_sel_str="SELECT * FROM mail_account WHERE `email`=:p_email OR `email_remote`=:p_email";
		$qry_sel=$p_db->prepare($qry_sel_str);
		$qry_count=$p_db->prepare($qry_count_str);
		try{
			if($qry_count->execute(array(":p_email"=>$p_email))){
				$fa_count=$qry_count->fetch(PDO::FETCH_NUM);
				try{
					$qry_sel->execute(array(":p_email"=>$p_email));
					if($fa_count[0]==1){//IF EMAIL EXIST AT THE RECORD THEN CHECK FOR THE PASSWORD
						$data_tbl_email=$qry_sel->fetch();
						$record_password = $data_tbl_email['password'];
						$record_idmail_account = $data_tbl_email['idmail_account'];
						$record_email=$data_tbl_email['email'];
						if($record_password===get_enc_password($p_password,ENC_PASSWORD)){
							$ret_arr["idmail_account"]=$record_idmail_account;//THE RETURN IF TRUE!!!!!
							$ret_arr["email"]=$record_email;
						}else{
							$ret_arr["idmail_account"]=-1;
							$ret_arr["error_msg"]="Password is incorrect, please check again";
						}
					}else{
						$ret_arr["idmail_account"]=-1;
						$ret_arr["error_msg"]="Seem email doesn't exist at database";
					}
				}catch(PDOException $e){
					$ret_arr["idmail_account"]=-1;
					$ret_arr["error_msg"]=$e->getMessage();
				}
			}else{
				$ret_arr["idmail_account"]=-1;
				$ret_arr["error_msg"]="Error when do query.".$qry_count_str;
			}
		}catch(PDOException $e){
			$ret_arr["idmail_account"]=-1;
			$ret_arr["error_msg"]=$e->getMessage();
		}
		return $ret_arr;
	}
?>
