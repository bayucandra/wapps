<?php

// require_once($path_relative."/functions/general.php");
// require_once($path_relative."/config.php");
// require_once($path_relative."/connect/db.php");
if (!isset($php_mailer_path))
    die("PHP mailer not included");
require_once($php_mailer_path);

b_set_time_zone("Asia/Jakarta");

class BMail extends PHPMailer {

    private $mbox, $db, $msg_encoding, $arr_settings_server;
    private $mail_account_arr, $is_verbose, $mail_box_attachment_path;
    private $cur_message_idx_arr; //Array ( [0] => 2 [1] => 1 )
    private $cur_msg_content_arr; //Array ("msg_html","msg_plain");

    function __construct($p_db_PDO, $p_arr = array()) {//$p_arr=array("verbose"=>true/false,"msg_encoding"=>"UTF-8 etc.","mail_box_attachment_path")
	parent::__construct(true);
	$this->db = $p_db_PDO;

	$this->arr_settings_server = $this->get_settings_server();
	if (!$this->arr_settings_server) {
	    $this->log_insert("Error when parsing system configuration from database server(constructor error)");
	    return false;
	}

	$this->is_verbose = false;
	$this->cur_msg_content_arr_reset();
	if (isset($p_arr)) {
	    if (isset($p_arr["verbose"]))
		$this->is_verbose = $p_arr["verbose"];
	    if (isset($p_arr["msg_encoding"]))
		$this->msg_encoding = $p_arr["msg_encoding"];
	    if (isset($p_arr["mail_box_attachment_path"]))
		$this->mail_box_attachment_path = $p_arr["mail_box_attachment_path"];
	}

	$this->mbox = false; //INIT with false
	if ($this->is_verbose)
	    echo "<b>=========START CONSTRUCTOR=========</b></br><br/>";

	$this->mail_account_arr = $this->get_mail_account_list();

	if ($this->is_verbose)
	    echo "<br/><b>*********END CONSTRUCTOR***********</b><br/>";
    }

    private function get_account_arr() {
	$idmail_account = $_SESSION[SESSION_NM]["idmail_account"];
	$qry_str_sel = "SELECT * FROM `mail_account` WHERE idmail_account=$idmail_account LIMIT 1";
	$qry_sel = $this->db->prepare($qry_str_sel);
	try {
	    $qry_sel->execute();
	    $fa_sel = $qry_sel->fetch(PDO::FETCH_ASSOC);
	    $account_detail_arr = array("email" => $fa_sel["email"],
		"email_remote" => $fa_sel["email_remote"],
		"password" => $fa_sel["password"]);
	    return $account_detail_arr;
	} catch (PDOException $e) {
	    $this->log_insert("FUNCTION error get_password:" . $e->getMessage());
	}
    }

    public function send_mail($arr_params = array("idmail_account"=>-1, "addr_to_csv" => "","addr_cc_csv" => "","subject"=>"","message"=>"", "att_json_arr" => "","compose_itemId"=>null, "idmail_box" => null, "use_local_account" => true, "direct_sending" => true)) {
	$account_arr=$this->get_account_arr();
	echo $account_arr["email"]."=".$account_arr["password"];
	try{
	    $this->IsSMTP();
//	    $this->SMTPDebug=1;

	    $this->SMTPAuth = true;
    //	$this->Host = $_SERVER["HTTP_HOST"];
	    $this->Host = $this->arr_settings_server['mail_smtp_server'];
	    $this->Port = $this->arr_settings_server['mail_smtp_port'];
	    $this->Username = $account_arr["email"];
	    $this->Password = $account_arr["password"];

	    $this->clearAllRecipients();
	    $this->clearAttachments();

	    $this->set_addrs($arr_params["addr_to_csv"],"TO");
	    if($arr_params["use_local_account"])
		$this->setFrom($account_arr["email"]);
	    $this->Subject = $arr_params["subject"];
	    $this->msgHTML($arr_params["message"]);
	    $this->add_attachments($arr_params["idmail_account"],$arr_params["att_json_arr"],$arr_params["compose_itemId"]);
	    $this->Send();
	}catch (phpmailerException $e) {
	    echo $e->errorMessage(); //Pretty error messages from PHPMailer
	}
    }
    
