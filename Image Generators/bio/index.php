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
			print "<textarea cols='50' rows='2'>http://www.ss13.eu/tgdb/bio/saves/$file</textarea><br><br>";
			print "<b>BBCode:</b><br><textarea cols='50' rows='2'>[img]http://www.ss13.eu/tgdb/bio/saves/".$file."[/img]</textarea><br><br>";
			print "Click <a href='index.php'>here</a> to crate a new image or <a href='http://www.ss13.eu'>here</a> to return to the main page.";
		}else{
			print "Saving failed, go back in your browser or click <a href='index.php'>here</a> and try again";
		}
		return;
	}

	$mugshot_offset_x = 10;
	$mugshot_offset_y = 13;

	$width=320;
	$height=65;

	$pixelxoffset = 0;
	$pixelyoffset = 0;

	$str_employee_size = 11;
	$line1size = 15;
	$line2size = 9;
	$str_employee_x = 183;
	$line1x = 183;
	$line2x = 183;
	$str_employee_y = 20;
	$line1y = 40;
	$line2y = 54;
	

	$line1 = "Line 1";
	$line2 = "Line 2";

	if(isset($_POST["line1"])){
		$line1 = $_POST["line1"];
	}
	if(isset($_POST["line2"])){
		$line2 = $_POST["line2"];
	}
	
	$line1 = str_replace("\\","",$line1);
	$line2 = str_replace("\\","",$line2);

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
	
	$skintone = 0;
	if(isset($_POST["skintone"])){
		$skintone = intval($_POST["skintone"]);
	}
	
	/*
	$bgimage = "blankbg.png";
	if(isset($_POST["bg"])){
		$bgimage = $_POST["bg"];
	}*/

	$mugshotbg = "blank";
	if(isset($_POST["mugshotbg"])){
		$mugshotbg = $_POST["mugshotbg"];
	}
	
	$stationname = "Space Station 13";
	$colorscheme = "default";
	if(isset($_POST["stationname"])){
		$stationnum = intval($_POST["stationname"]);
		switch($stationnum){
			case 0: $stationname = "Space Station 13"; break;
			case 1: $stationname = "Ice Station"; $colorscheme = "ice"; break;
			case 2: $stationname = "Lava Station"; $colorscheme = "lava"; break;
			case 3: $stationname = "Ocean Station"; $colorscheme = "ocean"; break;
			case 4: $stationname = "Space Station 13"; $colorscheme = "old"; break;
		}
	}
	
	if($preventclothing == 0){
		$haircolorr = 0;
		if(isset($_POST["haircolorr"])){
			$haircolorr = intval($_POST["haircolorr"]);
		}
		$haircolorg = 0;
		if(isset($_POST["haircolorg"])){
			$haircolorg = intval($_POST["haircolorg"]);
		}
		$haircolorb = 0;
		if(isset($_POST["haircolorb"])){
			$haircolorb = intval($_POST["haircolorb"]);
		}
		
		$eyecolorr = 0xA1;
		if(isset($_POST["eyecolorr"])){
			$eyecolorr = intval($_POST["eyecolorr"]);
		}
		$eyecolorg = 0xE8;
		if(isset($_POST["eyecolorg"])){
			$eyecolorg = intval($_POST["eyecolorg"]);
		}
		$eyecolorb = 0xE5;
		if(isset($_POST["eyecolorb"])){
			$eyecolorb = intval($_POST["eyecolorb"]);
		}
		
		$lipcolorr = 0;
		if(isset($_POST["lipcolorr"])){
			$lipcolorr = intval($_POST["lipcolorr"]);
		}
		$lipcolorg = 0;
		if(isset($_POST["lipcolorg"])){
			$lipcolorg = intval($_POST["lipcolorg"]);
		}
		$lipcolorb = 0;
		if(isset($_POST["lipcolorb"])){
			$lipcolorb = intval($_POST["lipcolorb"]);
		}

		$prop = "blank";
		if(isset($_POST["prop"])){
			$prop = $_POST["prop"];
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

	if($line1size == 0){
		$line1size = 8;
	}
	if($line2size == 0){
		$line2size = 8;
	}

	$image = imagecreatetruecolor( $width, $height );

	$bgimage = "blankbg$colorscheme.png";
	if(file_exists("./$bgimage")){
		$image = imagecreatefrompng ( "./$bgimage" );
		imagealphablending($image , true );
		/*
		if(file_exists("props/$prop")){
			$imageprop = imagecreatefrompng ( "props/$prop" );
			if($imageprop != null){
				imagecopy($image, $imageprop, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
			}
		}*/
	}
	
	//Add mob
	if(file_exists("mob/$mob")){
		
		//Mugshot background and square
			//background
		$mughostbgcolor = imagecolorallocate( $image, 0xB0, 0xB0, 0xB0 );
		imagefilledrectangle($image, $mugshot_offset_x+$pixelxoffset, $mugshot_offset_y+$pixelyoffset - 3, $mugshot_offset_x+$pixelxoffset + 45-1, $mugshot_offset_y+$pixelyoffset + 42-1, $mughostbgcolor);
			//mugshotbg
		if(file_exists("mugshotbg/$mugshotbg")){
			$imagemugshotbg = imagecreatefrompng ( "mugshotbg/$mugshotbg" );
			if($imagemugshotbg != null){
				//imagecopyresized ( $image , $imageunder , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
				imagecopy($image, $imagemugshotbg, $mugshot_offset_x+$pixelxoffset, $mugshot_offset_y+$pixelyoffset - 3, 0, 0, 45, 45);
			}
		}
			//frame
		$mughostrectcolor = imagecolorallocate( $image, 0, 0, 0 );
		imagerectangle($image, $mugshot_offset_x+$pixelxoffset - 1, $mugshot_offset_y+$pixelyoffset - 4, $mugshot_offset_x+$pixelxoffset + 45, $mugshot_offset_y+$pixelyoffset + 42, $mughostrectcolor);
		
		//Mob
		$imagemob = imagecreatefrompng ( "mob/$mob" );
		imagefilter($imagemob, IMG_FILTER_BRIGHTNESS, -$skintone);
		if($imagemob != null){
			imagecopyresized ( $image , $imagemob , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
			//imagecopy($image, $imagemob, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
			
			//lips
			if($lipcolorr != 0 || $lipcolorg != 0 || $lipcolorb != 0){
				$colorlips = imagecolorallocate( $image, $lipcolorr, $lipcolorg, $lipcolorb );
				imagefilledrectangle($image, 31+$pixelxoffset, 37+$pixelyoffset, 33+$pixelxoffset, 39+$pixelyoffset, $colorlips);
			}
			
			
			//eyes
			if($preventclothing == 0){
				$coloreyeright = imagecolorallocate( $image, $eyecolorr, $eyecolorg, $eyecolorb );
				imagefilledrectangle($image, 28+$pixelxoffset, 31+$pixelyoffset, 30+$pixelxoffset, 33+$pixelyoffset, $coloreyeright);	//right
				$coloreyeleft = imagecolorallocate( $image, $eyecolorr, $eyecolorg, $eyecolorb );
				imagefilledrectangle($image, 34+$pixelxoffset, 31+$pixelyoffset, 36+$pixelxoffset, 33+$pixelyoffset, $coloreyeleft);	//left
			}
			
			//hair
			if(file_exists("hair/$hair")){
				$imagehair = imagecreatefrompng ( "hair/$hair" );
				imagefilter($imagehair, IMG_FILTER_COLORIZE, $haircolorr, $haircolorg, $haircolorb);
				if($imagehair != null){
					imagecopyresized ( $image , $imagehair , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			/*
			$coloreyewhite = imagecolorallocate( $image, 0xFF, 0xFF, 0xFF );
			$coloreyeblack = imagecolorallocate( $image, 0, 0 ,0 );
				//right
			$coloreyeright = imagecolorallocate( $image, 0xA1, 0xE8, 0xE5 );
			imagefilledrectangle($image, 28+$pixelxoffset, 31+$pixelyoffset, 30+$pixelxoffset, 33+$pixelyoffset, $coloreyewhite);	//white
			imagefilledrectangle($image, 29+$pixelxoffset, 32+$pixelyoffset, 30+$pixelxoffset, 33+$pixelyoffset, $coloreyeright);	//color
			imagefilledrectangle($image, 30+$pixelxoffset, 33+$pixelyoffset, 30+$pixelxoffset, 33+$pixelyoffset, $coloreyeblack);	//black
				//left
			$coloreyeleft = imagecolorallocate( $image, 0xA1, 0xE8, 0xE5 );
			imagefilledrectangle($image, 34+$pixelxoffset, 31+$pixelyoffset, 36+$pixelxoffset, 33+$pixelyoffset, $coloreyewhite);	//white
			imagefilledrectangle($image, 34+$pixelxoffset, 32+$pixelyoffset, 35+$pixelxoffset, 33+$pixelyoffset, $coloreyeleft);	//color
			imagefilledrectangle($image, 34+$pixelxoffset, 33+$pixelyoffset, 34+$pixelxoffset, 33+$pixelyoffset, $coloreyeblack);	//black
			*/
			
			//under/jumpsuit
			if(file_exists("under/$under")){
				$imageunder = imagecreatefrompng ( "under/$under" );
				if($imageunder != null){
					imagecopyresized ( $image , $imageunder , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//suit
			if(file_exists("suit/$suit")){
				$imagesuit = imagecreatefrompng ( "suit/$suit" );
				if($imagesuit != null){
					imagecopyresized ( $image , $imagesuit , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//facial hair
			if(file_exists("facial/$facial")){
				$imagefacial = imagecreatefrompng ( "facial/$facial" );
				imagefilter($imagefacial, IMG_FILTER_COLORIZE, $haircolorr, $haircolorg, $haircolorb);
				if($imagefacial != null){
					imagecopyresized ( $image , $imagefacial , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//glasses
			if(file_exists("glasses/$glasses")){
				$imageglasses = imagecreatefrompng ( "glasses/$glasses" );
				if($imageglasses != null){
					imagecopyresized ( $image , $imageglasses , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//mask
			if(file_exists("mask/$mask")){
				$imagemask = imagecreatefrompng ( "mask/$mask" );
				if($imagemask != null){
					imagecopyresized ( $image , $imagemask , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
			
			//headgear
			if(file_exists("headgear/$headgear")){
				$imageheadgear = imagecreatefrompng ( "headgear/$headgear" );
				if($imageheadgear != null){
					imagecopyresized ( $image , $imageheadgear , $mugshot_offset_x+$pixelxoffset , $mugshot_offset_y+$pixelyoffset , 8 , 0 , 45 , 42 , 15 , 14 );
					//imagecopy($image, $imageheadgear, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
				}
			}
		}
	}
	
		
	$width = imagesx ( $image );
	$height = imagesy ( $image );
	$useborder = 0;
	
	switch($colorscheme){
		case "default":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color_title = imagecolorallocate( $image, 0x3b, 0x3b, 0x3b);
			$text_color_title_b = imagecolorallocate( $image, 0x93, 0x93, 0x93);
			$text_color1 = imagecolorallocate( $image, 0x3b, 0x3b, 0x3b);
			$text_color1_b = imagecolorallocate( $image, 0x93, 0x93, 0x93);
			$text_color2 = imagecolorallocate( $image, 0x93, 0x93, 0x93);
			$text_color2_b = imagecolorallocate( $image, 0x3b, 0x3b, 0x3b);
			$useborder = 1;
		break;
		case "ice":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color_title = imagecolorallocate( $image, 0x3e, 0x46, 0x7a);
			$text_color1 = imagecolorallocate( $image, 0x59, 0x64, 0xab);
			$text_color2 = imagecolorallocate( $image, 0x3e, 0x46, 0x7a);
		break;
		case "lava": 
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color_title = imagecolorallocate( $image, 0xC4, 0xDF, 0xE1);
			$text_color1 = imagecolorallocate( $image, 0xFF, 0xFF, 0xFF);
			$text_color2 = imagecolorallocate( $image, 0xC4, 0xDF, 0xE1);
		break;
		case "ocean":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color_title = imagecolorallocate( $image, 0xb7, 0xba, 0xce);
			$text_color1 = imagecolorallocate( $image, 0xc8, 0xca, 0xd9);
			$text_color2 = imagecolorallocate( $image, 0xb7, 0xba, 0xce);
		break;
		case "old":
			$background = imagecolorallocate( $image, 0, 0, 0 );
			$text_color_title = imagecolorallocate( $image, 0xC4, 0xDF, 0xE1);
			$text_color1 = imagecolorallocate( $image, 0xFF, 0xFF, 0xFF);
			$text_color2 = imagecolorallocate( $image, 0xC4, 0xDF, 0xE1);
		break;
	}

	$font = 4;
	$height = imagefontheight($font) ;

	$string1 = "$line1";
	$string2 = "$line2";
	
	$size1 = ImageTTFBBox($line1size,0,'consolas.ttf',$string1);
	while($size1[2] > 200){
		$line1size -= 1;
		$size1 = ImageTTFBBox($line1size,0,'consolas.ttf',$string1);
	}
	
	$size2 = ImageTTFBBox($line2size,0,'consolas.ttf',$string2);
	while($size2[2] > 200){
		$line2size -= 1;
		$size2 = ImageTTFBBox($line2size,0,'consolas.ttf',$string2);
	}

	$title = "Employee of $stationname";
	$sizetitle = ImageTTFBBox($str_employee_size,0,'consolas.ttf',$title);
	if($useborder == 1){
		imagettftext ($image , $str_employee_size , 0 , $str_employee_x - floor($sizetitle[2]/2) -1 , $str_employee_y -1, $text_color_title_b , "consolas.ttf" , $title );
		imagettftext ($image , $str_employee_size , 0 , $str_employee_x - floor($sizetitle[2]/2) -1 , $str_employee_y +1, $text_color_title_b , "consolas.ttf" , $title );
		imagettftext ($image , $str_employee_size , 0 , $str_employee_x - floor($sizetitle[2]/2) +1 , $str_employee_y -1, $text_color_title_b , "consolas.ttf" , $title );
		imagettftext ($image , $str_employee_size , 0 , $str_employee_x - floor($sizetitle[2]/2) +1 , $str_employee_y +1, $text_color_title_b , "consolas.ttf" , $title );
	}
	imagettftext ($image , $str_employee_size , 0 , $str_employee_x - floor($sizetitle[2]/2) , $str_employee_y, $text_color_title , "consolas.ttf" , $title );
	if($useborder == 1){
		imagettftext ($image , $line1size, 0 , $line1x - floor($size1[2]/2) -1, $line1y -1, $text_color1_b , "consolas.ttf" , $string1 );
		imagettftext ($image , $line1size, 0 , $line1x - floor($size1[2]/2) -1, $line1y +1, $text_color1_b , "consolas.ttf" , $string1 );
		imagettftext ($image , $line1size, 0 , $line1x - floor($size1[2]/2) +1, $line1y -1, $text_color1_b , "consolas.ttf" , $string1 );
		imagettftext ($image , $line1size, 0 , $line1x - floor($size1[2]/2) +1, $line1y +1, $text_color1_b , "consolas.ttf" , $string1 );
	}
	imagettftext ($image , $line1size, 0 , $line1x - floor($size1[2]/2), $line1y, $text_color1 , "consolas.ttf" , $string1 );
	if($useborder == 1){
		imagettftext ($image , $line2size, 0 , $line2x - floor($size2[2]/2) -1, $line2y -1, $text_color2_b , "consolas.ttf" , $string2 );
		imagettftext ($image , $line2size, 0 , $line2x - floor($size2[2]/2) -1, $line2y +1, $text_color2_b , "consolas.ttf" , $string2 );
		imagettftext ($image , $line2size, 0 , $line2x - floor($size2[2]/2) +1, $line2y -1, $text_color2_b , "consolas.ttf" , $string2 );
		imagettftext ($image , $line2size, 0 , $line2x - floor($size2[2]/2) +1, $line2y +1, $text_color2_b , "consolas.ttf" , $string2 );
	}
	imagettftext ($image , $line2size, 0 , $line2x - floor($size2[2]/2), $line2y, $text_color2 , "consolas.ttf" , $string2 );
	
	$dirhandle = opendir("w");
	$maxfid = 0;
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || !(endsWith($entry,".png"))){
			continue;
		}
		$f = explode("_",$entry);
		if($f[0] == "bio"){
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
		imagepng($image,"w/bio_$identifier.png");
		imagedestroy($image);
		print '
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- bio -->
		<ins class="adsbygoogle"
			 style="display:inline-block;width:728px;height:90px"
			 data-ad-client="ca-pub-6063115855965447"
			 data-ad-slot="5348254727"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>';
		print "<br><img src='w/bio_$identifier.png'>";
	/*}*/
	print "<br><form method='post'><input type='hidden' name='file' value='bio_$identifier.png'><input type='submit' name='save' value='save'></form>";

	print "<form method='post'>";

	print "<br><b>Name:</b> <input type='text' name='line1' value='$line1'>";
	print "<br><b>Job/Role:</b> <input type='text' name='line2' value='$line2'>";
	print "<br><b>Employee of:</b>";
	print "<select name='stationname'>";
	if($stationname == "Space Station 13" && $colorscheme == "default"){
		print "<option value='0' selected>Space Station 13</option>";
	}else{
		print "<option value='0'>Space Station 13</option>";
	}
	if($stationname == "Ice Station"){
		print "<option value='1' selected>Ice Station (Starbound)</option>";
	}else{
		print "<option value='1'>Ice Station (Starbound)</option>";
	}
	if($stationname == "Lava Station"){
		print "<option value='2' selected>Lava Station (Starbound)</option>";
	}else{
		print "<option value='2'>Lava Station (Starbound)</option>";
	}
	if($stationname == "Ocean Station"){
		print "<option value='3' selected>Ocean Station (Starbound)</option>";
	}else{
		print "<option value='3'>Ocean Station (Starbound)</option>";
	}
	if($stationname == "Space Station 13" && $colorscheme == "old"){
		print "<option value='4' selected>Space Station 13 (Old)</option>";
	}else{
		print "<option value='4'>Space Station 13 (Old)</option>";
	}
	print "</select>";

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
	print "<br><b>Body:</b>	<select name='mob' id=mobselect' onChange='dostuff(\"mob\",\"mob\");'>";

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
	
	//skintone
	print "<br><b>Skin tone modifier:</b> <input type='text' name='skintone' value='$skintone'> (negative numbers to make lighter, positive numbers to make darker. (common values from -30 to 200))";
	
	//hair
	print "<br><b>Hair:</b>	<select name='hair' id=hairselect' onChange='dostuff(\"hair\",\"hairselect\");'>
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
	print "<br><b>Hair/facial hair color:</b> r:<input type='text' name='haircolorr' value='$haircolorr'>, g:<input type='text' name='haircolorg' value='$haircolorg'>, b:<input type='text' name='haircolorb' value='$haircolorb'> (0-255, copy from ss13 or an color chooser.)";
	
	//Eye color
	print "<br><b>Eye color:</b> r:<input type='text' name='eyecolorr' value='$eyecolorr'>, g:<input type='text' name='eyecolorg' value='$eyecolorg'>, b:<input type='text' name='eyecolorb' value='$eyecolorb'> (0-255, copy from ss13 or an color chooser.)";
	
	//Lipstick color
	print "<br><b>Lipstick color:</b> r:<input type='text' name='lipcolorr' value='$lipcolorr'>, g:<input type='text' name='lipcolorg' value='$lipcolorg'>, b:<input type='text' name='lipcolorb' value='$lipcolorb'> (0-255, set all to 0 to not use lipstick)";
	
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

	print "Prop preview: (<span id='propname'></span>)<br>";
	print "<img src='' id='proppreview'>";
		
		
?>