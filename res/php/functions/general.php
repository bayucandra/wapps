<?php
	function db_random_num_array($p_pdo_db,$p_qry_str,$p_arr_opt=array()){
	//format for $p_qry_str if contain limit mustbe = <space>{LIMIT}, 1st column will be used for randoming
	//$p_arr_opt contain: "qry_str_num_rows"=>Query of total records number(SELECT COUNT(`field_name`)), "per_page" as total howmany rows will be taken to randomizing(just like limit per page).
		$num_rows_total=0;
		$qry_str=$p_qry_str;
		$qry_limit_replacement="";
		if(count($p_arr_opt)>0){
			//BEGIN GET TOTAL NUMBER OF RECORD===========================
			$qry_num_rows=$p_pdo_db->prepare($p_arr_opt["qry_str_num_rows"]);
			try{
				$qry_num_rows->execute();
				$fa_num_rows=$qry_num_rows->fetch(PDO::FETCH_NUM);
				$num_rows_total=$fa_num_rows[0];
			}catch(PDOException $e){
				die("Die!!! Error when querying num rows for random purpose.".$e->getMessage().$p_arr_opt["qry_str_num_rows"]);
			}
			//END GET TOTAL NUMBER OF RECORDS*********************
			$total_page=ceil($num_rows_total/$p_arr_opt["per_page"]);
			$page_selected=mt_rand(1,$total_page);
			$limit_start=($page_selected-1)*$p_arr_opt["per_page"];
			$qry_limit_replacement=" LIMIT $limit_start , ".$p_arr_opt['per_page'];
		}
		$qry_str=str_replace(" {LIMIT}",$qry_limit_replacement,$qry_str);
		$qry_rand=$p_pdo_db->prepare($qry_str);
		$arr_selected_records=array();//Which will be get the random value from
		try{
			$qry_rand->execute();
			$arr_selected_records=$qry_rand->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			die("Die!!! Error when trying to query for random value".$e->getMessage().$qry_str);
		}
		$rand_arr_idx=mt_rand(1,count($arr_selected_records));
		return $arr_selected_records[$rand_arr_idx-1];//-1 because array index start from 0. Return associative array with field name as the name.
	}
	function dump_data($p_content,$p_filename){
		$filename=$p_filename;
		$content=$p_content;
		if(is_writeable($filename)){
			if(!$handle=fopen($filename,'a')){
				echo "Cant open file";
				exit;
			}
			if(fwrite($handle,$content)===FALSE){
				echo "Can't write file";
				exit;
			}
			fclose($handle);
		}else{
			echo "The file $filename is not writeable";
		}
	}
	function log_insert($db,$p_log_str){
		$log_str=substr($p_log_str,0,253);
		$current_date=new DateTime();
		$current_date_str=$current_date->format("Y-m-d H:i:s");
		
		$qry_str_log="INSERT INTO `logs` VALUES(:current_date,:log_str)";
		$qry_log=$db->prepare($qry_str_log);
		try{
			$qry_log->execute(array(':current_date'=>$current_date_str,':log_str'=>$log_str));
		}catch(PDOException $e){
			die("Die!!! Error when trying to insert log".$e->getMessage().$qry_str_log);
		}
	}
	function get_enc_password($p_input_password,$p_enc_password){
		switch($p_enc_password){
			case "md5":
				return md5($p_input_password);
				break;
			case "plain":
				return $p_input_password;
				break;
			case "sha1":
				return sha1($p_input_password);
				break;
			default:
				return false;
		}
	}
	function b_set_time_zone($p_time_zone){
		$php_version=phpversion();
		$php_version_sufix=explode(".",$php_version);
		if($php_version_sufix[0]>4){
			date_default_timezone_set($p_time_zone);
		}
	}

	function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		
		return $ipaddress;
	}
?>