    private function set_addrs($p_csv_str,$addr_type){
	$rfc822_addrs_arr=brfc822_addr_parse($p_csv_str);
	if(!$rfc822_addrs_arr)
		return;
	foreach($rfc822_addrs_arr as $rfc822_addr_arr){
		if($addr_type=="TO"){
		    $this->AddAddress($rfc822_addr_arr["email_address"],$rfc822_addr_arr["contact_name"]);
		}elseif($addr_type=="CC"){
		    $this->AddCC($rfc822_addr_arr["email_address"],$rfc822_addr_arr["contact_name"]);
		}
	}
    }
    private function add_attachments($p_idmail_account,$p_json,$p_compose_itemId){
	foreach($p_json as $record){
	    $file_name=$record["file_name"];
	    $att_path=$this->mail_box_attachment_path."/".$p_idmail_account."/tmp_att/".$p_compose_itemId."/".$file_name;
	    $this->addAttachment($att_path,$file_name);
	}
    }

    function error_handler($msg) {
	print("My Site Error");
	print("Description:");
	printf("%s", $msg);
	exit;
    }

    public function get_settings_server() {//Getting settings from database server
	$qry_str_check_empty = "SELECT COUNT(*) FROM `settings` WHERE value=''";
	$qry_check_empty = $this->db->prepare($qry_str_check_empty);
	try {
	    $qry_check_empty->execute();
	    $arr_check_empty = $qry_check_empty->fetch(PDO::FETCH_NUM);
	    if ($arr_check_empty[0] > 0) {
		$this->log_insert("Error!!!, there is configuration remain unset.");
		return false;
	    }
	} catch (PDOException $e) {
	    $this->log_insert("There is error when checking for configuration empty:" . $e->getMessage());
	    return false;
	}
	$qry_str_sel = "SELECT * FROM `settings`";
	$qry_sel = $this->db->prepare($qry_str_sel);
	try {
	    $qry_sel->execute();
	    $arr_config_server_tmp = array();
	    while ($fa_sel = $qry_sel->fetch(PDO::FETCH_ASSOC)) {
		$description = $fa_sel["description"];
		$value = $fa_sel["value"];
		$arr_config_server_tmp[$description] = $value;
	    }
	    return $arr_config_server_tmp;
	} catch (PDOException $e) {
	    $this->log_insert("Error when getting configurations from database server:" . $e->getMessage());
	    return false;
	}
    }

    private function log_insert($p_str) {
	log_insert($this->db, $p_str);
    }

    private function verbose($p_str) {
	if ($this->is_verbose) {
	    echo $p_str . "<br />";
	}
    }

    private function mbox_init($p_email, $p_password) {//$p_arr_account=array(email,password);
	$is_mbox_clear = true;
	if ($this->mbox !== false)
	    $is_mbox_clear = imap_close($this->mbox);

	if ($is_mbox_clear === true) {
	    $exp_email = explode("@", $p_email);
	    $mailhost = $exp_email[1];
	    $username = $p_email;
	    $password = $p_password;
	    $this->mbox = imap_open("{" . $mailhost . ":110/pop3/novalidate-cert}", $username, $password);
	    $mbox_message = "Mailbox stream opened <b>successfuly</b>";
	    if ($this->check_conn() === false)
		$mbox_message = "Mailbox stream <b>failed</b> to open.";
	    $this->verbose("*FUNCTION mbox_init() => init mbox stream, param:\$p_email=$p_email . Status:$mbox_message");
	}else {
	    $this->log_insert("Error when init POP3 Connection for email:" . $p_email);
	    return false;
	}
    }

    private function check_conn() {
	if ($this->mbox === false)
	    return false;
    }

    public function sync_accounts($p_account_idx = 0) {
	$this->del_unfinished_mail();
	$this->verbose("*FUNCTION sync_accounts()=>parsing database, param \$p_account_idx=$p_account_idx");
	$idmail_account = $this->mail_account_arr[$p_account_idx]["idmail_account"];
	$email_remote = $this->mail_account_arr[$p_account_idx]["email_remote"];
	$password = $this->mail_account_arr[$p_account_idx]["password"];
	$this->mbox_init($email_remote, $password);
	$this->get_msg_idxs(); //GET MESSAGE INDEXES ARRAY
	$this->sync_mail($idmail_account);
	//BEGIN RECURSION
	if ($p_account_idx < (count($this->mail_account_arr) - 1))
	    $this->sync_accounts($p_account_idx + 1);
    }

