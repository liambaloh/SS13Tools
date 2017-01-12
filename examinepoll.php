<?php
include("db.php");
?>

<?php

function percent2str($percent){
	$s = strval( ($percent*100) );
	$i = strpos( $s, "." );
	if( $i == null){
		return "".$s."%";
	}
	return "". substr( $s, 0, $i)."%";
}

if(isset($_GET["pollid"])){
	$pollid = $_GET["pollid"];
}else{
	die("no poll id provided.");
}

$minplayerage = 0;
$maxplayerage = 720;
$minageatpollend = 0;
$maxageatpollend = 720;
$mindayssincelogin = 0;
$maxdayssincelogin = 720;
$adminvotes = "all";

if(isset($_GET["minplayerage"])){
	$minplayerage = $_GET["minplayerage"];
}
if(isset($_GET["maxplayerage"])){
	$maxplayerage = $_GET["maxplayerage"];
}
if(isset($_GET["minageatpollend"])){
	$minageatpollend = $_GET["minageatpollend"];
}
if(isset($_GET["maxageatpollend"])){
	$maxageatpollend = $_GET["maxageatpollend"];
}
if(isset($_GET["mindayssincelogin"])){
	$mindayssincelogin = $_GET["mindayssincelogin"];
}
if(isset($_GET["maxdayssincelogin"])){
	$maxdayssincelogin = $_GET["maxdayssincelogin"];
}

$pollsperpage = 10;

print "<h1 align='center'>Ingame poll results for /tg/ station 13</h1>";

print "<div align='center'><b>Examine poll results</b></div>";
print "<table align='center' width='800'><tr><td width='50%'>";
print "<form method='GET'>";
print "<input type='hidden' name='pollid' value='$pollid'>";
print "<p><div align='center'><b>Search criteria</b>";

print "<br><b>Voter age at poll end (days since first login)</b>";
print "<br><b>Min: </b><input type='text' name='minageatpollend' value=".$minageatpollend."> ";
print "<b>Max: </b><input type='text' name='maxageatpollend' value=".$maxageatpollend.">";

print "<br><p><b>Voter age today (days since first login)</b>";
print "<br><b>Min: </b><input type='text' name='minplayerage' value=".$minplayerage."> ";
print "<b>Max: </b><input type='text' name='maxplayerage' value=".$maxplayerage.">";

print "<br><p><b>Days since voter's last login</b>";
print "<br><b>Min: </b><input type='text' name='mindayssincelogin' value=".$mindayssincelogin."> ";
print "<b>Max: </b><input type='text' name='maxdayssincelogin' value=".$maxdayssincelogin.">";

print "<br><b>Admin votes:</b>";
print "<select name='adminvotes'>
   <option value='all'>Admin and player votes</option>
   <option value='adminonly'>Only admin votes</option>
   <option value='playeronly'>Only player votes</option>
 </select>";

print "<br><input type='submit' value='apply filter'>";

print "</form>";

print "</td><td align='center' width='50%'>";

print "<font size='2'><b>Examples:</b>";
print "<br><b>Player age at poll end:</b> Set the minimum to 30, to only display people, who have played for at least a month (ignores newbies); OR set the maximum to 30 to only see what those, who were newbies at the time of voting.";
print "<br><b>Player age today:</b> Not sure what this could be used for, but it's here just in case you need it.";
print "<br><b>Days since voter's last login:</b> Set the minimum to 14 to remove everyone, who has not logged into the game in the last 14 days. This is to help determine what the still-active players think. Consider re-votes if the number of still-active players is low.";
print "<br><b>Admin votes:</b> Usage self evident.";
print "</font>";

print "</td></tr></table>";

print "</div>";


$d = mysql_query("SELECT *, DATEDIFF(Now(), endtime) AS endeddaysago FROM erro_poll_question WHERE id = $pollid") or die(mysql_error());
		
