
<head>
	<title>Moodlet Generator - Space Station 13 - /tg/station branch</title>
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
			print "<textarea cols='50' rows='2'>http://www.ss13.eu/tgdb/moodlet/saves/$file</textarea><br><br>";
			print "<b>BBCode:</b><br><textarea cols='50' rows='2'>[img]http://www.ss13.eu/tgdb/moodlet/saves/".$file."[/img]</textarea><br><br>";
			print "Click <a href='index.php'>here</a> to crate a new image or <a href='http://www.ss13.eu'>here</a> to return to the main page.";
		}else{
			print "Saving failed, go back in your browser or click <a href='index.php'>here</a> and try again";
		}
		return;
	}
	
	$prop_x = 20;
	$prop_y = 22;
	$prop_border_height = 44;
	$prop_border_width = 44;
	$prop_height = 32;
	$prop_width = 32;

	$width=310;
	$height=160;
	
	$def_font_size = 11;
	$max_input_width = 212;
	$max_input_width_desc = 266;
	
	$title = "Moodlet title";
	$title_x = 72;
	$title_y = 36;
	$title_font_size = $def_font_size;
	$title_row_diff = 14;
	$title_height_tollerance = 31; //How tall can the title be before desc gets moved down.
	
	$desc = "No description";
	$desc_x = 21;
	$desc_y = 82;
	$desc_offset_y = 0;	//If title is too big.
	$desc_font_size = 9;
	$desc_row_diff = 16;
	$desc_bottom_row_y = 0;	//Stores the y coordinate of the lowest row, to position the mood thing
	
	$mood = "No mood effect";
	$mood_x = 21;
	$mood_y = 0; //set later, used to determine y postition of time left
	$mood_offset_from_bottom_row_y = 24;
	$mood_font_size = 9;
	
	$bgimage = "bgimage_neutral.png";
	
	$time = "1 hour";
	$time_x = 21;
	$time_offset_from_mood_y = 19;
	$time_font_size = 9;
	
	$prop = "";
	if(isset($_POST["prop"])){
		$prop = $_POST["prop"];
	}
	$title = "Moodlet title";
	if(isset($_POST["title"])){
		$title = $_POST["title"];
	}
	$desc = "No description";
	if(isset($_POST["desc"])){
		$desc = $_POST["desc"];
	}
	$time = "1 hour";
	if(isset($_POST["time"])){
		$time = $_POST["time"];
	}
	$mood = "No mood effect";
	$moodnum = 0;
	if(isset($_POST["mood"])){
		$moodnum = intval($_POST["mood"]);
		if($moodnum < 0){
			$mood = "$moodnum mood";
			$bgimage = "bgimage_bad.png";
		}else if($moodnum > 0){
			$mood = "+$moodnum Mood";
			$bgimage = "bgimage_good.png";
		}else{
			$mood = "No mood effect";
			$bgimage = "bgimage_neutral.png";
		}
	}
	
	$title = str_replace("\\","",$title);
	$desc = str_replace("\\","",$desc);
	$time = str_replace("\\","",$time);
	$mood = str_replace("\\","",$mood);
	

	$image_prop = imagecreatetruecolor( $prop_border_width, $prop_border_height );
	
	$bg = "bgmoodlet.png";
	if(file_exists("./$bg")){
		$image = imagecreatefrompng ( "./$bg" );
		imagealphablending($image , true );
		imageSaveAlpha($image, true);
	}else{
		$image = imagecreatetruecolor( $width, $height );
	}
	
	$prop_bgcolor = imagecolorallocate( $image_prop, 0xA2, 0x94, 0x90 );	//a29490 Was B0B0B0
	$text_color = imagecolorallocate( $image, 0x0A, 0x23, 0x6D );	//a29490 Was B0B0B0
	imagefilledrectangle($image_prop, 0, 0, $prop_border_width, $prop_border_height, $propbgcolor);
	if(file_exists( "./".$bgimage)){
		$image_prop = imagecreatefrompng ( "./".$bgimage );
	}
	
	//Add mob
	
	$src_x = 0;
	$src_y = 0;
	$src_z = 32;	//width
	$src_q = 32;	//height
	
	$dst_z = 32;	//width
	$dst_q = 32;	//height
	$dst_x = 6;
	$dst_y = 6;
	
	if(file_exists("props/$prop")){
		//Mob
		$image_prop_tmp = imagecreatefrompng ( "props/$prop" );
		if($image_prop_tmp != null){
			imagecopyresized ( $image_prop , $image_prop_tmp, $dst_x, $dst_y, $src_x, $src_y,  $dst_z, $dst_q, $src_z, $src_q  );
		}
	}
	
	$width = imagesx ( $image );
	$height = imagesy ( $image );
	
	$words_line = array();
	$title_array = explode(" ", $title);
	
	$current_i = 0;
	foreach($title_array as $word){
		if(!isset($words_line[$current_i])){
			$words_line[$current_i] = $word;
			continue;
		}
		$size1 = ImageTTFBBox($title_font_size,0,'serif_bold.ttf', $words_line[$current_i] . " $word" );
		if($size1[2] <= $max_input_width){
			$words_line[$current_i] .= " $word";
		}else{
			$current_i++;
			$words_line[$current_i] = "$word";
		}
	}
	
	imagecopy($image, $image_prop, $prop_x, $prop_y, 0, 0, $prop_border_width, $prop_border_height);

	foreach($words_line as $i => $title_line){
		imagettftext ($image , $title_font_size, 0 , $title_x, $title_y + ($i * $title_row_diff), $text_color , "serif_bold.ttf" , $title_line );
		if( ($i * $title_row_diff) > $title_height_tollerance){
			$desc_offset_y = ($i * $title_row_diff) - $title_height_tollerance;
		}
	}
	
	$desc_array = explode("\n",$desc);
	$row_i = 0;
	foreach($desc_array as $desc_row){
		$desc_row_word_array = explode(" ", $desc_row);
		$desc_words_line = array();
		$current_i = 0;
		$print_last = 1;
		foreach($desc_row_word_array as $word){
			if(!isset($desc_words_line[$current_i])){
				$desc_words_line[$current_i] = $word;
				$print_last = 1;
				continue;
			}
			$size1 = ImageTTFBBox($desc_font_size,0,'consola.ttf', $desc_words_line[$current_i] . " $word" );
			if($size1[2] <= $max_input_width_desc){
				$desc_words_line[$current_i] .= " $word";
				$print_last = 1;
			}else{
				imagettftext ($image , $desc_font_size, 0 , $desc_x, $desc_y + $desc_offset_y + ($row_i * $desc_row_diff), $text_color , "consola.ttf" , $desc_words_line[$current_i] );
				$desc_bottom_row_y = $desc_y + $desc_offset_y + ($row_i * $desc_row_diff);
				$current_i++;
				$row_i++;
				$desc_words_line[$current_i] = "$word";
				$print_last = 0;
			}
		}
	
		if($print_last == 1){	//last line was not full
			imagettftext ($image , $desc_font_size, 0 , $desc_x, $desc_y + $desc_offset_y + ($row_i * $desc_row_diff), $text_color , "consola.ttf" , $desc_words_line[$current_i] );
			$desc_bottom_row_y = $desc_y + $desc_offset_y + ($row_i * $desc_row_diff);
			$row_i++;
		}
	}
	
	if($moodnum > 0){
		$mood_color = imagecolorallocate( $image, 0x00, 0xA5, 0x8C );	//a29490 Was B0B0B0
	}else if($moodnum < 0){
		$mood_color = imagecolorallocate( $image, 0xCD, 0x10, 0x60 );	//a29490 Was B0B0B0
	}else{
		$mood_color = imagecolorallocate( $image, 0x0A, 0x23, 0x6D );	//a29490 Was B0B0B0
	}
	
	$mood_y = $desc_bottom_row_y + $mood_offset_from_bottom_row_y;
	imagettftext ($image , $mood_font_size, 0 , $mood_x, $mood_y, $mood_color, "serif_bold.ttf" , $mood );
	
	$time_y = $mood_y + $time_offset_from_mood_y;
	imagettftext ($image , $time_font_size, 0 , $time_x, $time_y, $text_color, "serif_bold.ttf" , "Time left: ". $time );
	
	$dirhandle = opendir("w");
	$maxfid = 0;
	while (false !== ($entry = readdir($dirhandle))) {
		if($entry == "." || $entry == ".." || !(endsWith($entry,".png"))){
			continue;
		}
		$f = explode("_",$entry);
		if($f[0] == "moodlet"){
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
		imagepng($image,"w/moodlet_$identifier.png");
		imagedestroy($image);
		
		print "<table><tr><td>";
		print "<img src='w/moodlet_$identifier.png'>";
		print "</td><td>";
		print "Prop preview: (<span id='propname'></span>)<br>";
		print "Select images from the lists below to display a preview<br>";
		print "<img src='' id='proppreview'>";
		print "</td></tr></table>";
	/*}*/
	print "<br><form method='post'><input type='hidden' name='file' value='moodlet_$identifier.png'><input type='submit' name='save' value='save'></form>";

	print "<form method='post'>";
	print "<table><tr><td valign='top'>";
	print "<br><b>Title:</b> <input type='text' name='title' value='$title'>";
	
	//stamp
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
	
	print "<br><b>Description:</b><br><textarea name='desc' rows='5' cols='30'>$desc</textarea>";
	print "<br><b>Mood effect:</b> <input type='text' name='mood' value='$moodnum'>";
	print "<br><b>Time left:</b> <input type='text' name='time' value='$time'>";
	
	print "<br><input type='submit' value='Generate'>";
	print "</form>";
		
		
?>