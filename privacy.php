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


$d1 = mysql_query("SELECT COUNT(id) AS count_signed FROM erro_privacy e WHERE e.option='SIGNED'") or die(mysql_error());
$d2 = mysql_query("SELECT COUNT(id) AS count_anonymous FROM erro_privacy e WHERE e.option='ANONYMOUS'") or die(mysql_error());
$d3 = mysql_query("SELECT COUNT(id) AS count_nostats FROM erro_privacy e WHERE e.option='NOSTATS'") or die(mysql_error());
$d4 = mysql_query("SELECT COUNT(id) AS count_abstain FROM erro_privacy e WHERE e.option='ABSTAIN'") or die(mysql_error());

$count_signed = 0;
$count_privacy = 0;
$count_nostats = 0;
$count_abstain = 0;

while($i = mysql_fetch_array($d1)){	
	if(isset($i["count_signed"])){
		$count_signed = $i["count_signed"];
	}
}
while($i = mysql_fetch_array($d2)){	
	if(isset($i["count_anonymous"])){
		$count_privacy = $i["count_anonymous"];
	}
}
while($i = mysql_fetch_array($d3)){	
	if(isset($i["count_nostats"])){
		$count_nostats = $i["count_nostats"];
	}
}
while($i = mysql_fetch_array($d4)){	
	if(isset($i["count_abstain"])){
		$count_abstain = $i["count_abstain"];
	}
}

$count_total = $count_signed + $count_privacy + $count_nostats + $count_abstain;

$width = 800;

if($count_total > 0){
	$percentage_signed = $count_signed / $count_total;
	$percentage_privacy = $count_privacy / $count_total;
	$percentage_nostats = $count_nostats / $count_total;
	$percentage_abstain = $count_abstain / $count_total;
	
	$width_signed = floor($percentage_signed*$width);
	$width_privacy = floor($percentage_privacy*$width);
	$width_nostats = floor($percentage_nostats*$width);
	$width_abstain = floor($percentage_abstain*$width);
	
	print "<table align='center'><tr><td>";
	print "<img src='bars/privacy_0.PNG' width='$width_signed' height='20'>";
	print "<img src='bars/privacy_1.PNG' width='$width_privacy' height='20'>";
	print "<img src='bars/privacy_2.PNG' width='$width_nostats' height='20'>";
	print "<img src='bars/privacy_3.PNG' width='$width_abstain' height='20'>";
	print "</td></tr></table>";
	
	print "<p>";
	
	print "<table align='center'><tr><td>";
	print "<img src='bars/privacy_0.PNG' width='20' height='20'> <b>Signed: </b>$count_signed / $count_total <b>(".percent2str($percentage_signed).")</b><br>";
	print "<img src='bars/privacy_1.PNG' width='20' height='20'> <b>Privacy: </b>$count_privacy / $count_total <b>(".percent2str($percentage_privacy).")</b><br>";
	print "<img src='bars/privacy_2.PNG' width='20' height='20'> <b>No stats: </b>$count_nostats / $count_total <b>(".percent2str($percentage_nostats).")</b><br>";
	print "<img src='bars/privacy_3.PNG' width='20' height='20'> <b>Abstain: </b>$count_abstain / $count_total <b>(".percent2str($percentage_abstain).")</b><br>";
	print "</td></tr></table>";


}else{
	print "<table align='center'><tr><td>";
	print "<b>No votes yet.</b>";
	print "</td></tr></table>";
}






















?>