    private function sync_mail($p_idmail_account, $p_idx = 0) {
	if ($this->check_conn() === false)
	    return false;
	$header = imap_headerinfo($this->mbox, $this->cur_message_idx_arr[$p_idx]);

	$this->verbose("*FUNCTION sync_mail()=> syncing mailbox messages to DB.");
	if ($this->is_msg_id_exist($header->message_id) === false) {
	    $this->save_msg($p_idmail_account, $this->cur_message_idx_arr[$p_idx]);
	}
	//BEGIN RECURSION
	if ($p_idx < (count($this->cur_message_idx_arr) - 1))
	    $this->sync_mail($p_idmail_account, $p_idx + 1);
    }

    private function save_msg($p_idmail_account, $p_msg_idx) {
	$header_rfc822 = imap_rfc822_parse_headers(imap_fetchheader($this->mbox, $p_msg_idx));
	$idmail_account = $p_idmail_account;
	$message_id = $header_rfc822->message_id;
	$subject = $header_rfc822->subject;
	$message_date_obj = new DateTime($header_rfc822->date);
	$message_date = $message_date_obj->format("Y-m-d H:i:s");
	$qry_str_ins_mail_box = "INSERT INTO `mail_box`(idmail_box_type,idmail_account,message_id,subject,message_date,message_plain,message_html)
			VALUES(1,$idmail_account,'$message_id','$subject','$message_date','','')";
	$qry_ins_mail_box = $this->db->prepare($qry_str_ins_mail_box);
	$err_db_msg = "<b>none</b>";
	try {
	    $qry_ins_mail_box->execute();
	    if ($qry_ins_mail_box->rowCount() == 1) {
		$idmail_box = $this->db->lastInsertId();
		$this->msgtodb($p_idmail_account, $p_msg_idx, $idmail_box);
		$this->mail_box_addresses_insert($idmail_box, $header_rfc822);
	    }
	} catch (PDOException $e) {
	    $err_msg = $e->getMessage();
	    $this->log_insert("Error when calling save_msg:" . $err_msg);
	    $err_db_msg = $err_msg;
	}
	$this->verbose("*FUNCTION save_msg() => INSERTING message detail to DB. Message/Error:" . $err_db_msg);
    }

    private function msgtodb($p_idmail_account, $p_msg_idx, $p_idmail_box) {
	$ifs = imap_fetchstructure($this->mbox, $p_msg_idx);
	if (isset($ifs->parts)) {
	    foreach ($ifs->parts as $partno_idx => $sub_part)
		$this->msg_structure_parse($p_idmail_account, $p_msg_idx, $p_idmail_box, $sub_part, $partno_idx + 1);
	} else {
	    $this->msg_structure_parse($p_idmail_account, $p_msg_idx, $p_idmail_box, $ifs, 0);
	}
	$this->msgtodb_save_text($p_idmail_box);
    }

    private function msg_structure_parse($p_idmail_account, $p_msg_idx, $p_idmail_box, $p_ifs_objs, $p_partno) {
// 		$this->verbose("parsing part:".$p_partno);
	$data = ($p_partno) ?
		imap_fetchbody($this->mbox, $p_msg_idx, $p_partno) :
		imap_body($this->mbox, $p_msg_idx);
	if ($p_ifs_objs->encoding == 4) {
	    $data = quoted_printable_decode($data);
	} elseif ($p_ifs_objs->encoding == 3)
	    $data = base64_decode($data);
	//PARAMETERS
	$params = array();
	if ($p_ifs_objs->parameters)
	    foreach ($p_ifs_objs->parameters as $parameter)
		$params[strtolower($parameter->attribute)] = $parameter->value;
	//BEGIN TEXT (STORE AT OBJECT cur_msg_content_arr)=========
	if ($p_ifs_objs->type == 0 && $data) {
	    if (strtolower($p_ifs_objs->subtype) == 'plain')
		$this->cur_msg_content_arr["msg_plain"].=trim(mb_convert_encoding($data, $this->msg_encoding, $params["charset"])) . "\n\n";
	    else
		$this->cur_msg_content_arr["msg_html"].=mb_convert_encoding($data, $this->msg_encoding, $params["charset"]) . "<br/><br/>";
	}
	//END TEXT******************
	//BEGIN ATTACHMENTS===============
	if (isset($params["filename"]) || isset($params["name"])) {
	    $disposition = strtolower($p_ifs_objs->disposition);
	    $basename = (isset($params["filename"])) ? $params["filename"] : $params["name"];
	    $this->save_attachment($data, $p_idmail_account, $p_idmail_box, $basename, $disposition, $p_ifs_objs->type);
	}
	//END ATTACHEMENTS***************
	unset($data); //SAVE MEMORY BY DELETING $data
	//RECURSION
	if (isset($p_ifs_objs->parts)) {
	    foreach ($p_ifs_objs->parts as $partno2 => $ifs_objs2)
		$this->msg_structure_parse($p_idmail_account, $p_msg_idx, $p_idmail_box, $ifs_objs2, $p_partno . "." . ($partno2 + 1));
	}
    }

