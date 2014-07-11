<?php
require_once(PATH_SMARTY);
class BAdmin extends Smarty{
	private $db;
	private $errors;
	private $tpls;
	private $session_nm;//Session name -> in case will have multiple session
	public function __construct($p_database){
		$this->session_nm=SESSION_NM;
		$this->db=$p_database;
		parent::__construct();
		$this->setTemplateDir(PATH_TEMPLATE);
		$this->post_check();
		$this->get_check();
	}
	public function get_session_name(){
		return $this->session_nm;
	}
	public function json_session(){
		//BEGIN TRANSLATE SESSION DATA TO JSON==========================
		$session_data=array();
		$session_data['iduser']="";
		$session_data['idgroup']="";
		$session_data['privileges']="";
		if(isset($_SESSION[$this->session_nm]["iduser"])){
			$session_data['iduser']=$_SESSION[$this->session_nm]["iduser"];
			$session_data['idgroup']=$_SESSION[$this->session_nm]["idgroup"];
		}
		if(isset($_SESSION[$this->session_nm]["privileges"]))
			$session_data['privileges']=$_SESSION[$this->session_nm]["privileges"];
// 		$json_session=array("session_data"=>$session_data);
		$json_encoded=json_encode($session_data);
		$json_privilege_js="<script type=\"text/javascript\">
			var json_session=JSON.parse('$json_encoded');
	</script>\n\r";
		return $json_privilege_js;
		//END TRANSLATE SESSION DATA TO JSON**************************
	}
	public function init_session($p_iduser){
		$query=$this->db->prepare("SELECT * FROM `user` WHERE `iduser`=$p_iduser LIMIT 1");
		try{
			$query->execute();
			$data_tbl_user_detail=$query->fetch();
			$_SESSION[$this->session_nm]["first_name"]=$data_tbl_user_detail["first_name"];
			$_SESSION[$this->session_nm]["last_name"]=$data_tbl_user_detail["last_name"];
			$_SESSION[$this->session_nm]["idgroup"]=$data_tbl_user_detail["idgroup"];
		}catch(PDOException $e){
			die($e->getMessage());
		}
		$this->set_session_privilege();
	}
	public function set_session_privilege(){
		$str_qry_privilege="SELECT * FROM user_privilege WHERE iduser=".$_SESSION[$this->session_nm]["iduser"];
		$qry_privilege=$this->db->prepare($str_qry_privilege);
		try{
			$qry_privilege->execute();
			while($fa_user_privilege=$qry_privilege->fetch(PDO::FETCH_ASSOC)){
				$_SESSION[$this->session_nm]["privileges"][]=$fa_user_privilege["privilege_name"];
			}
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}
	public function errors_html(){
		$error_messages="";
		if(isset($this->errors)&&count($this->errors)>0){
			foreach($this->errors as $error){
				$error_messages.="<h4 class=\"alert_error\">$error</h4><br />";
			}
		}
		return $error_messages;
	}
	public function display_login(){
		$app_detail_arr=unserialize(APP_DETAIL);
		$this->assign("login_action_form",$_SERVER["PHP_SELF"]);
		$this->assign("error_messages",$this->errors_html());
		$this->assign("title",$app_detail_arr["app_title"]." login page");
		$this->tpls.=$this->fetch("login.php");
	}
	public function logged_in(){
		return isset($_SESSION[$this->session_nm]['iduser'])?true:false;
	}
	//BEGIN: ADMIN MODE REDIRECT=====================================
	public function logged_in_protect($p_url_redirect){//KEEP LOGIN USER IN
		if(DEV_MODE==1)//IF Development mode
			header("Location: $p_url_redirect");
		if($this->logged_in()===true){
			header("Location: $p_url_redirect");
			exit();
		}
	}
	public function logged_out_protect($p_url_redirect){//KEEP NON LOGIN USER OUT
		if(DEV_MODE==1)//IF Development mode
			return;
		if($this->logged_in()===false){
			header("Location: $p_url_redirect");
			exit();
		}
	}
	//END: ADMIN MODE REDIRECT**********************************************************
	public function login($p_username,$p_password){
		$str_qry="SELECT * FROM `user` WHERE `username`='$p_username'";
		$query=$this->db->prepare($str_qry);
		try{
			$query->execute();
			$data_tbl_user =$query->fetch();
			$record_password = $data_tbl_user['password'];
			$record_iduser = $data_tbl_user['iduser'];
			if($record_password===get_enc_password($p_password,ENC_PASSWORD)){
				return $record_iduser;
			}else{
				return false;
			}
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}
	public function logout($p_url_redirect){
		$_SESSION[$this->session_nm]=array();
		unset($_SESSION[$this->session_nm]);
		header("Location: $p_url_redirect");
	}
	public function post_check(){
		$this->post_check_login();
	}
	public function get_check(){
		if(empty($_GET)===false){
			if(isset($_GET["logout"])){
				$this->logout("login.php");
			}
		}
	}
	public function post_check_login(){
		if(empty($_POST)===false){
			if(isset($_POST["wsys_login"])){
				$username=trim($_POST["username"]);
				$password=trim($_POST["password"]);
				if(empty($username)===true || empty($password)===true){
					$this->errors[]="Username or password can't be empty";
				}else{
					$login=$this->login($username,$password);//return iduser for admin
					if($login===false){
						$this->errors[]="Sorry, username/password is incorrect. Please check again.";
					}else{
						$_SESSION[$this->session_nm]["iduser"]=$login;
						$this->init_session($login);
					}
				}
			}
		}
	}
	public function user_exist($p_username){
		$query=$this->db->prepare("SELECT COUNT(`iduser`) FROM user WHERE `username`='$p_username'");
		try{
			$query->execute();
			$rows=$query->fetchColumn();
			if($rows==1){
				return true;
			}else{
				return false;
			}
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}

	public function __destruct(){
		echo $this->tpls;
		unset($this->tpls);
	}
}

?>
