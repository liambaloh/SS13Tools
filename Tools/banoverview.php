<?php 
include("db.php");

$banned_users = Array();

function rights2text($permissions, $permission, $value){
	if(($permissions & $permission) != 0){
		return $value;
	}else{
		return "";
	}
}



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
	
function output($s,$banlog){
		
	$mckey = $s["ckey"];
	$mip = $s["ip"];
	$mls = $s["lastseen"];
	$mfs = $s["firstseen"];

	print "<p><table bgcolor='#ffffdd' align='center' width='1100' cellspacing='0' cellpadding='2'>";
	print "<tr bgcolor='#ffffff'><td width='25%'>&nbsp;</td><td width='25%'>&nbsp;</td><td width='25%'>&nbsp;</td><td width='25%'>&nbsp;</td></tr>";
	print "<tr align='center' bgcolor='#dddddd'><td colspan='2'><font size='7'><b>".$mckey."</b></font><br><font size='2'><b>$mip</b></font></td><td width='25%'>First seen (days ago)<br><font size='6'><b>$mfs</b></font></td><td width='25%'>Last seen (days ago)<br><font size='6'><b>$mls</b></font></td></tr>";
	
	$lightcolor = true;
	foreach($banlog[$mckey] as $b){
		$lightcolor = !$lightcolor;
		$bantime = $b["bantime"];
		$bantype = $b["bantype"];
		$reason = $b["reason"];
		$job = $b["job"];
		$duration = $b["duration"];
		$admin = $b["admin"];
		$unbanned = $b["unbanned"];
		$ubdaysago = $b["ubdaysago"];
		$daysago = $b["daysago"];
		$edits = $b["edits"];
		
		$cfiller = "dd";
		if($lightcolor){
			$cfiller = "ee";
		}
		
		$color = "#ff$cfiller$cfiller";
		if($unbanned != ""){
			$color = "#".$cfiller."ff$cfiller";
		}
		
		
		print "<tr bgcolor='$color'>";
		
		print "<td align='center'>";
		
		switch($bantype){
			case "PERMABAN":
				print "<font color='red'><b>$bantype</b></font>";
			break;
			case "TEMPBAN":
				print "<b>$bantype ($duration minutes)</b>";
			break;
			case "JOB_PERMABAN":
				if(file_exists("jobs/$job.png")){
					print "<img src='jobs/$job.png'><br>";
				}else if(file_exists("jobs/$job.gif")){
					print "<img src='jobs/$job.gif'><br>";
				}
				print "<b>$bantype ($job)</b>";
			break;
			case "JOB_TEMPBAN":
				if(file_exists("jobs/$job.png")){
					print "<img src='jobs/$job.png'><br>";
				}else if(file_exists("jobs/$job.gif")){
					print "<img src='jobs/$job.gif'><br>";
				}
				print "<b>$bantype ($job)</b>";
				print "<br><b>($duration minutes)</b>";
			break;
			case "ADMIN_PERMABAN":
				print "<font color='red' size='4'><b>$bantype</b></font>";
			break;
			case "ADMIN_TEMPBAN":
				print "<font color='red' size='4'><b>$bantype ($duration minutes)</b></font>";
			break;
			case "APPEARANCE_PERMABAN":
				print "<b>$bantype</b>";
			break;
			default:
				print "<b>$bantype<br>(<font color='red'>UNKNOWN BAN TYPE</font>)</b>";
			break;
		}
		if($unbanned != ""){
			print "<br>(Unbanned)";
		}
		print "</td>";

		print "<td align='center' colspan = '2'>";
		
		print "Banned by <b>$admin</b> for reason:<br>";
		print "<cite>\"$reason\"</cite>";
		if($unbanned != ""){
			print "<br>Unbanned <b>$ubdaysago</b> days ago by <b>$unbanned</b>. The ban was active for <b>".($daysago-$ubdaysago)."</b> days.";
		}
		print "</td>";

		print "<td align='center'>";
		
		print "This ban was applied (days ago)<br>";
		print "<font size='6'>$daysago</font>";
		
		print "</td>";
		
		print "</tr>";
		$edits = str_replace("<br><BR>","<br>",$edits);
		if($edits != ""){
			print "<tr><td colspan='4'><b>Edits done to the above ban:</b><cite>$edits</cite></td></tr>";
		}
		
	}
	print "</table></p>";

}

print "<h1 align='center'>Ban log for /tg/ station 13</h1>";

$viewadminlog = 0;

if($auth == 0){
	$ip = $_SERVER['REMOTE_ADDR'];
	$d = mysql_query("SELECT *, datediff(Now(), lastseen) AS timeago FROM ss13player WHERE ip = '$ip' AND lastadminrank != 'Player'") or die(mysql_error());
	while($i = mysql_fetch_array($d)){
		if($i["lastadminrank"] != "Player"){
			$auth = 1;
			$user = $i["ckey"];
			
			if($i["timeago"] >= 1){
				print "<div align='center'><b>Your last login into the game from this location happened more than a day ago. Please log into the game and come back to authenticate yourself.</b></div>";
				$auth = 0;
				$user = "";
			}
			break;
		}
	}
}

