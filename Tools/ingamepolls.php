<?php
include("db.php");
?>

<?php

$pollsperpage = 10;

print "<h1 align='center'>Ingame poll results for /tg/ station 13</h1>";

include "menu.php";


$page = 1;
if(isset($_GET["page"])){
	$page = intval($_GET["page"]);
	if($page < 1){
		$page = 1;
	}
}
$startat = ($page-1) * $pollsperpage;

print "<p><div align='center'>Displaying polls from ".($startat+1)." to ".($startat+$pollsperpage)."<br>";

for($j = 1; $j <= 20; $j++){
	if($j == $page){
		print "$j ";
	}else{
		print "<a href='ingamepolls.php?page=$j'>$j</a> ";
	}
}
print "</div></p>";

$d = mysql_query("SELECT *, (endtime) < NOW() as endtimeover, (starttime) > NOW() AS notyetstarted FROM ss13poll_question ORDER BY id DESC LIMIT $startat,$pollsperpage") or die(mysql_error());
		
print "<table width='900' align='center' bgcolor='#eeffee' cellspacing='0' cellpadding='4'>";

$rankcolors = Array();
$lastadmincolor = 0;
while($i = mysql_fetch_array($d)){	

	$pollid = $i["id"];
	$polltype = $i["polltype"];
	$question = $i["question"];
	$adminonly = $i["adminonly"];
	$starttime = $i["starttime"];
	$dontshow = $i["dontshow"];
	$endtimeover = $i["endtimeover"];
	$notyetstarted = $i["notyetstarted"];
	$endtime = $i["endtime"];
	$dontshowresults = 0;
	print "<tr>";
	print "<td colspan='4' align='center' bgcolor='white'>&nbsp;</td>";
	print "</tr>";
	
	print "<tr bgcolor='#ddffdd'>";
	print "<th colspan='4' align='center'>";
	print "<a name='p$pollid'>";
	if($adminonly){
		print "<b>(<font color='#997700'>Admin only poll</font>)</b> ";
	}
	print $question;
	print " (<a href='ingamepolls.php#p$pollid'>link</a>)";
	print "<font size='1'><br><b>$starttime - $endtime</b></font>";
	print "</th>";
	print "</tr>";
	
	if($dontshow == 1 && $endtimeover == 0){
		$dontshowresults = 1;
		print "<tr><td colspan='4'><div align='center'><b>Results will be available at $endtime</div></b></td></tr>";
	}
	
	if(($polltype == "OPTION" || $polltype == "MULTICHOICE")){
		$d2 = mysql_query("	
			SELECT text, percentagecalc, votecount
			FROM (
				(
					SELECT o.text AS propertext, COUNT(v.id) AS votecount
					FROM ss13poll_option o, ss13poll_vote v
					WHERE o.pollid = $pollid
					  AND o.id = v.optionid
					GROUP BY o.id
				) c
				RIGHT JOIN
				(
					SELECT text, percentagecalc
					FROM ss13poll_option 
					WHERE pollid = $pollid
				) d
				ON (c.propertext = d.text)
			)") or die(mysql_error());
		
		$votestats = Array();
		$totalvotes = 0;
		$totalpercentagevotes = 0;
		$maxvotes = 0;
		
		while($i2 = mysql_fetch_array($d2)){
			$optiontext = $i2["text"];
			$optionpercentagecalc = $i2["percentagecalc"];
			$optionvotes = $i2["votecount"];
			
			$totalvotes += intval($optionvotes);
			if($optionpercentagecalc == 1){
				$totalpercentagevotes += intval($optionvotes);
			}
			if(intval($optionvotes) > $maxvotes){
				$maxvotes = intval($optionvotes);
			}
			$votestats[$optiontext]["votes"] = intval($optionvotes);
			$votestats[$optiontext]["percentagecalc"] = $i2["percentagecalc"];
		}
		
		//Absolute representation
		if($dontshowresults == 0){
			if($totalvotes > 0){
				print "<tr bgcolor='#ddffdd'>";
				print "<th colspan='4' align='center'>";
				$bar_i = 1;
				foreach ($votestats as $option => $votes){
					$allvotes = $votes["votes"];
					$percentagecalc = $votes["percentagecalc"];
					$barwidth = round( ($allvotes / $totalvotes) * 700 );
					
					$percentage = "N/A";
					if($percentagecalc == 1 && $totalpercentagevotes > 0){
						$percentage = "". round( ($allvotes / $totalpercentagevotes)*100 ) ."%";
					}
					$votestats[$option]["percentage"] = $percentage;
					
					$bar_i = $bar_i % 20;
					$barname = "bars/p$bar_i.png";
					$bar_i += 3;
					$option_tmp = str_replace("'", "", $option);
					print "<img src='$barname' height='5' width='$barwidth' title='$option_tmp - $percentage'>";
				}
				print "<font size='2'><b><br>(Hover over the colored bar to read description)</b></font>";
				print "</th>";
				print "</tr>";
			}else{
				print "<tr bgcolor='#ddffdd'>";
				print "<th colspan='4' align='center'>";
				
				print "<font size='2'><b>(Poll has no votes yet)</b></font>";
				
				print "</th>";
				print "</tr>";
			}
		}
		
		if($adminonly == 1){
			$ar_votestats = Array();
			
			$dar = mysql_query("SELECT o.text, v.adminrank, count(v.id) AS votes FROM ss13poll_vote v, ss13poll_option o WHERE o.id = v.optionid AND v.pollid = $pollid GROUP BY o.text, adminrank ORDER BY o.text, v.adminrank");
			while($iar = mysql_fetch_array($dar)){
				$ar_text = $iar["text"];
				$ar_adminrank = $iar["adminrank"];
				$ar_votes = $iar["votes"];
				
				switch($ar_adminrank){
					case "Admin Observer":
					case "AdminObserver":
						$ar_adminrank = "Admin Observer";
					break;
					
					case "AdminCandidate":
					case "Trial Admin":
					case "TrialAdmin":
						$ar_adminrank = "Trialmin";
					break;
					
					case "Coder":
					case "Codermin":
						$ar_adminrank = "Coder";
					break;
					
					case "Community Creator":
					case "Host":
					case "Database Admin":
					case "Game Admin":
					case "Game Master":
					case "GameAdmin":
					case "GameMaster":
					case "Head Admin":
					case "Headmin":
						$ar_adminrank = "Fullmin";
					break;
					
					case "Player":
						$ar_adminrank = "Player";
					break;
					default:
						$ar_adminrank = "Other";
					break;
				}
				
				if(!isset($rankcolors[$ar_adminrank])){
					$lastadmincolor++;
					$rankcolors[$ar_adminrank] = $lastadmincolor;
				}
				
				if(!isset($ar_votestats[$ar_text])){
					$ar_votestats[$ar_text] = Array();
				}
				if(!isset($ar_votestats[$ar_text][$ar_adminrank])){
					$ar_votestats[$ar_text][$ar_adminrank] = 0;
				}
				$ar_votestats[$ar_text][$ar_adminrank] += intval($ar_votes);
			}
			
			//Relative representation (ADMIN ONLY POLLS)
			foreach ($votestats as $option => $votes){
				$allvotes = $votes["votes"];
				$percentage = $votes["percentage"];
				
				print "<tr>";
				print "<td width='300' align='right'>$option</td>";

				if($maxvotes > 0){
					$barwidth = round( ($allvotes / $maxvotes) * 390 );
				}else{
					$barwidth = 0;
				}
				print "<td width='100' align='center'><b>$allvotes</b></td>";
				print "<td width='100' align='center'><b>$percentage</b></td>";
				print "<td width='400' align='left'>";
				if(isset($ar_votestats[$option])){
					foreach($ar_votestats[$option] as $ar_rank => $num){
						$barwidth1 = round( ($num / $maxvotes) * 390);
						print "<img src='bars/p".$rankcolors[$ar_rank].".png' height='10' width='$barwidth1' title='$ar_rank: $num vote(s)'>";
					}
				}
				print "</td>";

				print "</tr>";
			}
			
		}else{
		
		
			//Relative representation (NON ADMIN ONLY VOTES)
			foreach ($votestats as $option => $votes){
				$allvotes = $votes["votes"];
				$percentage = $votes["percentage"];
				
				print "<tr>";
				print "<td width='300' align='right'>$option</td>";

				if($maxvotes > 0){
					$barwidth = round( ($allvotes / $maxvotes) * 390 );
				}else{
					$barwidth = 0;
				}
				
				if($dontshowresults == 1){
					$allvotes = "soon";
					$barwidth = 0;
					$percentage = 0;
				}
				
				print "<td width='100' align='center'><b>$allvotes</b></td>";
				print "<td width='100' align='center'><b>$percentage</b></td>";
				print "<td width='400' align='left'><img src='bars/10.PNG' height='10' width='$barwidth'></td>";

				print "</tr>";
			}
		}
	}else if($polltype == "TEXT"){
		$d1 = mysql_query("	
			SELECT COUNT(id) AS replies
			FROM ss13poll_textreply
			WHERE pollid = $pollid
			  AND replytext != 'ABSTAIN'") or die(mysql_error());
		
		$replies = 0;
		if($i = mysql_fetch_array($d1)){
			$replies = $i["replies"];
		}
		
		print "<tr bgcolor='#ddffdd'>";
		print "<td colspan='4' align='center'>";
		print "Number of replies: <b>$replies</b>";
		print "</td>";
		print "</tr>";
	}else if($polltype == "NUMVAL"){
		$d1 = mysql_query("	
			SELECT o.text, o.minval, o.maxval, o.descmin, o.descmid, o.descmax, v.rating, count(v.id) as count 
			FROM ss13poll_vote v, ss13poll_option o
			WHERE v.pollid = $pollid
			  AND v.optionid = o.id
			GROUP BY optionid, rating") or die(mysql_error());
			
		$votestat = Array();
		$max = 0;
		
		while($i1 = mysql_fetch_array($d1)){
			$text = $i1["text"];
			$rating = intval($i1["rating"]);
			$count = intval($i1["count"]);
			
			if(!isset($votestat[$text])){
				$minval = intval($i1["minval"]);
				$maxval = intval($i1["maxval"]);
				$descmin = $i1["descmin"];
				$descmid = $i1["descmid"];
				$descmax = $i1["descmax"];
				$votestat[$text] = Array();
				$midval = floor( ($minval + $maxval) / 2 );
				
				$votestat[$text]["sum"] = 0;
				$votestat[$text]["ratingsum"] = 0;
				$votestat[$text]["minval"] = $minval;
				$votestat[$text]["midval"] = $midval;
				$votestat[$text]["maxval"] = $maxval;
				$votestat[$text]["descmin"] = $descmin;
				$votestat[$text]["descmid"] = $descmid;
				$votestat[$text]["descmax"] = $descmax;
			}
			
			if($i1["rating"] == null){	//Abstainers
				continue;
			}
			
			$votestat[$text][$rating] = $count;
			$votestat[$text]["sum"] += $count;
			$votestat[$text]["ratingsum"] += ($count * $rating);
			if($count > $max){
				$max = $count;
			}
		}
		
		$color1 = "#eeffee";
		$color2 = "#e0ffe0";
		$color = $color2;
		foreach($votestat as $option => $array){
			if($color == $color1){$color = $color2;}else{$color = $color1;}
			$allvotes = $array["sum"];
			$minval = $array["minval"];
			$maxval = $array["maxval"];
			$ratingsum = $array["ratingsum"];
			$avg = (floor($ratingsum*100 / $allvotes)/100);
			
			if($dontshowresults == 1){
				$avg = "soon";
			}
			
			print "<tr bgcolor='$color'>";
			print "<td align='right' width='300'>$option</td>";
			print "<td align='center' width='100'><b>N = $allvotes</b></td>";
			print "<td align='center' width='100'><b>AVG = $avg</b></td>";
			print "<td width='400' align='left' colspan='2'>";
			print "<table width='400' style='table-layout: fixed;'>";
			for($rating = $minval; $rating <= $maxval; $rating++){
				$ratingvotes = 0;
				if(isset($array[$rating])){
					$ratingvotes = $array[$rating];
				}
				$barwidth = 1;
				$percentage_for_option = 0;
				if($max > 0 && $ratingvotes > 0){
					$percentage_for_option = ($ratingvotes / $allvotes);
					$barwidth = floor( ($ratingvotes / $max) * 190 );
				}
				
				if($dontshowresults == 1){
					$ratingvotes = "soon";
					$barwidth = 0;
					$percentage_for_option = 0;
				}
				
				print "<tr><td align='center' width='50'><b>$rating</b></td><td align='center' width='50'>$ratingvotes</td><td align='center' width='75'>(".floor($percentage_for_option*100) ."%)</td><td width='200'><img src='bars/10.PNG' height='10' width='$barwidth'></td>";
			}
			print "</table>";
			print "</td>";
			print "</tr>";
		}
			
		$replies = 0;
		if($i = mysql_fetch_array($d1)){
			$replies = $i["replies"];
		}
	}
	
	
}
print "</table>";