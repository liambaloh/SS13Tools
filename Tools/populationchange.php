<?php
include("db.php");
?>
<?php

$inactivityMargin = 7;
if(isset($_GET["inactivityThreshold"])){
	$inactivityMargin = intval($_GET["inactivityThreshold"]);
}

$population = Array();


print "<h1 align='center'>Player migration statistics for /tg/ station 13</h1>";

print "<form style='text-align: center;' method='get'>I consider players who have not logged in for <input type='number' min='0' max='10000' name='inactivityThreshold' value='$inactivityMargin'> days as inactive.<br><input type='submit' value='Update'></form>";

$d_minmax = mysql_query("SELECT YEAR(MIN(firstseen)) as minyear, MONTH(MIN(firstseen)) as minmonth, DAY(MIN(firstseen)) as minday, YEAR(MAX(firstseen)) as maxyear, MONTH(MAX(firstseen)) as maxmonth, DAY(MAX(firstseen)) as maxday FROM `ss13playerview` WHERE 1") or die(mysql_error());
if($imm = mysql_fetch_array($d_minmax)){
	$startyear = $imm["minyear"];
	$startmonth = $imm["minmonth"];
	$startday = $imm["minday"];
	$endyear = $imm["maxyear"];
	$endmonth = $imm["maxmonth"];
	$endday = $imm["maxday"];
	$maxinaday = 0;
	
	if(isset($playerstay[$daysplayed])){
		$playerstay[$daysplayed]++;
	}else{
		$playerstay[$daysplayed] = 1;
	}
	
	// Start date
	$date = $startyear.'-'.$startmonth.'-'.$startday;
	// End date
	$end_date = $endyear.'-'.$endmonth.'-'.$endday;
 
	$d_join = mysql_query("
		SELECT a.year, a.month, a.day, a.joincount, b.leavecount 
		FROM 
		( SELECT YEAR(firstseen) as year, MONTH(firstseen) as month, DAY(firstseen) as day, count(id) as joincount FROM `ss13playerview` GROUP BY year, month, day ) a,
		( SELECT YEAR(lastseen) as year, MONTH(lastseen) as month, DAY(lastseen) as day, count(id) as leavecount FROM `ss13playerview` GROUP BY year, month, day ) b
		WHERE a.year = b.year AND a.month = b.month AND a.day = b.day
	") or die(mysql_error());
	while($ij = mysql_fetch_array($d_join)){
		$dday = $ij["day"];
		$dmonth = $ij["month"];
		$dyear = $ij["year"];
		$jcount = $ij["joincount"];
		$lcount = $ij["leavecount"];
		$daystats = Array();
		$daystats["day"] = $dday;
		$daystats["month"] = $dmonth;
		$daystats["year"] = $dyear;
		$daystats["joincount"] = $jcount;
		$daystats["leavecount"] = $lcount;
		$population[] = $daystats;
		
		if(abs($jcount) > $maxinaday){
			$maxinaday = abs($jcount);
		}
		if(abs($lcount) > $maxinaday){
			$maxinaday = abs($lcount);
		}
	}
 
	$datecolor = "#e8e8e8";
	$joincolor = "#f2ffeb";
	$leavecolor = "#ffebeb";
	$joindifcolor = "#f2ffeb";
	$leavedifcolor = "#ffebeb";
	$zerodifcolor = "#e8e8e8";
 
	print "<table width='1100' cellpadding='5' cellspacing='0' align='center'><tr><th width='100' bgcolor='#d6d6d6' rowspan='2'>Date</th><th width='50' bgcolor='#ffd0d0' rowspan='2'>L</th><th width='50' bgcolor='#d0ffd1' rowspan='2'>J</th><th width='75' bgcolor='#d6d6d6' rowspan='2'>Dif</th><th width='400' bgcolor='#ffd0d0'>Players last seen</th><th width='400' bgcolor='#d0ffd1'>Players first seen</th></tr><tr><th width='75' bgcolor='#d6d6d6' colspan='2'>Bold colour is the 'net gain' or loss, pale colours are the total gain and loss.</th></tr>";
	while (strtotime($date) <= strtotime($end_date)) {
		
		$fday = date('d',strtotime($date));
		$fmonth = date('m',strtotime($date));
		$fyear = date('Y',strtotime($date));
		
		
		foreach( $population as $i => $tested ){
			if(!isset($tested["year"]) || !isset($tested["month"]) || !isset($tested["day"])){
				continue;
			}
			if($tested["year"] != $fyear || $tested["month"] != $fmonth || $tested["day"] != $fday){
				continue;
			}
			
			if(!isset($tested["joincount"])){
				$tested["joincount"] = 0;
			}
			if(!isset($tested["leavecount"])){
				$tested["leavecount"] = 0;
			}
			$jcount = $tested["joincount"];
			$lcount = $tested["leavecount"];
			
			$difference = $jcount - $lcount;
			
			$width_red = 0;
			$width_green = 0;
			
			if( $difference > 0 ){
				$width_green = floor( ($difference / $maxinaday)*390 );
			}else{
				$width_red = floor( (abs($difference) / $maxinaday)*390 );
			}
			$width_green_total = floor( ($jcount / $maxinaday)*390 ) - $width_green;
			$width_red_total = floor( ($lcount / $maxinaday)*390 ) - $width_red;
			
			print "<tr><td align='center' bgcolor='$zerodifcolor'>$fyear-$fmonth-$fday</td>";
			print "<td align='center' bgcolor='$leavecolor'><font color='#770000'>$lcount</font></td>";
			print "<td align='center' bgcolor='$joincolor'><font color='#2c8200'>$jcount</font></td>";
			if($difference > 0){
				print "<td align='center' bgcolor='$joindifcolor'><font color='#2c8200'><b>$difference</b></font></td>";
			}else if($difference < 0){
				print "<td align='center' bgcolor='$leavedifcolor'><font color='#770000'><b>$difference</b></font></td>";
			}else{
				print "<td align='center' bgcolor='$zerodifcolor'><font color='black'><b>$difference</b></font></td>";
			}
			print "<td bgcolor='$leavedifcolor' align='right'><img src='bars/leavepale.PNG' height='10' width='$width_red_total'><img src='bars/leave.PNG' height='10' width='$width_red'></td>";
			print "<td bgcolor='$joindifcolor'><img src='bars/join.PNG' height='10' width='$width_green'><img src='bars/joinpale.PNG' height='10' width='$width_green_total'></td></tr>";
			
			//print "<br>ON ".$fyear." - ".$fmonth." - ".$fday.", the following migration stats were gathered: NEW PLAYERS: ".$tested["joincount"]."; LAST SEEN PLAYERS: ".$tested["leavecount"];
			
		}
		
		
		
		$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
	}
	print "</table>";
	
}

/*
print "<h1 align='center'>Average player stay on /tg/ station 13</h1>";

print "<p><div align='center'>Note that the column height for players who have never returned is not to scale with the others to ensure that others can be read more clearly.</div></p>";

$playerstay = Array();
$maxplaytime = 0;
$maxplayersinaday = 0;

$d_playerstay = mysql_query("
							SELECT COUNT( id ) AS playernumber, DATEDIFF( lastseen, firstseen ) AS daysplayed
							FROM `ss13playerview` 
							GROUP BY daysplayed
							ORDER BY daysplayed
						") or die(mysql_error());
$maxplayertime = 0;
while($ips = mysql_fetch_array($d_playerstay)){
	
	$playernumber = $ips["playernumber"];
	$daysplayed = $ips["daysplayed"];

	$playerstay[$daysplayed] = $playernumber;
	if($daysplayed > $maxplayertime){
		$maxplaytime = $daysplayed;
	}
	if($daysplayed != 0){
		if($playernumber > $maxplayersinaday){
			$maxplayersinaday = $playernumber;
		}
	}
}

$playerstaytablebody = "#f2ffeb";
$playerstaytablehead = "#d0ffd1";

print "<table width='1100' cellpadding='5' cellspacing='0' align='center'><tr>";
print "<th bgcolor='$playerstaytablehead'> </th>";
print "<td align='center' valign='bottom' bgcolor='$playerstaytablebody'><img src='bars/zerodays.PNG' width='10' height='300'></td>";
for($i = 1; $i <= $maxplaytime; $i++){
	$num = 0;
	if(isset($playerstay[$i])){
		$num = $playerstay[$i];
	}
	$height = floor(($num / $maxplayersinaday) * 300);
	
	print "<td align='center' valign='bottom' bgcolor='$playerstaytablebody'><img src='bars/join.PNG' width='10' height='$height'></td>";
}
print "</tr><tr>";
print "<th bgcolor='$playerstaytablehead'>Players</th>";
for($i = 0; $i <= $maxplaytime; $i++){

	print "<td align='center' bgcolor='$playerstaytablehead'>";
	if(isset($playerstay[$i])){
		print $playerstay[$i];
	}else{
		print 0;
	}
	print "</td>";
}
print "</tr><tr>";
print "<th bgcolor='$playerstaytablehead'>Days</th>";
for($i = 0; $i <= $maxplaytime; $i++){
	print "<th bgcolor='$playerstaytablehead'>$i</th>";
}
print "</tr>";
print "</table>";
*/

//Avg active player age
print "<h1 align='center'>Active player age on /tg/ station 13</h1>";

print "<p><div align='center'>Active means they last logged in within the last $inactivityMargin days.</div></p>";

$playerstay = Array();
$maxplaytime = 0;
$maxplayersinaday = 0;

$d_playerstay = mysql_query("
							SELECT COUNT( id ) AS playernumber, DATEDIFF( lastseen, firstseen ) AS daysplayed
							FROM `ss13playerview` 
							WHERE DATEDIFF( Now(), lastseen ) <= $inactivityMargin
							GROUP BY daysplayed
							ORDER BY daysplayed
						") or die(mysql_error());
$maxplayertime = 0;
while($ips = mysql_fetch_array($d_playerstay)){
	
	$playernumber = $ips["playernumber"];
	$daysplayed = $ips["daysplayed"];

	$playerstay[$daysplayed] = $playernumber;
	if($daysplayed > $maxplayertime){
		$maxplaytime = $daysplayed;
	}
	if($daysplayed != 0){
		if($playernumber > $maxplayersinaday){
			$maxplayersinaday = $playernumber;
		}
	}
}

$playerstaytablebody = "#f2ffeb";
$playerstaytablehead = "#d0ffd1";

print "<table width='1100' cellpadding='5' cellspacing='0' align='center'><tr>";
print "<th bgcolor='$playerstaytablehead'> </th>";
print "<td align='center' valign='bottom' bgcolor='$playerstaytablebody'><img src='bars/zerodays.PNG' width='10' height='300'></td>";
for($i = 1; $i <= $maxplaytime; $i++){
	$num = 0;
	if(isset($playerstay[$i])){
		$num = $playerstay[$i];
	}
	$height = floor(($num / $maxplayersinaday) * 300);
	
	print "<td align='center' valign='bottom' bgcolor='$playerstaytablebody'><img src='bars/join.PNG' width='10' height='$height'></td>";
}
print "</tr><tr>";
print "<th bgcolor='$playerstaytablehead'>Players</th>";
for($i = 0; $i <= $maxplaytime; $i++){

	print "<td align='center' bgcolor='$playerstaytablehead'>";
	if(isset($playerstay[$i])){
		print $playerstay[$i];
	}else{
		print 0;
	}
	print "</td>";
}
print "</tr><tr>";
print "<th bgcolor='$playerstaytablehead'>Days</th>";
for($i = 0; $i <= $maxplaytime; $i++){
	print "<th bgcolor='$playerstaytablehead'>$i</th>";
}
print "</tr>";
print "</table>";

//Average inactive player lifespan

print "<h1 align='center'>Inactive player lifespan on /tg/ station 13</h1>";

print "<p><div align='center'>Inactive means they have not logged in for the past $inactivityMargin days.</div></p>";

$playerstay = Array();
$maxplaytime = 0;
$maxplayersinaday = 0;

$d_playerstay = mysql_query("
							SELECT COUNT( id ) AS playernumber, DATEDIFF( lastseen, firstseen ) AS daysplayed
							FROM `ss13playerview` 
							WHERE DATEDIFF( Now(), lastseen ) > $inactivityMargin
							GROUP BY daysplayed
							ORDER BY daysplayed
						") or die(mysql_error());
$maxplayertime = 0;
while($ips = mysql_fetch_array($d_playerstay)){
	
	$playernumber = $ips["playernumber"];
	$daysplayed = $ips["daysplayed"];

	$playerstay[$daysplayed] = $playernumber;
	if($daysplayed > $maxplayertime){
		$maxplaytime = $daysplayed;
	}
	if($daysplayed != 0){
		if($playernumber > $maxplayersinaday){
			$maxplayersinaday = $playernumber;
		}
	}
}

$playerstaytablebody = "#f2ffeb";
$playerstaytablehead = "#d0ffd1";

print "<table width='1100' cellpadding='5' cellspacing='0' align='center'><tr>";
print "<th bgcolor='$playerstaytablehead'> </th>";
print "<td align='center' valign='bottom' bgcolor='$playerstaytablebody'><img src='bars/zerodays.PNG' width='10' height='300'></td>";
for($i = 1; $i <= $maxplaytime; $i++){
	$num = 0;
	if(isset($playerstay[$i])){
		$num = $playerstay[$i];
	}
	$height = floor(($num / $maxplayersinaday) * 300);
	
	print "<td align='center' valign='bottom' bgcolor='$playerstaytablebody'><img src='bars/join.PNG' width='10' height='$height'></td>";
}
print "</tr><tr>";
print "<th bgcolor='$playerstaytablehead'>Players</th>";
for($i = 0; $i <= $maxplaytime; $i++){

	print "<td align='center' bgcolor='$playerstaytablehead'>";
	if(isset($playerstay[$i])){
		print $playerstay[$i];
	}else{
		print 0;
	}
	print "</td>";
}
print "</tr><tr>";
print "<th bgcolor='$playerstaytablehead'>Days</th>";
for($i = 0; $i <= $maxplaytime; $i++){
	print "<th bgcolor='$playerstaytablehead'>$i</th>";
}
print "</tr>";
print "</table>";



?>