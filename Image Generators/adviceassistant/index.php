
<script type='text/javascript'>

	function dostuff(){
		var select = document.getElementById("propselect");
		var preview = document.getElementById("bgpreview");
		
		preview.setAttribute('src', "background"+select.value+".PNG");
		//document.write("T");
	}

</script>

<?php
		
		$width=640;
		$height=480;
		
		$line1size = 30;
		$line1x = 50;
		$line1y = 45;
		$line2size = 30;
		$line2x = 50;
		$line2y = 470;
		
		$line1 = "Line1";
		$line2 = "Line2";
		$bgimage = "background.PNG";
		
		if(isset($_GET["line1"])){
			$line1 = $_GET["line1"];
		}
		if(isset($_GET["line2"])){
			$line2 = $_GET["line2"];
		}
		if(isset($_GET["line1size"])){
			$line1size = intval($_GET["line1size"]);
		}
		if(isset($_GET["line2size"])){
			$line2size = intval($_GET["line2size"]);
		}
		if(isset($_GET["bg"])){
			$bg = $_GET["bg"];
			switch($bg){
				case "shock":
					$bgimage = "backgroundshock.PNG";
				break;
				case "ass":
					$bgimage = "backgroundass.PNG";
				break;
				case "janitor":
					$bgimage = "backgroundjanitor.PNG";
				break;;
				case "gloves":
					$bgimage = "backgroundgloves.PNG";
				break;
				case "syndicate":
					$bgimage = "backgroundsyndicate.PNG";
				break;
				case "ian":
					$bgimage = "backgroundian.PNG";
				break;
			}
		}
		
		if($line1size == 0){
			$line1size = 30;
		}
		if($line2size == 0){
			$line2size = 30;
		}
		
		$image = imagecreatetruecolor( $width, $height );
		
		$url = $bgimage;
		$image = imagecreatefrompng ( $url );
			
		$width = imagesx ( $image );
		$height = imagesy ( $image );
		
		$background = imagecolorallocate( $image, 0, 0, 0 );
		$text_colour = imagecolorallocate( $image, 255, 255, 255 );
		
		$font = 4;
		$height = imagefontheight($font) ;
		
		$textColor = imagecolorallocate ($image, 0x0, 0x0, 0);
		$string = "TEST THIS THING";
		
		// determine numeric center of image
		$size = ImageTTFBBox($line1size,0,'ariblk.ttf',$line1);
		$line1x = (640 - (abs($size[2]- $size[0])))/2;
		$size = ImageTTFBBox($line2size,0,'ariblk.ttf',$line2);
		$line2x = (640 - (abs($size[2]- $size[0])))/2;
		//$Y = ((640 - (abs($size[5] - $size[3])))/2 + (abs($size[5] - $size[3])));
		
		imagettftext ($image , $line1size , 0 , $line1x , $line1y, $textColor , "ariblk.ttf" , $line1 );
		imagettftext ($image , $line2size , 0 , $line2x , $line2y, $textColor , "ariblk.ttf" , $line2 );
		
		if(($line1 != "Line1" && $line2 != "Line2") && (($line1 != "" && $line2 != "") )){
			imagepng($image,"w/output_tmp.png");
			imagedestroy($image);
			header('Location: ./' . "w/output_tmp.png");
		}else{
			imagedestroy($image);
			print "<img src='backgroundass.PNG' id='bgpreview'>";
		}
		print "<br><b>The image you generate is <font color='red'>TEMPORARY</font>. You will need to save it to your PC and upload it somewhere to show it.</b>";
		print "<form method='get'>";
		
		print "<br><b>Upper line:</b> <input type='text' name='line1'> at size <input type='text' name='line1size' value='$line1size'>";
		print "<br><b>Bottom line:</b> <input type='text' name='line2'> at size <input type='text' name='line2size' value='$line2size'>";
		print "<br><b>Background:</b>	<select name='bg' id='propselect' onChange='dostuff();'>
				   <option value='ass'>Assistant</option>
				   <option value='shock'>Assistant shock</option>
				   <option value='janitor'>Janitor</option>
				   <option value='gloves'>Yellow gloves</option>
				   <option value='syndicate'>Syndicate</option>
				   <option value='ian'>Ian</option>
				 </select>";
		print "<br><input type='submit' value='Generate'>";
		print "</form>";
		
?>