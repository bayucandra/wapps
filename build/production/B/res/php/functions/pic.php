<?php
function image_resize($p_str_img_path,$n_d,$p_arr_opt){// $n_d=-1 if same as original size $p_arr_opt["str_dest_path"], $p_arr_opt["watermark"]
	$str_img_path=$p_str_img_path;
	$str_dest_path="";
	if(isset($p_arr_opt["str_dest_path"]))
		$str_dest_path=$p_arr_opt["str_dest_path"];

	$fot_size=0;
	//if(!file_exists($str_img_path))
		//$str_img_path=$p_arr_opt["no_pic"];
	$fot_size=getimagesize($str_img_path);
	list($width,$height)=$fot_size;
	if($n_d==-1){$n_w=$width;$n_h=$height;}
		elseif(($width>$n_d||$height>$n_d)&&($width>$height)){$rat=$width/$height;$n_w=$n_d;$n_h=ceil($n_w/$rat);//echo "<br> rat1 $rat height $height width $width newh $n_h neww $n_w";
		}
		elseif(($width>$n_d||$height>$n_d)&&($width<$height)){$rat=$height/$width;$n_h=$n_d;$n_w=ceil($n_h/$rat);}
		elseif($width==$height){$n_w=$n_h=$n_d;}
	
		elseif(($width<$n_d&&$height<$n_d)&&($width>$height)){$rat=$n_d/$width;$n_w=$n_d;$n_h=ceil($rat*$height);}
		elseif(($width<$n_d&&$height<$n_d)&&($width<$height)){$rat=$n_d/$height;$n_h=$n_d;$n_w=ceil($rat*$width);}
		elseif(($width==$n_d&&$height<$n_d)&&($width>$height)){$n_w=$width;$n_h=$height;}
		elseif(($height==$n_d&&$width<$n_d)&&($width<$height)){$n_w=$width;$n_h=$height;}

//echo "<b>$n_d<br />$n_w<br />$n_h<br/>rat$rat<br>width$width</b>";
	$sumb=imagecreatefromjpeg($str_img_path);
	if(isset($p_arr_opt["watermark"])){
		$wm_path=$p_arr_opt["watermark"]["path"];
		$wm_percent=$p_arr_opt["watermark"]["percent"];
		if(file_exists($wm_path)){
			$wm_size=getimagesize($wm_path);
			list($wm_width,$wm_height)=$wm_size;
			$wm_ratio=$wm_width/$wm_height;
			$wm_nw=($wm_percent/100)*$width;
			$wm_nh=$wm_nw/$wm_ratio;
			
			$wm_pos_x=($width-$wm_nw)/2;
			$wm_pos_y=($height-$wm_nh)/2;
			
			$wm_sumb=imagecreatefrompng($wm_path);
			$wm_tuj=imagecreatetruecolor($wm_nw,$wm_nh);
			imagealphablending($wm_tuj,false);
			$wm_color_transparent=imagecolorallocatealpha($wm_tuj,0,0,0,127);
			imagefill($wm_tuj,0,0,$wm_color_transparent);
			imagecopyresampled($wm_tuj,$wm_sumb,0,0,0,0,$wm_nw,$wm_nh,$wm_width,$wm_height) or die("Failed to resize watermark");
			imagecopy($sumb,$wm_tuj,$wm_pos_x,$wm_pos_y,0,0,$wm_nw,$wm_nh);
		}
	}
	
	$tuj=imagecreatetruecolor($n_w,$n_h);

	imagecopyresampled($tuj,$sumb,0,0,0,0,$n_w,$n_h,$fot_size[0],$fot_size[1]) or die("Failed to resize Image");
	if($str_dest_path==""){
		$show=imagejpeg($tuj,NULL,90) or die("Failed to save Image");
		imagedestroy($tuj);
		return $show;
	}else{
		if(imagejpeg($tuj,$str_dest_path,90)){
			imagedestroy($tuj);
			return true;
		}else{
			return false;
		}
	}
}
?>
