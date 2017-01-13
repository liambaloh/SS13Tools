<?php
include("db.php");

$daysToCheck = 7;
$timeBetweenUpdates = Array(5, 30, 30);
$allowedTimes = Array(7, 31, 30);
$paramIndex = 0;
//$headminCandidateList = Array("As334","Iamgoofball","KorPhaeron","Shaps","Shadowlight213","Aliannera","Lzimann","TechnoAlchemist","David273","Paprka","Skorvold","Tedward1337","Jud1c470r","Hornygranny","IrishWristWatch0","Thunder12345","Tedward1337","Jacough","Bawhoppennn","Paprka","Paprka",);
$headminCandidateList = Array("Iamgoofball","David273","Aliannera","Jud1c470r","AnonusTanir","Aloraydrel","PKPenguin321","Bawhoppennn","Sligneris","TehSteveo","DemonFiren","KPeculier","Thunder12345","An0n3","Sawrge ","Okand37","Krusvik","Shadowlight213","IrishWristWatch0","Paprka","Dorsidwarf");

if(isset($_GET["time"])){
	$daysToCheck = intval($_GET["time"]);
	$paramIndex = array_search($daysToCheck, $allowedTimes);
	if($paramIndex === FALSE){
		die("Invalid time parameter, allowed values: ".implode(", ", $allowedTimes));
	}
}

$nextupdate = json_decode(file_get_contents("nextupdate.json"), true);
if(strtotime($nextupdate[$daysToCheck]["TIME"]) - Time() >= 0){
	print file_get_contents("cache-$daysToCheck.html");
	die();
}

ob_start();

$headminCandidates = 0;
if( isset($_GET["headmin_elections"]) ){
	die("No longer available. <a href='index.php'>See for admins</a>.");
	$headminCandidates = 1;
	$daysToCheck = 31;
}