    private function save_attachment($p_data, $p_idmail_account, $p_idmail_box, $p_basename, $p_disposition, $p_type) {
	$dst_path_base = $this->mail_box_attachment_path . "/" . $p_idmail_account . "/" . $p_idmail_box;
	if (!file_exists($dst_path_base)) {
	    if (!mkdir($dst_path_base, 0775, true))
		return;
	}
	$order_no = $this->save_attachment_db($p_idmail_box, $p_basename, $p_disposition, $p_type);
	$dst_path_file = $dst_path_base . "/" . $order_no . "-" . $p_basename;

	$fp = fopen($dst_path_file, 'w');
	if ($fp !== false) {
	    fwrite($fp, $p_data);
	    fclose($fp);
	}
    }

    private function save_attachment_db($p_idmail_box, $p_basename, $p_disposition, $p_type) {
	$order_no = 0;
	$qry_str_count_exist = "SELECT COUNT(idmail_box) FROM mail_box_attachments WHERE idmail_box=$p_idmail_box LIMIT 1";
	$qry_count_exist = $this->db->prepare($qry_str_count_exist);
	try {
	    if ($qry_count_exist->execute()) {
		$fa_count = $qry_count_exist->fetch(PDO::FETCH_NUM);
		$attachment_count = $fa_count[0];
		$order_no = $attachment_count + 1;
		$qry_str_ins = "INSERT INTO mail_box_attachments(`order_no`,`idmail_box`,`basename`,`disposition`,`type`)
						VALUES($order_no,$p_idmail_box,:basename,'$p_disposition','$p_type')";
		$qry_ins = $this->db->prepare($qry_str_ins);
		try {
		    $qry_ins->execute(array(":basename" => $p_basename));
		} catch (PDOException $e) {
		    $this->log_insert("save_attachment_db()-insert:" . $e->getMessage());
		}
	    }
	} catch (PDOException $e) {
	    $this->log_insert("save_attachment_db():" . $e->getMessage());
	}
	return $order_no;
    }

    private function msgtodb_save_text($p_idmail_box) {//THIS IS LAST PROCEDURE OF POP3 MESSAGE SYNC. MUST SET mail_box.is_finished = 1
	$error_msg = "";
	$qry_str_ins = "UPDATE `mail_box`
				SET `message_plain`=:message_plain,
					`message_html`=:message_html
				WHERE `idmail_box`=$p_idmail_box LIMIT 1";
	$qry_ins = $this->db->prepare($qry_str_ins);
	try {
	    $qry_ins->execute(array(":message_plain" => $this->cur_msg_content_arr["msg_plain"]
		, ":message_html" => $this->cur_msg_content_arr["msg_html"]));
	} catch (PDOException $e) {
	    $error_msg.=$e->getMessage() . "=========<br />";
	    $this->log_insert("Error for function msgtodb_save_text():" . $e->getMessage());
	}
	$this->verbose("*FUNCTION msgtodb_save_text()=>saving text messages to DB:" . $error_msg);
	$this->cur_msg_content_arr_reset(); //RESET MESSAGES AFTER SAVED
    }

    private function cur_msg_content_arr_reset() {
	$this->cur_msg_content_arr = array("msg_plain" => "", "msg_html" => "");
    }

