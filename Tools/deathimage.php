<?php
	include("db.php");	
	
	if( isset($_GET["noscale"]) ){
		$noscale = 1;
	}else{
		$noscale = 0;
		if( isset($_GET["oldscale"]) ){
			$oldscale = 1;
		}else{
			$oldscale = 0;
		}
		if( isset($_GET["definedscale"]) ){
			$definedscale = intval($_GET["definedscale"]);
		}else{
			$definedscale = 0;
		}
	}
	if( isset($_GET["useimage"]) ){
		$useimage = 1;
	}else{
		$useimage = 0;
	}
	if( isset($_GET["zlevel"]) ){
		$zlevel = $_GET["zlevel"];
		if(intval($zlevel) != 1){
			$useimage = 0;
		}
	}else{
		$zlevel = 1;
	}
	
	$words = array("alter","analyzer","backup","cache","change","check","checksum","commit","create","delete","describe","do","drop","explain","flush","grant","handler","insert","join","kill","load","lock","optimize","purge","rename","repair","replace","reset","restore","revoke","rollback","savepoint","select","set","show","start","stop","truncate","union","unlock","use");
	
		$class = "";
		if( isset($_GET["class"]) ){
			$class = $_GET["class"];
		}
		$antag = "";
		if( isset($_GET["antag"]) ){
			$antag = $_GET["antag"];
		}
		$key = "";
		if( isset($_GET["key"]) ){
			$key = $_GET["key"];
		}
		$days = "30";
		if( isset($_GET["days"]) ){
			$days = $_GET["days"];
		}
		
		$class_str = "";
		$c_explain_str = "";
		if($class != "" && $class != "All"){
			$c_explain_str = "class = $class";
			$class_str = "AND job LIKE '%$class%'";
		}
		$antag_str = "";
		$a_explain_str = "";
		if($antag != "" && $antag != "No"){
			$a_explain_str = "antag = $antag";
			$antag_str = "AND special LIKE '%$antag%'";
		}
		$key_str = "";
		$k_explain_str = "";
		if($key != ""){
			$k_explain_str = "($key)";
			$key_str = "AND byondkey LIKE '%$key%'";
		}
		$days_str = "";
		$d_explain_str = "";
		if($days != ""){
			$d_explain_str = "$days days;";
		}
		
		$cakd = $class ." ". $antag ." ". $key ." ". $days ." ". $noscale ." ". $oldscale ." ". $useimage ." ". $zlevel ." ". $definedscale;
		
		
		
		$cakd_alpha = "";
		$cakd_lower = strtolower($cakd);
		
		for($i = 0; $i < strlen($cakd_lower); $i++){
			if( $cakd_lower[$i] >= 'a' && $cakd_lower[$i] <= 'z' ){
				$cakd_alpha .= $cakd_lower[$i];
			}else{
				$cakd_alpha .= " ";
			}
		}
		
		foreach($words as $word){
			$cakd_list = explode(" ", $cakd_alpha);
			foreach($cakd_list as $cakd_word){
				if($word == $cakd_word){
					die ("Use of forbidden word: <b>$word</b>");
				}
			}
		}
		
		//print $cakd_lower ."<br>";
		//print "<b>". $cakd_alpha ."</b><br>";
		
		$sql = "SELECT COUNT(coord) as number, coord, INSTR(coord,',') as len FROM `SS13death` WHERE tod >= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -$days DAY) AND coord LIKE '%, $zlevel' $class_str $antag_str $key_str GROUP BY coord ORDER BY len, coord";
		
		$d = mysql_query($sql) or die(mysql_error());
		
		$max_death_per_tile = 0;
		$sum_death = 0;
		
		while($i = mysql_fetch_array($d)){
			$coord_tab = explode(", ",$i["coord"]);
			$x_d = $coord_tab[0];
			$y_d = $coord_tab[1];
			$deaths_tab[$x_d][$y_d] = $i["number"];
			if($max_death_per_tile < $i["number"]){
				$max_death_per_tile = $i["number"];
			}
			$sum_death += $i["number"];
		}
		
		$w_multi = 2;
		$h_multi = 2;
		
		$width = $w_multi * 256;
		$height = $h_multi * 256;
		
		$width = ceil($width / 256) * 256;
		$height = ceil($height / 256) * 256;
		
		$image = imagecreatetruecolor( $width, $height );
		
		if( isset($_GET["useimage"]) ){
			$url = "station.png";
			$image = imagecreatefrompng ( $url );
			
			$width = imagesx ( $image );
			$height = imagesy ( $image );
			/*
			for($y = 0; $y < $height; $y++){
				for($x = 0; $x < $width; $x++){
					$color_str = dechex(imagecolorat ( $image_tmp , $x , $y ));
					$red = hexdec( substr($color_str,0,2) );
					$green = hexdec( substr($color_str,2,2) );
					$blue = hexdec( substr($color_str,4,2) );
					print "[".dechex($color_str)." = ".$red .", ". $green .", ". $blue ."] ";
					$color_tmp = imagecolorallocate ( $image , $red , $green , $blue );
					imagesetpixel ( $image , $x, $y , $color_tmp );
					//imagecolorset ( $image , $color_tmp , $red , $green , $blue );
				}
			}*/
			//print "$url";
			//print "<img src='$url'>";
		}
		
		$w_pix_per_tile = ceil($width / 256);
		$h_pix_per_tile = ceil($height / 256);
		
		
		
		
		$background = imagecolorallocate( $image, 0, 0, 0 );
		//imagefilledrectangle($image, 0, 0, $width, $height, $background);
		$text_colour = imagecolorallocate( $image, 255, 255, 0 );
		
		$font = 4;
		$height = imagefontheight($font) ;
		
		if($definedscale == 0){
			$max_death_per_tile = $max_death_per_tile / 2;
		}else{
			$max_death_per_tile = $definedscale;
		}
		
		$textColor = imagecolorallocate ($image, 0x88, 0x88, 0);
		$string = "";
		if( $a_explain_str != "" && $c_explain_str != "" ){
			$string = "Deaths for $a_explain_str and $c_explain_str $k_explain_str : last $d_explain_str";
		}else if ($a_explain_str != ""){
			$string = "Deaths for $a_explain_str $k_explain_str : last $d_explain_str";
		}else if ($c_explain_str != ""){
			$string = "Deaths for $c_explain_str $k_explain_str : last $d_explain_str";
		}else{
			$string = "Deaths aboard SS13 $k_explain_str : last $d_explain_str";
		}
		$string .= " z = $zlevel";
		imagestring ($image, $font, 10, 10,  $string, $textColor);
		$string = "n = $sum_death";
		imagestring ($image, $font, 10, 22,  $string, $textColor);
		if( isset($_GET["noscale"]) ){
			$string = "fully yellow = undefined";
			imagestring ($image, $font, 10, 34,  $string, $textColor);
		}else if($oldscale==0){
			$string = "fully red = ".ceil($max_death_per_tile);
			imagestring ($image, $font, 10, 34,  $string, $textColor);
		}else{
			$string = "fully yellow = ".ceil($max_death_per_tile);
			imagestring ($image, $font, 10, 34,  $string, $textColor);
		}
		for($y = 0; $y < 256; $y++){
			for($x = 0; $x < 256; $x++){
				if( isset($deaths_tab[$x][256-$y]) ){
					$ratio = min(1,$deaths_tab[$x][256-$y] / $max_death_per_tile);
					$color_intensity = floor(255 * (1-$ratio));
					$color_intensity_inv = floor(255 * ($ratio));
					if( isset($_GET["noscale"]) ){
						$tmp_color = imagecolorallocate( $image, 0xFF, 0xFF, 0 );
					}else if ($oldscale == 0){
						$tmp_color = imagecolorallocate( $image, 0xFF, $color_intensity, 0 );
					}else{
						$tmp_color = imagecolorallocate( $image, $color_intensity_inv, $color_intensity_inv, 0 );
					}
					for($y2 = 0; $y2 < $h_pix_per_tile; $y2++){
						for($x2 = 0; $x2 < $w_pix_per_tile; $x2++){
							imagesetpixel ( $image , ((($x-1)*$w_pix_per_tile)+$x2) , ((($y-1)*$h_pix_per_tile)+$y2) , $tmp_color );
						}
					}
				}
			}
		}
		//imagecolorat ( resource $image , int $x , int $y )
		//$r = ($rgb >> 16) & 0xFF;
		//$g = ($rgb >> 8) & 0xFF;
		//$b = $rgb & 0xFF;
		//imagecolorset ( $image , int $index , int $red , int $green , int $blue [, int $alpha = 0 ] )
		
		
		$identifier = "".$useimage."_".$noscale."_default"."_z$zlevel"."_$days"."_$oldscale"."_$definedscale";
		if($antag != "" && $class != ""){
			$identifier = "".$useimage."_".$noscale."_".$antag."_".$class."_$key"."_z$zlevel"."_$days"."_$oldscale"."_$definedscale";
		}else if($antag != ""){
			$identifier = "".$useimage."_".$noscale."_".$antag."_$key"."_z$zlevel"."_$days"."_$oldscale_$definedscale";
		}else if($class != ""){
			$identifier = "".$useimage."_".$noscale."_".$class."_$key"."_z$zlevel"."_$days"."_$oldscale_$definedscale";
		}
		
		imagepng($image,"w/img_$identifier.png");
		imagedestroy($image);
		
		print "<img src='w/img_$identifier.png'>";
		
		$djob = mysql_query("SELECT DISTINCT job AS job FROM `SS13death` WHERE TO_days(NOW()) - TO_days(tod) <= '30' ORDER BY job") or die(mysql_error());
		$dantag = mysql_query("SELECT DISTINCT special AS antag FROM `SS13death` WHERE TO_days(NOW()) - TO_days(tod) <= '30' ORDER BY antag") or die(mysql_error());
		
		print "<form method='get'>";
		
		//print "<br><b>Class:</b> <input type='text' name='class'>";
		
		print "<br><b>Class:</b> <select name='class'>";
		print "<option value='All'>All</option>";
		while($ijob = mysql_fetch_array($djob)){
			print "<option value='".$ijob["job"]."'>".$ijob["job"]."</option>";
		}
		print "</select>";
		
		print "<br><b>Antagonist:</b> <select name='antag'>";
		print "<option value='No'>No disctinction</option>";
		while($iantag = mysql_fetch_array($dantag)){
			print "<option value='".$iantag["antag"]."'>".$iantag["antag"]."</option>";
		}
		print "</select>";
		print "<br><b>Key:</b> <input type='text' name='key'>";
		print "<br><b>Days:</b> <input type='text' name='days' value='30'>";
		print "<br><b>Z-level:</b> <input type='text' name='zlevel' value='1'>";
		print "<br><b>No scale:</b> <input type='checkbox' name='noscale'>";
		print "<br><b>Old scale:</b> <input type='checkbox' name='oldscale'>";
		print "<br><b>Defined scale:</b> <input type='text' name='definedscale' value='0'>";
		print "<br><b>Use image:</b> <input type='checkbox' name='useimage' checked>";
		print "<br><input type='submit' value='Reload'>";
		
		
		print "</form>";
		
?>