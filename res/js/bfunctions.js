function bisnull(p_var){
	if((typeof(p_var)==='undefined')||(p_var==null)||(p_var==''))return true;
	else return false;
}
function json_max_id(p_json,p_field){
	var max_id=0;
	if(p_json.length>0){
		for(var i=0;i<p_json.length;i++){
			var cur_record=p_json[i];
			for(var key in cur_record){
				if(key==p_field){
					if(cur_record[key]>max_id){
						max_id=cur_record[key];
					}
				}
			}
		}
	}
	return max_id;
}
function arr_max(p_arr){
	var max_val=0;
	if(p_arr.length>0){
		for(var i=0;i<p_arr.length;i++){
			if(p_arr[i]>max_val){
				max_val=p_arr[i];
			}
		}
	}
	return max_val;
}
function addr_rfc822_htmldecode(p_str){
	var tmp_str=p_str.replace(/&lt;/g,'<');
	tmp_str=tmp_str.replace(/&gt;/g,'>');
	return tmp_str;
}
function addr_rfc822_parsing(p_str){
	var rfc822_addr_str_raw=addr_rfc822_htmldecode(p_str);
	var rfc822_addr_str=rfc822_addr_str_raw.trim();
	var split_lt_str=rfc822_addr_str.split('<');
	var arr_refine={contact_name:'',email_address:''};
	for(var i=0;i<split_lt_str.length;i++){
		if(i==0){
			var str_contact_name=split_lt_str[i].trim();
			arr_refine.contact_name=str_contact_name;
		}else{
			var str_email_address=split_lt_str[i].replace('>','');
			arr_refine.email_address=str_email_address.trim();
		}
	}
	return arr_refine;
}