function StartsWith($Haystack, $Needle){
    // Recommended version, using strpos
    return strpos($Haystack, $Needle) === 0;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

function getTimeStr($time){

	return "". str_pad( strval( floor($time/60) ), 2, "0", STR_PAD_LEFT) .":". str_pad( strval( $time%60 ) , 2, "0", STR_PAD_LEFT);
}

?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">		  
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="Matev� Baloh">
	<title>TGS Do Admins Play?</title>
	<script src='js/jquery.js'></script>
</head>
<body>
	
	
	<h1 align='center'>/tg/ Station 13: Do admins actually play the game?</h1>
	
	<div align='center'><p>This tool looks through the player list, selects those which were admins whenever they last logged in, then looks through their recent connections to determine how active they've recently been. Activity data (play time and server preference) is only available for the last <? print $daysToCheck; ?> days and is generous: The way play time is calculated is by assuming that an admin who connected to a round played until the round's conclusion (subsequent connections to the same round are ignored). This means that all the numbers presented here are absolute maximums and likely highly overestimate each admin's activity.</p>
	
	<p>Some of these players are no longer admins. They have however not logged in since the time they were de-adminned, hence why they are included. There is currently no good way of determining who is and who isn't an active admin on the server. :(</p></div>

<?php

$printRounds      = false;
$printConnections = false;
$printServerData  = false;
$authRequired	  = false;

$headminCandidateListPrepared = Array();
foreach($headminCandidateList as $i => $candidate){
	$headminCandidateListPrepared[] = "'$candidate'";
}

if($authenticated || !$authRequired){
	$nextupdate[$daysToCheck]["TIME"] = date("j F Y H:i:s", Time() + $timeBetweenUpdates[$paramIndex]);
	file_put_contents("nextupdate.json", json_encode($nextupdate)); 
	
	
	$sql = "SELECT id, ckey, lastseen, lastadminrank, datediff(now(), lastseen) as daysago FROM ss13player WHERE lastadminrank != 'Player' ORDER BY ckey DESC";
	if($headminCandidates){
		$sql = "SELECT id, ckey, lastseen, lastadminrank, datediff(now(), lastseen) as daysago FROM ss13player WHERE ckey in (".(implode(",",$headminCandidateListPrepared)).")";
	}
	
	$d = mysql_query($sql) or die(mysql_error());
	
	$data = Array();
	$rounds = Array();
	$adminCkeys = Array();
	$adminCkeysSQL = Array();
	$connections = Array();
	$serverData = Array();
	$serverIPs = Array();
	
	while($i = mysql_fetch_array($d)){
		$admin = Array();
		$ckey = $i["ckey"];
		$admin["ckey"] = $ckey;
		$admin["lastseen"] = $i["lastseen"];
		$admin["lastadminrank"] = $i["lastadminrank"];
		$admin["daysago"] = $i["daysago"];
		if($i["daysago"] <= 0){
			$admin["lastplayed_desc"] = "today";
		}else if($i["daysago"] <= 1){
			$admin["lastplayed_desc"] = "yesterday";
		}else if($i["daysago"] <= 2){
			$admin["lastplayed_desc"] = "2 days ago";
		}else if($i["daysago"] <= 3){
			$admin["lastplayed_desc"] = "3 days ago";
		}else if($i["daysago"] <= 4){
			$admin["lastplayed_desc"] = "4 days ago";
		}else if($i["daysago"] <= 5){
			$admin["lastplayed_desc"] = "5 days ago";
		}else if($i["daysago"] <= 6){
			$admin["lastplayed_desc"] = "6 days ago";
		}else if($i["daysago"] <= 13){
			$admin["lastplayed_desc"] = "last week";
		}else if($i["daysago"] <= 20){
			$admin["lastplayed_desc"] = "2 weeks ago";
		}else if($i["daysago"] <= 27){
			$admin["lastplayed_desc"] = "3 weeks ago";
		}else if($i["daysago"] <= 59){
			$admin["lastplayed_desc"] = "a month ago";
		}else if($i["daysago"] <= 89){
			$admin["lastplayed_desc"] = "2 months ago";
		}else if($i["daysago"] <= 119){
			$admin["lastplayed_desc"] = "3 months ago";
		}else if($i["daysago"] <= 149){
			$admin["lastplayed_desc"] = "4 months ago";
		}else if($i["daysago"] <= 179){
			$admin["lastplayed_desc"] = "5 months ago";
		}else if($i["daysago"] <= 365){
			$admin["lastplayed_desc"] = "more than half a year ago";
		}else if($i["daysago"] <= 730){
			$admin["lastplayed_desc"] = "more than a year ago";
		}else if($i["daysago"] <= 1095){
			$admin["lastplayed_desc"] = "more than 2 years ago";
		}else if($i["daysago"] <= 1460){
			$admin["lastplayed_desc"] = "more than 3 years ago";
		}else if($i["daysago"] <= 1825){
			$admin["lastplayed_desc"] = "more than 4 years ago";
		}else if($i["daysago"] <= 2190){
			$admin["lastplayed_desc"] = "more than 5 years ago";
		}else{
			$admin["lastplayed_desc"] = "at this point it doesn't matter. 6 years+";
		}
		
		if(array_search($ckey, $adminCkeys) === FALSE){
			$adminCkeys[] = $ckey;
			$adminCkeysSQL[] = "'$ckey'";
		}
		$admin["play_time_n_days"] = 0;
		$admin["played_in_rounds"] = Array();
		
		$data[$ckey] = $admin;
	}
	
	$d = mysql_query("SELECT time, var_name, details FROM ss13feedback WHERE (datediff(Now(), time) < $daysToCheck) AND (var_name = 'round_end' OR var_name = 'server_ip' OR var_name = 'round_start')") or die(mysql_error());
	
	$round = Array();
	while($i = mysql_fetch_array($d)){
		switch($i["var_name"]){
			case "round_start":
				$round = Array();
				$round["round_start"] = $i["details"];
				$round["dt_round_start"] = strtotime($i["details"]);
			break;
			case "server_ip":
				$round["server_ip"] = $i["details"];
				if(array_search($round["server_ip"], $serverIPs) === FALSE){
					$serverIPs[] = $round["server_ip"];
				}
			break;
			case "round_end":
				$round["round_end"] = $i["details"];
				$round["dt_round_end"] = strtotime($i["details"]);
				$round["duration"] = $round["dt_round_end"] - $round["dt_round_start"];
				$rounds[] = $round;
				$serverData[$round["server_ip"]][] = Array("start" => $round["dt_round_start"], "end" => $round["dt_round_end"]);
			break;
		}
	}
	
	$ckeys = join(',',$adminCkeysSQL);
	
	$sql = "SELECT id, datetime, serverip, ckey 
			FROM ss13connection_log 
			WHERE 
				(datediff(Now(), datetime) < $daysToCheck) 
			AND 
				(ckey in ($ckeys))";
	
	$d = mysql_query($sql) or die(mysql_error());
	while($i = mysql_fetch_array($d)){
		$conn = Array();
		$conn["id"] = $i["id"];
		$conn["datetime"] = $i["datetime"];
		$conn["serverip"] = $i["serverip"];
		$conn["ckey"] = $i["ckey"];
		$conn["dt_datetime"] = strtotime($i["datetime"]);
		
		$connections[] = $conn;
	}
	
	if($printServerData){
		print "<table align='center'>";
		print "<tr>";
		print "<th>ID</th>";
		print "<th>start</th>";
		print "<th>end</th>";
		print "<th>duration</th>";
		print "</tr>";
		$round = Array();
		foreach($serverData as $ip => $r){
			print "<tr>";
			print "<td align='center' colspan='4'>$ip</td>";
			print "</tr>";
			foreach($r as $i => $d){
				$start = $d["start"];
				$end = $d["end"];
				print "<tr>";
				print "<td align='center'>$i</td>";
				print "<td align='center'>$start</td>";
				print "<td align='center'>$end</td>";
				print "<td align='center'>".($end - $start)."</td>";
				print "</tr>";
			}
		}
		print "</table>";
	}
	
	$maxPlayTime = 0;
	
	if($printConnections){
		print "<table align='center'>";
		print "<tr>";
		print "<th>datetime</th>";
		print "<th>serverip</th>";
		print "<th>ckey</th>";
		print "<th>dt_datetime</th>";
		print "<th>roundID</th>";
		print "<th>timeOnServer</th>";
		print "</tr>";
	}
	foreach($connections as $i => $c){
		$datetime = $c["datetime"];
		$serverip = $c["serverip"];
		$ckey = $c["ckey"];
		$dt_datetime = $c["dt_datetime"];
		
		$roundID = "unknown";
		$timeOnServer = "unknown";
		
		foreach($serverData[$serverip] as $j => $d){
			if($d["start"] - 300 <= $dt_datetime && $dt_datetime <= $d["end"]){
				$roundID = $j;
				if(!isset($data[$ckey]["played_in_rounds"][$serverip])){
					$data[$ckey]["played_in_rounds"][$serverip] = Array();
				}
				if(array_search($roundID, $data[$ckey]["played_in_rounds"][$serverip]) !== FALSE){
					//print "<br/><font color='red'>$ckey reconnected to round $serverip - $roundID</font>";
					//Reconnecting to an already applied round
					continue;
				}
				$data[$ckey]["played_in_rounds"][$serverip][] = $roundID;
				$timeOnServer = $d["end"] - $dt_datetime;
				$data[$ckey]["play_time_n_days"] += $timeOnServer;
				
				if($maxPlayTime < $data[$ckey]["play_time_n_days"]){
					$maxPlayTime = $data[$ckey]["play_time_n_days"];
				}
				
				if(!isset($data[$ckey]["playOnServer"][$serverip])){
					$data[$ckey]["playOnServer"][$serverip] = 0;
				}
				$data[$ckey]["playOnServer"][$serverip] += $timeOnServer;
				break;
			}
		}
		
		if($printConnections){
			print "<tr>";
			print "<td align='center'>$datetime</td>";
			print "<td align='center'>$serverip</td>";
			print "<td align='center'>$ckey</td>";
			print "<td align='center'>$dt_datetime</td>";
			print "<td align='center'>$roundID</td>";
			print "<td align='center'>$timeOnServer</td>";
			print "</tr>";
		}
	}
	if($printConnections){
		print "</table>";
	}
	
	if($printRounds){
		print "<table align='center'>";
		print "<tr>";
		print "<th>round_start</th>";
		print "<th>server_ip</th>";
		print "<th>round_end</th>";
		print "<th>dt_round_start</th>";
		print "<th>dt_round_end</th>";
		print "<th>duration</th>";
		print "</tr>";
		$round = Array();
		foreach($rounds as $i => $r){
			$round_start = $r["round_start"];
			$server_ip = $r["server_ip"];
			$round_end = $r["round_end"];
			$dt_round_start = $r["dt_round_start"];
			$dt_round_end = $r["dt_round_end"];
			$duration = $r["duration"];
			
			print "<tr>";
			print "<td align='center'>$round_start</td>";
			print "<td align='center'>$server_ip</td>";
			print "<td align='center'>$round_end</td>";
			print "<td align='center'>$dt_round_start</td>";
			print "<td align='center'>$dt_round_end</td>";
			print "<td align='center'>$duration</td>";
			print "</tr>";
		}
		print "</table>";
	}
	
	function cmp($a, $b)
	{
		return $a["daysago"] > $b["daysago"];
	}

	usort($data, "cmp");
	
	print "<table align='center'>";
	print "<tr>";
	print "<th>Ckey</th>";
	print "<th>Admin rank</th>";
	print "<th>Last played</th>";
	print "<th>Last played<br />[days ago]</th>";
	print "<th>Play time in last $daysToCheck days</th>";
	print "<th>Server preference <br/>";
	foreach($serverIPs as $i => $ip){
		print "<img src='bars/p$i.png' height='10' width='10' title='Server $ip'> = $ip; ";
	}
	print "<br/>Hover over any bar below for more information";
	print "</th>";
	
	print "</tr>";
	
	foreach($data as $i => $admin){
		$ckey = $admin["ckey"];
		$lastadminrank = $admin["lastadminrank"];
		$daysago = $admin["daysago"];
		$lastplayed_desc = $admin["lastplayed_desc"];
		$play_time_n_days = $admin["play_time_n_days"];
		
		$hours = floor($play_time_n_days / 3600);
		$mins = floor($play_time_n_days / 60 % 60);
		$secs = floor($play_time_n_days % 60);
		
		$time = "none";
		if($hours > 0){
			$time = $hours."h ".$mins."m";
		}else if($mins > 0){
			$time = $mins."m ".$secs."s";
		}else if($secs > 0){
			$time = $secs."s";
		}
		
		print "<tr>";
		print "<td align='center'>$ckey</td>";
		print "<td align='center'>$lastadminrank</td>";
		print "<td align='center'>$lastplayed_desc</td>";
		print "<td align='center'>$daysago</td>";
		print "<td align='center'>$time</td>";
		print "<td align='left'>";
		foreach($serverIPs as $i => $ip){
			if($maxPlayTime == 0){
				continue;
			}
			
			$srvTime = $admin["playOnServer"][$ip];
			$played_in_rounds = count($admin["played_in_rounds"][$ip]);
			
			$hours = floor($srvTime / 3600);
			$mins = floor($srvTime / 60 % 60);
			$secs = floor($srvTime % 60);
			
			$timeSrv = "none";
			if($hours > 0){
				$timeSrv = $hours."h ".$mins."m";
			}else if($mins > 0){
				$timeSrv = $mins."m ".$secs."s";
			}else if($secs > 0){
				$timeSrv = $secs."s";
			}
			
			$width = round(($srvTime / $maxPlayTime) * 800);
			print "<img src='bars/p$i.png' height='10' width='$width' title='Server $ip, time = $timeSrv, rounds played = $played_in_rounds'>";
		}
		print "</td>";
		print "</tr>";
	}
	
	print "</table>";
	
	$out = ob_get_contents();
	file_put_contents("cache-$daysToCheck.html", $out);
	
}else{
	print "
	
<div align='center'>
	<p><form method='post' align='center'>
		Username: <input type='text' name='un' value='tgstation'>
		<br>Password: <input type='password' name='pw'>
		<br><input type='submit' name='try' value='Log in'>
	</form>
</div>
	";
}

?>