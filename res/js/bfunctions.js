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
