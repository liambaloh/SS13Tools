
<script type='text/javascript'>

	function dostuff(){
		var select = document.getElementById("propselect");
		var preview = document.getElementById("proppreview");
		
		preview.setAttribute('src', "props/"+select.value);
		//document.write("T");
	}

</script>

<?php

	$width=343;
	$height=19;

	$pixelxoffset = 0;
	$pixelyoffset = 0;

	$line1size = 8;
	$line1x = 50;
	$line1y = 13;

	$name = "Name";
	$job = "Job";
	$bgimage = "userbar1.png";

	if(isset($_GET["name"])){
		$name = $_GET["name"];
	}
	if(isset($_GET["job"])){
		$job = $_GET["job"];
	}

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
	if(isset($_GET["job"])){
		$job = $_GET["job"];
	}
	$bgimage = "blue.png";
	if(isset($_GET["bg"])){
		$bgimage = $_GET["bg"];
	}

	$prop = "blank";
	if(isset($_GET["prop"])){
		$prop = $_GET["prop"];
	}

	if($line1size == 0){
		$line1size = 30;
	}

	$image = imagecreatetruecolor( $width, $height );

	$bgimage;
	if(file_exists("bgs/$bgimage")){
		$image = imagecreatefrompng ( "bgs/$bgimage" );
		if(file_exists("props/$prop")){
			$imageprop = imagecreatefrompng ( "props/$prop" );
			if($imageprop != null){
				imagecopymerge($image, $imageprop, 40+$pixelxoffset, -4+$pixelyoffset, 0, 0, 32, 32, 100);
			}
		}
	}
		
	$width = imagesx ( $image );
	$height = imagesy ( $image );

	$background = imagecolorallocate( $image, 0, 0, 0 );
	$text_colour = imagecolorallocate( $image, 0xFF, 0xFF, 0xFF);

	$font = 4;
	$height = imagefontheight($font) ;

	$textColor = imagecolorallocate ($image, 0xFF, 0xFF, 0xFF);
	$textColorBlack = imagecolorallocate ($image, 0, 0, 0);
	$string = "$name - $job";

	// determine numeric center of image
	$size = ImageTTFBBox($line1size,0,'visitor1.ttf',$string);
	$linex = ($width - abs($size[2]))-12;
	//$Y = ((640 - (abs($size[5] - $size[3])))/2 + (abs($size[5] - $size[3])));

	//black background:
	imagettftext ($image , $line1size , 0 , $linex-1 , $line1y-1, $textColorBlack , "visitor1.ttf" , $string );
	imagettftext ($image , $line1size , 0 , $linex-1 , $line1y+1, $textColorBlack , "visitor1.ttf" , $string );
	imagettftext ($image , $line1size , 0 , $linex+1 , $line1y-1, $textColorBlack , "visitor1.ttf" , $string );
	imagettftext ($image , $line1size , 0 , $linex+1 , $line1y+1, $textColorBlack , "visitor1.ttf" , $string );


	//imagettftext ($image , $line1size , 0 , $line1x , $line1y, $textColor , "visitor1.ttf" , $string );
	imagettftext ($image , $line1size , 0 , $linex , $line1y, $textColor , "visitor1.ttf" , $string );

	$identifier = rand( 100000,999999 );

	imagepng($image,"w/userbar_$identifier.png");
	imagedestroy($image);
	print "<img src='w/userbar_$identifier.png'>";
	print "<br>Please save this image to somewhere else as it can get deleted from this server.";

	print "<form method='get'>";

	print "<br><b>Name:</b> <input type='text' name='name' value='$name'>";
	print "<br><b>Job:</b> <input type='text' name='job' value='$job'>";

	$bgfiles = Array();
	$dirhandle = opendir("bgs");
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".."){
			continue;
		}
		$bgfiles[] = $entry;
	}
	sort($bgfiles);
	closedir($dirhandle);

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