    private function del_unfinished_mail() {
	$error_msg = "";
	$qry_str_del_mail_attachments = "DELETE mba
				FROM mail_box_attachments mba
					LEFT JOIN mail_box mb ON ( mba.idmail_box = mb.idmail_box )
				WHERE mb.is_finished=0";
	$qry_del_mail_attachments = $this->db->prepare($qry_str_del_mail_attachments);
	try {
	    if ($qry_del_mail_attachments->execute()) {
		$qry_str_del_mail_box_addresses = "DELETE mba
					FROM mail_box_addresses mba
						LEFT JOIN mail_box mb ON(mba.idmail_box = mb.idmail_box)
					WHERE mb.is_finished=0";
		$qry_del_mail_box_addresses = $this->db->prepare($qry_str_del_mail_box_addresses);
		try {
		    if ($qry_del_mail_box_addresses->execute()) {
			$qry_str_del_mail_box = "DELETE FROM mail_box 
							WHERE is_finished=0";
			$qry_del_mail_box = $this->db->prepare($qry_str_del_mail_box);
			try {
			    $qry_del_mail_box->execute();
			} catch (PDOException $e) {
			    $error_msg.=$e->getMessage() . "========<br/>";
			    $this->log_insert("Error for function del_unfinished_mail() for query:\$qry_str_del_mail_box:" . $e->getMessage());
			}
		    }
		} catch (PDOException $e) {
		    $error_msg.=$e->getMessage() . "======<br/>";
		    $this->log_insert("Error for function del_unfinished_mail() for query:\$qry_str_del_mail_box_addresses:" . $e->getMessage());
		}
	    }
	} catch (PDOException $e) {
	    $error_msg.=$e->getMessage() . "========<br/>";
	    $this->log_insert("Error for function del_unfinished_mail() for query:\$qry_str_del_mail_attachments:" . $e->getMessage());
	}
	$this->verbose("*FUNCTION del_unfinished_mail()=>deleting unfinished mail_box. Error message:" . ((empty($error_msg)) ? "<b>No Error</b>" : $error_msg));
    }

    private function is_msg_id_exist($p_msg_idx) {
	$error_msg = "Empty";
	$qry_str_check = "SELECT COUNT(`idmail_account`) FROM `mail_box` WHERE `message_id`=:message_id";
	$qry_check = $this->db->prepare($qry_str_check);
	try {
	    $qry_check->execute(array(":message_id" => $p_msg_idx));
	    $fa_count = $qry_check->fetch(PDO::FETCH_NUM);
	    if ($fa_count[0] > 0) {
		return true;
	    } else {
		return false;
	    }
	} catch (PDOException $e) {
	    $error_msg = $e->getMessage();
	    $this->log_insert("Error when calling is_msg_id_exist():" . $e->getMessage());
	    return false; //Consider as no message_id exist yet if error
	}

	$this->verbose("*FUNCTION:Checking if have mesage_id: $p_msg_idx already exist at the mailbox record. Error message: $error_msg");
    }

    private function mail_box_addresses_insert($p_idmail_box, $p_header_rfc822) {
	if (isset($p_header_rfc822->to))
	    $this->mail_box_addresses_sql($p_idmail_box, "to", $p_header_rfc822->to);
	if (isset($p_header_rfc822->from))
	    $this->mail_box_addresses_sql($p_idmail_box, "from", $p_header_rfc822->from);
	if (isset($p_header_rfc822->cc))
	    $this->mail_box_addresses_sql($p_idmail_box, "cc", $p_header_rfc822->cc);
	if (isset($p_header_rfc822->reply_to))
	    $this->mail_box_addresses_sql($p_idmail_box, "reply_to", $p_header_rfc822->reply_to);
	if (isset($p_header_rfc822->sender))
	    $this->mail_box_addresses_sql($p_idmail_box, "sender", $p_header_rfc822->sender);

	//BEGIN SET mail_box.is_finished=1
	$qry_str_set_finished = "UPDATE `mail_box`
				SET is_finished=1
				WHERE idmail_box=$p_idmail_box
				LIMIT 1";
	$qry_set_finished = $this->db->prepare($qry_str_set_finished);
	try {
	    $qry_set_finished->execute();
	} catch (PDOException $e) {
	    $error_msg.=$e->getMessage() . "=======<br />";
	    $this->log_insert("Error for function msgtodb_save_text() when trying set is_finished=1" . $e->getMessage());
	}
    }

