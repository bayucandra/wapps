<?php
$path_relative=".";
require_once($path_relative."/functions/general.php");
// require_once(../config.php);
b_set_time_zone("Asia/Jakarta");
class BMail{
	private $mbox,$db,$msg_encoding;
	private $mail_account_arr,$is_verbose;
	private $cur_message_idx_arr;//Array ( [0] => 2 [1] => 1 )
	private $cur_msg_content_arr;//Array ("msg_html","msg_plain");
	function __construct($p_db_PDO,$p_arr=array()){//$p_arr=array("verbose"=>true/false,"msg_encoding")
		$this->is_verbose=false;
		$this->cur_msg_content_arr_reset();
		if(isset($p_arr)){
			if(isset($p_arr["verbose"]))
				$this->is_verbose=$p_arr["verbose"];
		}
		
		$this->db=$p_db_PDO;
		$this->mbox=false;//INIT with false
		$this->mail_account_arr=$this->get_mail_account_list();
	}
	private function log_insert($p_str){
		log_insert($this->db,$p_str);
	}
	private function verbose($p_str){
		if($this->is_verbose){
			echo $p_str."<br />";
		}
	}
	private function mbox_init($p_email,$p_password){//$p_arr_account=array(email,password);
		$is_mbox_clear=true;
		if($this->mbox!==false)
			$is_mbox_clear=imap_close($this->mbox);

		if($is_mbox_clear===true){
			$exp_email=explode("@",$p_email);
			$mailhost=$exp_email[1];
			$username=$p_email;
			$password=$p_password;
			$this->mbox=imap_open("{".$mailhost.":110/pop3/novalidate-cert}",$username,$password);
			$mbox_message="Mailbox stream opened <b>successfuly</b>";
			if($this->check_conn()===false)
				$mbox_message="Mailbox stream <b>failed</b> to open.";
			$this->verbose("*FUNCTION mbox_init() => init mbox stream, param:\$p_email=$p_email . Status:$mbox_message");
		}else{
			$this->log_insert("Error when init POP3 Connection for email:".$p_email);
			return false;
		}
	}
	private function check_conn(){
		if($this->mbox===false)
			return false;
	}
	public function sync_accounts($p_account_idx=0){
		$this->del_unfinished_mail();
		$this->verbose("*FUNCTION sync_accounts()=>parsing database, param \$p_account_idx=$p_account_idx");
		$idmail_account=$this->mail_account_arr[$p_account_idx]["idmail_account"];
		$email=$this->mail_account_arr[$p_account_idx]["email"];
		$password=$this->mail_account_arr[$p_account_idx]["password"];
		$this->mbox_init($email,$password);
		$this->get_msg_idxs();//GET MESSAGE INDEXES ARRAY
		$this->sync_mail($idmail_account);
		//BEGIN RECURSION
		if( $p_account_idx<(count($this->mail_account_arr)-1) )
			$this->sync_accounts($p_account_idx+1);
	}
	private function sync_mail($p_idmail_account,$p_idx=0){
		if($this->check_conn()===false)
			return false;
		$header=imap_headerinfo($this->mbox,$this->cur_message_idx_arr[$p_idx]);
		print_r(imap_rfc822_parse_headers(imap_fetchheader($this->mbox,$this->cur_message_idx_arr[$p_idx])));

		$this->verbose("*FUNCTION sync_mail()=> syncing mailbox messages to DB.");
		if($this->is_msg_id_exist($header->message_id)===false){
			$this->save_msg($p_idmail_account,$this->cur_message_idx_arr[$p_idx]);
		}
		//BEGIN RECURSION
		if( $p_idx<(count($this->cur_message_idx_arr)-1) )
			$this->sync_mail($p_idmail_account,$p_idx+1);
	}
	private function save_msg($p_idmail_account,$p_msg_idx){
		$header_rfc822=imap_rfc822_parse_headers(imap_fetchheader($this->mbox,$p_msg_idx));
		$idmail_account=$p_idmail_account;
		$message_id=$header_rfc822->message_id;
		$subject=$header_rfc822->subject;
		$message_date_obj=new DateTime($header_rfc822->date);
		$message_date=$message_date_obj->format("Y-m-d H:i:s");
		$qry_str_ins_mail_box="INSERT INTO `mail_box`(idmail_account,message_id,subject,message_date,message_plain,message_html)
			VALUES($idmail_account,'$message_id','$subject','$message_date','','')";
		$qry_ins_mail_box=$this->db->prepare($qry_str_ins_mail_box);
		$err_db_msg="<b>none</b>";
		try{
			$qry_ins_mail_box->execute();
			if($qry_ins_mail_box->rowCount()==1){
				$idmail_box=$this->db->lastInsertId();
				$this->msgtodb($p_msg_idx,$idmail_box);
			}
		}catch(PDOException $e){
			$this->log_insert("Error when calling save_msg:".$e->getMessage());
			$err_db_msg=$e->getMessage();
		}
		$this->verbose("*FUNCTION save_msg() => INSERTING message detail to DB. Message/Error:".$err_db_msg);
	}
	private function msgtodb($p_msg_idx,$p_idmail_box){
		$ifs=imap_fetchstructure($this->mbox,$p_msg_idx);
		if(isset($ifs->parts)){
			foreach($ifs->parts as $partno_idx=>$sub_part)
				$this->msg_structure_parse($p_msg_idx,$sub_part,$partno_idx+1);
		}else{
			$this->msg_structure_parse($p_msg_idx,$ifs,0);
		}
		$this->msgtodb_save_text($p_idmail_box);
	}
	private function msg_structure_parse($p_msg_idx,$p_ifs_objs,$p_partno){
		$this->verbose("parsing part:".$p_partno);
		$data=($p_partno)?
			imap_fetchbody($this->mbox,$p_msg_idx,$p_partno):
			imap_body($this->mbox,$p_msg_idx);
		if($p_ifs_objs->encoding==4){
			$data=quoted_printable_decode($data);
		}elseif($p_ifs_objs->encoding==3)
			$data=base64_decode($data);
		//PARAMETERS
		$params=array();
		if($p_ifs_objs->parameters)
			foreach($p_ifs_objs->parameters as $parameter)
				$params[strtolower($parameter->attribute)]=$parameter->value;
		//TEXT (STORE AT OBJECT cur_msg_content_arr)
		if($p_ifs_objs->type==0 && $data){
			if(strtolower($p_ifs_objs->subtype)=='plain')
				$this->cur_msg_content_arr["msg_plain"].=trim(mb_convert_encoding($data,"UTF-8",$params["charset"]))."\n\n";
			else
				$this->cur_msg_content_arr["msg_html"].=mb_convert_encoding($data,"UTF-8",$params["charset"])."<br/><br/>";
		}
		//RECURSION
		if(isset($p_ifs_objs->parts)){
			foreach($p_ifs_objs->parts as $partno2=>$ifs_objs2)
				$this->msg_structure_parse($p_msg_idx,$ifs_objs2,$p_partno.".".($partno2+1));
		}
	}
	private function msgtodb_save_text($p_idmail_box){//THIS IS LAST PROCEDURE OF POP3 MESSAGE SYNC. MUST SET mail_box.is_finished = 1
		$error_msg="";
		$qry_str_ins="UPDATE `mail_box`
				SET `message_plain`=:message_plain,
					`message_html`=:message_html
				WHERE `idmail_box`=$p_idmail_box LIMIT 1";
		$qry_ins=$this->db->prepare($qry_str_ins);
		try{
			if($qry_ins->execute(array(":message_plain"=>$this->cur_msg_content_arr["msg_plain"]
				,":message_html"=>$this->cur_msg_content_arr["msg_html"]))
			){//BEGIN SET mail_box.is_finished=1
				$qry_str_set_finished="UPDATE `mail_box`
						SET is_finished=1
						WHERE idmail_box=$p_idmail_box
						LIMIT 1";
				$qry_set_finished=$this->db->prepare($qry_str_set_finished);
				try{
					$qry_set_finished->execute();
				}catch(PDOException $e){
					$error_msg.=$e->getMessage()."=======<br />";
					$this->log_insert("Error for function msgtodb_save_text() when trying set is_finished=1".$e->getMessage());
				}
			}
		}catch(PDOException $e){
			$error_msg.=$e->getMessage()."=========<br />";
			$this->log_insert("Error for function msgtodb_save_text():".$e->getMessage());
		}
		$this->verbose("*FUNCTION msgtodb_save_text()=>saving text messages to DB:".$error_msg);
		$this->cur_msg_content_arr_reset();//RESET MESSAGES AFTER SAVED
	}
	private function cur_msg_content_arr_reset(){
		$this->cur_msg_content_arr=array("msg_plain"=>"","msg_html"=>"");
	}
	private function del_unfinished_mail(){
		$error_msg="";
		$qry_str_del_mail_attachments="DELETE mat
				FROM mail_attachments mat
					LEFT JOIN mail_box mb ON ( mat.idmail_box = mb.idmail_box )
				WHERE mb.is_finished=0";
		$qry_del_mail_attachments=$this->db->prepare($qry_str_del_mail_attachments);
		try{
			if($qry_del_mail_attachments->execute()){
				$qry_str_del_mail_box_addresses="DELETE mba
					FROM mail_box_addresses mba
						LEFT JOIN mail_box mb ON(mba.idmail_box = mb.idmail_box)
					WHERE mb.is_finished=0";
				$qry_del_mail_box_addresses=$this->db->prepare($qry_str_del_mail_box_addresses);
				try{
					if($qry_del_mail_box_addresses->execute()){
						$qry_str_del_mail_box="DELETE FROM mail_box 
							WHERE is_finished=0";
						$qry_del_mail_box=$this->db->prepare($qry_str_del_mail_box);
						try{
							$qry_del_mail_box->execute();
						}catch(PDOException $e){
							$error_msg.=$e->getMessage()."========<br/>";
							$this->log_insert("Error for function del_unfinished_mail() for query:\$qry_str_del_mail_box:".$e->getMessage());
						}
					}
				}catch(PDOException $e){
					$error_msg.=$e->getMessage()."======<br/>";
					$this->log_insert("Error for function del_unfinished_mail() for query:\$qry_str_del_mail_box_addresses:".$e->getMessage());
				}
			}
		}catch(PDOException $e){
			$error_msg.=$e->getMessage()."========<br/>";
			$this->log_insert("Error for function del_unfinished_mail() for query:\$qry_str_del_mail_attachments:".$e->getMessage());
		}
		$this->verbose("*FUNCTION del_unfinished_mail()=>deleting unfinished mail_box. Error message:".((empty($error_msg))?"<b>No Error</b>":$error_msg));
	}
	private function is_msg_id_exist($p_msg_idx){
		$error_msg="Empty";
		$qry_str_check="SELECT COUNT(`idmail_account`) FROM `mail_box` WHERE `message_id`=:message_id";
		$qry_check=$this->db->prepare($qry_str_check);
		try{
			$qry_check->execute(array(":message_id"=>$p_msg_idx));
			$fa_count=$qry_check->fetch(PDO::FETCH_NUM);
			if($fa_count[0]>0){
				return true;
			}else{
				return false;
			}
			
		}catch(PDOException $e){
			$error_msg=$e->getMessage();
			$this->log_insert("Error when calling is_msg_id_exist():".$e->getMessage());
			return false;//Consider as no message_id exist yet if error
		}
		
		$this->verbose("*FUNCTION:Checking if have mesage_id: $p_msg_idx already exist at the mailbox record. Error message: $error_msg");
	}
	function get_msg_idxs(){
		if($this->check_conn()===false)
			return false;
		$message_idxs=imap_search($this->mbox,"ALL");//Later make this by date filtering to ignore very old emails!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		if($message_idxs!==false){
// 			rsort($message_idxs);
			$this->cur_message_idx_arr=$message_idxs;
		}
		$this->verbose("*FUNCTION get_msg_idxs()=> get messages at related email account. Return :<b>".count($message_idxs)."</b> of mailbox");
	}
	private function get_mail_account_list(){
// 		$json_result="";
		$this->verbose("*CONSTRUCTOR call get_mail_account_list() => init by collecting all address available at DB record for syncing process.");
		$qry_str_sel="SELECT * FROM `mail_account` ORDER BY `idmail_account` ASC";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_sel->execute();
			return $qry_sel->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			$this->verbose("*Error with get_mail_account_list() => ".$e->getMessage());
			$this->log_insert("There was error with get_mail_account_list()".$e->getMessage());
			return array();/*
			$json_result=array(
				'success' => false,
				'message' => $e->getMessage()
			);*/
		}
	}
	public function getmsg($p_idx){
		if($this->check_conn())
			return false;
		$header=imap_headerinfo($this->mbox,$p_idx);
		$header_rfc822=imap_rfc822_parse_headers(imap_fetchheader($this->mbox,$p_idx));
		print_r($header);echo "<br />";
		$date_msg=new DateTime($header_rfc822->date);
// 		echo $date_msg->format('Y-m-d H:i:s')."===".$header_rfc822->date."<br />";
// 		echo $header_rfc822->from[0]->host."<br />";
	}
	public function parse_msg(){
		$this->mbox_init("test@allfromboatfurniture.com","test");
		$this->save_msg(1,5);
/*
		$data=imap_fetchbody($this->mbox,5,"1.2.1");
		echo $data;*/
/*
		$ifs=imap_fetchstructure($this->mbox,5);
		print_r($ifs);*/
	}
	function fetch(){
		$this->mbox_init("test@allfromboatfurniture.com","test");
		if($this->check_conn()===false)
			return false;
		$messages=imap_search($this->mbox,"ALL");
		if($messages!==false){
			rsort($messages);
			foreach($messages as $message_idx){
				$this->getmsg($message_idx);
			}
		}
	}
	/*
	function __destruct(){
		if($this->mbox!==false)
			imap_close($this->mbox);
	}*/