if($i = mysql_fetch_array($d)){
	$polltype = $i["polltype"];
	$question = $i["question"];
	$adminonly = $i["adminonly"];
	$starttime = $i["starttime"];
	$endtime = $i["endtime"];
	$endeddaysago = $i["endeddaysago"];
	$multiplechoiceoptions = $i["multiplechoiceoptions"];
	
	print "<table width='900' align='center' bgcolor='#eeffee' cellspacing='0' cellpadding='4'>";
	print "<tr bgcolor='#ddffdd'>";
	print "<th colspan='4' align='center'>";
	if($adminonly){
		print "<b>(<font color='#997700'>Admin only poll</font>)</b> ";
	}
	print $question;
	print "<font size='1'><br><b>$starttime - $endtime";
	print "</b></font>";
	print "</th>";
	print "</tr>";
	
	$d2 = mysql_query("SELECT v.ckey, v.optionid, v.datetime, DATEDIFF(v.datetime, '$starttime') as dayvote, v.adminrank, v.rating, p.firstseen, p.lastseen, DATEDIFF(Now(), p.firstseen) AS firstseendaysago, DATEDIFF(Now(), p.lastseen) AS lastseendaysago, DATEDIFF('$endtime', p.firstseen) AS ageatpollend FROM erro_poll_vote v, erro_player p WHERE pollid = $pollid AND p.ckey = v.ckey") or die(mysql_error());
	
	$pollstats = array();
	$allckeys = array();
	
	while($i2 = mysql_fetch_array($d2)){
		
		$ckey = $i2["ckey"];
		$optionid = $i2["optionid"];
		$datetime = $i2["datetime"];
		$adminrank = $i2["adminrank"];
		$rating = intval($i2["rating"]);
		$firstseen = $i2["firstseen"];
		$lastseen = $i2["lastseen"];
		$firstseendaysago = intval($i2["firstseendaysago"]);
		$lastseendaysago = intval($i2["lastseendaysago"]);
		$ageatpollend = intval($i2["ageatpollend"]);
		
		$allckeys[$ckey] = $optionid;
		
		if(!isset($pollstats["datetime"][$datetime])){
			$pollstats["datetime"][$datetime] = array();
		}
		$pollstats["datetime"][$datetime][] = $ckey;
		
		if(!isset($pollstats["adminrank"][$adminrank])){
			$pollstats["adminrank"][$adminrank] = array();
		}
		$pollstats["adminrank"][$adminrank][] = $ckey;
		
		if(!isset($pollstats["firstseendaysago"][$firstseendaysago])){
			$pollstats["firstseendaysago"][$firstseendaysago] = array();
		}
		$pollstats["firstseendaysago"][$firstseendaysago][] = $ckey;
		
		if(!isset($pollstats["lastseendaysago"][$lastseendaysago])){
			$pollstats["lastseendaysago"][$lastseendaysago] = array();
		}
		$pollstats["lastseendaysago"][$lastseendaysago][] = $ckey;
		
		if(!isset($pollstats["ageatpollend"][$ageatpollend])){
			$pollstats["ageatpollend"][$ageatpollend] = array();
		}
		$pollstats["ageatpollend"][$ageatpollend][] = $ckey;
	}
	
	$validpollstats = array();
	foreach($allckeys as $ckey => $optionid){
		$validpollstats[$ckey] = $optionid;
	}
	
	if(isset($_GET["minplayerage"])){
		$minage = intval($_GET["minplayerage"]);
		foreach($pollstats["firstseendaysago"] as $age => $ckeylist){
			if($age < $minage){
				foreach($ckeylist as $ckey){
					$validpollstats[$ckey] = -abs($validpollstats[$ckey]);
				}
			}
		}
	}
	
	if(isset($_GET["maxplayerage"])){
		$maxage = intval($_GET["maxplayerage"]);
		foreach($pollstats["firstseendaysago"] as $age => $ckeylist){
			if($age > $maxage){
				foreach($ckeylist as $ckey){
					$validpollstats[$ckey] = -abs($validpollstats[$ckey]);
				}
			}
		}
	}
	
	if(isset($_GET["minageatpollend"])){
		$minage = intval($_GET["minageatpollend"]);
		foreach($pollstats["ageatpollend"] as $age => $ckeylist){
			if($age < $minage){
				foreach($ckeylist as $ckey){
					$validpollstats[$ckey] = -abs($validpollstats[$ckey]);
				}
			}
		}
	}
	
	if(isset($_GET["maxageatpollend"])){
		$maxage = intval($_GET["maxageatpollend"]);
		foreach($pollstats["ageatpollend"] as $age => $ckeylist){
			if($age > $maxage){
				foreach($ckeylist as $ckey){
					$validpollstats[$ckey] = -abs($validpollstats[$ckey]);
				}
			}
		}
	}
	
	if(isset($_GET["mindayssincelogin"])){
		$mininactivity = intval($_GET["mindayssincelogin"]);
		foreach($pollstats["lastseendaysago"] as $inactivity => $ckeylist){
			if($inactivity < $mininactivity){
				foreach($ckeylist as $ckey){
					$validpollstats[$ckey] = -abs($validpollstats[$ckey]);
				}
			}
		}
	}
	
	if(isset($_GET["maxdayssincelogin"])){
		$maxinactivity = intval($_GET["maxdayssincelogin"]);
		foreach($pollstats["lastseendaysago"] as $inactivity => $ckeylist){
			if($inactivity > $maxinactivity){
				foreach($ckeylist as $ckey){
					$validpollstats[$ckey] = -abs($validpollstats[$ckey]);
				}
			}
		}
	}
	
	if(isset($_GET["adminvotes"])){
		$adminvotes = $_GET["adminvotes"];
		if($adminvotes == "adminonly"){
			foreach($pollstats["adminrank"] as $rank => $ckeylist){
				if($rank == "Player" || $rank == ""){
					foreach($ckeylist as $ckey){
						$validpollstats[$ckey] = -abs($validpollstats[$ckey]);
					}
				}
			}
		}else if($adminvotes == "playeronly"){
			foreach($pollstats["adminrank"] as $rank => $ckeylist){
				if($rank != "Player" && $rank != ""){
					foreach($ckeylist as $ckey){
						$validpollstats[$ckey] = -abs($validpollstats[$ckey]);
					}
				}
			}
		}
	}
	
	
	$filteredpollstats = array();
	foreach($validpollstats as $ckey => $optionid){
		if(!isset($filteredpollstats[$optionid])){
			$filteredpollstats[$optionid] = 0;
		}
		$filteredpollstats[$optionid]++;
	}
	
	$maxvotes = 0;
	$allvotes = 0;
	$allvalidvotes = 0;
	foreach($filteredpollstats as $optionid => $votes){
		$thisvotes = 0;
		$allvotes += $votes;
		if($optionid == abs($optionid)){
			$allvalidvotes += $votes;
		}
		if(isset($filteredpollstats[-($optionid)])){
			$thisvotes = $filteredpollstats[-$optionid] + $votes;
		}else{
			$thisvotes = $votes;
		}
		if($thisvotes > $maxvotes){
			$maxvotes = $thisvotes;
		}
	}
	if($endeddaysago > 0){
		print "<tr><td align='center' colspan='4'><b>This poll ended $endeddaysago days ago</b></td></tr>";
	}else{
		print "<tr><td align='center' colspan='4'><b>This poll has not yet ended</b></td></tr>";
	}
	
	print "<tr><td align='center' colspan='4'><b>$allvalidvotes of $allvotes total votes match the search criteria (".percent2str($allvalidvotes/$allvotes)." of all votes)</b></td></tr>";
	
	print "<tr><td align='center'><b>Option</b></td><td align='center'><b>Votes #</b></td><td align='center'><b>Votes %</b></td><td align='center'><b>Bar</b></td></tr>";
	
	$d3 = mysql_query("SELECT * FROM erro_poll_option WHERE pollid = $pollid") or die(mysql_error());
	while($i3 = mysql_fetch_array($d3)){
		$optionid = $i3["id"];
		$text = $i3["text"];
		
		$validvotes = 0;
		if(isset($filteredpollstats[$optionid])){
			$validvotes = $filteredpollstats[$optionid];
		}
		$invalidvotes = 0;
		if(isset($filteredpollstats[-$optionid])){
			$invalidvotes = $filteredpollstats[-$optionid];
		}
		
		$barlength_valid = round(($validvotes / $maxvotes)*400);
		$barlength_invalid = round(($invalidvotes / $maxvotes)*400);
		
		if(isset($filteredpollstats[$optionid]) && isset($filteredpollstats[-$optionid])){
			print "<tr><td><b>$text</b></td><td><b>".$filteredpollstats[$optionid]."</b> <font size='2'>(+".$filteredpollstats[-$optionid].")</font></td>";
		}else if(isset($filteredpollstats[$optionid])){
			print "<tr><td><b>$text</b></td><td><b>".$filteredpollstats[$optionid]."</b></td>";
		}else if(isset($filteredpollstats[-$optionid])){
			print "<tr><td><b>$text</b></td><td><b>0</b> <font size='2'>(+".$filteredpollstats[-$optionid].")</font></td>";
		}else{
			print "<tr><td><b>$text</b></td><td><b>0</b></td>";
		}
		
		print "<td align='center'><font size='2'>";
		print "<b>".percent2str($validvotes / $allvotes)." of all</b>";
		print "<br><b>".percent2str($validvotes / $allvalidvotes)." of valid</b>";
		print"</font></td>";
		
		print "<td><img src='bars/privacy_0.PNG' height='10' width='$barlength_valid'><img src='bars/privacy_1.PNG' height='10' width='$barlength_invalid'></td>";
	}
	
	/*foreach($filteredpollstats as $optionid => $number){
		print "<br>$optionid -> $number votes";
	}*/
	
	print "<tr><td colspan='4' align='center'>";
	print "<b>Filter conditions:</b>";
	if(isset($_GET["minageatpollend"])){
		print "<br>* Min player age at poll end: ".$_GET["minageatpollend"]." days.";
	}
	if(isset($_GET["maxageatpollend"])){
		print "<br>* Max player age at poll end: ".$_GET["maxageatpollend"]." days.";
	}
	if(isset($_GET["minplayerage"])){
		print "<br>* Min player age now: ".$_GET["minplayerage"]." days.";
	}
	if(isset($_GET["maxplayerage"])){
		print "<br>* Max player age now: ".$_GET["maxplayerage"]." days.";
	}
	if(isset($_GET["mindayssincelogin"])){
		print "<br>* Min days since last login: ".$_GET["mindayssincelogin"]." days.";
	}
	if(isset($_GET["maxdayssincelogin"])){
		print "<br>* Max days since last login: ".$_GET["maxdayssincelogin"]." days.";
	}
	if(isset($_GET["adminvotes"])){
		if($_GET["adminvotes"] == "adminonly"){
			print "<br>* Only show votes by admins.";
		}else if($_GET["adminvotes"] == "playeronly"){
			print "<br>* Only show votes by players (leaves out admin votes).";
		}
		
	}
	print "</td></tr>";
	
	print "</table>";
}else{
	print "No poll with this id found";
}
