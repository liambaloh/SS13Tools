
<head>
	<title>Papers, please Passport Generator - Space Station 13 - /tg/station branch</title>
</head>
<script type='text/javascript'>

	function dostuff(folder, id){
		//document.write("T");
		//document.write(""+folder+"/"+select.value);
		var select = document.getElementById(id);
		var preview = document.getElementById("proppreview");
		var name = document.getElementById("propname");
		name.innerHTML = ""+folder+"/"+select.value;
		
		
		preview.setAttribute('src', ""+folder+"/"+select.value);
		//document.write("T");
	}

</script>

<?php



function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

	if(isset($_POST["save"])){
		if(isset($_POST["file"])){
			$file = $_POST["file"];
		}else{
			print "Saving failed, go back in your browser or click <a href='index.php'>here</a> and try again";
		}
		if(file_exists("w/$file")){
			if(file_exists("saves/$file")){
				print "This file already exists:<br>";
			}else{
				copy("w/$file","saves/$file");
			}
			print "<img src='saves/$file'><br><br>";
			print "<textarea cols='50' rows='2'>http://www.ss13.eu/tgdb/passport/saves/$file</textarea><br><br>";
			print "<b>BBCode:</b><br><textarea cols='50' rows='2'>[img]http://www.ss13.eu/tgdb/passport/saves/".$file."[/img]</textarea><br><br>";
			print "Click <a href='index.php'>here</a> to crate a new image or <a href='http://www.ss13.eu'>here</a> to return to the main page.";
		}else{
			print "Saving failed, go back in your browser or click <a href='index.php'>here</a> and try again";
		}
		return;
	}

	$mugshot_offset_x = 166;
	$mugshot_offset_y = 176;
	$mugshot_height = 96;
	$mugshot_width = 80;
	$mugshot_x = 0;
	$mugshot_y = 0;

	$width=260;
	$height=324;
	
	$def_font_size = 12;
	$def_font_size_nation = 6;
	$def_font_size_stampdate = 12;
	$max_input_width = 118;
	$max_input_width_nation = 118;
	$max_input_width_name = 230;
	$max_input_width_stampdate = 94;
	
	$nation = "Arstotzka";	//TODO
	$nation_size = 11;
	$nation_x = 10;
	$nation_y = 30;
	$nation_font_size = $def_font_size_nation;
	
	$dob = "UNKNOWN";
	$dob_size = 11;
	$dob_x = 10;
	$dob_y = 30;
	$dob_font_size = $def_font_size;
	
	$sex = "UNKNOWN";
	$sex_size = 11;
	$sex_x = 10;
	$sex_y = 30;
	$sex_font_size = $def_font_size;
	
	$iss = "UNKNOWN";
	$iss_size = 11;
	$iss_x = 10;
	$iss_y = 30;
	$iss_font_size = $def_font_size;
	
	$exp = "UNKNOWN";
	$exp_size = 11;
	$exp_x = 10;
	$exp_y = 30;
	$exp_font_size = $def_font_size;
	
	$name = "UNKNOWN, UNKNOWN";
	$name_size = 11;
	$name_x = 10;
	$name_y = 30;
	$name_font_size = $def_font_size;
	
	$stampdate = "1982 12.16.";
	$stampdate_size = 11;
	$stampdate_x = 47+4;
	$stampdate_y = 21;
	$stampdate_font_size = $def_font_size_stampdate;
	
	if(isset($_POST["nation"])){
		$nationid = intval($_POST["nation"]);
		switch($nationid){
			case 0:
				$nation = "Arstotzka";
			break;
			case 1:
				$nation = "Antegria";
			break;
			case 2:
				$nation = "Impor";
			break;
			case 3:
				$nation = "Kolechia";
			break;
			case 4:
				$nation = "Obristan";
			break;
			case 5:
				$nation = "Republia";
			break;
			case 6:
				$nation = "United Federation";
			break;
			case 7:
				$nation = "United Nanotrasen";
			break;
			case 8:
				$nation = "Cobrastan";
			break;
		
		}
	}
	
	$halfsize = 0;
	if(isset($_POST["halfsize"])){
		$halfsize = intval($_POST["halfsize"]);
	}
	
	$nation_lower = strtolower($nation);
	if(isset($_POST["dob"])){
		$dob = $_POST["dob"];
	}
	if(isset($_POST["sex"])){
		$sex = $_POST["sex"];
	}
	if(isset($_POST["iss"])){
		$iss = $_POST["iss"];
	}
	if(isset($_POST["exp"])){
		$exp = $_POST["exp"];
	}
	if(isset($_POST["name"])){
		$name = $_POST["name"];
	}
	if(isset($_POST["stampdate"])){
		$stampdate = $_POST["stampdate"];
	}
	
	$mob_height = "tall";
	if(isset($_POST["height"])){
		$height_int = intval($_POST["height"]);
		switch($height_int){
			case 0:
				$mob_height = "tall";
			break;
			case 1:
				$mob_height = "short";
			break;
		}
	}
	
	$nation = str_replace("\\","",$nation);
	$dob = str_replace("\\","",$dob);
	$sex = str_replace("\\","",$sex);
	$iss = str_replace("\\","",$iss);
	$exp = str_replace("\\","",$exp);
	$name = str_replace("\\","",$name);
	$stampdate = str_replace("\\","",$stampdate);
	
	if(isset($_POST["xoffset"])){
		$xoffsetstr = $_POST["xoffset"];
		if($xoffsetstr != null){
			$pixelxoffset = intval($xoffsetstr);
		}
	}
	if(isset($_POST["yoffset"])){
		$yoffsetstr = $_POST["yoffset"];
		if($yoffsetstr != null){
			$pixelyoffset = intval($yoffsetstr);
		}
	}
	
	$preventclothing = 0;
	$mob = "male.png";
	if(isset($_POST["mob"])){
		$mob = $_POST["mob"];
		if($mob != "male.png" && $mob != "female.png"){
			$preventclothing = 1;
		}
	}
	
	$gender = "animal";
	$undies = 0;
	if($mob == "male.png"){
		$gender = "male";
		$undies = 1;
	}else if($mob == "female.png"){
		$gender = "female";
		$undies = 1;
	}
	
	$underwear = "white.png";
	if(isset($_POST["underwear"])){
		$underwear = $_POST["underwear"];
	}
	
	$skintone = 0;
	if(isset($_POST["skintone"])){
		$skintone = intval($_POST["skintone"]);
	}

	$mugshotbg = "blank";
	if(isset($_POST["mugshotbg"])){
		$mugshotbg = $_POST["mugshotbg"];
	}
	
	if($preventclothing == 0){
		$haircolor = 0;
		if(isset($_POST["haircolor"])){
			$haircolor = intval($_POST["haircolor"]);
		}
		
		$eyecolorr = 0xa2;
		$eyecolorg = 0xa2;
		$eyecolorb = 0xa2;
		
		$lipcolorr = 0;
		$lipcolorg = 0;
		$lipcolorb = 0;

		$prop = "blank";
		if(isset($_POST["prop"])){
			$prop = $_POST["prop"];
		}
		
		$stamp = "none";
		if(isset($_POST["stamp"])){
			$stamp = $_POST["stamp"];
		}

		$facial = "blank";
		if(isset($_POST["facial"])){
			$facial = $_POST["facial"];
		}

		$hair = "blank";
		if(isset($_POST["hair"])){
			$hair = $_POST["hair"];
		}

		$under = "blank";
		if(isset($_POST["under"])){
			$under = $_POST["under"];
		}

		$suit = "blank";
		if(isset($_POST["suit"])){
			$suit = $_POST["suit"];
		}

		$glasses = "blank";
		if(isset($_POST["glasses"])){
			$glasses = $_POST["glasses"];
		}
		
		$mask = "blank";
		if(isset($_POST["mask"])){
			$mask = $_POST["mask"];
		}
		
		$headgear = "blank";
		if(isset($_POST["headgear"])){
			$headgear = $_POST["headgear"];
		}
	}

	$image_mugshot = imagecreatetruecolor( $mugshot_width, $mugshot_height );
	$image = imagecreatetruecolor( $width, $height );
	
	$bgimage = "blankbg_$nation_lower.png";
	if(file_exists("./$bgimage")){
		$image = imagecreatefrompng ( "./$bgimage" );
		imagealphablending($image , true );
	}
	
	//Add mob
	
	$src_x = 2;
	$src_y = 0;
	$src_z = 27;	//width
	$src_q = 24;	//height
	
	$mob_mugshot_offset_y = 0;
	$dst_z = 80;	//width
	$dst_q = 96;	//height
	switch($mob_height){
		case "tall":
			$dst_q = 96;	//height
			$mob_mugshot_offset_y = 0;
		break;
		case "short":
			$dst_q = 72;	//height
			$mob_mugshot_offset_y = 16;
		break;
	}
	$dst_x = 0;
	$dst_y = 8 + $mob_mugshot_offset_y;
	
	
	if(file_exists("mob/$mob")){
		//Mugshot background and square
			//background
		$mughostbgcolor = imagecolorallocate( $image_mugshot, 0xA2, 0x94, 0x90 );	//a29490 Was B0B0B0
		imagefilledrectangle($image_mugshot, 0, 0, $mugshot_width, $mugshot_height, $mughostbgcolor);
			//mugshotbg
		if(file_exists("mugshotbg/$mugshotbg")){
			$image_mugshot = imagecreatefrompng ( "mugshotbg/$mugshotbg" );
		}
		//Mob
		$imagemob = imagecreatefrompng ( "mob/$mob" );
		imagefilter($imagemob, IMG_FILTER_BRIGHTNESS, -$skintone);
		if($imagemob != null){
			imagecopyresized ( $image_mugshot , $imagemob, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
			if($undies == 1){
				$underwear_array = explode("_",$underwear);
				if(count($underwear_array) == 2){
					$underwear = $gender ."_". $underwear_array[1];
				}else{
					$underwear = $gender ."_". $underwear_array[1] ."_". $underwear_array[2];
				}
				
				if(file_exists("underwear/$underwear")){
					$imageunderwear = imagecreatefrompng ( "underwear/$underwear" );
					imagecopyresized ( $image_mugshot , $imageunderwear, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
				}else{
					$imageunderwear = imagecreatefrompng ( "underwear/$gender"."_white.png" );
					imagecopyresized ( $image_mugshot , $imageunderwear, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
				}
			}
			//imagecopy($image, $imagemob, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
			
			//lips
			if($lipcolorr != 0 || $lipcolorg != 0 || $lipcolorb != 0){
				$colorlips = imagecolorallocate( $image_mugshot, $lipcolorr, $lipcolorg, $lipcolorb );
				imagefilledrectangle($image_mugshot, 31, 37, 33, 39, $colorlips);
			}
			
			
			//eyes
			if($preventclothing == 0){
				$coloreyeright = imagecolorallocate( $image_mugshot, $eyecolorr, $eyecolorg, $eyecolorb );
				imagefilledrectangle($image_mugshot, 34, 35, 36, 37, $coloreyeright);	//right
				$coloreyeleft = imagecolorallocate( $image_mugshot, $eyecolorr, $eyecolorg, $eyecolorb );
				imagefilledrectangle($image_mugshot, 40, 35, 42, 37, $coloreyeleft);	//left
			}
			
			//hair
			if(file_exists("hair/$hair")){
				$imagehair = imagecreatefrompng ( "hair/$hair" );
				imagefilter($imagehair, IMG_FILTER_BRIGHTNESS, $haircolor);
				if($imagehair != null){
					imagecopyresized ( $image_mugshot , $imagehair , $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//under/jumpsuit
			if(file_exists("under/$under")){
				$imageunder = imagecreatefrompng ( "under/$under" );
				if($imageunder != null){
					imagecopyresized ( $image_mugshot , $imageunder , $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//suit
			if(file_exists("suit/$suit")){
				$imagesuit = imagecreatefrompng ( "suit/$suit" );
				if($imagesuit != null){
					imagecopyresized ( $image_mugshot , $imagesuit, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//facial hair
			if(file_exists("facial/$facial")){
				$imagefacial = imagecreatefrompng ( "facial/$facial" );
				imagefilter($imagefacial, IMG_FILTER_BRIGHTNESS, $haircolor);
				if($imagefacial != null){
					imagecopyresized ( $image_mugshot , $imagefacial, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//glasses
			if(file_exists("glasses/$glasses")){
				$imageglasses = imagecreatefrompng ( "glasses/$glasses" );
				if($imageglasses != null){
					imagecopyresized ( $image_mugshot , $imageglasses, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//mask
			if(file_exists("mask/$mask")){
				$imagemask = imagecreatefrompng ( "mask/$mask" );
				if($imagemask != null){
					imagecopyresized ( $image_mugshot , $imagemask, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//headgear
			if(file_exists("headgear/$headgear")){
				$imageheadgear = imagecreatefrompng ( "headgear/$headgear" );
				if($imageheadgear != null){
					imagecopyresized ( $image_mugshot , $imageheadgear, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
		}
	}
	
	$size1 = ImageTTFBBox($stampdate_font_size,0,'pixium.ttf',$stampdate);
	while($size1[2] > $max_input_width_stampdate){
		$stampdate_font_size -= 1;
		$size1 = ImageTTFBBox($stampdate_font_size,0,'pixium.ttf',$stampdate);
	}
	
	//stamp
	if(file_exists("stamp/$stamp")){
		$imagestamp = imagecreatefrompng ( "stamp/$stamp" );
		if($imagestamp != null){
			if($stamp == "denied.png" || $stamp == "granted.png"){
				$text_color_stamp = imagecolorallocate( $imagestamp, 0x53, 0x70, 0x1b );
				if($stamp == "granted.png"){
					$text_color_stamp = imagecolorallocate( $imagestamp, 0x53, 0x70, 0x1b );	//53701b green
				}else{
					$text_color_stamp = imagecolorallocate( $imagestamp, 0x70, 0x1b, 0x1b );	//701b1b red
				}
				
				$stampdate_x = ($stampdate_x - ($size1[2]/2));
				
				//imagettftext ($imagestamp , $stampdate_font_size, 0 , ($stampdate_x - (size1[2]/2)), $stampdate_y, $text_color_stamp , "pixium.ttf" , $stampdate );
				
				imagettftext ($imagestamp , $stampdate_font_size, 0 , $stampdate_x, $stampdate_y, $text_color_stamp , "pixium.ttf" , $stampdate );
			}
			imagecopyresized ( $image , $imagestamp , 60, 45, 0, 0, 140, 66, 140, 66);
			//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
		}
	}
	
	//$identifier = "hi";
	//imagepng($image_mugshot,"w/mugshot_hi1.png");
	//imagepng($image,"w/mugshot_hi2.png");
	
	$width = imagesx ( $image );
	$height = imagesy ( $image );
	
	$passport_code	= "";
	$passport_code_font_size = 12;
	$passport_code_width = 0;
	for($i = 0; $i < 5; $i++){
		if(rand(0,1) == 1){
			$passport_code .= chr(rand(65,90));
		}else{
			$passport_code .= chr(rand(48,57));
		}
	}
	$passport_code .= "-";
	for($i = 0; $i < 5; $i++){
		if(rand(0,1) == 1){
			$passport_code .= chr(rand(65,90));
		}else{
			$passport_code .= chr(rand(48,57));
		}
	}
	
	$size1 = ImageTTFBBox($passport_code_font_size,0,'pixium.ttf',$passport_code);
	while($size1[2] > $max_input_width_name){
		$passport_code_font_size -= 1;
		$size1 = ImageTTFBBox($passport_code_font_size,0,'pixium.ttf',$passport_code);
	}
	$passport_code_width = $size1[2];
	
	
	$write_passport_code = 1;
	switch($nation_lower){
		case "antegria":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 290;
			$dob_x = 46;
			$dob_y = 214;
			$sex_x = 46;
			$sex_y = 232;
			$iss_x = 46;
			$iss_y = 250;
			$exp_x = 46;
			$exp_y = 268;
			$passport_code_x = 242 - $passport_code_width;
			$passport_code_y = 310;
			
			$mugshot_offset_x = 166;
			$mugshot_offset_y = 176;
			
		break;
		case "arstotzka":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 186;
			$dob_x = 135;
			$dob_y = 208;
			$sex_x = 135;
			$sex_y = 224;
			$iss_x = 135;
			$iss_y = 240;
			$exp_x = 135;
			$exp_y = 256;
			$passport_code_x = 16;
			$passport_code_y = 308;
			
			$mugshot_offset_x = 15;
			$mugshot_offset_y = 196;
		break;
		case "impor":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 184;
			$dob_x = 140;
			$dob_y = 204;
			$sex_x = 140;
			$sex_y = 220;
			$iss_x = 140;
			$iss_y = 236;
			$exp_x = 140;
			$exp_y = 252;
			$passport_code_x = 238 - $passport_code_width;
			$passport_code_y = 304;
			
			$mugshot_offset_x = 18;
			$mugshot_offset_y = 192;
		break;
		case "kolechia":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 208;
			$dob_x = 142;
			$dob_y = 226;
			$sex_x = 142;
			$sex_y = 242;
			$iss_x = 142;
			$iss_y = 258;
			$exp_x = 142;
			$exp_y = 274;
			$passport_code_x = 244 - $passport_code_width;
			$passport_code_y = 308;
			
			$mugshot_offset_x = 16;
			$mugshot_offset_y = 214;
		break;
		case "obristan":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0xED, 0xE0, 0xD8);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 208;
			$dob_x = 55;
			$dob_y = 234;
			$sex_x = 55;
			$sex_y = 250;
			$iss_x = 55;
			$iss_y = 266;
			$exp_x = 55;
			$exp_y = 282;
			$passport_code_x = 20;
			$passport_code_y = 308;
			
			$mugshot_offset_x = 168;
			$mugshot_offset_y = 214;
		break;
		case "republia":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 186;
			$dob_x = 56;
			$dob_y = 208;
			$sex_x = 56;
			$sex_y = 224;
			$iss_x = 56;
			$iss_y = 240;
			$exp_x = 56;
			$exp_y = 256;
			$passport_code_x = 244 - $passport_code_width;
			$passport_code_y = 308;
			
			$mugshot_offset_x = 170;
			$mugshot_offset_y = 192;
		break;
		case "united federation":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 208;
			$dob_x = 140;
			$dob_y = 224;
			$sex_x = 140;
			$sex_y = 240;
			$iss_x = 140;
			$iss_y = 256;
			$exp_x = 140;
			$exp_y = 272;
			$passport_code_x = 244 - $passport_code_width;
			$passport_code_y = 308;
			
			$mugshot_offset_x = 16;
			$mugshot_offset_y = 212;
		break;
		case "united nanotrasen":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 208;
			$dob_x = 140;
			$dob_y = 224;
			$sex_x = 140;
			$sex_y = 240;
			$iss_x = 140;
			$iss_y = 256;
			$exp_x = 140;
			$exp_y = 272;
			$passport_code_x = 244 - $passport_code_width;
			$passport_code_y = 308;
			
			$mugshot_offset_x = 16;
			$mugshot_offset_y = 212;
		break;
		case "cobrastan":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			$text_color_name = imagecolorallocate( $image, 0x57, 0x48, 0x48);
			
			$name_x = 16;
			$name_y = 186;
			$dob_x = 144;
			$dob_y = 210;
			$sex_x = 144;
			$sex_y = 226;
			$iss_x = 144;
			$iss_y = 242;
			$exp_x = 144;
			$exp_y = 258;
			$write_passport_code = 0;
			$passport_code_x = 238 - $passport_code_width;
			$passport_code_y = 304;
			
			$mugshot_offset_x = 16;
			$mugshot_offset_y = 196;
		break;
	}
	
	$size1 = ImageTTFBBox($nation_font_size,0,'pixium.ttf',$nation);
	while($size1[2] > $max_input_width_nation){
		$nation_font_size -= 1;
		$size1 = ImageTTFBBox($nation_font_size,0,'pixium.ttf',$nation);
	}
	
	$size1 = ImageTTFBBox($dob_font_size,0,'pixium.ttf',$dob);
	while($size1[2] > $max_input_width){
		$dob_font_size -= 1;
		$size1 = ImageTTFBBox($dob_font_size,0,'pixium.ttf',$dob);
	}
	
	$size1 = ImageTTFBBox($sex_font_size,0,'pixium.ttf',$sex);
	while($size1[2] > $max_input_width){
		$sex_font_size -= 1;
		$size1 = ImageTTFBBox($sex_font_size,0,'pixium.ttf',$sex);
	}
	
	$size1 = ImageTTFBBox($iss_font_size,0,'pixium.ttf',$iss);
	while($size1[2] > $max_input_width){
		$iss_font_size -= 1;
		$size1 = ImageTTFBBox($iss_font_size,0,'pixium.ttf',$iss);
	}
	
	$size1 = ImageTTFBBox($exp_font_size,0,'pixium.ttf',$exp);
	while($size1[2] > $max_input_width){
		$exp_font_size -= 1;
		$size1 = ImageTTFBBox($exp_font_size,0,'pixium.ttf',$exp);
	}
	
	$size1 = ImageTTFBBox($name_font_size,0,'pixium.ttf',$name);
	while($size1[2] > $max_input_width_name){
		$name_font_size -= 1;
		$size1 = ImageTTFBBox($name_font_size,0,'pixium.ttf',$name);
	}
	
	$blackandwhite = 1;
	if($blackandwhite){
		//a29490 - 979797
	
		imagefilter ( $image_mugshot, IMG_FILTER_GRAYSCALE);
		imagefilter ( $image_mugshot, IMG_FILTER_BRIGHTNESS, -0x07);
		imagefilter ( $image_mugshot, IMG_FILTER_COLORIZE, 0x12, 0x04, 0x00 );	//a29490
	}
	
	imagecopy($image, $image_mugshot, $mugshot_offset_x,$mugshot_offset_y,0,0,$mugshot_width,$mugshot_height);

	imagettftext ($image , $dob_font_size, 0 , $dob_x, $dob_y, $text_color , "pixium.ttf" , $dob );
	imagettftext ($image , $sex_font_size, 0 , $sex_x, $sex_y, $text_color , "pixium.ttf" , $sex );
	imagettftext ($image , $iss_font_size, 0 , $iss_x, $iss_y, $text_color , "pixium.ttf" , $iss );
	imagettftext ($image , $exp_font_size, 0 , $exp_x, $exp_y, $text_color , "pixium.ttf" , $exp );
	imagettftext ($image , $name_font_size, 0 , $name_x, $name_y, $text_color_name , "pixium.ttf" , $name );
	if($write_passport_code == 1){
		imagettftext ($image , $passport_code_font_size, 0 , $passport_code_x, $passport_code_y, $text_color , "pixium.ttf" , $passport_code );
	}
	
	if($halfsize){
		$imagetmp = imagecreatetruecolor( $width, ($height / 2) );
		imagecopy($imagetmp, $image, 0, 0, 0, $height/2, $width, $height/2);
		$image = $imagetmp;
	}
	
	$dirhandle = opendir("w");
	$maxfid = 0;
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || !(endsWith($entry,".png"))){
			continue;
		}
		$f = explode("_",$entry);
		if($f[0] == "passport"){
			$almostnumber = $f[1];
			$f2 = explode(".",$almostnumber);
			$fid = intval($f2[0]);
			if($fid > $maxfid){
				$maxfid = $fid;
			}
		}
	}
	closedir($dirhandle);
	if($maxfid > 0){
		$identifier = $maxfid+1;
	}else{
		$identifier = 1;
	}

		/*
	if($line1 == "Line 1" && $line2 = "Line 2"){
		print "<img src='default.png'>";
	}else{
	*/
		imagepng($image,"w/passport_$identifier.png");
		imagedestroy($image);
		
		print "<table><tr><td>";
		print '
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- passport -->
		<ins class="adsbygoogle"
			 style="display:inline-block;width:336px;height:280px"
			 data-ad-client="ca-pub-6063115855965447"
			 data-ad-slot="3871521529"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>';
		print "</td><td>";
		print "<img src='w/passport_$identifier.png'>";
		print "</td><td>";
		print "Prop preview: (<span id='propname'></span>)<br>";
		print "Select images from the lists below to display a preview<br>";
		print "<img src='' id='proppreview'>";
		print "</td></tr></table>";
	/*}*/
	print "<br><form method='post'><input type='hidden' name='file' value='passport_$identifier.png'><input type='submit' name='save' value='save'></form>";

	print "<form method='post'>";
	print "<table><tr><td valign='top'>";
	print "<br><b>Passport style:</b>";
	print "<select name='nation' id='nationselect' onChange='dostuff(\"nation\",\"nationselect\");'>";
	if($nation == "Arstotzka" && $colorscheme == "default"){
		print "<option value='0' selected>Arstotzka</option>";
	}else{
		print "<option value='0'>Arstotzka</option>";
	}
	if($nation == "Antegria"){
		print "<option value='1' selected>Antegria</option>";
	}else{
		print "<option value='1'>Antegria</option>";
	}
	if($nation == "Impor"){
		print "<option value='2' selected>Impor</option>";
	}else{
		print "<option value='2'>Impor</option>";
	}
	if($nation == "Kolechia"){
		print "<option value='3' selected>Kolechia</option>";
	}else{
		print "<option value='3'>Kolechia</option>";
	}
	if($nation == "Obristan"){
		print "<option value='4' selected>Obristan</option>";
	}else{
		print "<option value='4'>Obristan</option>";
	}
	if($nation == "Republia"){
		print "<option value='5' selected>Republia</option>";
	}else{
		print "<option value='5'>Republia</option>";
	}
	if($nation == "United Federation"){
		print "<option value='6' selected>United Federation</option>";
	}else{
		print "<option value='6'>United Federation</option>";
	}
	if($nation == "United Nanotrasen"){
		print "<option value='7' selected>United Nanotrasen</option>";
	}else{
		print "<option value='7'>United Nanotrasen</option>";
	}
	if($nation == "Cobrastan"){
		print "<option value='8' selected>Cobrastan (forged)</option>";
	}else{
		print "<option value='8'>Cobrastan (forged)</option>";
	}
	print "</select>";
	print "<br><b>Name:</b> <input type='text' name='name' value='$name'>";
	print "<br><b>Date of birth:</b> <input type='text' name='dob' value='$dob'>";
	print "<br><b>Sex:</b> <input type='text' name='sex' value='$sex'>";
	print "<br><b>Issuer:</b> <input type='text' name='iss' value='$iss'>";
	print "<br><b>Expiration:</b> <input type='text' name='exp' value='$exp'>";
	
	//stamp
	print "<br><b>Stamp:</b>	<select name='stamp' id='stampselect' onChange='dostuff(\"stamp\",\"stampselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("stamp");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $stamp){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	print "<br><b>Stamp date</b>: <input type='text' name='stampdate' value='$stampdate'></input>";
	
	print "</td><td valign='top'>";
	
	$bgfiles = Array();
	$bgfiles[] = $bgimage;
	/*$dirhandle = opendir("bgs");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$bgfiles[] = $entry;
	}
	sort($bgfiles);
	closedir($dirhandle);*/

	/*
	print "<br><b>Background:</b>	<select name='bg'>";
	foreach($bgfiles as $bgentry){
		if($bgentry == $bgimage){
			print "<option value='$bgentry' selected>$bgentry</option>";
		}else{
			print "<option value='$bgentry'>$bgentry</option>";
		}
	}
	print "</select>";
	*/
	//mugshotbg
	print "<br><b>Mugshot background:</b>	<select name='mugshotbg' id='mugshotbgselect' onChange='dostuff(\"mugshotbg\",\"mugshotbgselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("mugshotbg");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $mugshotbg){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	//mob
	print "<br><b>Body:</b>	<select name='mob' id='mobselect' onChange='dostuff(\"mob\",\"mobselect\");'>";

	$propfiles = Array();
	$dirhandle = opendir("mob");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $mob){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	//underwear
	print "<br><b>Underwear:</b>	<select name='underwear' id='underwearselect' onChange='dostuff(\"underwear\",\"underwearselect\");'>";

	$propfiles = Array();
	$dirhandle = opendir("underwear");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $underwear){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select><br>(Must match gender defined in body! Ignore for animals)";
	
	//size
	print "<br><b>Height:</b>	<select name='height'>";
	if($mob_height == "tall"){
		print "<option value='0' selected>Tall</option>";
	}else{
		print "<option value='0'>Tall</option>";
	}
	if($mob_height == "short"){
		print "<option value='1' selected>Short</option>";
	}else{
		print "<option value='1'>Short</option>";
	}
	print "</select>";
	
	//skintone
	print "<br><b>Skin tone modifier:</b> <input type='text' name='skintone' value='$skintone' maxlength='3' size='3'> <br>(negative numbers to make lighter, positive to make darker.<br>common values from -30 to 200)";
	
	print "</td><td valign='top'>";
	
	//hair
	print "<br><b>Hair:</b>	<select name='hair' id='hairselect' onChange='dostuff(\"hair\",\"hairselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("hair");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $hair){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	//facial
	print "<br><b>Facial hair:</b>	<select name='facial' id='facialselect' onChange='dostuff(\"facial\",\"facialselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("facial");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $facial){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	//Hair color
	print "<br><b>Hair/facial hair color:</b>";
	print "lightness: <input type='text' name='haircolor' value='$haircolor' maxlength='3' size='3'> <br>(0-255; 0 = black; 255 = white)";
	
	//jumpsuit
	print "<br><b>Jumpsuit:</b>	<select name='under' id='underselect' onChange='dostuff(\"under\",\"underselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("under");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $under){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	
	//suit
	print "<br><b>Suit:</b>	<select name='suit' id='suitselect' onChange='dostuff(\"suit\",\"suitselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("suit");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $suit){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	//mask
	print "<br><b>Mask:</b>	<select name='mask' id='maskselect' onChange='dostuff(\"mask\",\"maskselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("mask");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $mask){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	//headgear
	print "<br><b>Headgear:</b>	<select name='headgear' id='headgearselect' onChange='dostuff(\"headgear\",\"headgearselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("headgear");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $headgear){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	
	//glasses
	print "<br><b>Glasses:</b>	<select name='glasses' id='glassesselect' onChange='dostuff(\"glasses\",\"glassesselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("glasses");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $glasses){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	print "<br><b>Image size:</b>	<select name='halfsize'>
			   <option value='0'>Full</option>";
		if($halfsize == 1){
			print "<option value='1' selected>Bottom only</option>";
		}else{
			print "<option value='1'>Bottom only</option>";
		}
	print "</select>";
	
	print "</td></tr></table>";
	
	/*
	//Props
	
	print "<br><b>Prop:</b>	<select name='prop' id='propselect' onChange='dostuff(\"props\",\"propselect\");'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("props");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || $entry == "Thumbs.db"){
			continue;
		}
		$propfiles[] = $entry;
	}
	sort($propfiles);
	closedir($dirhandle);
	foreach($propfiles as $propentry){
		if($propentry == $prop){
			print "<option value='$propentry' selected>$propentry</option>";
		}else{
			print "<option value='$propentry'>$propentry</option>";
		}
	}
	print "</select>";
	
	//End select dropdowns
	
	print "<br><b>Prop pixel offset: x:</b> <input type='text' name='xoffset' value='$pixelxoffset'> <b>y:</b> <input type='text' name='yoffset' value='$pixelyoffset'>";*/
	print "<br><input type='submit' value='Generate'>";
	print "</form>";
		
		
?>