if($auth == 0){
	print "
	<div align='center'>
		<p>You were not authenticated as an administrator of TG-Station. If you are one, please log into the game from this computer and reload the page.
	</div>
	";
}else{
	
	//Player from IP/CID lookup 1
	$iplookupval = "";
	if(isset($_GET["playerfromip"])){
		$iplookupval = $_GET["playerfromip"];
	}
	$cidlookupval = "";
	if(isset($_GET["playerfromcid"])){
		$cidlookupval = $_GET["playerfromcid"];
	}
	$ckeylookupval = "";
	if(isset($_GET["playerfromckey"])){
		$ckeylookupval = $_GET["playerfromckey"];
	}
	
	//Player from IP/CID lookup 2
	print "<div align='center'>";
	if(isset($_GET["playerfromip"])){
		print "<h2>Player lookup from IP ".$_GET["playerfromip"].":</h2>";
	}else if(isset($_GET["playerfromcid"])){
		print "<h2>Player lookup from Computer ID ".$_GET["playerfromcid"].":</h2>";
	}else{
		print "<h2>Player lookup from IP / Computer ID:</h2>";
	}
	print "
		<p><form method='get' align='center'>
			<b>Ckey part:</b> <input type='text' name='playerfromckey' value='$ckeylookupval'>
			<input type='submit' name='action' value='Find players from ckey part'>
		</form>
		<form method='get' align='center'>
			<b>IP:</b> <input type='text' name='playerfromip' value='$iplookupval'>
			<input type='submit' name='action' value='Find players from IP'>
		</form>
		<form method='get' align='center'>
			<b>Computer ID:</b> <input type='text' name='playerfromcid' value='$cidlookupval'>
			<input type='submit' name='action' value='Find players from CID'>
		</form>
	</div>
	";
	
	
	//Player from ckey/IP/CID lookup 3
	
	//ckey lookup
	if(isset($_GET["playerfromckey"])){
		print "<div align='center'>";
		$lookupckey = $_GET["playerfromckey"];
		
		$dip = mysql_query("SELECT * FROM ss13player WHERE ckey LIKE '%$lookupckey%'") or die(mysql_error());
		while($iip = mysql_fetch_array($dip)){
			
			$mip = $iip["ip"];
			$mcid = $iip["computerid"];
			$mckey = $iip["ckey"];
			$mfirstseen = $iip["firstseen"];
			$mlastseen = $iip["lastseen"];
			
			$mckey = str_replace($lookupckey,"<font color='#008000'>$lookupckey</font>",$mckey);
			print "<b>$mckey</b> (Computer ID: <b>$mcid</b>; IP: <b>$mip</b>) was first seen on <b>$mfirstseen</b> and last seen on <b>$mlastseen</b><br>";
		}
		print "</div>";
	}
	//Ip lookup
	if(isset($_GET["playerfromip"])){
		print "<div align='center'>";
		$lookupip = $_GET["playerfromip"];
		$mdot1 = strpos($lookupip, ".");
		$mdot2 = strpos($lookupip, ".", $mdot1+1);
		$mdot3 = strpos($lookupip, ".", $mdot2+1);
		
		$mip1 = substr($lookupip, 0, $mdot1);
		$mip2 = substr($lookupip, $mdot1+1, $mdot2-$mdot1-1);
		$mip3 = substr($lookupip, $mdot2+1, $mdot3-$mdot2-1);
		$mip4 = substr($lookupip, $mdot3+1);
		
		$dip = mysql_query("SELECT * FROM ss13player WHERE ip LIKE '$mip1.$mip2%'") or die(mysql_error());
		while($iip = mysql_fetch_array($dip)){
			
			$mip = $iip["ip"];
			$mcid = $iip["computerid"];
			$mckey = $iip["ckey"];
			$mfirstseen = $iip["firstseen"];
			$mlastseen = $iip["lastseen"];
			
			if($mip == $lookupip){
				print "<br><b><font color='#008000'>Full IP match</font>:</b> ";
			}else if(startsWith($mip,"$mip1.$mip2.$mip3")){
				print "<br><b><font color='#808000'>/24 IP match</font>:</b> ";
			}else{
				print "<br><b><font color='#800000'>/16 IP match</font>:</b> ";
			}
			
			print "<b>$mckey</b> (Computer ID: <b>$mcid</b>; IP: <b>$mip</b>) was first seen on <b>$mfirstseen</b> and last seen on <b>$mlastseen</b>";
		}
		print "</div>";
	}
	//Computer id lookup
	if(isset($_GET["playerfromcid"])){
		print "<div align='center'>";
		$lookupcid = $_GET["playerfromcid"];
		
		$dip = mysql_query("SELECT * FROM ss13player WHERE computerid = '$lookupcid'") or die(mysql_error());
		while($iip = mysql_fetch_array($dip)){
			
			$mip = $iip["ip"];
			$mcid = $iip["computerid"];
			$mckey = $iip["ckey"];
			$mfirstseen = $iip["firstseen"];
			$mlastseen = $iip["lastseen"];
			print "<b>Computer ID match: $mckey</b> (Computer ID: <b>$mcid</b>; IP: <b>$mip</b>) was first seen on <b>$mfirstseen</b> and last seen on <b>$mlastseen</b>";
		}
		print "</div>";
	}
	
	
	//History lookup 1
	$lookupval = "";
	if(isset($_GET["historylookup"])){
		$lookupval = $_GET["historylookup"];
	}
	
	//History lookup form
	print "<div align='center'>";
	print "<h2 align='center'>Player History Lookup</h2>";
	print "<p><form method='get' align='center'>
			Ckey: <input type='text' name='historylookup' value='$lookupval'>
			<br><input type='submit' name='action' value='Normal lookup'>
			<br><input type='submit' name='detailedoutput' value='Detailed lookup'>
		</form>
	</div>
	";
	
	//Connection log lookup 1
	$cllookupckey = "";
	if(isset($_GET["connectionlookup"])){
		$cllookupckey = $_GET["connectionlookup"];
	}
	$cllookupip = "";
	if(isset($_GET["connectionlookupip"])){
		$cllookupip = $_GET["connectionlookupip"];
	}
	$cllookupcid = "";
	if(isset($_GET["connectionlookupcid"])){
		$cllookupcid = $_GET["connectionlookupcid"];
	}
	
	//Connection log lookup form
	print "<div align='center'>";
	print "<h2 align='center'>Player Connection Log Lookup</h2>";
	print "<p><form method='get' align='center'>
			Ckey: <input type='text' name='connectionlookup' value='$cllookupckey'>
			<br>IP: <input type='text' name='connectionlookupip' value='$cllookupip'>
			<br>Cid: <input type='text' name='connectionlookupcid' value='$cllookupcid'>
			<br><input type='submit' name='cnaction' value='Normal lookup (Ckey only!)'>
			<br><input type='submit' name='cnaction' value='Detailed lookup'>
		</form>
	</div>
	";
	
	//History lookup 3
	if(isset($_GET["historylookup"])){
		$ckey = $_GET["historylookup"];
		$da = mysql_query("SELECT * FROM ss13player WHERE ckey = '$ckey'") or die(mysql_error());
		if($ia = mysql_fetch_array($da)){
			$ip = $ia["ip"];
			$cid = $ia["computerid"];
			
			$dot1 = strpos($ip, ".");
			$dot2 = strpos($ip, ".", $dot1+1);
			$dot3 = strpos($ip, ".", $dot2+1);
			
			$ip1 = substr($ip, 0, $dot1);
			$ip2 = substr($ip, $dot1+1, $dot2-$dot1-1);
			$ip3 = substr($ip, $dot2+1, $dot3-$dot2-1);
			$ip4 = substr($ip, $dot3+1);
			
			$ip1matches = Array();
			$ip2matches = Array();
			$ip3matches = Array();
			$ip4matches = Array();
			$cidmatches = Array();
			$banlog = Array();
			
			mysql_query("DROP TEMPORARY TABLE IF EXISTS ss13tmp_matches") or die(mysql_error());
			mysql_query("CREATE TEMPORARY TABLE ss13tmp_matches (ckey VARCHAR(32))") or die(mysql_error());
			$tmp_insert_statement = "INSERT INTO ss13tmp_matches VALUES ";
			
			$dip = mysql_query("SELECT * FROM ss13player WHERE ip LIKE '$ip1%'") or die(mysql_error());
			print "<div align='center'>";
			print "<h1>IP Matches:</h1>";
			while($iip = mysql_fetch_array($dip)){
				$mdot1 = strpos($ip, ".");
				$mdot2 = strpos($ip, ".", $mdot1+1);
				$mdot3 = strpos($ip, ".", $mdot2+1);
				
				$mip1 = substr($ip, 0, $mdot1);
				$mip2 = substr($ip, $mdot1+1, $mdot2-$mdot1-1);
				$mip3 = substr($ip, $mdot2+1, $mdot3-$mdot2-1);
				$mip4 = substr($ip, $mdot3+1);
				
				$mip = $iip["ip"];
				$mckey = $iip["ckey"];
				$mfirstseen = $iip["firstseen"];
				$mlastseen = $iip["lastseen"];
				
				
				$tmp_insert_statement .= " ('$mckey'),";
				
				$banlog[$mckey] = Array();
				
				$m = 0;
				
				if(startsWith($mip, $ip1.".".$ip2.".".$ip3.".".$ip4)){
					$m = 4;
					$mip = "<font color='#008800'>".$ip1.".".$ip2.".".$ip3.".".$ip4."</font>";
				}else if(startsWith($mip, $ip1.".".$ip2.".".$ip3)){
					$m = 3;
					$mip = "<font color='#008800'>".$ip1.".".$ip2.".".$ip3."</font><font color='#880000'>".substr($mip, $mdot3)."</font>";
				}else  if(startsWith($mip, $ip1.".".$ip2)){
					$m = 2;
					$mip = "<font color='#008800'>".$ip1.".".$ip2."</font><font color='#880000'>".substr($mip, $mdot2)."</font>";
				}else  if(startsWith($mip, $ip1)){
					$m = 1;
					$mip = "<font color='#008800'>".$ip1."</font><font color='#880000'>".substr($mip, $mdot1)."</font>";
				}else {
					print "LOLERROR";
				}
				$descriptor = Array();
				$descriptor["ckey"] = $mckey;
				$descriptor["desc"] = "<b>$mckey</b> - <b>$mip</b><br>First seen: <b>$mfirstseen</b> - Last seen: <b>$mlastseen</b><br>";
				$descriptor["ip"] = $mip;
				$descriptor["firstseen"] = round(abs((strtotime($mfirstseen) - strtotime(date("Y-m-d"))) / (60 * 60 * 24)));
				$descriptor["lastseen"] = round(abs((strtotime($mlastseen) - strtotime(date("Y-m-d"))) / (60 * 60 * 24)));
				
				if($m == 4){
					$ip4matches[$mckey] = $descriptor;
				}else if($m == 3){
					$ip3matches[$mckey] = $descriptor;
				}else  if($m == 2){
					$ip2matches[$mckey] = $descriptor;
				}else  if($m == 1){
					$ip1matches[$mckey] = $descriptor;
				}else {
					print "LOLERROR";
				}
			}
			
			$dcid = mysql_query("SELECT * FROM ss13player WHERE computerid = '$cid'") or die(mysql_error());
			while($icid = mysql_fetch_array($dcid)){
				
				$mip = $icid["ip"];
				$mckey = $icid["ckey"];
				$mfirstseen = $icid["firstseen"];
				$mlastseen = $icid["lastseen"];
				$tmp_insert_statement .= " ('$mckey'),";
				$descriptor = Array();
				$descriptor["ckey"] = $mckey;
				$descriptor["desc"] = "<b>$mckey</b> - <b>$mip</b><br>First seen: <b>$mfirstseen</b> - Last seen: <b>$mlastseen</b><br>";
				$descriptor["ip"] = $mip;
				$descriptor["firstseen"] = round(abs((strtotime($mfirstseen) - strtotime(date("Y-m-d"))) / (60 * 60 * 24)));
				$descriptor["lastseen"] = round(abs((strtotime($mlastseen) - strtotime(date("Y-m-d"))) / (60 * 60 * 24)));
				
				$banlog[$mckey] = Array();
				
				$cidmatches[$mckey] = $descriptor;
			}
			
			$tmp_insert_statement = substr($tmp_insert_statement, 0, $tmp_insert_statement-1);
			mysql_query($tmp_insert_statement) or die(mysql_error());
			
			$dban = mysql_query("SELECT b.* FROM ss13ban b WHERE b.ckey in (SELECT DISTINCT ckey FROM ss13tmp_matches) ORDER BY bantime") or die(mysql_error());
			
			while($iban = mysql_fetch_array($dban)){
				$bckey = $iban["ckey"];
				
				$id = count($banlog[$bckey])+1;
				$banlog[$bckey][$id]["bantime"] = $iban["bantime"];
				$banlog[$bckey][$id]["bantype"] = $iban["bantype"];
				$banlog[$bckey][$id]["reason"] = $iban["reason"];
				$banlog[$bckey][$id]["job"] = $iban["job"];
				$banlog[$bckey][$id]["duration"] = $iban["duration"];
				$banlog[$bckey][$id]["admin"] = $iban["a_ckey"];
				$banlog[$bckey][$id]["unbanned"] = $iban["unbanned_ckey"];
				$banlog[$bckey][$id]["edits"] = $iban["edits"];
				
				$daysago = round(abs((strtotime($iban["bantime"]) - strtotime(date("Y-m-d"))) / (60 * 60 * 24)));
				$ubdaysago = null;
				if($iban["unbanned_datetime"] != ""){
					$ubdaysago = round(abs((strtotime($iban["unbanned_datetime"]) - strtotime(date("Y-m-d"))) / (60 * 60 * 24)));
				}
				$banlog[$bckey][$id]["daysago"] = $daysago;
				$banlog[$bckey][$id]["ubdaysago"] = $ubdaysago;
			}
			
			print "<h2>Full IP Matches:</h2>";
			foreach($ip4matches as $ckey => $s){
				output($s,$banlog);
			}
			
			print "<h2>/24 IP Matches:</h2>";
			foreach($ip3matches as $ckey => $s){
				output($s,$banlog);
			}
			
			print "<h2>/16 IP Matches:</h2>";
			foreach($ip2matches as $ckey => $s){
				output($s,$banlog);
			}
			
			print "<h2>/8 IP Matches:</h2>";
			if(isset($_GET["detailedoutput"])){
				foreach($ip1matches as $ckey => $s){
					output($s,$banlog);
				}
			}else{
				print "<p><table align='center' width='1100' cellspacing='0' cellpadding='2'><tr><td align='center'>";
				foreach($ip1matches as $ckey => $s){
					print "$ckey - ".$s["ip"].", ";
				}
				print "</td></tr></table>";
			}
			
			print "<h1>CID Matches:</h1>";
			foreach($cidmatches as $ckey => $s){
				output($s,$banlog);
			}
			print "</div>";
			
		}else{
			print "<div align='center'>";
			print "Ckey not found in database. Make sure you entered the ckey and not the key. The match must be exact.";
			print "</div>";
		}
	}
	
	
	//Connection log lookup 2
	if(isset($_GET["connectionlookup"])){
	
		//Connection log normal lookup
		if(isset($_GET["cnaction"]) && startsWith($_GET["cnaction"],"Normal lookup")){

			$ckey = $_GET["connectionlookup"];
			$da = mysql_query("SELECT date(datetime) as day, count(*) as connections FROM ss13connection_log WHERE ckey = '$ckey' group by date(datetime)") or die(mysql_error());
			print "<div align='center'>";
			print "<h1>Connection log lookup for $ckey:</h1>";
			print "<table align='center' width='600'><tr><td><font size='2'>There are two displays here: <b><font color='#80ff80'>The green display</font></b> shows the relative <b>total number of connections to all servers on a day</b>. This display is RELATIVE to this person's maximum numbers of connections in a day ever logged. The other, <b><font color='#ff0000'>red display</font></b> shows the <b>time (in days) between successive connections</b>. This display is ABSOLUTE, with <b><font color='#8080ff'>fully red</font></b> always being set to 90 days, so 3 months. The reason for it being 90 is because that is the agreed upon number of days of inactivity it takes for an admin to get changed to admin observer. While this display isn't necessarily for that purpose, the number should be familiar enough to have meaning. The right column is only there to <b>give a sense of how recent the data is</b> now. It is <b><font color='#8080ff'>fully blue</font></b> for today's log and it gets fully white after 180 days. The bluer the row, the more recent it is.</font><p>The 4th column's 'i Y j M k D' display may be off by a few days at any given point, so it isn't 'absolutely precise' in its conversion from days to this display. The number of days in brackets is correct, the other display is there just to give you a better sense of how long ago it was, but since months aren't always the same, it can cause it to be few days off.<p>The extra columns mean: S = Sibyl; B = Basil; O = Other.</td></tr></table><p>";
			print "</div>";
			print "<table width='600' align='center' cellspacing='0' cellpadding='5'>";
			print "<tr bgcolor='#80ff80'><td width='150' align='center'><b>Day</b></td><td align='center' colspan='4'><b>Connections</b></td><td align='center' width='125'><font size='2'><b>Day difference<br>(from previous)</b></font></td><td align='center' width='200'><font size='2'><b>Day difference<br>(from today)</b></font></td></tr>";
			
			$connections = Array();
			while($ia = mysql_fetch_array($da)){
				$day = $ia["day"];
				$num = $ia["connections"];
			
				$connections[$day] = $num;
				$connections_server[$day] = array();	//Prepare for later
			}
			
			//Sibyl
			$da = mysql_query("SELECT date(datetime) as day, count(*) as connections FROM ss13connection_log WHERE ckey = '$ckey' AND serverip LIKE '%1337' GROUP BY date(datetime)") or die(mysql_error());
			while($ia = mysql_fetch_array($da)){
				$day = $ia["day"];
				$num = $ia["connections"];
				$connections_server[$day]["SIBYL"] = $num;
			}
			//Basil
			$da = mysql_query("SELECT date(datetime) as day, count(*) as connections FROM ss13connection_log WHERE ckey = '$ckey' AND serverip LIKE '%2337' GROUP BY date(datetime)") or die(mysql_error());
			while($ia = mysql_fetch_array($da)){
				$day = $ia["day"];
				$num = $ia["connections"];
				$connections_server[$day]["BASIL"] = $num;
			}
			//Other
			$da = mysql_query("SELECT date(datetime) as day, count(*) as connections FROM ss13connection_log WHERE ckey = '$ckey' AND serverip NOT LIKE '%1337' AND  serverip NOT LIKE '%2337' GROUP BY date(datetime)") or die(mysql_error());
			while($ia = mysql_fetch_array($da)){
				$day = $ia["day"];
				$num = $ia["connections"];
				$connections_server[$day]["OTHER"] = $num;
			}
			
			$daymax = max($connections);
			$dayarchive;
			$now = time();
			
			$nowyear = intval(date("y", $now));
			$nowmonth = intval(date("m", $now));
			$nowday = intval(date("d", $now));
			
			$daydiff_array = Array();
			foreach($connections as $day => $num){
				//datediff
				$datecurrent = strtotime($day);
				$daydiff = "-";
				if($dayarchive){
					$datediff = $datecurrent - $dayarchive;
					$daydiff = round($datediff/(60*60*24));
				}
				$nowdatediff = 0;
				$nowdaydiff = 0;
				$nowdaydiffyear = 0;
				$nowdaydiffmonth = 0;
				$nowdaydiffday = 0;
				
				$nowdatediff = $now - $datecurrent;
				$nowdaydiff = floor($nowdatediff/(60*60*24));
				
				$dayyear = intval(date("y", $datecurrent));
				$daymonth = intval(date("m", $datecurrent));
				$dayday = intval(date("d", $datecurrent));
				
				$nowdaydiffyear = $nowyear - $dayyear;
				$nowdaydiffmonth = $nowmonth - $daymonth;
				$nowdaydiffday = $nowday - $dayday;
				
				if($nowdaydiffday < 0){
					$nowdaydiffmonth -= 1;
					switch($nowdaydiffmonth +1){
						case 1:
						case 3:
						case 5:
						case 7:
						case 8:
						case 10:
						case 12:
							$nowdaydiffday += 31;
							break;
						case 13:
							$nowdaydiffday += 31;
							$nowdaydiffyear -= 1;
						break;
						case 4:
						case 6:
						case 9:
						case 11:
							$nowdaydiffday += 30;
							break;
						case 2:
							if($nowdaydiffyear % 4 == 0){
								$nowdaydiffday += 29;
							}else{
								$nowdaydiffday += 28;
							}
							break;
					}
				}
				
				$nowdaydiff_s = "";
				$year_gt1 = 0;	//year greater than 1
				$month_gt1 = 0;	//year or month greater than 1
				if($nowdaydiffyear > 0){
					$nowdaydiff_s .= "$nowdaydiffyear Y ";
					$year_gt1 = 1;
				}
				if($nowdaydiffmonth > 0 || $year_gt1 == 1){
					$nowdaydiff_s .= "$nowdaydiffmonth M ";
					$month_gt1 = 1;
				}
				if($nowdaydiffday > 0 || $month_gt1 == 1){	//if year_gt1 is set, it will set month_gt1 to 1 too, even if month = 0
					$nowdaydiff_s .= "$nowdaydiffday D ";
				}
				
				$daydiff_array[$day] = Array();
				$daydiff_array[$day]["prev"] = $daydiff;
				$daydiff_array[$day]["now"] = $nowdaydiff_s;
				$daydiff_array[$day]["nowdays"] = $nowdaydiff;
				
				$dayarchive = $datecurrent;
			}
			
			print "<tr>
					<td align='center' colspan='5' bgcolor='#80ff80'><b><font size='1'>Fully green = $daymax connections</font></b></td>
					<td align='center' colspan='1' rowspan='2' bgcolor='#ffffff'><b><font size='1' color='#ff0000'>Fully red >= +90 days</font></b></td>
					<td align='center' colspan='1' rowspan='2' bgcolor='#8080ff'><b><font size='1' color='#ffffff'>Fully white >= 180 days ago</font></b></td>
					</tr>
					<tr>
					<td bgcolor='#80ff80'>&nbsp;</td>
					<td align='center' colspan='1' bgcolor='#80ff80' width='50'><b><font size='1'>Total</font></b></td>
					<td align='center' colspan='1' bgcolor='#80ff80'><b><font size='1'>S</font></b></td>
					<td align='center' colspan='1' bgcolor='#80ff80'><b><font size='1'>B</font></b></td>
					<td align='center' colspan='1' bgcolor='#80ff80'><b><font size='1'>O</font></b></td>
				</tr>";
			foreach($connections as $day => $num){
			
				$daydiff_prev = $daydiff_array[$day]["prev"];
				$daydiff_now = $daydiff_array[$day]["now"];
				$daydiff_nowdays = $daydiff_array[$day]["nowdays"];
			
				$sibyl_num = "";
				$basil_num = "";
				$other_num = "";
				if(isset($connections_server[$day]["SIBYL"])){
					$sibyl_num = $connections_server[$day]["SIBYL"];
				}
				if(isset($connections_server[$day]["BASIL"])){
					$basil_num = $connections_server[$day]["BASIL"];
				}
				if(isset($connections_server[$day]["OTHER"])){
					$other_num = $connections_server[$day]["OTHER"];
				}
			
				//color logins
				if($daymax == 0){
					$color_i = 0;
				}else{
					$color_i = 0xff - round(0x80 * ( ($num*1.0) / $daymax ));
				}
				$hexcolor_s = dechex($color_i);
				if($color_i < 0x10){
					$hexcolor_s = "0" . $hexcolor_s;
				}
				$color = "#".$hexcolor_s."ff".$hexcolor_s;
				
				//color datediff
				$color_i = 0xff - min(0xff, round(0xff * ( (intval($daydiff_prev)*1.0) / 90 )));
				
				$hexcolor_s = dechex($color_i);
				if($color_i < 0x10){
					$hexcolor_s = "0" . $hexcolor_s;
				}
				$color_datediff = "#ff".$hexcolor_s.$hexcolor_s;
				
				//color datediffnow
				$color_i = min(0xff, 0x80 + round(0x80 * ( (intval($daydiff_nowdays)*1.0) / 180 )));
				
				$hexcolor_s = dechex($color_i);
				if($color_i < 0x10){
					$hexcolor_s = "0" . $hexcolor_s;
				}
				$color_datediffnow = "#".$hexcolor_s.$hexcolor_s."ff";
				
				
				
				print "<tr bgcolor='$color'><td width='150' align='center'><b>$day</b></td><td align='center' width='100'><b>$num</b></td><td align='center' width='25'><font size='2'>$sibyl_num</font></td><td align='center' width='25'><font size='2'>$basil_num</font></td><td align='center' width='25'><font size='2'>$other_num</font></td><td align='center' width='125' bgcolor='$color_datediff'>". ($daydiff_prev == "-" ? "-" : "+".$daydiff_prev) ."</td><td align='center' width='200' bgcolor='$color_datediffnow'><font size='2'>$daydiff_now ($daydiff_nowdays days)</font></td></tr>";
			}
			
			print "</table>";
		}
		//end connection log normal lookup
		
	}
	
	//connection log detailed lookup
	if(isset($_GET["cnaction"]) && $_GET["cnaction"] == "Detailed lookup" && (isset($_GET["connectionlookup"]) || isset($_GET["connectionlookupip"]) || isset($_GET["connectionlookupcid"]))){
		
		$conditional = "1";
		$lookup_desc = "";
		if(isset($_GET["connectionlookup"]) && $_GET["connectionlookup"] != ""){
			$conditional .= " and ckey = '".$_GET["connectionlookup"]."'";
			$lookup_desc .= "ckey: ".$_GET["connectionlookup"]."; ";
		}
		if(isset($_GET["connectionlookupip"]) && $_GET["connectionlookupip"] != ""){
			$conditional .= " and ip = '".$_GET["connectionlookupip"]."'";
			$lookup_desc .= "IP: ".$_GET["connectionlookupip"]."; ";
		}
		if(isset($_GET["connectionlookupcid"]) && $_GET["connectionlookupcid"] != ""){
			$conditional .= " and computerid = '".$_GET["connectionlookupcid"]."'";
			$lookup_desc .= "CID: ".$_GET["connectionlookupcid"]."; ";
		}
		
		print "<div align='center'>";
		print "<h1>Detailed connection log lookup for $lookup_desc</h1>";
		print "<table align='center' width='600'><tr><td><font size='2'>This table shows the connection log history for the selected players. Connections are grouped by distinct combinations of date, ckey, ip, computer id and server ip.<p>Conditions are applied from left to right and the number in brackets represents the number of connections that corresponds to that condition and all conditions to the left. This is also signified by some rows being taller than others, splitting into multiple 'sub-rows' only after certain conditions are applied.</font></td></tr></table><p>";
		print "</div>";
		
		if ($conditional == "1"){
			print "<div align='center'><font color='red'>Not enough parameters for detailed connection log lookup</font></div>";
		}else{
			$ckey = $_GET["connectionlookup"];
			$da = mysql_query("
			select date(datetime) as day, ckey, ip, 
			computerid, serverip, count(id) as connections 
			from ss13connection_log 
			where $conditional
			group by date(datetime), ckey, ip, computerid, serverip
			order by date(datetime), ckey, ip, computerid, serverip			
			") or die(mysql_error());
			
			$data = array();
			
			$ckeys = array();
			$ips = array();
			$cids = array();
			
			while($ia = mysql_fetch_array($da)){
				$t_day = $ia["day"];
				$t_ckey = $ia["ckey"];
				$t_ip = $ia["ip"];
				$t_computerid = $ia["computerid"];
				$t_serverip = $ia["serverip"];
				$t_connections = $ia["connections"];
				
				//Log unique ckeys
				if(!isset($ckeys[$t_ckey])){
					$ckeys[$t_ckey] = array();
					$ckeys[$t_ckey]["connections"] = $t_connections;
					$ckeys[$t_ckey]["entries"] = 1;
				}else{
					$ckeys[$t_ckey]["connections"] += $t_connections;
					$ckeys[$t_ckey]["entries"]++;
				}
				
				//Log unique ips
				if(!isset($ips[$t_ip])){
					$ips[$t_ip] = array();
					$ips[$t_ip]["connections"] = $t_connections;
					$ips[$t_ip]["entries"] = 1;
				}else{
					$ips[$t_ip]["connections"] += $t_connections;
					$ips[$t_ip]["entries"]++;
				}
				
				//Log unique cids
				if(!isset($cids[$t_computerid])){
					$cids[$t_computerid] = array();
					$cids[$t_computerid]["connections"] = $t_connections;
					$cids[$t_computerid]["entries"] = 1;
				}else{
					$cids[$t_computerid]["connections"] += $t_connections;
					$cids[$t_computerid]["entries"]++;
				}
				
				//Build array tree
				if(!isset($data[$t_day])){
					$data[$t_day] = array();
					$data[$t_day]["rownum"] = 1;
					$data[$t_day]["conn"] = $t_connections;
					$data[$t_day]["rows"] = array();
				}else{
					$data[$t_day]["rownum"]++;
					$data[$t_day]["conn"] += $t_connections;
				}
				
				if(!isset($data[$t_day]["rows"][$t_ckey])){
					$data[$t_day]["rows"][$t_ckey] = array();
					$data[$t_day]["rows"][$t_ckey]["rownum"] = 1;
					$data[$t_day]["rows"][$t_ckey]["conn"] = $t_connections;
					$data[$t_day]["rows"][$t_ckey]["rows"] = array();
				}else{
					$data[$t_day]["rows"][$t_ckey]["rownum"]++;
					$data[$t_day]["rows"][$t_ckey]["conn"] += $t_connections;
				}
				
				if(!isset($data[$t_day]["rows"][$t_ckey]["rows"][$t_ip])){
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip] = array();
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rownum"] = 1;
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["conn"] = $t_connections;
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"] = array();
				}else{
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rownum"]++;
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["conn"] += $t_connections;
				}
				
				if(!isset($data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"][$t_computerid])){
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"][$t_computerid] = array();
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"][$t_computerid]["rownum"] = 1;
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"][$t_computerid]["conn"] = $t_connections;
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"][$t_computerid]["rows"] = array();
				}else{
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"][$t_computerid]["rownum"]++;
					$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"][$t_computerid]["conn"] += $t_connections;
				}
				$data[$t_day]["rows"][$t_ckey]["rows"][$t_ip]["rows"][$t_computerid]["rows"][$t_serverip] = $t_connections;
				$rows[] = "<tr><td>$t_day</td><td>$t_ip</td><td>$t_computerid</td><td>$t_serverip</td><td>$t_connections</td></tr>";
			}
			
			
			//Print ckey list
			$color1 = "#ccffff";
			$color2 = "#ddffff";
			$color = $color1;
			
			print "<div align='center'>";
			print "<b>List of returned ckeys:</b>";
			print "<table cellspacing='0' cellpadding='5' width='400' align='center'>";
			print "<tr bgcolor='#99ffff' align='center'><td><b>Ckey</b></td><td><b>List entries</b></td><td><b>Total connections</b></td></tr>";
			foreach($ckeys as $ckey => $ckeyrow){
				if($color == $color1){$color = $color2;}else{$color = $color1;}
				print "<tr bgcolor='$color' align='center'><td><b>$ckey</b></td><td>".$ckeyrow["entries"]."</td><td>".$ckeyrow["connections"]."</td></tr>";
			}
			print "</table><p>";
			print "</div>";
			//END Print ckey list
			
			//Print ip list
			$color1 = "#ddffdd";
			$color2 = "#eeffee";
			$color = $color1;
			
			print "<div align='center'>";
			print "<b>List of returned IPs:</b>";
			print "<table cellspacing='0' cellpadding='5' width='400' align='center'>";
			print "<tr bgcolor='#99ff99' align='center'><td><b>IP</b></td><td><b>List entries</b></td><td><b>Total connections</b></td></tr>";
			foreach($ips as $ip => $iprow){
				if($color == $color1){$color = $color2;}else{$color = $color1;}
				print "<tr bgcolor='$color' align='center'><td><b>$ip</b></td><td>".$iprow["entries"]."</td><td>".$iprow["connections"]."</td></tr>";
			}
			print "</table><p>";
			print "</div>";
			//END ip ckey list
			
			//Print cid list
			$color1 = "#ddddff";
			$color2 = "#eeeeff";
			$color = $color1;
			
			print "<div align='center'>";
			print "<b>List of returned CIDs:</b>";
			print "<table cellspacing='0' cellpadding='5' width='400' align='center'>";
			print "<tr bgcolor='#9999ff' align='center'><td><b>CID</b></td><td><b>List entries</b></td><td><b>Total connections</b></td></tr>";
			foreach($cids as $cid => $cidrow){
				if($color == $color1){$color = $color2;}else{$color = $color1;}
				print "<tr bgcolor='$color' align='center'><td><b>$cid</b></td><td>".$cidrow["entries"]."</td><td>".$cidrow["connections"]."</td></tr>";
			}
			print "</table><p>";
			print "</div>";
			//END cid ckey list
			
			print "<div align='center'>";
			print "<table cellspacing='0' cellpadding='5' border='1' width='900'>";
			print "<tr align='center'> <td><b>Date</b></td> <td><b>Ckey</b></td> <td><b>IP</b></td> <td><b>Computer id</b></td> <td><b>Server ip</b></td> <td><b>Connections</b></td> </tr>";
			foreach($data as $day => $dayrow){
				print "<tr>";
				$dayarray = $dayrow["rows"];
				$dayrownum = $dayrow["rownum"];
				$dayconn = $dayrow["conn"];
				print "<td rowspan='$dayrownum' bgcolor='#ffaaaa' align='center'>";
				print $day." (".$dayconn.")";
				print "</td>";
				foreach($dayarray as $ckey => $ckeyrow){
					$ckeyarray = $ckeyrow["rows"];
					$ckeyrownum = $ckeyrow["rownum"];
					$ckeyconn = $ckeyrow["conn"];
					print "<td rowspan='$ckeyrownum' bgcolor='#99ffff' align='center'>";
					print $ckey." (".$ckeyconn.")";
					print "</td>";
					foreach($ckeyarray as $ip => $iprow){
						$iparray = $iprow["rows"];
						$iprownum = $iprow["rownum"];
						$ipconn = $iprow["conn"];
						print "<td rowspan='$iprownum' bgcolor='#aaffaa' align='center'>";
						print $ip." (".$ipconn.")";;
						print "</td>";	
						foreach($iparray as $cid => $cidrow){
							$cidarray = $cidrow["rows"];
							$cidrownum = $cidrow["rownum"];
							$cidconn = $cidrow["conn"];
							print "<td rowspan='$cidrownum' bgcolor='#aaaaff' align='center'>";
							print $cid." (".$cidconn.")";;
							print "</td>";		
							foreach($cidarray as $sip => $connections){
								print "<td bgcolor='#ffff99' align='center'>";
								print $sip;
								print "</td>";
								print "<td bgcolor='#ff99ff' align='center'>";
								print $connections;
								print "</td>";
								print "</tr>";
								print "<tr>";
							}
						}
					}
				}
			}
			print "</table>";
			print "</div>";
		}
	}
	//end connection log detailed lookup

	
	
	//Connection log lookup 3 (ip and cid)
	if(isset($_GET["connectionlookupip"])){
		
		if(isset($_GET["action"]) && $_GET["action"] == "Detailed lookup"){
			$ckey = $_GET["connectionlookup"];
			$da = mysql_query("
			select ckey, date(datetime) as day, ip, 
			computerid, serverip, count(id) as connections 
			from ss13connection_log 
			where ckey = '$ckey' 
			group by ckey, date(datetime), ip, computerid, serverip
			order by ckey, date(datetime), ip, computerid, serverip			
			") or die(mysql_error());
			
			$data = array();
			
			while($ia = mysql_fetch_array($da)){
				$t_day = $ia["day"];
				$t_ip = $ia["ip"];
				$t_computerid = $ia["computerid"];
				$t_serverip = $ia["serverip"];
				$t_connections = $ia["connections"];
				
			}
		}
		
	}
	
	//end Connection log lookup 3 (ip and cid)
	
	
	//Notes
	if(isset($_GET["lookupnotes"])){
		$ckey = $_GET["lookupnotes"];
		print "<table width='1000' align='center' bgcolor='#a0a0ff' cellspacing='0' cellpadding='5'><tr><td colspan='2' align='center'><b>Player notes</b></td></tr>";
		$dn = mysql_query("SELECT *, DATEDIFF(Now(), datetime) AS note_age FROM ss13player_notes WHERE ckey = '$ckey' ORDER BY datetime DESC") or die(mysql_error());
		while($in = mysql_fetch_array($dn)){
			$serverip = $in["serverip"];
			$datetime = $in["datetime"];
			$note_age = $in["note_age"];
			$ackey = $in["adminckey"];
			$note = $in["note"];
			
			
			if(endsWith($i["serverip"], "198.27.66.166:1337")){
				$serverip = "Badger";
			}else if(endsWith($i["serverip"], ":1337")){
				if($bantime > "2013-05-15"){
					$serverip = "Sibyl";
				}else{
					$serverip = "Sibyl #1";
				}
			}else if (endsWith($i["serverip"], ":2337")){
				if($bantime > "2013-05-15"){
					$serverip = "Phillip";
				}else{
					$serverip = "Sibyl #2";
				}
			}else if (endsWith($i["serverip"], ":616")){
				$serverip = "Sigyn";
			}
			
			print "<tr>";
			print "<td colspan='2' align='center' bgcolor='#d0d0ff'>";
			print "<b>Note was saved on $serverip on $datetime</b>";
			print "</td>";
			print "</tr>";
			
			print "<tr bgcolor='#e0e0ff'>";
			print "<td width='250' align='center'>";
			print "<b>Posted by $ackey</b><br>";
			print "<b><font size='5'>$note_age</font></b><br>";
			print "days ago";
			print "</td>";
			print "<td width='750' valign='top' align='center'>";
			print "The note contians: <cite>\"$note\"</cite>";
			print "</td>";
			print "</tr>";
		}
		print "</table>";
	}
	
	$bancolor = "#ffebeb";
	$unbancolor = "#f2ffeb";
	$expiredcolor = "#e8e8e8";
	$color = $bancolor;
	
	$ckey_filter = "";
	$a_ckey_filter = "";
	$bantype_filter = "";
	$unbanned_filter = "";
	if(isset($_GET["ckey"])){
		$ckey_filter = "AND ckey LIKE '%".$_GET["ckey"]."%'";
	}
	if(isset($_GET["a_ckey"])){
		$a_ckey_filter = "AND a_ckey LIKE '%".$_GET["a_ckey"]."%'";
	}
	if(isset($_GET["bantype"])){
		if($_GET["bantype"] == "ANY"){
			//Do nothing
		}else if($_GET["bantype"] == "ANY_JOBBAN"){
			$bantype_filter = "AND bantype LIKE 'JOB_%'";
		}else if($_GET["bantype"] == "ANY_FULLBAN"){
			$bantype_filter = "AND (bantype='PERMABAN' OR bantype='TEMPBAN')";
		}else if($_GET["bantype"] == "ANY_ADMINBAN"){
			$bantype_filter = "AND bantype LIKE 'ADMIN_%'";
		}else{
			$bantype_filter = "AND bantype = '".$_GET["bantype"]."'";
		}
	}
	if(isset($_GET["unbanned"])){
		if($_GET["unbanned"] == "1"){
			$unbanned_filter = "AND (unbanned >= 1)";
		}else if($_GET["unbanned"] == "0"){
			$unbanned_filter = "AND isnull(unbanned)";
		}
	}
	
	
	$page = 0;
	if(isset($_GET["page"])){
		$page = intval($_GET["page"]);
	}
	if($page < 0){
		$page = 0;
	}
	
	print "<div align='center'>Logged in as $user</div>";
	
	$d = mysql_query("SELECT * FROM ss13ban WHERE 1 $ckey_filter $a_ckey_filter $bantype_filter $unbanned_filter ORDER BY bantime DESC LIMIT ". $page*100 .", 100") or die(mysql_error());
	
	$linkmodifiers = "";
	if(isset($_GET["ckey"])){
		$linkmodifiers .= "ckey=".$_GET["ckey"]."&";
	}
	if(isset($_GET["a_ckey"])){
		$linkmodifiers .= "a_ckey=".$_GET["a_ckey"]."&";
	}
	if(isset($_GET["bantype"])){
		$linkmodifiers .= "bantype=".$_GET["bantype"]."&";
	}
	if(isset($_GET["unbanned"])){
		$linkmodifiers .= "unbanned=".$_GET["unbanned"]."&";
	}
	
	print "<div align='center'>";
	if($page > 0){
		print "<a href='?$linkmodifiers"."page=". ($page-1) ."'>". (($page-1)*100) ." - ". ((($page-1)*100) +99) ."</a> - ". (($page)*100) ." - ". ((($page)*100) +99) ." - <a href='?$linkmodifiers"."page=". ($page+1) ."'>". (($page+1)*100) ." - ". ((($page+1)*100) +99) ."</a>";
	}else{
		print "". ($page*100) ." - ". (($page*100) +99) ." - <a href='?$linkmodifiers"."page=". ($page+1) ."'>". (($page+1)*100) ." - ". ((($page+1)*100) +99) ."</a>";
	}
	print "</div>";
	
	print "<table width='1100' bgcolor='#000000' cellpadding='5' cellspacing='2' align='center'>";
	
	print "<tr><td bgcolor='#ffffff' colspan='12'><form method='get'>Search: Banned ckey: <input type='text' name='ckey'> Admin ckey: <input type='text' name='a_ckey'>";
	
	print " <b>Ban type:<b> <select name='bantype'>
			<option value='ANY'>Any</option>
			<option value='ANY_FULLBAN'>Any fullban</option>
			<option value='ANY_JOBBAN'>Any jobban</option>
			<option value='ANY_ADMINBAN'>Any admin ban</option>
			<option value='PERMABAN'>Permaban</option>
			<option value='TEMPBAN'>Tempban</option>
			<option value='JOB_PERMABAN'>Job permaban</option>
			<option value='JOB_TEMPBAN'>Job tempban</option>
			<option value='ADMIN_PERMABAN'>Admin permaban</option>
			<option value='ADMIN_TEMPBAN'>Admin tempban</option>
			<option value='APPEARANCE_PERMABAN'>Appearance bermaban</option>
	</select>";
	
	print " <b>Unbanned:<b> <select name='unbanned'>
			<option value='ANY'>Any</option>
			<option value='1'>Unbanned</option>
			<option value='0'>Not Unbanned</option>
	</select>";
	
	print " <input type='submit' value='Search'>";
	
	print "</form></td></tr>";
	
	while($i = mysql_fetch_array($d)){
	
		$permaban = 0;
		$expired = 0;
		$jobban = 0;
		$color = $bancolor;
		$expires = $i["expiration_time"];
		$job = $i["job"];
		$bantime = $i["bantime"];
		$edits = $i["edits"];
		
		$daysago = round(abs((strtotime($i["bantime"]) - strtotime(date("Y-m-d"))) / (60 * 60 * 24)));
		$ubdaysago = null;
		if($i["unbanned_datetime"] != ""){
			$ubdaysago = round(abs((strtotime($i["unbanned_datetime"]) - strtotime(date("Y-m-d"))) / (60 * 60 * 24)));
		}
		
		if(startsWith($i["bantype"],"JOB")){
			$jobban = 1;
		}
		if(!endsWith($i["bantype"],"PERMABAN")){
			if(strtotime("now") > strtotime($expires)){
				$color = $expiredcolor;
				$expired = 1;
			}
		}else{
			$permaban = 1;
		}
		if($i["unbanned"] == 1){
			$color = $unbancolor;
		}
		
		$serverip = $i["serverip"];
		if(endsWith($i["serverip"], "198.27.66.166:1337")){
			$serverip = "Badger";
		}else if(endsWith($i["serverip"], ":1337")){
			if($bantime > "2013-05-15"){
				$serverip = "Sibyl";
			}else{
				$serverip = "Sibyl #1";
			}
		}else if (endsWith($i["serverip"], ":2337")){
			if($bantime > "2013-08-30"){
				$serverip = "Basil";
			}else if($bantime > "2013-05-15"){
				$serverip = "Phillip";
			}else{
				$serverip = "Sibyl #2";
			}
		}else if (endsWith($i["serverip"], ":616")){
			$serverip = "Sigyn";
		}
	
		print "<tr bgcolor='$color'>";
		print "<td align='center' width='50'>";
		if(file_exists("jobs/$job.png")){
			print "<img src='jobs/$job.png'><br>";
		}else if(file_exists("jobs/$job.gif")){
			print "<img src='jobs/$job.gif'><br>";
		}
		print "</td>";
		
		print "<td align='center' width='250'>";
		print "<b>".$i["bantype"]."</b><br>";
		if($jobban){
			print "<font size='2'><b>".$i["job"]."</b></font><br>";
		}
		if(!$permaban){
			print "<font size='2'><b>".$i["duration"]." minutes</b></font><br>";
			print "<font size='2'><b>Expires on ".$i["expiration_time"]."</b></font>";
		}
		print "</td>";
		print "<td align='center'>";
		print "<b>".$i["ckey"]."</b> was banned by <b>".$i["a_ckey"]."</b> on <b>".$serverip."</b> on ".$i["bantime"]."<br>";
		print "<cite>Reason: \"".$i["reason"]."\"</cite>";
		print "</td>";
		print "<td align='center' width='250'>";
		if($i["unbanned"] != null){
			print "Unbanned by <b>".$i["unbanned_ckey"]."</b> on <br><b>".$i["unbanned_datetime"]."</b><br><font size='2'>Ban was active for <b>".($daysago-$ubdaysago)."</b> days.</font>";
		}else if($expired){
			print "<b>Expired</b>";
		}else{
			print "<b>Ban still active</b>";
		}
		print "</td>";
		print "</tr>";
		if($edits != ""){
			$edits = str_replace("<br><BR>","<br>",$edits);
			print "<tr bgcolor='$color'>";
			print "<td colspan='4'><b>Edits to the above ban:</b>$edits</td>";
			print "</tr>";
		}
	}
	
	print "</table>";
}
?>