
<script type='text/javascript'>

	function dostuff(){
		var select = document.getElementById("propselect");
		var preview = document.getElementById("proppreview");
		
		preview.setAttribute('src', "props/"+select.value);
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

	$width=320;
	$height=64;

	$pixelxoffset = 0;
	$pixelyoffset = 0;

	$line1size = 12;
	$line2size = 12;
	$line1x = 60;
	$line2x = 60;
	$line1y = 28;
	$line2y = 50;

	$line1 = "Line 1";
	$line2 = "Line 2";
	$bgimage = "blankbg.png";

	if(isset($_GET["line1"])){
		$line1 = $_GET["line1"];
	}
	if(isset($_GET["line2"])){
		$line2 = $_GET["line2"];
	}
	
	$line1 = str_replace("\\","",$line1);
	$line2 = str_replace("\\","",$line2);

	if(isset($_GET["xoffset"])){
		$xoffsetstr = $_GET["xoffset"];
		if($xoffsetstr != null){
			$pixelxoffset = intval($xoffsetstr);
		}
	}
	if(isset($_GET["yoffset"])){
		$yoffsetstr = $_GET["yoffset"];
		if($yoffsetstr != null){
			$pixelyoffset = intval($yoffsetstr);
		}
	}
	$bgimage = "blankbg.png";
	if(isset($_GET["bg"])){
		$bgimage = $_GET["bg"];
	}

	$prop = "blank";
	if(isset($_GET["prop"])){
		$prop = $_GET["prop"];
	}

	if($line1size == 0){
		$line1size = 8;
	}
	if($line2size == 0){
		$line2size = 8;
	}

	$image = imagecreatetruecolor( $width, $height );

	$bgimage;
	if(file_exists("./$bgimage")){
		$image = imagecreatefrompng ( "./$bgimage" );
		imagealphablending($image , true );
		if(file_exists("props/$prop")){
			$imageprop = imagecreatefrompng ( "props/$prop" );
			if($imageprop != null){
				imagecopy($image, $imageprop, 15+$pixelxoffset, 16+$pixelyoffset, 0, 0, 32, 32);
			}
		}
	}
	
	
		
	$width = imagesx ( $image );
	$height = imagesy ( $image );

	$background = imagecolorallocate( $image, 0, 0, 0 );
	$text_color1 = imagecolorallocate( $image, 0xFF, 0xFF, 0x00);
	$text_color2 = imagecolorallocate( $image, 0xFF, 0xFF, 0xFF);

	$font = 4;
	$height = imagefontheight($font) ;

	$string1 = "$line1";
	$string2 = "$line2";
	
	$size = ImageTTFBBox($line1size,0,'mc.ttf',$string1);
	while($size[2] > 200){
		$line1size -= 1;
		$size = ImageTTFBBox($line1size,0,'mc.ttf',$string1);
	}
	
	$size = ImageTTFBBox($line2size,0,'mc.ttf',$string2);
	while($size[2] > 200){
		$line2size -= 1;
		$size = ImageTTFBBox($line2size,0,'mc.ttf',$string2);
	}

	// determine numeric center of image
	//$size = ImageTTFBBox($line1size,0,'mc.ttf',$string);
	//$Y = ((640 - (abs($size[5] - $size[3])))/2 + (abs($size[5] - $size[3])));
	
	/*
	//black background:
	imagettftext ($image , $line1size , 0 , $line1x-1 , $line1y-1, $textColorBlack , "mc.ttf" , $string1 );
	imagettftext ($image , $line1size , 0 , $line1x-1 , $line1y+1, $textColorBlack , "mc.ttf" , $string1 );
	imagettftext ($image , $line1size , 0 , $line1x+1 , $line1y-1, $textColorBlack , "mc.ttf" , $string1 );
	imagettftext ($image , $line1size , 0 , $line1x+1 , $line1y+1, $textColorBlack , "mc.ttf" , $string1 );
	
	imagettftext ($image , $line2size , 0 , $line2x-1 , $line2y-1, $textColorBlack , "mc.ttf" , $string2 );
	imagettftext ($image , $line2size , 0 , $line2x-1 , $line2y+1, $textColorBlack , "mc.ttf" , $string2 );
	imagettftext ($image , $line2size , 0 , $line2x+1 , $line2y-1, $textColorBlack , "mc.ttf" , $string2 );
	imagettftext ($image , $line2size , 0 , $line2x+1 , $line2y+1, $textColorBlack , "mc.ttf" , $string2 );
	*/

	//imagettftext ($image , $line1size , 0 , $line1x , $line1y, $textColor , "mc.ttf" , $string );
	imagettftext ($image , $line1size , 0 , $line1x , $line1y, $text_color1 , "mc.ttf" , $string1 );
	imagettftext ($image , $line2size , 0 , $line2x , $line2y, $text_color2 , "mc.ttf" , $string2 );
	
	$dirhandle = opendir("w");
	$maxfid = 0;
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || !(endsWith($entry,".png"))){
			continue;
		}
		$f = explode("_",$entry);
		if($f[0] == "achievement"){
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

	if($line1 == "Line 1" && $line2 = "Line 2"){
		print "<img src='default.png'>";
	}else{
		imagepng($image,"w/achievement_$identifier.png");
		imagedestroy($image);
		print "<img src='w/achievement_$identifier.png'>";
	}
	print "<br>Please save this image to somewhere else as it can get deleted from this server.";

	print "<form method='get'>";

	print "<br><b>Line 1:</b> <input type='text' name='line1' value='$line1'>";
	print "<br><b>Line 2:</b> <input type='text' name='line2' value='$line2'>";

	$bgfiles = Array();
	$bgfiles[] = $bgimage;
	/*$dirhandle = opendir("bgs");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".."){
			continue;
		}
		$bgfiles[] = $entry;
	}
	sort($bgfiles);
	closedir($dirhandle);*/

	print "<br><b>Background:</b>	<select name='bg'>";
	foreach($bgfiles as $bgentry){
		if($bgentry == $bgimage){
			print "<option value='$bgentry' selected>$bgentry</option>";
		}else{
			print "<option value='$bgentry'>$bgentry</option>";
		}
	}
	print "</select>";
			 
	print "<br><b>Prop:</b>	<select name='prop' id='propselect' onChange='dostuff();'>
			   <option value='0'>Nothing</option>";

	$propfiles = Array();
	$dirhandle = opendir("props");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".."){
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
	print "<br><b>Prop pixel offset: x:</b> <input type='text' name='xoffset' value='$pixelxoffset'> <b>y:</b> <input type='text' name='yoffset' value='$pixelyoffset'>";
	print "<br><input type='submit' value='Generate'>";
	print "</form>";

	print "Prop preview:<br>";
	print "<img src='' id='proppreview'>";
		
		
?>