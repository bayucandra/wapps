<?php
	require("../classes/pop3class/mime_parser.php");
	require("../classes/pop3class/rfc822_addresses.php");
	require("../classes/pop3class/pop3.php");
	stream_wrapper_register('mlpop3', 'pop3_stream');  /* Register the pop3 stream handler class */

	function pop3_list($p_arr){//$p_arr=>("hostname","port","tls","user","password","pop3_debug","html_debug")
		$pop3=new pop3_class;
		$pop3->hostname=$p_arr["hostname"];
		$pop3->port=$p_arr["port"];
		$pop3->tls=$p_arr["tls"];
		
		$user=$p_arr["user"];
		$password=$p_arr["password"];
		$pop3->realm="";
		$pop3->workstation="";
		$apop=0;
		$pop3->authentication_mechanism="USER";
		
		if(isset($p_arr["pop3_debug"]))
			$pop3->debug=$p_arr["pop3_debug"];
		if(isset($p_arr["html_debug"]))
			$pop3->html_debug=1;

// 		$pop3->join_continuation_header_lines=1;
		
		$arr_state=array("pop3_error"=>false,"pop3_message"=>"");
		$error_tmp="";

		if(($arr_state["pop3_message"].=$pop3->Open())==""){
	// 		echo "<PRE>Connected to the POP3 server &quot;".$pop3->hostname."&quot;.</PRE>\n";
			if(($arr_state["pop3_message"].=$pop3->Login($user,$password,$apop))==""){
	// 			echo "<PRE>User &quot;$user&quot; logged in.</PRE>\n";
				$msg_count=0;
				$msg_size=0;
				if(($error_tmp=$pop3->Statistics($msg_count,$msg_size))==""){
	// 				echo "<PRE>There are $messages messages in the mail box with a total of $size bytes.</PRE>\n";
					if($msg_count>0){
						$connection_name=null;
						$pop3->GetConnectionName($connection_name);
						for($i=1;$i<=$msg_count;$i++){
							parsing_message($connection_name,$i);
						}
					}
					$arr_state["pop3_message"].=$pop3->Close();
				}else{
					$arr_state["pop3_error"]=true;
					$arr_state["pop3_message"].=".".$error_tmp;
				}
			}else{
				$arr_state["pop3_error"]=true;
			}
		}else{
			$arr_state["pop3_error"]=true;
		}
	}
	
	function parsing_message($p_connection_name,$p_msg_idx){
/*
		5-(5-1)=1
		5-(4-1)=2
		5-(3-1)=3
		5-(2-1)=4
		5-(1-1)=5*/

// 		$msg_idx_reverse=$p_total_msg-($p_msg_idx-1);

		$message_file='mlpop3://'.$p_connection_name.'/'.$p_msg_idx;
		$mime=new mime_parser_class;

		/*
		* Set to 0 for not decoding the message bodies
		*/
		$mime->decode_bodies = 1;

		$parameters=array(
			'File'=>$message_file,

			/* Read a message from a string instead of a file */
			/* 'Data'=>'My message data string',              */

			/* Save the message body parts to a directory     */
			/* 'SaveBody'=>'/tmp',                            */

			/* Do not retrieve or save message body parts     */
				'SkipBody'=>1,
		);
		$success=$mime->Decode($parameters, $decoded);


		if(!$success)
			echo '<h2>MIME message decoding error: '.HtmlSpecialChars($mime->error)."</h2>\n";
		else
		{
			echo '<h2>MIME message decoding successful</h2>'."\n";
			echo '<h2>Message structure</h2>'."\n";
			echo '<pre>';
			var_dump($decoded[0]);
			echo '</pre>';
			if($mime->Analyze($decoded[0], $results))
			{
				echo '<h2>Message analysis</h2>'."\n";
				echo '<pre>';
				var_dump($results);
				echo '</pre>';
			}
			else
				echo 'MIME message analyse error: '.$mime->error."\n";
		}
		//BEGIN LOOPING=============================================
// 		if($p_msg_idx>1){
// 			parsing_message($p_connection_name,$p_total_msg,$p_msg_idx-1);
// 		}
		//END LOOPING***********************************************
	}
?>