    private function mail_box_addresses_sql($p_idmail_box, $p_addr_type, $p_addrs) {
	foreach ($p_addrs as $addr) {
	    $personal = "";
	    if (isset($addr->personal))
		$personal = $addr->personal;
	    $email_addr = $addr->mailbox . "@" . $addr->host;
	    $qry_str_ins = "INSERT INTO `mail_box_addresses`(idmail_box,idmail_addr_type,personal,email_address)
				VALUES($p_idmail_box,(SELECT idmail_addr_type FROM mail_address_type WHERE `description`='$p_addr_type' LIMIT 1)
					,'$personal','$email_addr')";
	    $qry_ins = $this->db->prepare($qry_str_ins);
	    try {
		$qry_ins->execute();
	    } catch (PDOException $e) {
		$this->log_insert("mail_box_addresses_sql():" . $e->getMessage());
	    }
	}
    }

    function get_msg_idxs() {
	if ($this->check_conn() === false)
	    return false;
	$message_idxs = imap_search($this->mbox, "ALL"); //Later make this by date filtering to ignore very old emails!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	if ($message_idxs !== false) {
// 			rsort($message_idxs);
	    $this->cur_message_idx_arr = $message_idxs;
	}
	$this->verbose("*FUNCTION get_msg_idxs()=> get messages at related email account. Return :<b>" . count($message_idxs) . "</b> of mailbox");
    }

    private function get_mail_account_list() {
// 		$json_result="";
	$this->verbose("*CONSTRUCTOR call get_mail_account_list() => init by collecting all address available at DB record for syncing process.");
	$qry_str_sel = "SELECT * FROM `mail_account` ORDER BY `idmail_account` ASC";
	$qry_sel = $this->db->prepare($qry_str_sel);
	try {
	    $qry_sel->execute();
	    return $qry_sel->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
	    $this->verbose("*Error with get_mail_account_list() => " . $e->getMessage());
	    $this->log_insert("There was error with get_mail_account_list()" . $e->getMessage());
	    return array(); /*
	      $json_result=array(
	      'success' => false,
	      'message' => $e->getMessage()
	      ); */
	}
    }

    public function att_upload($p_files, $p_idmail_account, $p_panel_itemid) {
	ini_set("upload_max_filesize", "20M");
	ini_set("post_max_size", "30M");
	$json_result = array(
	    "success" => true,
	    "message" => "",
	    "file_name" => ""
	);
	$json_result["file_name"] = $p_files["file_attach"]["name"];
	$dst_path_base = $this->mail_box_attachment_path . "/" . $p_idmail_account . "/tmp_att/" . $p_panel_itemid;
	if (!file_exists($dst_path_base)) {
	    if (!mkdir($dst_path_base, 0775, true))
		return;
	}
	$upload_path = $dst_path_base . "/" . $p_files["file_attach"]["name"];
	$json_result["message"] = $upload_path;
	if (!move_uploaded_file($p_files["file_attach"]["tmp_name"], $upload_path))
	    $json_result["success"] = false;
	echo json_encode($json_result);
    }

    /*
      public function getmsg($p_idx){
      if($this->check_conn())
      return false;
      $header=imap_headerinfo($this->mbox,$p_idx);
      $header_rfc822=imap_rfc822_parse_headers(imap_fetchheader($this->mbox,$p_idx));
      print_r($header);echo "<br />";
      $date_msg=new DateTime($header_rfc822->date);
      // 		echo $date_msg->format('Y-m-d H:i:s')."===".$header_rfc822->date."<br />";
      // 		echo $header_rfc822->from[0]->host."<br />";
      } */

    /*
      public function parse_msg(){
      $this->mbox_init("test@allfromboatfurniture.com","test");
      $this->save_msg(1,5);

      // 		$data=imap_fetchbody($this->mbox,5,"1.2.1");
      // 		echo $data;

      // 		$ifs=imap_fetchstructure($this->mbox,5);
      // 		print_r($ifs);
      } */
    /*
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
      } */
    /*
      function __destruct(){
      if($this->mbox!==false)
      imap_close($this->mbox);
      } */

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
      ) */
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