/*
	RFC822=>stdClass Object ( 
		[date] => Tue, 08 Jul 2014 14:12:55 +0700 
		[Date] => Tue, 08 Jul 2014 14:12:55 +0700 
		[subject] => Test 2nd 
		[Subject] => Test 2nd 
		[message_id] => <53BB99F7.2020706@jeparagreenfurniture.com> 
		[toaddress] => test@allfromboatfurniture.com 
		[to] => Array ( [0] => stdClass Object ( [mailbox] => test [host] => allfromboatfurniture.com ) ) 
		[fromaddress] => Bayu IT WJB 
		[from] => Array ( [0] => stdClass Object ( [personal] => Bayu IT WJB [mailbox] => it [host] => jeparagreenfurniture.com ) ) 
		[reply_toaddress] => Bayu IT WJB 
		[reply_to] => Array ( [0] => stdClass Object ( [personal] => Bayu IT WJB [mailbox] => it [host] => jeparagreenfurniture.com ) ) 
		[senderaddress] => Bayu IT WJB
		[sender] => Array ( [0] => stdClass Object ( [personal] => Bayu IT WJB [mailbox] => it [host] => jeparagreenfurniture.com ) )
	)*/
/*
	Standard=>stdClass Object ( 
		[date] => Tue, 08 Jul 2014 14:12:55 +0700 
		[Date] => Tue, 08 Jul 2014 14:12:55 +0700 
		[subject] => Test 2nd 
		[Subject] => Test 2nd 
		[message_id] => <53BB99F7.2020706@jeparagreenfurniture.com> 
		[toaddress] => test@allfromboatfurniture.com [to] => Array ( [0] => stdClass Object ( [mailbox] => test [host] => allfromboatfurniture.com ) ) [fromaddress] => Bayu IT WJB [from] => Array ( [0] => stdClass Object ( [personal] => Bayu IT WJB [mailbox] => it [host] => jeparagreenfurniture.com ) ) [reply_toaddress] => Bayu IT WJB [reply_to] => Array ( [0] => stdClass Object ( [personal] => Bayu IT WJB [mailbox] => it [host] => jeparagreenfurniture.com ) ) [senderaddress] => Bayu IT WJB [sender] => Array ( [0] => stdClass Object ( [personal] => Bayu IT WJB [mailbox] => it [host] => jeparagreenfurniture.com ) ) [Recent] => N [Unseen] => [Flagged] => [Answered] => [Deleted] => [Draft] => [Msgno] => 2 [MailDate] => 8-Jul-2014 14:12:55 +0700 [Size] => 3009 [udate] => 1404803575 ) */
}
?>
