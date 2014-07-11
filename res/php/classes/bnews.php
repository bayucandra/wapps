<?php

require_once("functions/general.php");
require_once("functions/pic.php");
require_once("PHPMailer/class.phpmailer.php");
b_set_time_zone("Asia/Jakarta");
class BNews extends PHPMailer{
	private $idx_recipient_sent,$arr_email_conf;
	private $image_path_base;
	private $unsubscribe_url;
	private $db;
	private $arr_configs_server;//Get configurations from database server
	
	public function __construct($p_db_link){//$p_arr_email_conf serialized
		parent::__construct(true);
		$this->db=$p_db_link;
		$this->arr_configs_server=$this->set_configs_server();
		if(!$this->arr_configs_server){
			$this->log_insert("Error when parsing system configuration from database server(constructor error)");
			return false;
		}
		$this->set_email_conf();
		$this->unsubscribe_url="http://".$_SERVER['HTTP_HOST']."/".APP_DIR."/unsubscribe.php";
		$this->idx_recipient_sent=0;
		$this->image_path_base="../images/jobs/";
	}
	function error_handler($msg) {
		print("My Site Error");
		print("Description:");
		printf("%s", $msg);
		exit;
	}
	public function set_configs_server(){//Getting configurations from database server
		$qry_str_check_empty="SELECT COUNT(*) FROM configurations WHERE value=''";
		$qry_check_empty=$this->db->prepare($qry_str_check_empty);
		try{
			$qry_check_empty->execute();
			$arr_check_empty=$qry_check_empty->fetch(PDO::FETCH_NUM);
			if($arr_check_empty[0]>0){
				$this->log_insert("Error!!!, there is configuration remain unset.");
				return false;
			}
		}catch(PDOException $e){
			$this->log_insert("There is error when checking for configuration empty:".$e->getMessage());
			return false;
		}
		$qry_str_sel="SELECT * FROM configurations";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_sel->execute();
			$arr_config_server_tmp=array();
			while($fa_sel=$qry_sel->fetch(PDO::FETCH_ASSOC)){
				$description=$fa_sel["description"];
				$value=$fa_sel["value"];
				$arr_config_server_tmp[$description]=$value;
			}
			return $arr_config_server_tmp;
		}catch(PDOException $e){
			$this->log_insert("Error when getting configurations from database server:".$e->getMessage());
			return false;
		}
	}
	private function set_email_conf(){
		if(!$this->arr_configs_server){
			$this->log_insert("Can't set_email_conf there is problem with arr_configs_server");
			return false;
		}
		$this->IsSMTP();
		
		$this->SMTPAuth		= true;
		$this->Host		= $this->arr_configs_server['smtp_host'];
		$this->Port		= $this->arr_configs_server['smtp_port'];
		$this->Username		= $this->arr_configs_server['smtp_username'];
		$this->Password		= $this->arr_configs_server['smtp_password'];
		$this->AddReplyTo($this->arr_configs_server['reply_to_addr'],$this->arr_configs_server['reply_to_name']);
		$this->SetFrom($this->arr_configs_server['from_addr'],$this->arr_configs_server['from_name']);
		$this->ConfirmReadingTo=$this->arr_configs_server['from_addr'];//Read receipt
	}
	public function set_email_content($p_idjobs,$p_md5_idrecipient){
		if(!$this->arr_configs_server){
			$this->log_insert("Can't set_email_content there is problem with arr_configs_server");
			return false;
		}
		$qry_str_content="SELECT * FROM `jobs` WHERE `idjobs`=$p_idjobs LIMIT 1";
		$qry_content=$this->db->prepare($qry_str_content);
		try{
			$qry_content->execute();
			$fa_content=$qry_content->fetch(PDO::FETCH_ASSOC);
			
			$this->Subject = $fa_content["subject"];
			
			$picture_name=$fa_content["picture_name"];
			$url=$fa_content["url"];
			$picture_name_md5=MD5($picture_name);
			$attachment_path=$this->arr_configs_server['attachment_base_path']."/".$p_idjobs.".".$picture_name;
// 			$this->AddAttachment($attachment_path,$fa_content["picture_name"]);
			$this->AddEmbeddedImage($attachment_path,$picture_name_md5,$picture_name,"base64","image/jpeg");
			
			$message_html="";
			$message_html.="<html>
				<head><meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\"></head>
				<body style=\"color:#333333;font-family:arial,hevetica,sans,tahoma,sans-serif,serif;font-size:12px;\">";
			if($url==""){
				$message_html.="
					<br /><img alt=\"$picture_name\" src=\"cid:$picture_name_md5\">";
			}else{
				$message_html.="
					News URL: <a href=\"$url\">$url</a>
					<br /><a href=\"$url\" style=\"border:none;\"><img alt=\"$picture_name\" src=\"cid:$picture_name_md5\"></a>";
			}
			$message_html.="<br /><div>You are recieving this message because you are relation or partner of <b>".COMPANY_NAME."</b>";
			$message_html.="<br /> If you no longer wish to receive our emails, you can unsubscribe by click the link below:";
			$message_html.="<br /><a style=\"color:#0000ff;text-decoration:underline;\" href=\"".$this->unsubscribe_url."?rd=".mt_rand()."&&un=".$p_md5_idrecipient."\">Unsubscribe</a>";
			$message_html.="</div></body></html>";
			$this->MsgHTML($message_html);
// 			$this->AltBody=$fa_content["picture_name"];
		}catch(PDOException $e){
			$this->log_insert("Error when generating newsletter content:".$e->getMessage());
		}
	}
	private function is_job_expired(){
		$ret_bool=false;
		$date_last_job_executed=$this->get_last_job_executed();
		$date_current=new DateTime();
		$date_diff=$date_current->diff($date_last_job_executed);
		$date_diff_hour=$date_diff->format("%h");
		$date_diff_minutes=$date_diff->format("%i");
		$date_diff_day=$date_diff->format("%a");
		$date_diff_month=$date_diff->format("%m");
		$date_diff_year=$date_diff->format("%y");
		$date_diff_minutes_total=($date_diff_hour*60)+$date_diff_minutes;
		$is_over_hour=($date_diff_day>0)||($date_diff_month>0)||($date_diff_year>0);
		if($date_diff_minutes_total>=($this->arr_configs_server['last_job_expiration']/60)||$is_over_hour){//IF SENDING JOB EXPIRED
			$ret_bool=true;
		}
		return $ret_bool;
	}
	private function is_cron_job(){
		if(isset($_REQUEST["is_cron"]))return true;
		else return false;
	}
	public function send_news(){
// 		ini_set("max_execution_time",300);
		if($this->is_cron_job()){
			$this->set_cron_is_active(1);
		}
		if(!$this->arr_configs_server){
			$this->log_insert("Error, can't sending newsletter because there is problem with configuration setting");
			die("Die!!!.E-mail configuration have not set or there is some error with server configuration");
		}
		//=====================BEGIN CHECKING EXPIRATION JOB RUNNING========================================
		if($this->get_is_news_sending()){
			if($this->is_job_expired()){
				$this->set_is_news_sending(0);//SET AS NO JOB RUNING
				$this->log_insert("There is force reset is_news_sending value due Expiration last_job_executed: ".$date_last_job_executed->format("Y-m-d H:i:s"));
			}else{
				return false;//--!!!!!!!!!!!!!!CANCEL EXECUTION IF SURE THAT THERE IS JOB RUNING
			}
		}elseif($this->get_cron_is_active()&&(!$this->is_cron_job())){//IF CRON ALREADY DOING THE JOB AND NOT EXPIRED, NOT ALLOW JAVASCRIPT EXECUTE SENDING JOB
			if($this->is_job_expired()){//CRON JOB EXPIRED, NOT DOING THE JOB
				$this->set_cron_is_active(0);
			}else{
				return false;//CANCEL THE EXECUTION IF NOT EXPIRED YET===================
			}
		}
		//*************************END CHECKING EXPIRATION JOBS RUNNING***********************************************
		$this->set_is_news_sending(1);
		$qry_str_count_pending_news="SELECT COUNT(sr.idrecipient) FROM `sent_report` sr
				LEFT JOIN `recipient` rc ON(sr.idrecipient=rc.idrecipient)
				LEFT JOIN `jobs` jb ON(sr.idjobs=jb.idjobs)
				LEFT JOIN unsubscribed us ON(sr.idrecipient=us.idrecipient)
			WHERE sr.is_sent=0 AND us.idrecipient IS NULL AND jb.hold=0 AND sr.tries<".$this->arr_configs_server['max_send_tries']."
			  AND rc.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
			ORDER BY jb.idjobs ASC
			LIMIT 1";

		$qry_count_pending_news=$this->db->prepare($qry_str_count_pending_news);
		$fa_sel_pending_news=array();//init var
		$is_recipient_detail_exist=false;
		$idrecipient=-1;
		$idjobs=-1;
		$subject="";
		$md5_idrecipient=-1;
		try{
			$qry_count_pending_news->execute();
			$count_pending_news=$qry_count_pending_news->fetch(PDO::FETCH_NUM);
			if($count_pending_news[0]>0){
				$qry_idjobs_rand="SELECT sr.idjobs,sr.idrecipient FROM `sent_report` sr
						LEFT JOIN `recipient` rc ON(sr.idrecipient=rc.idrecipient)
						LEFT JOIN `jobs` jb ON(sr.idjobs=jb.idjobs)
						LEFT JOIN unsubscribed us ON(sr.idrecipient=us.idrecipient)
					WHERE sr.is_sent=0 AND us.idrecipient IS NULL AND jb.hold=0 AND sr.tries<".$this->arr_configs_server['max_send_tries']."
					AND rc.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
					ORDER BY jb.idjobs ASC {LIMIT}";
				$idjobs_idrecipient_rand=db_random_num_array($this->db,$qry_idjobs_rand,array("qry_str_num_rows"=>$qry_str_count_pending_news,"per_page"=>500));
				$qry_str_sel_pending_news="SELECT sr.*,rc.*,jb.*,us.idrecipient AS us_idrecipient,MD5(rc.idrecipient) AS md5_idrecipient FROM `sent_report` sr
						LEFT JOIN `recipient` rc ON(sr.idrecipient=rc.idrecipient)
						LEFT JOIN `jobs` jb ON(sr.idjobs=jb.idjobs)
						LEFT JOIN unsubscribed us ON(sr.idrecipient=us.idrecipient)
					WHERE sr.is_sent=0 AND us.idrecipient IS NULL AND jb.hold=0 AND sr.tries<".$this->arr_configs_server['max_send_tries']."
					AND rc.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$' AND sr.idjobs=".$idjobs_idrecipient_rand['idjobs']." AND sr.idrecipient=".$idjobs_idrecipient_rand['idrecipient']."
					LIMIT 1";
				$qry_sel_pending_news=$this->db->prepare($qry_str_sel_pending_news);
				$qry_sel_pending_news->execute();
				$fa_sel_pending_news=$qry_sel_pending_news->fetch(PDO::FETCH_ASSOC);
				if(isset($fa_sel_pending_news["email"])&&isset($fa_sel_pending_news["full_name"]))
					$is_recipient_detail_exist=true;
				$idrecipient=$fa_sel_pending_news["idrecipient"];
				$idjobs=$fa_sel_pending_news["idjobs"];
				$subject=$fa_sel_pending_news["subject"];
				$md5_idrecipient=$fa_sel_pending_news["md5_idrecipient"];
			}else{//STOP/CANCEL IF no more recipient left to send the news by return false
				$this->set_is_news_sending(0);
				
				$this->set_last_job_executed();
				if(isset($_REQUEST["news_js_sending"]))
					$this->set_js_last_job_finished();
				
				if(DEV_MODE==1)
				  $this->log_insert("Dev Mode: Cancel sending, no user list available");
				return false;
			}
		}catch(PDOException $e){
			$this->log_insert("Failed when query recipient to send message:".$e->getMessage());
		}
		
		if($is_recipient_detail_exist){//if 'return false' procedure above doesn't work
			try{
				$this->set_email_content($idjobs,$md5_idrecipient);
				
				$this->AddAddress($fa_sel_pending_news["email"],$fa_sel_pending_news["full_name"]);
				$this->increase_sent_tries($idrecipient,$idjobs);
				if($this->Send()){
					$this->set_last_news_sent();
					
					$this->set_last_job_executed();
					if(isset($_REQUEST["news_js_sending"]))
						$this->set_js_last_job_finished();
						
					$this->log_insert("News sent to:".$fa_sel_pending_news["email"].", subject:".$subject.",idjobs:".$idjobs);
					$qry_str_set_sent="UPDATE `sent_report` SET `is_sent`=1 WHERE `idrecipient`=$idrecipient AND `idjobs`=$idjobs";
					$qry_set_sent=$this->db->prepare($qry_str_set_sent);
					try{
						$qry_set_sent->execute();
					}catch(PDOException $e){
						$this->log_insert("Error when mark recipient at Job as 'sent':".$e->getMessage());
					}
					/*
					if($p_num_try>1)
						$this->send_news($p_num_try-1,false);//RECURSIVE SENDING*/
				}else{
					$this->log_insert("News send failed to:".$fa_sel_pending_news["email"].", subject:".$subject.",idjobs:".$idjobs);
				}
				$this->set_is_news_sending(0);
			}catch(phpmailerException $e){
				$this->set_last_job_executed();
				if(isset($_REQUEST["news_js_sending"]))
					$this->set_js_last_job_finished();
				$this->set_is_news_sending(0);
				$this->log_insert("Error when sending email to:".$fa_sel_pending_news["email"].", subject:".$subject. ",idjobs:".$idjobs.",".$e->errorMessage());
			}
		}else{
			$this->log_insert("There is no recipient available");
			$this->set_is_news_sending(0);
			
			$this->set_last_job_executed();
			if(isset($_REQUEST["news_js_sending"]))
				$this->set_js_last_job_finished();
		}
	}
	private function log_insert($p_log_str){
		sleep(5);
		$log_str=substr($p_log_str,0,253);
// 		$current_date=date("Y-m-d H:i:s");
		$current_date=new DateTime();
		$current_date_str=$current_date->format("Y-m-d H:i:s");
		
		$qry_str_log="INSERT INTO `logs` VALUES(:current_date,:log_str)";
		$qry_log=$this->db->prepare($qry_str_log);
		try{
			$qry_log->execute(array(':current_date'=>$current_date_str,':log_str'=>$log_str));
		}catch(PDOException $e){
			die("Die!!! Error when trying to insert log".$e->getMessage().$qry_str_log);
		}
	}
	private function get_last_log_date(){
		$qry_str_get="SELECT * FROM `logs` ORDER BY `date` LIMIT 1";
		$qry_get=$this->db->prepare($qry_str_get);
		try{
			$qry_get->execute();
			$fa_get=$qry_get->fetch(PDO::FETCH_ASSOC);
			if(isset($fa_get["date"])){
				return $fa_get["date"];
			}else{
				return "0000-00-00 00:00:00";
			}
		}catch(PDOException $e){
			$this->log_insert("Error when trying get last log date:".$e->getMessage());
		}
	}
	private function set_js_last_job_finished(){
		$current_date=new DateTime();
		$current_date_str=$current_date->format("Y-m-d H:i:s");
		
		$qry_str_set="UPDATE `system_tasks` SET `value`=:current_date WHERE `name`='js_last_job_finished'";
		$qry_set=$this->db->prepare($qry_str_set);
		try{
			$qry_set->execute(array(":current_date"=>$current_date_str));
		}catch(PDOException $e){
			$this->log_insert("Error when set js_last_job_finished:".$e->getMessage());
		}
	}
	private function get_js_last_job_finished(){
		$qry_str_get="SELECT * FROM `system_tasks` WHERE `name`='js_last_job_finished'";
		$qry_get=$this->db->prepare($qry_str_get);
		try{
			$qry_get->execute();
			$fa_get=$qry_get->fetch(PDO::FETCH_ASSOC);
			return $fa_get["value"];
		}catch(PDOException $e){
			$this->log_insert("Error when get js_last_job_finished:".$e->getMessage());
		}
	}
	private function set_is_news_sending($p_bool){
		$qry_str_set="UPDATE `system_tasks` SET `value`='$p_bool' WHERE `name`='is_news_sending'";
		$qry_set=$this->db->prepare($qry_str_set);
		try{
			$qry_set->execute();
		}catch(PDOException $e){
			$this->log_insert("Error when set is_news_sending:".$e->getMessage());
		}
	}
	private function get_is_news_sending(){
		$qry_str_get="SELECT * FROM `system_tasks` WHERE `name`='is_news_sending'";
		$qry_get=$this->db->prepare($qry_str_get);
		try{
			$qry_get->execute();
			$fa_get=$qry_get->fetch(PDO::FETCH_ASSOC);
			return $fa_get["value"];
		}catch(PDOException $e){
			$this->log_insert("Error when get is_news_sending:".$e->getMessage());
		}
	}
	private function set_last_news_sent(){
		$current_date=new DateTime();
		$current_date_str=$current_date->format("Y-m-d H:i:s");
		$qry_str_set="UPDATE `system_tasks` SET `value`=:current_date_str WHERE `name`='last_news_sent'";
		$qry_set=$this->db->prepare($qry_str_set);
		try{
			$qry_set->execute(array(":current_date_str"=>$current_date_str));
		}catch(PDOException $e){
			$this->log_insert("Error when set last_news_sent:".$e->getMessage());
		}
	}
	private function get_last_news_sent(){
		$qry_str_get="SELECT * FROM `system_tasks` WHERE `name`='last_news_sent'";
		$qry_get=$this->db->prepare($qry_str_get);
		try{
			$qry_get->execute();
			$fa_get=$qry_get->fetch(PDO::FETCH_ASSOC);
			return $fa_get["value"];
		}catch(PDOException $e){
			$this->log_insert("Error when get last_news_sent:".$e->getMessage());
		}
	}
	private function set_last_job_executed(){//Set on the last of recursive process of sending
		$last_job_dt=new DateTime();
		$last_job_dt_str=$last_job_dt->format("Y-m-d H:i:s");
		$qry_str_set="UPDATE `system_tasks` SET `value`=:last_job_dt_str WHERE `name`='last_job_executed'";
		$qry_set=$this->db->prepare($qry_str_set);
		try{
			$qry_set->execute(array(':last_job_dt_str'=>$last_job_dt_str));
		}catch(PDOException $e){
			$this->log_insert("Error when set last_job_executed:".$e->getMessage());
		}
	}
	private function get_last_job_executed(){
		$qry_str_get="SELECT * FROM `system_tasks` WHERE `name`='last_job_executed'";
		$qry_get=$this->db->prepare($qry_str_get);
		try{
			$qry_get->execute();
			$fa_get=$qry_get->fetch(PDO::FETCH_ASSOC);
			$ret_date=DateTime::createFromFormat("Y-m-d H:i:s",$fa_get["value"]);
			return $ret_date;
		}catch(PDOException $e){
			$this->log_insert("Error when get last_job_executed:".$e->getMessage());
		}
	}
	private function set_cron_is_active($p_value){
		$qry_str_set="UPDATE `configurations` SET `value`=:value WHERE `description`='cron_is_active'";
		$qry_set=$this->db->prepare($qry_str_set);
		try{
			$qry_set->execute(array(':value'=>$p_value));
		}catch(PDOException $e){
			$this->log_insert("There was error when setting cron_is_active".$e->getMessage());
		}
	}
	private function get_cron_is_active(){
		$qry_str_get="SELECT `value` FROM `configurations` WHERE `description`='cron_is_active'";
		$qry_get=$this->db->prepare($qry_str_get);
		try{
			$qry_get->execute();
			$fa_get=$qry_get->fetch(PDO::FETCH_ASSOC);
			$value=$fa_get["value"];
			if($value=='1')return true;
			else return false;
		}catch(PDOException $e){
			$this->log_insert("There was error when getting cron_is_active value:".$e->getMessage());
		}
	}
	private function increase_sent_tries($p_idrecipient,$p_idjobs){//Set on the last of recursive process of sending
		$qry_str_get="SELECT `tries` FROM `sent_report` WHERE idrecipient=:idrecipient AND idjobs=:idjobs";
		$qry_get=$this->db->prepare($qry_str_get);

		$qry_str_set="UPDATE `sent_report` SET `tries`=:tries WHERE idrecipient=:idrecipient AND idjobs=:idjobs LIMIT 1";
		$qry_set=$this->db->prepare($qry_str_set);
		try{
			$qry_get->execute(array(":idrecipient"=>$p_idrecipient,":idjobs"=>$p_idjobs));
			$fa_qry_get=$qry_get->fetch(PDO::FETCH_ASSOC);
			if($fa_qry_get['tries']<3){
				$inc_tries=$fa_qry_get['tries']+1;
				$qry_set->execute(array(":tries"=>$inc_tries,":idrecipient"=>$p_idrecipient,":idjobs"=>$p_idjobs));
			}
		}catch(PDOException $e){
			$this->log_insert("Error when increasing sent `tries':".$e->getMessage());
		}
	}
	public function post_news_job(){
		if(!$this->arr_configs_server){
			return json_encode(array(
				"success"=>false,
				"message"=>"Can't post job. There is error with configurations at server"
			));
		}
		$subject=$_REQUEST["news_title"];
		$url=$_REQUEST["url"];
		$total_recipient_inserted=0;
		$auto_inc_idjobs=-1;

		$image_element_name="news_image";
		$image_file=$_FILES[$image_element_name];
		$image_size=getimagesize($image_file["tmp_name"]);
		$image_name=$image_file['name'];
		
		$arr_recipient_list=explode(",",$_REQUEST["news_recipients"]);
		$current_date=date("Y-m-d H:i:s");
		
		$this->db->beginTransaction();
			$is_db_error=false;
			$is_image_error=false;
			$db_error_msg="";
			$img_error_msg="";
			$qry_str_ins_job="INSERT INTO `jobs`(`subject`,`url`,`date`,`picture_name`)
				VALUES('$subject','$url','$current_date','$image_name')";
			$qry_ins_job=$this->db->prepare($qry_str_ins_job);
			try{
				$qry_ins_job->execute();
				$auto_inc_idjobs=$this->db->LastInsertId('idjobs');
			}catch(PDOException $e){
				$is_db_error=true;
				$db_error_msg.="Error Insert jobs:".$e->getMessage();
			}
			if(!$is_db_error){
				foreach($arr_recipient_list as $idrecipient_group){
// 					$qry_str_recipients="SELECT * FROM `recipient` WHERE `idrecipient_group`=$idrecipient_group";
					$qry_str_recipients="SELECT rc.* FROM `recipient` rc
							LEFT JOIN `unsubscribed` us ON(rc.idrecipient=us.idrecipient)
						WHERE us.idrecipient IS NULL AND rc.idrecipient_group=:idrecipient_group";
					$qry_recipients=$this->db->prepare($qry_str_recipients);
					try{
						$qry_recipients->execute(array(":idrecipient_group"=>$idrecipient_group));
						while($fa_recipient=$qry_recipients->fetch(PDO::FETCH_ASSOC)){
							$id_recipient=$fa_recipient['idrecipient'];
							$id_jobs=$auto_inc_idjobs;
							$qry_ins_sent_report="INSERT INTO `sent_report`(idrecipient,idjobs)
								VALUES($id_recipient,$id_jobs)";
							$qry_sent_report=$this->db->prepare($qry_ins_sent_report);
							try{
								$qry_sent_report->execute();
								$total_recipient_inserted++;
							}catch(PDOException $e){
								$is_db_error=true;
								$db_error_msg.="Error Insert Recipient:".$e->getMessage();
							}
						}
					}catch(PDOException $e){
						$is_db_error=true;
						$db_error_msg.="Error recipient query:".$e->getMessage();
					}
				}
			}
			if(!$is_db_error){//Image upload function
				if(!empty($image_file['error'])){
					$is_image_error=true;
					switch($image_file['error']){
						case '1':
							$img_error_msg .= 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
							break;
						case '2':
							$img_error_msg .= 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
							break;
						case '3':
							$img_error_msg .= 'The uploaded file was only partially uploaded';
							break;
						case '6':
							$img_error_msg .= 'Missing a temporary folder';
							break;
						case '7':
							$img_error_msg .= 'Failed to write file to disk';
							break;
						case '8':
							$img_error_msg .= 'File upload stopped by extension';
							break;
						case '999':
							$img_error_msg .= "Unknown error of image";
							break;
						default:
							$img_error_msg .= 'No error code avaiable';
							break;
					}
				}elseif(!preg_match("/.jpg/i",$image_name)){
					$img_error_msg.="Please upload JPEG only";
				}else{//IF no error then start uploading
					$tmp_path=$image_file['tmp_name'];
					$image_path=$this->image_path_base.$auto_inc_idjobs.".".$image_name;
					$upload_image=image_resize($tmp_path,-1,array("str_dest_path"=>$image_path));
					if(!$upload_image){
						$is_image_error=true;
						$img_error_msg.="There is error when copy image to the path";
					}
				}
			}
		if($is_db_error||$is_image_error){//IF FAIL
			$this->db->rollBack();
			return json_encode(array(
				"success"=>false,
				"message"=>"There was error(s) when try to submit Newsletter job. Error Message:".$db_error_msg." And ".$img_error_msg
			));
		}else{//IF SUCCESS
			$this->db->commit();
			return json_encode(array(
				"success" => true,
				"message" => "News job entered successfully with total: $total_recipient_inserted of recipient(s)"
			));
		}
	}
	public function news_preview_html(){
		$ret_html="";
		$idjobs=$_REQUEST['idjobs'];
		$image_name="";
		$qry_str_jobs="SELECT * FROM `jobs` WHERE `idjobs`=:idjobs";
		$qry_jobs=$this->db->prepare($qry_str_jobs);
		try{
			$qry_jobs->execute(array(":idjobs"=>$idjobs));
			$fa_jobs=$qry_jobs->fetch(PDO::FETCH_ASSOC);
			$image_name=$fa_jobs["picture_name"];
		}catch(PDOException $e){
			$ret_html.="There is error when get selected jobs picture: ".$e->getMessage();
		}
		if(empty($image_name))return "";
		
		$ret_html.="<img src=\"res/images/jobs/".$idjobs.".".$image_name."\" />";
		return $ret_html;
	}
}
?>
