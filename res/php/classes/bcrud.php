<?php
class BCrud{
	private $db;
	public function __construct($p_db_PDO){
		$this->db=$p_db_PDO;
	}
	private function log_insert($p_str){
		log_insert($this->db,$p_str);
	}
	public function mail_inbox_list($p_idmail_account){
		$json_result=array(
			"success"=>false,
			"records"=>array(),
			"message"=>""
		);
		$qry_str_sel="SELECT mb.idmail_box,CONCAT(mba.personal,' &lt;',mba.email_address,'&gt;') AS addr_from, mb.subject,mb.message_date,mb.message_plain,mb.message_html
				,(SELECT COUNT(*) FROM mail_box_attachments mbatt WHERE mbatt.idmail_box=mb.idmail_box) AS att_count
			FROM mail_box mb
				LEFT JOIN mail_box_type mbt
					ON(mb.idmail_box_type=mbt.idmail_box_type)
				LEFT JOIN (mail_box_addresses mba
					LEFT JOIN mail_address_type mat
						ON(mba.idmail_addr_type=mat.idmail_addr_type)
				)ON(mb.idmail_box=mba.idmail_box)
				WHERE mb.idmail_account=$p_idmail_account
					AND mat.description='from' AND mbt.description='inbox'
				ORDER BY mb.message_date DESC";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_sel->execute();
			$json_result["success"]=true;
			$tmp_records=$qry_sel->fetchAll(PDO::FETCH_ASSOC);
			foreach($tmp_records as $record){
				$tmp_record=array();
				foreach($record as $key=>$col_val){
					if($key=="message_html"){
						if(!empty($col_val)){
							$doc=new DOMDocument();
							$doc->loadHTML($col_val);
							$mock=new DOMDocument();
							$body=$doc->getElementsByTagName('body')->item(0);
							foreach($body->childNodes as $child){
								$mock->appendChild($mock->importNode($child,true));
							}
							$tmp_record[$key]=$mock->saveHTML();
						}else{
							$tmp_record[$key]=$col_val;
						}
					}else{
						$tmp_record[$key]=$col_val;
					}
				}
				$json_result["records"][]=$tmp_record;
			}
// 			$json_result["records"]=$qry_sel->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			$json_result["success"]=false;
			$json_result["message"]=$e->getMessage();
		}
		echo json_encode($json_result);
	}
	public function mail_address_list($p_idmail_box){
		$json_result=array(
			"success"=>true,
			"addr_to"=>array(),
			"addr_cc"=>array(),
			"message"=>""
		);
// 		$is_to_addr_exist=false;
		$qry_str_count_to="SELECT COUNT(*) FROM mail_box_addresses mba
				LEFT JOIN mail_address_type mat
					ON(mba.idmail_addr_type=mat.idmail_addr_type)
			WHERE mba.idmail_box=$p_idmail_box
				AND mat.description='to'";
		$qry_count_to=$this->db->prepare($qry_str_count_to);
		try{
			$qry_count_to->execute();
			$fn_count_to=$qry_count_to->fetch(PDO::FETCH_NUM);
			if($fn_count_to[0]>0){
				$qry_str_sel_to="SELECT mba.* FROM mail_box_addresses mba
						LEFT JOIN mail_address_type mat
							ON(mba.idmail_addr_type=mat.idmail_addr_type)
					WHERE mba.idmail_box=$p_idmail_box
						AND mat.description='to'";
				$qry_sel_to=$this->db->prepare($qry_str_sel_to);
				try{
					$qry_sel_to->execute();
// 					$is_to_addr_exist=true;
// 					$html_to="<table class=\"inbox-header-tbl\"><tr><td class=\"label\">To</td>";
// 					$str_addrs_to="";
					while(($fa_sel_to=$qry_sel_to->fetch(PDO::FETCH_ASSOC))!==false){
						if(!empty($fa_sel_to["personal"]))
							$json_result["addr_to"][]=$fa_sel_to["personal"]." &lt;".$fa_sel_to["email_address"]."&gt;";
						else
							$json_result["addr_to"][]=$fa_sel_to["email_address"];
					}
// 					$str_addrs_to=substr($str_addrs_to,0,strlen($str_addrs_to)-2);
// 					$html_to.="<td><div class=\"addr-wrapper\">".htmlentities($str_addrs_to)."</div></td>";
// 					$html_to.="</tr>";
// 					echo $html_to;
				}catch(PDOException $e){
					$json_result["success"]=false;
					$json_result["message"].=$e->getMessage();
				}
			}
		}catch(PDOException $e){
			$json_result["success"]=false;
			$json_result["message"].=$e->getMessage();
		}
		//BEGIN CC ADDRESS
// 		$is_cc_addr_exist=false;
		$qry_str_count_cc="SELECT COUNT(*) FROM mail_box_addresses mba
				LEFT JOIN mail_address_type mat
					ON(mba.idmail_addr_type=mat.idmail_addr_type)
			WHERE mba.idmail_box=$p_idmail_box
				AND mat.description='cc'";
		$qry_count_cc=$this->db->prepare($qry_str_count_cc);
		try{
			$qry_count_cc->execute();
			$fn_count_cc=$qry_count_cc->fetch(PDO::FETCH_NUM);
			if($fn_count_cc[0]>0){
				$qry_str_sel_cc="SELECT mba.* FROM mail_box_addresses mba
						LEFT JOIN mail_address_type mat
							ON(mba.idmail_addr_type=mat.idmail_addr_type)
					WHERE mba.idmail_box=$p_idmail_box
						AND mat.description='cc'";
				$qry_sel_cc=$this->db->prepare($qry_str_sel_cc);
				try{
					$qry_sel_cc->execute();
// 					$is_cc_addr_exist=true;
// 					$html_cc="";
// 					if(!$is_to_addr_exist)
// 						$html_cc.="<table class=\"inbox-header-tbl\">";
// 					$html_cc.="<tr><td class=\"label\">CC</td>";
// 					$str_addrs_cc="";
					while(($fa_sel_cc=$qry_sel_cc->fetch(PDO::FETCH_ASSOC))!==false){
						if(!empty($fa_sel_cc["personal"]))
							$json_result["addr_cc"][]=$fa_sel_cc["personal"]." &lt;".$fa_sel_cc["email_address"]."&gt;";
						else
							$json_result["addr_cc"][]=$fa_sel_cc["email_address"];
					}
// 					$str_addrs_cc=substr($str_addrs_cc,0,strlen($str_addrs_cc)-2);
// 					$html_cc.="<td><div class=\"addr-wrapper\">".htmlentities($str_addrs_cc)."</div></td>";
// 					$html_cc.="</tr>";
// 					echo $html_cc;
				}catch(PDOException $e){
					$json_result["success"]=false;
					$json_result["message"].=$e->getMessage();
				}
			}
		}catch(PDOException $e){
			$json_result["success"]=false;
			$json_result["message"].=$e->getMessage();
		}/*
		if($is_to_addr_exist||$is_cc_addr_exist){
			echo "</table>";
		}*/
		echo json_encode($json_result);
		
	}
	public function mail_attachment_list($p_idmail_box){
		$json_result=array(
			"success"=>false,
			"records"=>array(),
			"message"=>""
		);
		$qry_str_sel="SELECT * FROM mail_box_attachments
			WHERE idmail_box=$p_idmail_box";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_sel->execute();
			$json_result["success"]=true;
			$json_result["records"]=$qry_sel->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			$json_result["success"]=false;
			$json_result["message"]=$e->getMessage();
		}
		echo json_encode($json_result);
	}
	public function generic_data($p_table_str,$p_field_str,$p_cond_str){
		$json_result=array(
			"success"=>false,
			"records"=>array(),
			"message"=>""
		);
		$qry_str_sel="SELECT * FROM `$p_table_str`
			WHERE `$p_field_str`=$p_cond_str";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_sel->execute();
			$json_result["success"]=true;
			$json_result["records"]=$qry_sel->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			$json_result["success"]=false;
			$json_result["message"]=$e->getMessage();
		}
		echo json_encode($json_result);
	}
	public function mail_address_book($p_idmail_account,$qry_cond){
		$json_result=array(
			"success"=>false,
			"records"=>array(),
			"message"=>"",
			"totalCount"=>0
		);
		
		$page=$_REQUEST['page'];$start=$_REQUEST['start'];$limit=$_REQUEST['limit'];
		$qry_str_sel="SELECT * FROM `mail_address_book`
			WHERE `idmail_account`=$p_idmail_account
				AND ((person_name LIKE '%$qry_cond%') OR (email LIKE '%$qry_cond%'))
			ORDER BY email ASC
			LIMIT $start,$limit";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_sel->execute();
			$json_result["success"]=true;
// 			$json_result["records"]=$qry_sel->fetchAll(PDO::FETCH_ASSOC);
			$tmp_records=$qry_sel->fetchAll(PDO::FETCH_ASSOC);
			foreach($tmp_records as $record){
				$rfc822_addr_str=$record["email"];
				$rfc822_addr_str_html=$record["email"];
				if(!empty($record["person_name"])){
					$rfc822_addr_str=$record["person_name"]." <".$record["email"].">";
					$rfc822_addr_str_html="<b>".$record["person_name"]."</b> &lt;".$record["email"]."&gt;";
				}
				$json_result["records"][]=array("addr"=>$rfc822_addr_str_html,"val"=>$rfc822_addr_str);
			}
		}catch(PDOException $e){
			$json_result["success"]=false;
			$json_result["message"]=$e->getMessage();
		}
		$qry_str_count="SELECT COUNT(idmail_account) FROM `mail_address_book`
			WHERE `idmail_account`=$p_idmail_account
				AND ((person_name LIKE '%$qry_cond%') OR (email LIKE '%$qry_cond%'))";
		$qry_count=$this->db->prepare($qry_str_count);
		try{
			$qry_count->execute();
			$fn_count=$qry_count->fetch(PDO::FETCH_NUM);
			$json_result["totalCount"]=$fn_count[0];
		}catch(PDOException $e){
			$this->log_insert("error function : mail_address_book()".$e->getMessage());
		}
		echo json_encode($json_result);
	}
/*	
	public function mailbox_list(){
		
		$xmlDoc=new DOMDocument();
		$root=$xmlDoc->AppendChild($xmlDoc->createElement("mailbox"));
		
		$xmlNode=$root->appendChild($xmlDoc->createElement("node"));
		$xmlNode->appendChild($xmlDoc->createElement("text", "Inbox"));
		$xmlNode->appendChild($xmlDoc->createElement("leaf",true));
		
		header("Content-Type: text/xml");
		$xmlDoc->formatOutput=true;
		echo $xmlDoc->saveXml();
	}*/
	
	
	//BEGIN WNEWS FUNCTIONS=================================================
	public function recipient_group_read(){
		$json_result="";
		$qry_str_sel="SELECT * FROM `recipient_group`";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_sel->execute();
			$json_result=array(
				'success' => true,
				'products_list' => $qry_sel->fetchAll(PDO::FETCH_ASSOC)
			);
		}catch(PDOException $e){
			$json_result=array(
				'success' => false,
				'message' => $e->getMessage()
			);
		}
		return json_encode($json_result);
	}
	public function news_list_selected_hold(){
		$idjobs=$_REQUEST['idjobs'];
		$hold_bool=$_REQUEST['current_hold_bool'];
		$hold=0;
		$hold_bool_updated="No";
		if($hold_bool=="No"){
			$hold=1;
			$hold_bool_updated="Yes";
		}
		$qry_str_upd="UPDATE `jobs` SET `hold`=$hold WHERE `idjobs`=$idjobs LIMIT 1";
		$qry_upd=$this->db->prepare($qry_str_upd);
		try{
			$qry_upd->execute();
			$json_result=array(
				'success'=>true,
				'message'=>'Update success',
				'hold_bool_updated'=>$hold_bool_updated
			);
			return json_encode($json_result);
		}catch(PDOException $e){
			log_insert($this->db,$e->getMessage());
			$json_result=array(
				'success'=>false,
				'message'=>'There was error when trying to hold the job:'.$e->getMessage()
			);
			return json_encode($json_result);
		}
	}
	public function news_list_read(){
		$page=$_REQUEST['page'];
		$start=$_REQUEST['start'];
		$limit=$_REQUEST['limit'];
		$json_result="";
		
		$qry_str_count="SELECT COUNT(jb.idjobs)
			FROM jobs jb ORDER BY jb.idjobs DESC";
		$qry_count=$this->db->prepare($qry_str_count);
		$qry_str_sel="SELECT jb.*,IF(jb.hold=1,\"Yes\",\"No\") AS hold_bool
				,(SELECT COUNT(idjobs) FROM sent_report sr WHERE sr.idjobs=jb.idjobs) AS total_recipient
				,(SELECT COUNT(idjobs) FROM sent_report sr WHERE sr.idjobs=jb.idjobs AND is_sent=1) AS total_sent
			FROM jobs jb ORDER BY jb.idjobs DESC LIMIT $start,$limit";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_count->execute();
			$fn_count=$qry_count->fetch(PDO::FETCH_NUM);
			$qry_sel->execute();
			$json_result=array(
				'success' => true,
				'news_list' => $qry_sel->fetchAll(PDO::FETCH_ASSOC),
				'totalCount' => $fn_count[0]
				
			);
			
		}catch(PDOException $e){
			$json_result=array(
				'success' => false,
				'message' => $e->getMessage()
			);
		}
		return json_encode($json_result);
	}
	public function recipient_of_news_read(){//Recipient related to master of `jobs` selected
		if(!isset($_REQUEST["idjobs"]))
			return;

		$start=$_REQUEST['start'];
		$limit=$_REQUEST['limit'];

		$idjobs=$_REQUEST["idjobs"];
		$json_result="";
		$qry_str_count="SELECT COUNT(sr.idjobs) FROM `sent_report` sr
			WHERE sr.`idjobs`=:idjobs";
		$qry_count=$this->db->prepare($qry_str_count);
		
		$qry_str_sel="SELECT IF(sr.is_sent=1,\"Yes\",\"No\") AS sent_status,sr.tries,rc.*,rg.description AS rg_description FROM `sent_report` sr
				LEFT JOIN (`recipient` rc
					LEFT JOIN `recipient_group` rg ON(rc.idrecipient_group=rg.idrecipient_group)
                )ON(sr.idrecipient=rc.idrecipient)
			WHERE sr.`idjobs`=:idjobs
			LIMIT $start,$limit";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_count->execute(array(":idjobs"=>$idjobs));
			$fn_count=$qry_count->fetch(PDO::FETCH_NUM);
			
			$qry_sel->execute(array(":idjobs"=>$idjobs));
			$json_result=array(
				'success'=>true,
				'recipient_of_news'=>$qry_sel->fetchAll(PDO::FETCH_ASSOC),
				'totalCount' => $fn_count[0]
			);
		}catch(PDOException $e){
			$json_result=array(
				'success'=>false,
				'message'=>$e->getMessage()
			);
		}
		return json_encode($json_result);
	}
	public function recipient_list_read(){
		$start=$_REQUEST['start'];
		$limit=$_REQUEST['limit'];
		
		if(!isset($_REQUEST["idrecipient_group"])){
			$json_result=array(
				'success'=>false,
				'message'=>"No recipient group selected"
			);
			return json_encode($json_result);
		}
		$idrecipient_group=$_REQUEST["idrecipient_group"];
		$qry_str_count="SELECT COUNT(rc.idrecipient) FROM `recipient` rc";
		$qry_count=$this->db->prepare($qry_str_count);
		$qry_str_sel="SELECT rc.*,IF(us.idrecipient IS NULL,\"No\",\"Yes\") AS unsubscribed FROM `recipient` rc
			LEFT JOIN unsubscribed us ON(rc.idrecipient=us.idrecipient)
			WHERE rc.idrecipient_group=$idrecipient_group
			ORDER BY rc.idrecipient ASC
			LIMIT $start,$limit";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_count->execute();
			$fn_count=$qry_count->fetch(PDO::FETCH_NUM);
			
			$qry_sel->execute();
			$json_result=array(
				'success'=>true,
				'recipient_list'=>$qry_sel->fetchAll(PDO::FETCH_ASSOC),
				'totalCount'=>$fn_count[0]
			);
			return json_encode($json_result);
		}catch(PDOException $e){
			$json_result=array(
				'success'=>false,
				'message'=>"Error: ".$e->getMessage()
			);
			return json_encode($json_result);
		}
	}
	public function recipient_list_create(){
		$full_name=$_REQUEST['full_name'];
		$email=$_REQUEST['email'];
		$idrecipient_group=$_REQUEST['idrecipient_group'];
		$qry_str_ins="INSERT INTO recipient(`full_name`,`email`,`idrecipient_group`) VALUES(:full_name,:email,:idrecipient_group)";
		$qry_ins=$this->db->prepare($qry_str_ins);
		try{
			$qry_ins->execute(array(
				":full_name"=>$full_name,
				":email"=>$email,
				":idrecipient_group"=>$idrecipient_group
			));
			$idrecipient_inserted=$this->db->lastInsertId();
			
			$json_result=array(
				"success"=>true,
				"message"=>"<b>$full_name </b>have been inserted to recipient list successfully.",
				"inserted"=>json_encode(array(
					"idrecipient"=>$idrecipient_inserted,
					"full_name"=>$full_name,
					"email"=>$email,
					"idrecipient_group"=>$idrecipient_group
				))
			);
			return json_encode($json_result);
		}catch(PDOException $e){
			$json_result=array(
				"success"=>false,
				"message"=>"There is error when try inserting<b> $full_name </b>to recipient list: ".$e->getMessage()
			);
			return json_encode($json_result);
		}
	}
	public function configurations_read(){
		$qry_str_sel="SELECT * FROM `configurations`";
		$qry_sel=$this->db->prepare($qry_str_sel);
		try{
			$qry_sel->execute();
			$json_result=array(
				"success"=>true,
				"configurations"=>$qry_sel->fetchAll(PDO::FETCH_ASSOC)
			);
			return json_encode($json_result);
		}catch(PDOException $e){
			log_insert($this->db,"There is error when reading 'configurations' table:".$e->getMessage());
			$json_result=array(
				"success"=>false,
				"message"=>"There is error when querying configurations table:".$e->getMessage()
			);
			return json_encode($json_result);
		}
	}
}
?>