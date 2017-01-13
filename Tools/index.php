<?php
include("db.php");
?>

<?php


//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

$time_in_days = 30; //Number of days it looks for in the past
$startDaysAgo = 0;	//Number of days from now to start presenting stats at	(end date)
$maxAllowedNumberOfDays = 30;	//Maximum number of days of data which is allowed to be processed.
$dbFeedbackTable = "SS13feedback";

?>


<div align='center'>
	<p><form method='post' align='center'>
		Username: <input type='text' name='un' value='tgstation'>
		<br>Password: <input type='password' name='pw'>
		<br>Time span: <input type='number' name='days' value='<?php print $maxAllowedNumberOfDays; ?>' min='0' max='<?php print $maxAllowedNumberOfDays; ?>'>
		<br>Day offset: <input type='number' name='offset' value='0' min='0' max='10000'>
		
		<p>Starting <i>day offset</i> ago, look up data for <i>time span</i> days into the past.<br>
		So for example, if it's 15 May and you want to look for the period between 3 May and 10 May, <br>
		set day offset to 5 (because 15 May - 5 = 10 May) and time span to 7 (because 10 May - 7 = 3 May)</p>
		<br><input type='submit' name='try' value='Log in'>
	</form>
</div>

<?php

//Log in user
if( isset($_POST["days"]) ){
	$_SESSION["days"] = $_POST["days"];
}
if( isset($_POST["offset"]) ){
	$_SESSION["offset"] = $_POST["offset"];
}

?>

<?php
//Helpers

function StartsWith($Haystack, $Needle){
    // Recommended version, using strpos
    return strpos($Haystack, $Needle) === 0;
}

function endsWith($haystack, $needle){
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

function getTimeStr($time){
	return "". str_pad( strval( floor($time/60) ), 2, "0", STR_PAD_LEFT) .":". str_pad( strval( $time%60 ) , 2, "0", STR_PAD_LEFT);
}

function percent2str($percent){
	$s = strval( ($percent*100) );
	$i = strpos( $s, "." );
	if( $i == null){
		return "".$s."%";
	}
	return "". substr( $s, 0, $i)."%";
}

function num2print($num, $decimals = 1){
	$s = strval( ($num) );
	$i = strpos( $s, "." );
	if( $i == null){
		return "".$s;
	}
	return "". substr( $s, 0, $i+1+$decimals);
}

function min2hour($min){
	return floor($min / 60);
}

function min2duration($min){
	$r = "";
	
	$year = floor( $min / ( 365*24*60 ) );
	if($year > 0){
		$min = $min % ( 365*24*60 );
		if($year == 1){
			$r .= "$year year, ";
		}else{
			$r .= "$year years, ";
		}
	}
	$month = floor( $min / ( 30*24*60 ) );
	if($month > 0){
		$min = $min % ( 30*24*60 );
		if($month == 1){
			$r .= "$month month, ";
		}else{
			$r .= "$month months, ";
		}
	}
	$day = floor( $min / (24*60) );
	if($day > 0){
		$min = $min % ( 24*60 );
		if($day == 1){
			$r .= "$day day, ";
		}else{
			$r .= "$day days, ";
		}
	}
	$hour = floor( $min / 60 );
	if($hour > 0){
		$min = $min % 60;
		if($hour == 1){
			$r .= "$hour hour, ";
		}else{
			$r .= "$hour hours, ";
		}
	}
	
	if($min > 0 || $r == ""){
		if($min == 1){
			$r .= "$min minute";
		}else{
			$r .= "$min minutes";
		}
	}
	return $r;
}
		
//Telecrystal items
function abbreviation2itemname($abb){
	switch($abb){
		case "RE": return "Revolver"; break;
		case "RA": return "Revolver Ammo"; break;
		case "XB": return "Energy Crossbow"; break;
		case "ES": return "Energy Sword"; break;
		case "CJ": return "Cham Jumpsuit"; break;
		case "SH": return "Syndicate Shoes"; break;
		case "AC": return "Agent Card"; break;
		case "VC": return "Voice Changer"; break;
		case "FI": return "Freedom Implant"; break;
		case "UI": return "Uplink Implant"; break;
		case "PP": return "ParaPen"; break;
		case "DC": return "Detomatix"; break;
		case "C4": return "Plastic Explosive (C4)"; break;
		case "PS": return "Power Sink"; break;
		case "SS": return "Space Suit"; break;
		case "CP": return "Charizard"; break;
		case "CD": return "Cloaker"; break;
		case "EC": return "Electromagnetic Sequencer (Emag)"; break;
		case "EM": return "EMP Grenades"; break;
		case "BT": return "Binary Headset"; break;
		case "AI": return "Hacked AI Module"; break;
		case "SB": return "Singularity Beacon"; break;
		case "ST": return "Syndicate Toolbox"; break;
		case "SP": return "Syndicate Soap"; break;
		case "BS": return "That Fucking Balloon"; break;
		case "BU": return "Bundle"; break;
		case "TP": return "Teleporter Circuitboard"; break;
		case "TM": return "Thermal Imaging Glasses"; break;
		case "RN": return "Random item"; break;
	}
	return $abb;
};

//Admin verbs
function abbreviation2adminverb($abb){
	switch($abb){
		case "A": return "Announce"; break;
		case "D": return "Dsay"; break;
		case "M": return "Asay"; break;
		case "O": return "Set observe"; break;
		case "P": return "Set play"; break;
		case "R": return "Restart"; break;
		case "S": return "Secrets"; break;
		case "X": return "Spawn Xeno"; break;
		case "AH": return "Adminhelp"; break;
		case "AV": return "Abort vote"; break;
		case "RR": return "Radio report"; break;
		case "CC": return "Check contents"; break;
		case "CP": return "Check plumbing"; break;
		case "DB": return "Drop bomb"; break;
		case "GD": return "Give Disease"; break;
		case "GK": return "Get key"; break;
		case "GM": return "Get mob"; break;
		case "GP": return "Game panel"; break;
		case "GS": return "Give spell"; break;
		case "IR": return "Immediate reboot"; break;
		case "JA": return "Jump to area"; break;
		case "JC": return "Jump to coordinate"; break;
		case "JK": return "Jump to key"; break;
		case "JM": return "Jump to mob"; break;
		case "JT": return "Jump to turf"; break;
		case "KA": return "Kill air"; break;
		case "MS": return "Make sound"; break;
		case "OC": return "OOC color"; break;
		case "OT": return "Object talk"; break;
		case "PO": return "Possess object"; break;
		case "PP": return "Player panel"; break;
		case "PR": return "Pray"; break;
		case "RO": return "Release object"; break;
		case "SA": return "Spawn atom"; break;
		case "SM": return "Stealth mode"; break;
		case "SN": return "Start now"; break;
		case "SS": return "Stop sounds"; break;
		case "SV": return "Start vote"; break;
		case "TA": return "Toggle aliens"; break;
		case "TE": return "Toggle entering"; break;
		case "TJ": return "Toggle admin jumping"; break;
		case "TP": return "Toggle prayer visibility"; break;
		case "TR": return "Toggle respawn"; break;
		case "TV": return "Toggle voting"; break;
		case "UP": return "Unprison"; break;
		case "VO": return "Voting"; break;
		case "ADC": return "Assume direct control"; break;
		case "AHS": return "Toggle adminhelp sound"; break;
		case "APC": return "Advanced proc call"; break;
		case "APM": return "Admin pm"; break;
		case "ASL": return "Air status in location"; break;
		case "CCR": return "Create command report"; break;
		case "CHA": return "Check antagonist"; break;
		case "DAS": return "Deadmin self"; break;
		case "DEL": return "Delete"; break;
		case "DG2": return "Debug game"; break;
		case "EMP": return "EMP explosion"; break;
		case "ETV": return "Edit tracker variables"; break;
		case "GAS": return "Get admin state"; break;
		case "GFA": return "Grant full access"; break;
		case "GIB": return "Gib"; break;
		case "GLN": return "Global narrate"; break;
		case "GOD": return "Godmode"; break;
		case "GPV": return "Give possession verb"; break;
		case "HMV": return "Hide most verbs"; break;
		case "ION": return "Ion storm"; break;
		case "LFS": return "List free slots"; break;
		case "MER": return "Make everyone random"; break;
		case "MEV": return "Mass edit variables"; break;
		case "PGS": return "Play global sound"; break;
		case "PLS": return "Play local sound"; break;
		case "PPN": return "Player panel new"; break;
		case "RMC": return "Restart master controller"; break;
		case "SAN": return "Set admin notice"; break;
		case "SAR": return "Show air report"; break;
		case "SEQ": return "Select equipment"; break;
		case "SGR": return "Show general report"; break;
		case "SMS": return "Subtle message"; break;
		case "SPP": return "Show player panel"; break;
		case "SRM": return "Switch radio mode"; break;
		case "STP": return "Show traitor panel"; break;
		case "STR": return "Show tension report"; break;
		case "TAI": return "Toggle AI"; break;
		case "TAL": return "Toggle Aliens"; break;
		case "TAR": return "Toggle admin revives"; break;
		case "TAS": return "Toggle admin spawning"; break;
		case "TDV": return "Toggle deadchat visibility"; break;
		case "TGE": return "Show/Hide GhostEars"; break;
		case "TGS": return "Show/Hide GhostSight"; break;
		case "TGU": return "Toggle guests"; break;
		case "THR": return "Toggle hear radio"; break;
		case "TRE": return "Toggle random events"; break;
		case "TSN": return "Toggle space ninja"; break;
		case "TTS": return "Toggle traitor scaling"; break;
		case "UBP": return "Unban panel"; break;
		case "UFE": return "Unfreeze everyone"; break;
		case "VJB": return "View jobbans"; break;
		case "VTL": return "Show server log"; break;
		case "mDV": return "enable debug verbs"; break;
		case "mOBJ": return "Count all objects"; break;
		case "mCRD": return "Camera range display"; break;
		case "mCRP": return "Camera report"; break;
		case "mIRD": return "Intercom range display"; break;
		case "APMM": return "Admin pm mob"; break;
		case "ATTL": return "Attack log"; break;
		case "CAIT": return "Create tri-AI"; break;
		case "CVRA": return "Change view range"; break;
		case "DAST": return "Display air status"; break;
		case "DELA": return "Del-all"; break;
		case "DEVR": return "Drop everything"; break;
		case "DIRN": return "Direct narrate"; break;
		case "DTHS": return "Death squad"; break;
		case "EXPL": return "Explosion"; break;
		case "GIBS": return "Gibself"; break;
		case "IONC": return "Custom ion law"; break;
		case "JDAG": return "Jump to dead air group"; break;
		case "KLAG": return "Kill local air group"; break;
		case "MKAL": return "Make alien"; break;
		case "MPAI": return "Make pAI"; break;
		case "MPWN": return "Make powernets"; break;
		case "MUTE": return "Mute"; break;
		case "REJU": return "Rejuvanate"; break;
		case "RLDA": return "Reload admins"; break;
		case "SMOB": return "Send mob"; break;
		case "SSAL": return "Show server attack log"; break;
		case "TBMS": return "Toggle build mode self"; break;
		case "THDC": return "Toggle hear dead chat"; break;
		case "TNCP": return "Toggle new click proc"; break;
		case "TOOC": return "Toggle OOC"; break;
		case "TTWH": return "Toggle tinted welding helmets"; break;
		case "UJBP": return "Unjobban panel"; break;
		case "WARN": return "Warn"; break;
		case "mOBJZ": return "Count objects on z level"; break;
		case "CSHUT": return "Call shuttle"; break;
		case "DELAY": return "Delay"; break;
		case "EDITV": return "Edit variables"; break;
		case "MKMET": return "Make metroid"; break;
		case "RSPCH": return "Respawn character"; break;
		case "SDTHS": return "Syndicate death squad"; break;
		case "STATM": return "Stabilize atmos"; break;
		case "TAVVH": return "Toggle admin verb visibility - hide"; break;
		case "TAVVS": return "Toggle admin verb visibility - show"; break;
		case "TAmbi": return "Hear/Silence Ambience"; break;
		case "TCBOO": return "There can be only one"; break;
		case "TDOOC": return "Toggle dead OOC"; break;
		case "TMidi": return "Hear/Silence Midis"; break;
		case "TSGON": return "Toggle station gravity on"; break;
		case "CCSHUT": return "Cancel shuttle"; break;
		case "PRISON": return "Prison"; break;
		case "TLobby": return "Hear/Silence LobbyMusic"; break;
		case "TSGOFF": return "Toggle station gravity off"; break;
		case "TICKLAG": return "Set ticklag"; break;
		case "AUTOMUTE": return "Automute"; break;
		
		
		case "DMC": return "Debug Controller - Master Controller"; break;
		case "DFailsafe": return "Debug Controller - Failsafe"; break;
		case "DTicker": return "Debug Controller - Ticker"; break;
		case "DLighting": return "Debug Controller - Lighting"; break;
		case "DAir": return "Debug Controller - Air"; break;
		case "DJobs": return "Debug Controller - Jobs"; break;
		case "DSun": return "Debug Controller - Sun"; break;
		case "DRadio": return "Debug Controller - Radio"; break;
		case "DSupply": return "Debug Controller - Supply"; break;
		case "DEmergency": return "Debug Controller - Emergency"; break;
		case "DConf": return "Debug Controller - Configuration"; break;
		case "DpAI": return "Debug Controller - pAI"; break;
		case "DCameras": return "Debug Controller - Cameras"; break;
		case "DEvents": return "Debug Controller - Events"; break;
	}
	return $abb;
}

//Wizard spell names
function abbreviation2spellname($abb){
	switch($abb){
		case "BD": return "Blind"; break;
		case "BL": return "Blink"; break;
		case "DT": return "Disable Tech"; break;
		case "DG": return "Disintegrate"; break;
		case "EJ": return "Ethernal Jaunt"; break;
		case "FB": return "Fireball"; break;
		case "FW": return "Forcewall"; break;
		case "KN": return "Knock"; break;
		case "MM": return "Magic Missile"; break;
		case "MU": return "Mutate"; break;
		case "MT": return "Mind Transfer"; break;
		case "SG": return "Summon Guns"; break;
		case "SM": return "Smoke"; break;
		case "SU": return "Summon Magic"; break;
		case "SS": return "Soul Stone"; break;
		case "ST": return "Staff"; break;
		case "TP": return "Teleport"; break;
		case "VR": return "Veil Render"; break;
		case "UM": return "Unmemorize"; break;
		case "SA": return "Staff Animation"; break;
		case "HH": return "Horseman"; break;
		case "HS": return "Armor Suit"; break;
		case "SO": return "Scrying Orb"; break;
		case "CO": return "Contract of Apprenticeship"; break;	//Was changed to "CT" at some point.
		case "CT": return "Contract of Apprenticeship"; break;
		case "FS": return "Flesh to stone"; break;
	}
	return $abb;
};

//Changeling powers
function abbreviation2changelingpower($abb){
	switch($abb){
		case "A1": return "Absorption stage 1"; break;
		case "A2": return "Absorption stage 2"; break;
		case "A3": return "Absorption stage 3"; break;
		case "A4": return "Absorption stage 4"; break;
		case "PS": return "Paralysis sting"; break;
		case "TS": return "Transformation Sting"; break;
		case "US": return "Unfat Sting"; break;
		case "UNS": return "Epinephrine Sacs"; break;
		case "CAM": return "Digital Camoflage"; break;
		case "DTHS": return "Death sting"; break;
		case "RR": return "Rapid Regeneration"; break;
		case "HS": return "Hallucination Sting"; break;
		case "FD": return "Regenerative Stasis (Fake death)"; break;
		case "TR": return "Transform"; break;
		case "LF": return "Lesser Form"; break;
		case "LFT": return "Lesser Transform"; break;
		case "GF": return "Greater Form"; break;
		case "RS": return "Ranged Sting"; break;
		case "SS": return "Silence Sting"; break;
		case "BS": return "Blind Sting"; break;
		case "DS": return "Deaf Sting"; break;
		case "HU": return "Hive Channel"; break;
		case "MV": return "Mimic Voice"; break;
		case "HD": return "Hive Absorb"; break;
		case "ED": return "Extract DNA Sting"; break;
		case "CS": return "Cryo sting"; break;
		case "MS": return "Mute sting"; break;
		case "SI": return "Spread infection"; break;
		case "AP": return "Anatomic Panacea"; break;
		case "CR": return "Regenerate"; break;
	}
	return $abb;
};

//Admin actions
function abbreviation2adminaction($abb){
	switch($abb){
		case "SC": return "sec clothes"; break;
		case "SG": return "Summon guns"; break;
		case "SM": return "Summon magic"; break;
		case "BH": return "Black hole"; break;
		case "QP": return "Quick power"; break;
		case "LF": return "Floor is lava"; break;
		case "SAC": return "Sec all clothes"; break;
		case "SC1": return "Sec classic"; break;
		case "T": return "Toxic air"; break;
		case "M": return "Complete monkeyification"; break;
		case "P": return "Power everything"; break;
		case "UP": return "Unpower everything"; break;
		case "AP": return "Activate prison"; break;
		case "DP": return "Deactivate prison"; break;
		case "TPS": return "Toggle prison status"; break;
		case "PW": return "Prison warp"; break;
		case "TA": return "Everyone is a traitor"; break;
		case "CK": return "Cookies spawned"; break;
		case "ShM": return "Mining shuttle move"; break;
		case "ShA": return "Admin shuttle move"; break;
		case "ShF": return "Ferry move"; break;
		case "ShX": return "Xeno dinghy move"; break;
		case "FL": return "Flicker lights"; break;
		case "MW": return "Meteor wave"; break;
		case "GA": return "Gravitational anomality"; break;
		case "STA": return "Spacetime anomality"; break;
		case "BL": return "Blob"; break;
		case "AL": return "Aliens"; break;
		case "SN": return "Space ninja"; break;
		case "C": return "Carp"; break;
		case "R": return "Irradiate station"; break;
		case "IR": return "Immovable rod"; break;
		case "BC": return "Toggle bomb cap"; break;
		case "PB": return "Prison break"; break;
		case "LO": return "Lightsout"; break;
		case "BO": return "Blackout"; break;
		case "V": return "Virus"; break;
		case "RET": return "Retardify"; break;
		case "FG": return "Make everything guns"; break;
		case "SG": return "Schoolgirl"; break;
		case "DF": return "Dorf mode"; break;
		case "I": return "Ion storm"; break;
		case "K": return "Kudzu"; break;
		case "ALS": return "Silent alien event"; break;
		case "SL": return "Spider Infestation"; break;
		case "CB": return "Comms Blackout"; break;
		case "WO": return "Whiteout"; break;
		case "OO": return "Only One"; break;
		case "EgL": return "Egalitarian Station mode"; break;
		case "BA": return "Bluespace Anomaly"; break;
	}
	return $abb;
};

//Cyborg statistics
function abbreviation2cyborgstat($abb){
	switch($abb){
		case "frames_built": return "Cyborgs built"; break;
		case "mmis_filled": return "Brains loaded into MMI-s"; break;
		case "birth": return "Cyborgs activated"; break;
		case "standard": return "Standard borgs"; break;
		case "engineering": return "Engineering borgs"; break;
		case "service": return "Service borgs"; break;
		case "miner": return "Mining borgs"; break;
		case "medical": return "Medical borgs"; break;
		case "security": return "Security borgs"; break;
		case "janitor": return "Janitor borgs"; break;
		case "ais_created": return "AI-s created"; break;
	}
	return $abb;
}

//Communications channel
function abbreviation2channel($abb){
	switch($abb){
		case "COM": return "Common"; break;
		case "SCI": return "Science"; break;
		case "HEA": return "Heads"; break;
		case "MED": return "Medical"; break;
		case "ENG": return "Engineering"; break;
		case "SEC": return "Security"; break;
		case "SRV": return "Service"; break;
		case "DTH": return "Deathsquad"; break;
		case "SYN": return "Syndicate"; break;
		case "MIN": return "Mining"; break;
		case "CAR": return "Cargo"; break;
		case "OTH": return "Other frequencies"; break;
		case "PDA": return "PDA"; break;
		case "RC": return "Request console"; break;
		case "NC": return "Newscaster stories"; break;
	}
	return $abb;
}

//Wisp actions
function Wisp2Action($key){
	switch($key){
		case "F": return "Freed"; break;
		case "R": return "Returned"; break;
	}
	return $key;
}

//Immortality talisman
function ImTalisman2Action($key){
	switch($key){
		case "U": return "Activated"; break;
	}
	return $key;
}

//Colours for mechas
function mecha2color($abb){
	switch($abb){
		case "odysseus": return "#eeeeee"; break;
		case "ripley": return "#ffffcc"; break;
		case "firefighter": return "#daeccf"; break;
		case "gygax": return "#ffeecc"; break;
		case "honker": return "#ffcccc"; break;
		case "durand": return "#cccccc"; break;
		case "marauder": return "#bbbbbb"; break;
	}
	return $ff0000;
}

//Jaunter Id to jaunt desctiptor:
function JaunterID2Descriptor($val){
	switch($val){
		case "E": return "EMP accidental activation"; break;
		case "C": return "Chasm save activation"; break;
		case "U": return "User activated"; break;
		case "M": return "Medical based activation"; break;
	}
	return $val;
}

//objective type to objective name
function type2objective($objective){
	return $objective;
}

//hours and minutes to minutes
function hourminute2minute($hour,$minute){
	return (($hour*60)+$minute);
}

?>


<?php
	
	//Check whether the time span we are processing is shorter than the maximum allowed.
	if( $time_in_days > $maxAllowedNumberOfDays){
		die("Maximum timespan which can be processed is $maxAllowedNumberOfDays. You wanted to process $time_in_days days of data.");
	}
	
	//Determine start and end dates for the stats we want to process
	$date = strtotime("now - ".($time_in_days + $startDaysAgo)." days");
	$dateEnd = strtotime("now - ".($startDaysAgo)." days");
	
	$date_str = date("d-m-Y", $date);
	$dateEnd_str = date("d-m-Y", $dateEnd);
	
	$date_tab = explode("-",$date_str);
	$dateEnd_tab = explode("-",$dateEnd_str);
	
	$day = $date_tab[0];
	$month = $date_tab[1];
	$year = $date_tab[2];
	$dayEnd = $dateEnd_tab[0];
	$monthEnd = $dateEnd_tab[1];
	$yearEnd = $dateEnd_tab[2];
	
	print "<div align='center'>Displaying results from the period of the last $time_in_days days, so Starting on $day - $month - $year, ending on $dayEnd - $monthEnd - $yearEnd.</div>";

	if($authenticated){
		//Do data aggregation only if you are authenticated.
		//This section is divided into three parts:
		//Data variables (data is aggregated into these)
		//Data aggregation (where feedback variables are parsed)
		//Aggregated data output (where the aggregated data is actually displayed)
		
		ob_start(); ?>
			
<html>
<head>
	<meta charset="utf-8"> 
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="Matevž Baloh">
	<title>Space Station 13 ingame statistics</title>
	<script type='text/javascript'>
		function expand(id, text, color){
			var span = document.getElementById(id);
			span.innerHTML = " - <b>"+text+"</b>";
		}
		
		function contract(id){
			var span = document.getElementById(id);
			span.innerHTML = "";
		}
	</script>
	<link rel="stylesheet" href="js/site.css">
	<script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
	<script src="js/chart.js"></script>
</head>
<body>
		
		<?php
		
		$d = mysql_query("SELECT * FROM $dbFeedbackTable WHERE time > '$year-$month-$day' AND time <= '$yearEnd-$monthEnd-$dayEnd' ORDER BY round_id") or die(mysql_error());
		
		$tmp_game_mode = "";
		$tmp_end_proper = 0;
		
		/* Data mining vars */
		
		//Now this is a bit of a mess. There is not much rhyme and reason to how data is aggregated.
		//But, well, it works. Some of it is however hard-coded (such as Telecrystal costs, radio channels,
		//etc. ) so it needs periodical manual updating.
		
		$total_rounds = 0;
		$successfully_completed_rounds = 0;
		
		$syndi_round = 0;
		$syndi_win = 0;
		$syndi_halfwin = 0;
		$syndi_loss = 0;
		$syndi_win_nuke = 0;
		$syndi_halfwin_nuke_failed_evac = 0;
		$syndi_halfwin_blew_wrong_station = 0;
		$syndi_halfwin_blew_wrong_station_failed_evac = 0;
		$syndi_loss_syndicate_team_dead = 0;
		$syndi_loss_evac_disk_secure = 0;
		$syndi_loss_evac_disk_not_secure = 0;
		$syndi_halfwin_detonation_averted = 0;
		$syndi_dur = 0;
		$syndi_dur_max = 0;
		$syndi_dur_min = 1000000;
		$rev_round = 0;
		$rev_win = 0;
		$rev_dur = 0;
		$rev_dur_max = 0;
		$rev_dur_min = 1000000;
		$wiz_round = 0;
		$wiz_loss = 0;
		$wiz_dur = 0;
		$wiz_dur_max = 0;
		$wiz_dur_min = 1000000;
		$cult_round = 0;
		$cult_win = 0;
		$cult_loss = 0;
		$cult_dur = 0;
		$cult_escapees = 0;
		$cult_escapees_max = 0;
		$cult_dur_max = 0;
		$cult_dur_min = 1000000;
		$malf_round = 0;
		$malf_win = 0;
		$malf_loss = 0;
		$malf_dur = 0;
		$malf_dur_max = 0;
		$malf_dur_min = 1000000;
		$blob_round = 0;
		$blob_win = 0;
		$blob_halfwin = 0;
		$blob_loss = 0;
		$blob_dur = 0;
		$blob_dur_max = 0;
		$blob_dur_min = 1000000;
		$traitor_round = 0;
		$traitor_dur = 0;
		$traitor_dur_max = 0;
		$traitor_dur_min = 1000000;
		$chan_round = 0;
		$chan_dur = 0;
		$chan_dur_max = 0;
		$chan_dur_min = 1000000;
		$trch_round = 0;
		$trch_dur = 0;
		$trch_dur_max = 0;
		$trch_dur_min = 1000000;
		$ext_round = 0;
		$ext_dur = 0;
		$ext_dur_max = 0;
		$ext_dur_min = 1000000;
		$meteor_round = 0;
		$meteor_dur = 0;
		$meteor_dur_max = 0;
		$meteor_dur_min = 1000000;
		$snd_round = 0;
		$snd_dur = 0;
		$snd_dur_max = 0;
		$snd_dur_min = 1000000;
		
		$mining_iron = 0;
		$mining_steel = 0;
		$mining_glass = 0;
		$mining_rglass = 0;
		$mining_gold = 0;
		$mining_silver = 0;
		$mining_diamond = 0;
		$mining_uranium = 0;
		$mining_plasma = 0;
		$mining_clown = 0;
		$mining_adamantine = 0;
		
		$alert_comms_green = 0;
		$alert_comms_blue = 0;
		$alert_keycard_auth_red = 0;
		
		$escaped_on_shuttle = 0;
		$escaped_on_pod_1 = 0;
		$escaped_on_pod_2 = 0;
		$escaped_on_pod_3 = 0;
		$escaped_on_pod_5 = 0;
		$escaped_on_pod_max = 0;
		
		$end_proper_nuke = 0;
		
		$mecha_ripley_created = 0;
		$mecha_odysseus_created = 0;
		$mecha_gygax_created = 0;
		$mecha_firefighter_created = 0;
		$mecha_honker_created = 0;
		$mecha_durand_created = 0;
		
		$ban_job = 0;
		$ban_tmp = 0;
		$ban_tmp_time = 0;
		$ban_perma = 0;
		$ban_edit = 0;
		$ban_unban = 0; 
		$ban_job_unban = 0;
		$admin_secrets = array( );
		
		$slimes_stats = array();
		
		$traitor_uplink_items_total = array();
		
		$traitor_uplink_items_traitor = array();
		
		$traitor_uplink_items_trch = array();
		
		$traitor_uplink_items_nuke = array();
		
		$traitor_uplink_items_rev = array();
		
		$food_made = Array();
		$objects_made = Array();
		$assembly_made = Array();
		$events_ran = Array();
		$food_harvested = Array();
		$mining_pick_usage = Array();
		$minerals_mined = Array();
		$combat_items = Array();
		$combat_target_zone = Array();
		$mobs_killed_mining = Array();
		$cell_used = Array();
		$gun_fired = Array();
		$data = Array();
		 
		//Cost of stuff in telecrystals, the ID defined stuff remains for legacy purposes - so old logs work.
		$traitor_uplink_items_cost = array(
			"RE" => 6,
			"RA" => 2,
			"XB" => 5,
			"EM" => 4,
			"VC" => 4,
			"CJ" => 3,
			"SH" => 3,
			"AC" => 3,
			"EC" => 3,
			"FI" => 3,
			"UI" => 10,
			"PP" => 3,
			"CP" => 4,
			"CD" => 4,
			"ES" => 4,
			"C4" => 2,
			"PS" => 5,
			"DC" => 3,
			"SS" => 3,
			"AI" => 7,
			"BT" => 3,
			"SB" => 7,
			"ST" => 1,
			"SP" => 1,
			"BS" => 10,
			"BU" => 10,
			"TP" => 20,
			"TM" => 3,
			"RN" => 0,
			
			
			"/obj/item/weapon/gun/projectile/revolver" => 6,
			"/obj/item/weapon/gun/projectile/automatic/c20r" => 7,
			"/obj/item/weapon/gun/projectile/automatic/l6_saw" => 20,
			"/obj/item/weapon/gun/energy/crossbow" => 5,
			"/obj/item/weapon/flamethrower/full/tank" => 5,
			"/obj/item/weapon/melee/energy/sword" => 4,
			"/obj/item/weapon/storage/box/syndie_kit/emp" => 3,
			"/obj/item/weapon/grenade/syndieminibomb" => 3,
			"/obj/item/weapon/grenade/spawnergrenade/manhacks" => 4,
			"/obj/item/weapon/reagent_containers/spray/chemsprayer/bioterror" => 10,
			"/obj/mecha/combat/gygax/dark/loaded" => 45,
			"/obj/mecha/combat/marauder/mauler/loaded" => 70,
			"/obj/item/ammo_box/a357" => 2,
			"/obj/item/ammo_box/magazine/m12mm" => 1,
			"/obj/item/ammo_box/magazine/m10mm" => 2,
			"/obj/item/ammo_box/magazine/m762" => 6,
			"/obj/item/weapon/pen/paralysis" => 3,
			"/obj/item/weapon/soap/syndie" => 1,
			"/obj/item/weapon/cartridge/syndicate" => 3,
			"/obj/item/weapon/silencer" => 2,
			"/obj/item/clothing/under/chameleon" => 3,
			"/obj/item/weapon/stamp/chameleon" => 1,
			"/obj/item/clothing/shoes/syndigaloshes" => 2,
			"/obj/item/weapon/card/id/syndicate" => 2,
			"/obj/item/clothing/mask/gas/voice" => 4,
			"/obj/item/device/chameleon" => 4,
			"/obj/item/device/camera_bug" => 2,
			"/obj/item/weapon/card/emag" => 3,
			"/obj/item/weapon/storage/toolbox/syndicate" => 1,
			"/obj/item/weapon/storage/firstaid/tactical" => 5,
			"/obj/item/weapon/storage/box/syndie_kit/space" => 3,
			"/obj/item/clothing/glasses/thermal/syndi" => 3,
			"/obj/item/device/encryptionkey/binary" => 3,
			"/obj/item/device/multitool/ai_detect" => 1,
			"/obj/item/weapon/aiModule/syndicate" => 7,
			"/obj/item/weapon/plastique" => 2,
			"/obj/item/device/powersink" => 5,
			"/obj/item/device/sbeacondrop" => 7,
			"/obj/item/device/sbeacondrop/bomb" => 5,
			"/obj/item/device/syndicatedetonator" => 1,
			"/obj/item/weapon/circuitboard/teleporter" => 20,
			"/obj/item/weapon/shield/energy" => 8,
			"/obj/item/weapon/storage/box/syndie_kit/imp_freedom" => 3,
			"/obj/item/weapon/storage/box/syndie_kit/imp_uplink" => 10,
			"/obj/item/weapon/storage/box/syndie_kit/imp_adrenal" => 4,
			"/obj/item/weapon/storage/box/syndicate" => 10,
			"/obj/item/toy/syndicateballoon" => 10
		);	//NOTE: Random item is handled sepparately and uses the "RN" id.
		
		
		/* Mooooan, can't be bothered - this is to translate from type path to ID, used for image file and probably item name.
		$traitor_uplink_items_typepath_to_item_id = Array(
			"/obj/item/weapon/gun/projectile/revolver" => "RE",
			"/obj/item/weapon/gun/projectile/automatic/c20r" => "C2",
			"/obj/item/weapon/gun/projectile/automatic/l6_saw" => "L6",
			"/obj/item/weapon/gun/energy/crossbow" => "CB",
			"/obj/item/weapon/flamethrower/full/tank" => "TA",
			"/obj/item/weapon/melee/energy/sword" => "SW",
			"/obj/item/weapon/storage/box/syndie_kit/emp" => "EM",
			"/obj/item/weapon/grenade/syndieminibomb" => "SB",
			"/obj/item/weapon/grenade/spawnergrenade/manhacks" => "SG",
			"/obj/item/weapon/reagent_containers/spray/chemsprayer/bioterror" => "BT",
			"/obj/mecha/combat/gygax/dark/loaded" => "",
			"/obj/mecha/combat/marauder/mauler/loaded" => "",
			"/obj/item/ammo_box/a357" => "",
			"/obj/item/ammo_box/magazine/m12mm" => "",
			"/obj/item/ammo_box/magazine/m10mm" => "",
			"/obj/item/ammo_box/magazine/m762" => "",
			"/obj/item/weapon/pen/paralysis" => "",
			"/obj/item/weapon/soap/syndie" => "",
			"/obj/item/weapon/cartridge/syndicate" => "",
			"/obj/item/weapon/silencer" => "",
			"/obj/item/clothing/under/chameleon" => "",
			"/obj/item/weapon/stamp/chameleon" => "",
			"/obj/item/clothing/shoes/syndigaloshes" => "",
			"/obj/item/weapon/card/id/syndicate" => "",
			"/obj/item/clothing/mask/gas/voice" => "",
			"/obj/item/device/chameleon" => "",
			"/obj/item/device/camera_bug" => "",
			"/obj/item/weapon/card/emag" => "",
			"/obj/item/weapon/storage/toolbox/syndicate" => "",
			"/obj/item/weapon/storage/firstaid/tactical" => "",
			"/obj/item/weapon/storage/box/syndie_kit/space" => v,
			"/obj/item/clothing/glasses/thermal/syndi" => "",
			"/obj/item/device/encryptionkey/binary" => "",
			"/obj/item/device/multitool/ai_detect" => "",
			"/obj/item/weapon/aiModule/syndicate" => "",
			"/obj/item/weapon/plastique" => "",
			"/obj/item/device/powersink" => "",
			"/obj/item/device/sbeacondrop" => "",
			"/obj/item/device/sbeacondrop/bomb" => "",
			"/obj/item/device/syndicatedetonator" => "",
			"/obj/item/weapon/circuitboard/teleporter" => "",
			"/obj/item/weapon/shield/energy" => "",
			"/obj/item/weapon/storage/box/syndie_kit/imp_freedom" => "",
			"/obj/item/weapon/storage/box/syndie_kit/imp_uplink" => "",
			"/obj/item/weapon/storage/box/syndie_kit/imp_adrenal" => "",
			"/obj/item/weapon/storage/box/syndicate" => "",
			"/obj/item/toy/syndicateballoon" => "",
			"/obj/item/weapon/storage/box/syndicate" => ""
		);*/
		
		$wizard_spell_learned_total = array();
		
		$wizard_spell_learned_actual = array();
		
		$changeling_powers = array();
		
		$cyborg_stats = array(
			"frames_built" => 0,
			"mmis_filled" => 0,
			"birth" => 0,
			"standard" => 0,
			"engineering" => 0,
			"service" => 0,
			"miner" => 0,
			"medical" => 0,
			"security" => 0,
			"janitor" => 0,
			"ais_created" => 0
		);
		$cyborg_stats_module_names = array(
			"standard",
			"engineering",
			"service",
			"miner",
			"medical",
			"security",
			"janitor"
		);
		$cyborg_stats_max = array(
			"frames_built" => 0,
			"mmis_filled" => 0,
			"birth" => 0,
			"standard" => 0,
			"engineering" => 0,
			"service" => 0,
			"miner" => 0,
			"medical" => 0,
			"security" => 0,
			"janitor" => 0,
			"ais_created" => 0
		);
		$radio_usage = array(
			"COM" => 0,
			"SCI" => 0,
			"HEA" => 0,
			"MED" => 0,
			"ENG" => 0,
			"SEC" => 0,
			"SRV" => 0,
			"DTH" => 0,
			"SYN" => 0,
			"CAR" => 0,
			"OTH" => 0,
			"PDA" => 0,
			"RC" => 0,
			"NC" => 0,
		);
			//"MIN" => 0,
		$radio_usage_department = array(
			"SCI",
			"HEA",
			"MED",
			"ENG",
			"SEC",
			"SRV",
			"MIN",
			"CAR"
		);
		
		//$antagonist_objective_success[$objective_type][$antag_type][$success]
		
		$uptime = 0;
		$uptime_minutes = 0;
		$hours_played = 0;
		$clients = 0;
		$round_start = "";
		$round_end = "";
		for($i = 0; $i < 100; $i++){
			$round_end_clients_num[$i] = 0;
		}
		for($i = 1; $i <= 24; $i++){
			$round_end_clients_hour[$i] = 0;
		}
		for($i = 1; $i <= 24; $i++){
			$round_end_clients_hour_recordings[$i] = 0; //Marks how many values were actually noted into each hour.
		}
		for($i = 1; $i <= 10; $i++){
			$rounds_survival_percentage[$i] = 0;
			$rounds_escapee_percentage[$i] = 0;
			$rounds_escaped_survivor_percentage[$i] = 0;
			for($j = 1; $j <= 10; $j++){
				$rounds_survival_matrix[$i][$j] = 0;
			}
			$rounds_rev_bypop_success[$i] = 0;
			$rounds_rev_bypop_fail[$i] = 0;
			$rounds_rev_bypop_failedround[$i] = 0;
			$rounds_cult_bypop_success[$i] = 0;
			$rounds_cult_bypop_fail[$i] = 0;
			$rounds_cult_bypop_failedround[$i] = 0;
			$rounds_malf_bypop_success[$i] = 0;
			$rounds_malf_bypop_fail[$i] = 0;
			$rounds_malf_bypop_failedround[$i] = 0;
			$rounds_nuke_bypop_success[$i] = 0;
			$rounds_nuke_bypop_halfwin[$i] = 0;
			$rounds_nuke_bypop_fail[$i] = 0;
			$rounds_nuke_bypop_failedround[$i] = 0;
			$rounds_nuke_bydur_success[$i] = 0;
			$rounds_nuke_bydur_halfwin[$i] = 0;
			$rounds_nuke_bydur_fail[$i] = 0;
			$rounds_nuke_bydur_failedround[$i] = 0;
		}
		
		$round_startends = Array();
		
		$wizard_spell_learned_sum = 0;
		$wizard_spell_learned_actual_sum = 0;
		
		$duration_in_minutes_max = 0;
		
		$handcuffs = 0;
		$cablecuffs = 0;
		$beartraps = 0;
		
		$chemical_reactions = Array();
		
		$newscaster_stories = 0;
		$newscaster_channels = 0;
		$newscaster_newspapers_printed = 0;
		
		/*end data mining vars*/
		
		while($i = mysql_fetch_array($d)){	
			//In this section, data is aggregated. This section is really, really messy.
			//Think of it as working like a virtual machine, where the feedback variable name
			//is the command and the variable value and details parameters are the "function"'s parameters.
			//In short, this section outgrew its simple coding approach and needs rewriting. Then again,
			//rewriting it will likely break the output section too.
		
			/*BEGIN DATA MINING*/
			
				//round statistics
			
			//tmp
			if( $i["var_name"] == "game_mode"){
				$tmp_game_mode = $i["details"];
			}
			
			//Round success (non-objective rounds)
			if( $i["var_name"] == "round_end_result"){
				if($tmp_game_mode != ""){
					$result = $i["details"];
					
					$ending = "";
					if(startsWith($result,"win")){
						$ending = "win";
					}
					if(startsWith($result,"loss")){
						$ending = "loss";
					}
					if(startsWith($result,"halfwin")){
						$ending = "halfwin";
					}
					
					if(!isset($round_success_rating[$tmp_game_mode]["RESULT"][$result])){
						$round_success_rating[$tmp_game_mode]["RESULT"][$result] = 1;
					}else{
						$round_success_rating[$tmp_game_mode]["RESULT"][$result]++;
					}
					if($ending != ""){
						$tmp_round_end_result = $ending;
						if(!isset($round_success_rating[$tmp_game_mode]["RESULTSUM"][$ending])){
							$round_success_rating[$tmp_game_mode]["RESULTSUM"][$ending] = 1;
						}else{
							$round_success_rating[$tmp_game_mode]["RESULTSUM"][$ending]++;
						}
						if($total_applied == 0){
							$total_applied = 1;
							if(!isset($round_success_rating[$tmp_game_mode]["TOTAL"]["total"])){
								$round_success_rating[$tmp_game_mode]["TOTAL"]["total"] = 1;
							}else{
								$round_success_rating[$tmp_game_mode]["TOTAL"]["total"]++;
							}
						}
					}
				}
			}
			if( $i["var_name"] == "end_proper"){
				if($tmp_game_mode != ""){
					$end_proper = $i["details"];
					if(!isset($round_success_rating[$tmp_game_mode]["PROPER"][$end_proper])){
						$round_success_rating[$tmp_game_mode]["PROPER"][$end_proper] = 1;
					}else{
						$round_success_rating[$tmp_game_mode]["PROPER"][$end_proper]++;
					}
					if($total_applied == 0){
						$total_applied = 1;
						if(!isset($round_success_rating[$tmp_game_mode]["TOTAL"]["total"])){
							$round_success_rating[$tmp_game_mode]["TOTAL"]["total"] = 1;
						}else{
							$round_success_rating[$tmp_game_mode]["TOTAL"]["total"]++;
						}
					}
				}
			}
			if( $i["var_name"] == "end_error"){
				if($tmp_game_mode != ""){
					$end_error = $i["details"];
					if(strpos($end_error,"admin reboot") >= 0){
						$end_error = "admin reboot";
					}
					if(!isset($round_success_rating[$tmp_game_mode]["ERROR"][$end_error])){
						$round_success_rating[$tmp_game_mode]["ERROR"][$end_error] = 1;
					}else{
						$round_success_rating[$tmp_game_mode]["ERROR"][$end_error]++;
					}
					if($total_applied == 0){
						if($tmp_game_mode == "traitor+changeling"){ print "(error) skipped round ".$i["round_id"]."."; }
						$total_applied = 1;
						if(!isset($round_success_rating[$tmp_game_mode]["TOTAL"]["total"])){
							$round_success_rating[$tmp_game_mode]["TOTAL"]["total"] = 1;
						}else{
							$round_success_rating[$tmp_game_mode]["TOTAL"]["total"]++;
						}
					}
				}
			}
			if( endsWith($i["var_name"], "_objective")){
				if($tmp_game_mode != ""){
					$antagtype_list = explode("_",$i["var_name"]);
					$antagtype = $antagtype_list[0];
					
					$objective_list = explode(" ",$i["details"]);
					foreach($objective_list as $objective_pair){
						$objective_pair_list = explode("|",$objective_pair);
						$objective = $objective_pair_list[0];
						$success = $objective_pair_list[1];
						if(isset($round_success_rating[$tmp_game_mode]["OBJECTIVE"][$antagtype][$objective][$success])){
							$round_success_rating[$tmp_game_mode]["OBJECTIVE"][$antagtype][$objective][$success]++;
						}else{
							$round_success_rating[$tmp_game_mode]["OBJECTIVE"][$antagtype][$objective][$success] = 1;
						}
					}
				}
			}
			if( endsWith($i["var_name"], "_success")){
				if($tmp_game_mode != ""){
					$antagtype_list = explode("_",$i["var_name"]);
					$antagtype = $antagtype_list[0];
					
					$success_list = explode(" ",$i["details"]);
					foreach($success_list as $success){
						if(isset($round_success_rating[$tmp_game_mode]["ANTAG"][$antagtype][$success])){
							$round_success_rating[$tmp_game_mode]["ANTAG"][$antagtype][$success]++;
						}else{
							$round_success_rating[$tmp_game_mode]["ANTAG"][$antagtype][$success] = 1;
						}
					}
				}
			}
			
			//end Round success (non-objective rounds)
			
			//Slimes
				//birth
			if($i["var_name"] == "slime_babies_born"){
				$slime_all = explode(" ",$i["details"]);
				foreach($slime_all as $slime){
					if(!isset($slimes_stats["birth"])){
						$slimes_stats["birth"] = array();
					}
					if(!isset($slimes_stats["birth"][$slime])){
						$slimes_stats["birth"][$slime] = 0;
					}
					$slimes_stats["birth"][$slime]++;
				}
			}
				//harvested
			if($i["var_name"] == "slime_core_harvested"){
				$slime_all = explode(" ",$i["details"]);
				foreach($slime_all as $slime){
					if(!isset($slimes_stats["harvested"])){
						$slimes_stats["harvested"] = array();
					}
					if(!isset($slimes_stats["harvested"][$slime])){
						$slimes_stats["harvested"][$slime] = 0;
					}
					$slimes_stats["harvested"][$slime]++;
				}
			}
				//used
			if($i["var_name"] == "slime_cores_used"){
				$slime_all = explode(" ",$i["details"]);
				foreach($slime_all as $slime){
					if(!isset($slimes_stats["used"])){
						$slimes_stats["used"] = array();
					}
					if(!isset($slimes_stats["used"][$slime])){
						$slimes_stats["used"][$slime] = 0;
					}
					$slimes_stats["used"][$slime]++;
				}
			}
			
			//end slimes
			
			//job preferences at round start
			
			if($i["var_name"] == "job_preferences"){
				$job_descriptor_all = explode("-",$i["details"]);
				foreach($job_descriptor_all as $job_descriptor){
					$job_descriptor_list = explode("|",$job_descriptor);
					$archive_job = $job;
					$job = "";
					$add = 0;
					foreach($job_descriptor_list as $element){
						$element = trim($element," |-");;
						if($element == ""){continue;}
						
						/*
						//I made it log wrongly at one point, so this has to be here ;-;
						if(startsWith($element,"|YOUNG=")){
							$job = $archive_job;
							$element_list = explode("=",$element);
							$add = intval($element_list[1]);
							if(isset($job_popularity[$job]["YOUNG"])){
								$job_popularity[$job]["YOUNG"]+=$add;
							}else{
								$job_popularity[$job]["YOUNG"]=$add;
							}
						}*/
						
						$preftype = "";
						if($job == ""){$job = $element;continue;}
						if(startsWith($element,"HIGH")){
							$element_list = explode("=",$element);
							$preftype = $element_list[0];
							$add = intval($element_list[1]);
						}
						if(startsWith($element,"MEDIUM")){
							$element_list = explode("=",$element);
							$preftype = $element_list[0];
							$add = intval($element_list[1]);
						}
						if(startsWith($element,"LOW")){
							$element_list = explode("=",$element);
							$preftype = $element_list[0];
							$add = intval($element_list[1]);
						}
						if(startsWith($element,"NEVER")){
							$element_list = explode("=",$element);
							$preftype = $element_list[0];
							$add = intval($element_list[1]);
						}
						if(startsWith($element,"BANNED")){
							$element_list = explode("=",$element);
							$preftype = $element_list[0];
							$add = intval($element_list[1]);
						}
						if(startsWith($element,"YOUNG")){
							$element_list = explode("=",$element);
							$preftype = $element_list[0];
							$add = intval($element_list[1]);
						}
						if(isset($job_popularity[$job][$preftype])){
							$job_popularity[$job][$preftype]+=$add;
						}else{
							$job_popularity[$job][$preftype]=$add;
						}
						$add = 0;
					}
				}
			}
			
			//end job preferences at round start
			
			
			
			//nuke
			if( $i["var_name"] == "game_mode" && $i["details"] == "nuclear emergency"){
				$syndi_round += 1;
			}
			
			if(  $i["var_name"] == "round_end_result" ){
				$round_end_result = $i["details"];
			}
			
			if( $i["var_name"] == "round_end_result" && $i["details"] == "win - syndicate nuke"){
				$syndi_win += 1;
				
				switch($i["details"]){
					case "win - syndicate nuke":
						$syndi_win_nuke++;
					break;
				}
			}
			if( $i["var_name"] == "round_end_result" && ( 	$i["details"] == "halfwin - detonation averted" || 
															$i["details"] == "halfwin - interrupted" || 
															$i["details"] == "halfwin - syndicate nuke - did not evacuate in time" || 
															$i["details"] == "halfwin - blew wrong station - did not evacuate in time" || 
															$i["details"] == "halfwin - blew wrong station" )){
				
				switch($i["details"]){
					case "halfwin - detonation averted":
						$syndi_halfwin_detonation_averted++;
					break;
					case "halfwin - syndicate nuke - did not evacuate in time":
						$syndi_halfwin_nuke_failed_evac++;
					break;
					case "halfwin - blew wrong station - did not evacuate in time":
						$syndi_halfwin_blew_wrong_station_failed_evac++;
					break;
					case "halfwin - blew wrong station":
						$syndi_halfwin_blew_wrong_station++;
					break;
				}
				
				$syndi_halfwin += 1;
			}
			
			if( $i["var_name"] == "round_end_result" && StartsWith($i["details"],"loss - evacuation - disk") ){
				$syndi_loss += 1;
				
				switch($i["details"]){
					case "loss - evacuation - disk secured - syndi team dead":
						$syndi_loss_syndicate_team_dead++;
					break;
					case "loss - evacuation - disk secured":
						$syndi_loss_evac_disk_secure++;
					break;
					case "loss - evacuation - disk not secured":
						$syndi_loss_evac_disk_not_secure++;
					break;
				}
			}		

			//traitor
			
			if( $i["var_name"] == "game_mode" && $i["details"] == "traitor"){
				$traitor_round += 1;
			}	

			//changeling
			
			if( $i["var_name"] == "game_mode" && $i["details"] == "changeling"){
				$chan_round += 1;
			}	

			//traitor+changeling
			
			if( $i["var_name"] == "game_mode" && $i["details"] == "traitor+changeling"){
				$trch_round += 1;
			}

			//extended
			
			if( $i["var_name"] == "game_mode" && $i["details"] == "extended"){
				$ext_round += 1;
			}

			//meteor
			
			if( $i["var_name"] == "game_mode" && $i["details"] == "meteor"){
				$meteor_round += 1;
			}

			//sandbox
			
			if( $i["var_name"] == "game_mode" && $i["details"] == "sandbox"){
				$snd_round += 1;
			}
			
			//rev
			if( $i["var_name"] == "game_mode" && $i["details"] == "revolution"){
				$rev_round += 1;
			}
			if( $i["var_name"] == "round_end_result" && $i["details"] == "win - heads killed"){
				$rev_win += 1;
			}
			
			//wizard
			if( $i["var_name"] == "game_mode" && $i["details"] == "wizard"){
				$wiz_round += 1;
			}
			if( $i["var_name"] == "round_end_result" && $i["details"] == "loss - wizard killed"){
				$wiz_loss += 1;
			}
			
			//cult
			if( $i["var_name"] == "game_mode" && $i["details"] == "cult"){
				$cult_round += 1;
			}
			if( $i["var_name"] == "round_end_result" && $i["details"] == "loss - staff stopped the cult"){
				$cult_loss += 1;
				$cult_escapees += $i["var_value"];
				if($cult_escapees_max < $i["var_value"]){
					$cult_escapees_max = $i["var_value"];
				}
			}
			if( $i["var_name"] == "round_end_result" && $i["details"] == "win - cult win"){
				$cult_win += 1;
				$cult_escapees += $i["var_value"];
				if($cult_escapees_max < $i["var_value"]){
					$cult_escapees_max = $i["var_value"];
				}
			}
			
			//malf
			if( $i["var_name"] == "game_mode" && $i["details"] == "AI malfunction"){
				$malf_round += 1;
			}
			if( $i["var_name"] == "round_end_result" && $i["details"] == "loss - staff win"){
				$malf_loss += 1;
			}
			if( $i["var_name"] == "round_end_result" && StartsWith($i["details"],"win - AI win")){
				$malf_win += 1;
			}
			
			//blob
			if( $i["var_name"] == "game_mode" && $i["details"] == "blob"){
				$blob_round += 1;
			}
			if( $i["var_name"] == "round_end_result" && $i["details"] == "win - blob eliminated"){
				$blob_win += 1;
			}
			if( $i["var_name"] == "round_end_result" && $i["details"] == "halfwin - nuke"){
				$blob_halfwin += 1;
			}
			if( $i["var_name"] == "round_end_result" && StartsWith($i["details"],"loss - blob took over")){
				$blob_loss += 1;
			}
			
				//end of round statistics
			
				//traitor changeling wizard success rates
				
			if( endsWith($i["var_name"], "_success")){
				$name_list = explode("_",$i["var_name"]);
				
				$mode = $name_list[0];
				
				$success_list = explode(" ",$i["details"]);
				
				foreach($success_list as $success){
					if(!isset($antagonistsuccess[$mode][$success])){
						$antagonistsuccess[$mode][$success] = 1;
					}else{
						$antagonistsuccess[$mode][$success]++;
					}
				}
				
			}
				
				//end traitor changeling wizard success rates
			
				//mining statistics
				
			if( $i["var_name"] == "mining_iron_produced"){
				$mining_iron += $i["var_value"];
			}
			if( $i["var_name"] == "mining_steel_produced"){
				$mining_steel += $i["var_value"];
			}
			if( $i["var_name"] == "mining_glass_produced"){
				$mining_glass += $i["var_value"];
			}
			if( $i["var_name"] == "mining_rglass_produced"){
				$mining_rglass += $i["var_value"];
			}
			if( $i["var_name"] == "mining_gold_produced"){
				$mining_gold += $i["var_value"];
			}
			if( $i["var_name"] == "mining_silver_produced"){
				$mining_silver += $i["var_value"];
			}
			if( $i["var_name"] == "mining_diamond_produced"){
				$mining_diamond += $i["var_value"];
			}
			if( $i["var_name"] == "mining_uranium_produced"){
				$mining_uranium += $i["var_value"];
			}
			if( $i["var_name"] == "mining_plasma_produced"){
				$mining_plasma += $i["var_value"];
			}
			if( $i["var_name"] == "mining_clown_produced"){
				$mining_clown += $i["var_value"];
			}
			if( $i["var_name"] == "mining_adamantine_produced"){
				$mining_adamantine += $i["var_value"];
			}
				
				//end mining statistics
			
				//mecha statistics
				
			if( startsWith($i["var_name"], "mecha_")){
				$mecha_tab = explode("_",$i["var_name"]);
				$mecha = $mecha_tab[1];
				if(!isset($mecha_stats[$mecha])){
					$mecha_stats[$mecha] = $i["var_value"];
				}else{
					$mecha_stats[$mecha] += $i["var_value"];
				}
			}
			if($i["var_name"] == "supply_mech_collection_redeemed"){
				if(!isset($mecha_stats["marauder"])){
					$mecha_stats["marauder"] = $i["var_value"];
				}else{
					$mecha_stats["marauder"] += $i["var_value"];
				}
			}
			
				//end mecha statistics
			
				//sec levels
				
			if( $i["var_name"] == "alert_comms_green"){
				$alert_comms_green += $i["var_value"];
			}
			if( $i["var_name"] == "alert_comms_blue"){
				$alert_comms_blue += $i["var_value"];
			}
			if( $i["var_name"] == "alert_keycard_auth_red"){
				$alert_keycard_auth_red += $i["var_value"];
			}
			
				//end sec levels
			
				//radio usage
				
			if( $i["var_name"] == "radio_usage"){
				$complete_str = $i["details"];
				$pairs_tab = explode(" ",$complete_str);
				foreach($pairs_tab as $pair){
					$pair_tab = explode("-",$pair);
					$channel = $pair_tab[0];
					$messages = $pair_tab[1];
					$radio_usage[$channel] += intval($messages);
				}
			}
			
			if( $i["var_name"] == "newscaster_stories"){
				$channel = "NC";
				$messages = intval($i["var_value"]);
				$newscaster_stories += $messages;
				if( isset($radio_usage[$channel]) ){
					$radio_usage[$channel] += $messages;
				}else{
					$radio_usage[$channel] = $messages;
				}
			}
			
			if( $i["var_name"] == "newscaster_channels"){
				$messages = intval($i["var_value"]);
				$newscaster_channels += $messages;
			}
			
			if( $i["var_name"] == "newscaster_newspapers_printed"){
				$messages = intval($i["var_value"]);
				$newscaster_newspapers_printed += $messages;
			}
			
				//end radio usage
			
				//antag objectives
				
			if( ($i["var_name"] == "traitor_objective") || 
				($i["var_name"] == "wizard_objective") ||
				($i["var_name"] == "changeling_objective") ){
				
				$index = strpos($i["var_name"], "_");
				
				$antag_type = substr($i["var_name"],0,$index);
				
				$objective_combo = explode(" ",$i["details"]);
				
				foreach($objective_combo as $objective_pair){
					$objective_tab = explode("|",$objective_pair);
					$objective_type = $objective_tab[0];
					$objective_success = $objective_tab[1];
					
					if( !isset($antagonist_objective_success[$objective_type][$antag_type][$objective_success]) ){
						$antagonist_objective_success[$objective_type][$antag_type][$objective_success] = 1;
					}else{
						$antagonist_objective_success[$objective_type][$antag_type][$objective_success]++;
					}
				}
			}
			
				
				//end antag objectives
			
				//escapes
				
			if( $i["var_name"] == "escaped_on_shuttle"){
				$escaped_on_shuttle += $i["var_value"];
			}
			if( $i["var_name"] == "escaped_on_pod_1"){
				$escaped_on_pod_1 += $i["var_value"];
				if($i["var_value"] > $escaped_on_pod_max)
					$escaped_on_pod_max = $i["var_value"];
			}
			if( $i["var_name"] == "escaped_on_pod_2"){
				$escaped_on_pod_2 += $i["var_value"];
				if($i["var_value"] > $escaped_on_pod_max)
					$escaped_on_pod_max = $i["var_value"];
			}
			if( $i["var_name"] == "escaped_on_pod_3"){
				$escaped_on_pod_3 += $i["var_value"];
				if($i["var_value"] > $escaped_on_pod_max)
					$escaped_on_pod_max = $i["var_value"];
			}
			if( $i["var_name"] == "escaped_on_pod_5"){
				$escaped_on_pod_5 += $i["var_value"];
				if($i["var_value"] > $escaped_on_pod_max)
					$escaped_on_pod_max = $i["var_value"];
			}
			if( $i["var_name"] == "end_proper" && $i["details"] == "nuke" ){
				$end_proper_nuke += 1;
			}
			
				//end escapes
			
				//religions
				
			if( $i["var_name"] == "religion_name"){
				$religion = $i["details"];
				if(!isset($religion_name_list[$religion])){
					$religion_name_list[$religion] = 1;
				}else{
					$religion_name_list[$religion]++;
				}
			}
				
			if( $i["var_name"] == "religion_deity"){
				$deity = $i["details"];
				if(!isset($religion_deity_list[$deity])){
					$religion_deity_list[$deity] = 1;
				}else{
					$religion_deity_list[$deity]++;
				}
			}
				
			if( $i["var_name"] == "religion_book"){
				$book = $i["details"];
				if(!isset($religion_book_list[$book])){
					$religion_book_list[$book] = 1;
				}else{
					$religion_book_list[$book]++;
				}
			}
				
			if( $i["var_name"] == "chaplain_weapon"){
				$chaplain_weapons = $i["details"];
				if(!isset($religion_name_list[$religion])){
					$chaplain_weapons_list[$chaplain_weapons] = 1;
				}else{
					$chaplain_weapons_list[$chaplain_weapons]++;
				}
			}
				
				//end religions
				
				//changeling powers
				
			if( $i["var_name"] == "changeling_powers"){
				$string_list = $i["details"];
				$list = explode(" ",$string_list);
				foreach($list as $abb){
					$abb = trim($abb," ");
					if($abb == ""){continue;}
					if(!isset($changeling_powers[$abb])){
						$changeling_powers[$abb] = 1;
					}else{
						$changeling_powers[$abb]++;
					}
				}
			}
				
				//end changeling powers
			
				//bans + admin stats
				
			if( $i["var_name"] == "admin_verb"){
				$verbs = $i["details"];
				
				//print "<br>verbs = ".$verbs;
				
				$verbs_list = explode(" ",$verbs);
				
				foreach($verbs_list as $verb){
					$verb = trim($verb," ");
					if($verb == ""){continue;}
					if(!isset($admin_verb_use[$verb])){
						$admin_verb_use[$verb] = 1;
					}else{
						$admin_verb_use[$verb]++;
					}
				}
			}
				
			if( $i["var_name"] == "ban_job"){
				$ban_job += $i["var_value"];
				
				$ban_job_list = explode(" - ",$i["details"]);
				
				foreach($ban_job_list as $job){
					$job = trim($job," -");
					if($job == ""){continue;}
					if(!isset($jobban_popularity[$job])){
						$jobban_popularity[$job] = 1;
					}else{
						$jobban_popularity[$job]++;
					}
				}
			}
			
			if( $i["var_name"] == "ban_tmp"){
				$ban_tmp += $i["var_value"];
			}
			if( $i["var_name"] == "ban_tmp_mins"){
				$ban_tmp_time += $i["var_value"];
			}
			if( $i["var_name"] == "ban_perma"){
				$ban_perma += $i["var_value"];
			}
			if( $i["var_name"] == "ban_unban"){
				$ban_unban += $i["var_value"];
			}
			if( $i["var_name"] == "ban_job_unban"){
				$ban_job_unban += $i["var_value"];
				
				$ban_job_unban_list = explode(" - ",$i["details"]);
				
				foreach($ban_job_unban_list as $job){
					$job = trim($job," -");
					if($job == ""){continue;}
					if(!isset($jobban_unban_popularity[$job])){
						$jobban_unban_popularity[$job] = 1;
					}else{
						$jobban_unban_popularity[$job]++;
					}
				}
			}
			if( $i["var_name"] == "ban_edit"){
				$ban_edit += $i["var_value"];
			}
			if( $i["var_name"] == "admin_secrets_fun_used"){
				$tmp_string = $i["details"];
				$tmp_list = explode(" ", $tmp_string);
				$ignoretillbrackets = 0;
				foreach( $tmp_list as $s ){
					if($ignoretillbrackets == 1){
						if(endsWith($s,")")){
							$ignoretillbrackets = 0;
						}
						continue;
					}
					if(startsWith($s,"TA")){
						$s = "TA";
						$ignoretillbrackets = 1;
					}
					if(!isset($admin_secrets[$s])){
						$admin_secrets[$s] = 1;
					}else{
						$admin_secrets[$s]++;
					}
				}
			}
			if( $i["var_name"] == "admin_cookies_spawned"){
				$cookies = intval($i["var_value"]);
				if(!isset($admin_secrets["CK"])){
					$admin_secrets["CK"] = $cookies;
				}else{
					$admin_secrets["CK"] += $cookies;
				}
			}
				//end bans + admin stats
			
				//traitor items
			if( $i["var_name"] == "traitor_uplink_items_bought"){
				$tmp_string = $i["details"];
				$tmp_list = explode(" ", $tmp_string);
				foreach( $tmp_list as $s ){
					if( !isset($traitor_uplink_items_total[$s]) ){
						$traitor_uplink_items_total[$s] = 1;
					}else{
						$traitor_uplink_items_total[$s]++;
					}
				}
				switch($tmp_game_mode){
					case "traitor":
						foreach( $tmp_list as $s ){
							if( !isset($traitor_uplink_items_traitor[$s])){
								$traitor_uplink_items_traitor[$s] = 1;
							}else{
								$traitor_uplink_items_traitor[$s]++;
							}
						}
					break;
					case "revolution":
						foreach( $tmp_list as $s ){
							if( !isset($traitor_uplink_items_rev[$s])){
								$traitor_uplink_items_rev[$s] = 1;
							}else{
								$traitor_uplink_items_rev[$s]++;
							}
						}
					break;
					case "traitor+changeling":
						foreach( $tmp_list as $s ){
							if( !isset($traitor_uplink_items_trch[$s])){
								$traitor_uplink_items_trch[$s] = 1;
							}else{
								$traitor_uplink_items_trch[$s]++;
							}
						}
					break;
					case "nuclear emergency":
						foreach( $tmp_list as $s ){
							if( !isset($traitor_uplink_items_nuke[$s])){
								$traitor_uplink_items_nuke[$s] = 1;
							}else{
								$traitor_uplink_items_nuke[$s]++;
							}
						}
					break;
				}
			}
				//end traitor items
			
				//cyborgs
			
			if( StartsWith($i["var_name"],"cyborg_") ){
				$var_specific_name = substr($i["var_name"], 7);
				
				$cyborg_stats[$var_specific_name] += $i["var_value"];
				if($cyborg_stats_max[$var_specific_name] < $i["var_value"]){
					$cyborg_stats_max[$var_specific_name] = $i["var_value"];
				}
			}	
				//end cyborgs
			
				//spells
				
			if( $i["var_name"] == "wizard_spell_learned"){
				$tmp_string = $i["details"];
				$tmp_string_2 = substr($tmp_string, strrpos($tmp_string, "UM") );
				$tmp_list = explode(" ", $tmp_string);
				$tmp_list_2 = explode(" ", $tmp_string_2);
				foreach( $tmp_list as $s ){
					if(!isset($wizard_spell_learned_total[$s])){
						$wizard_spell_learned_total[$s] = 1;
					}else{
						$wizard_spell_learned_total[$s]++;
					}
					$wizard_spell_learned_sum++;
				}
				foreach( $tmp_list_2 as $s ){
					if(!isset($wizard_spell_learned_actual[$s])){
						$wizard_spell_learned_actual[$s] = 1;
					}else{
						$wizard_spell_learned_actual[$s]++;
					}
					$wizard_spell_learned_actual_sum++;
				}
			}
				
				//end spells
				
				//food made
			if( $i["var_name"] == "food_made" ){
				$foodList = explode(" ", $i["details"]);
				foreach($foodList as $i => $food){
					if(!isset($food_made[$food])){
						$food_made[$food] = 0;
					}
					$food_made[$food]++;
				}
			}
				//end food made
				
				//objects made
			if( $i["var_name"] == "object_crafted" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					if(!isset($objects_made[$entry])){
						$objects_made[$entry] = 0;
					}
					$objects_made[$entry]++;
				}
			}
				//end objects made
				
				//assemblies made
			if( $i["var_name"] == "assembly_made" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					if(!isset($assembly_made[$entry])){
						$assembly_made[$entry] = 0;
					}
					$assembly_made[$entry]++;
				}
			}
				//end assemblies made
				
				//events ran
			if( $i["var_name"] == "event_ran" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					if(!isset($events_ran[$entry])){
						$events_ran[$entry] = 0;
					}
					$events_ran[$entry]++;
				}
			}
				//end events ran made
				
				//food harvested
			if( $i["var_name"] == "food_harvested" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					$entryPair = explode("|", $entry);
					$name = $entryPair[0];
					$amount = intval($entryPair[1]);
					if(!isset($food_harvested[$name])){
						$food_harvested[$name]["TOTAL"] = 0;
					}
					$food_harvested[$name]["TOTAL"] += $amount;
					
					if(!isset($food_harvested[$name]["DISTRIBUTION"][$amount])){
						$food_harvested[$name]["DISTRIBUTION"][$amount] = 0;
					}
					$food_harvested[$name]["DISTRIBUTION"][$amount] += 1;
				}
			}
				//end food harvested
			
				//mining pick usage
			if( $i["var_name"] == "pick_used_mining" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					if(!isset($mining_pick_usage[$entry])){
						$mining_pick_usage[$entry] = 0;
					}
					$mining_pick_usage[$entry]++;
				}
			}
				//end mining pick usage
				
				//minerals mined
			if( $i["var_name"] == "ore_mined" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					$entryPair = explode("|", $entry);
					$name = $entryPair[0];
					$amount = intval($entryPair[1]);
					if(!isset($minerals_mined[$name])){
						$minerals_mined[$name]["TOTAL"] = 0;
					}
					$minerals_mined[$name]["TOTAL"] += $amount;
					
					if(!isset($minerals_mined[$name]["DISTRIBUTION"][$amount])){
						$minerals_mined[$name]["DISTRIBUTION"][$amount] = 0;
					}
					$minerals_mined[$name]["DISTRIBUTION"][$amount] += 1;
				}
			}
				//end minerals mined
				
				//start stuff from https://github.com/tgstation/-tg-station/pull/17233
				
			if( $i["var_name"] == "mining_equipment_bought" ){
				$val = $i["details"];
				//print "mining_equipment_bought: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $ii => $v){
					$valPair = explode("|", $v);
					if(count($valPair) == 2){
						$key = $valPair[0];
						$value = $valPair[1];
						
						if(!isset($data["mining_equipment_bought"][$key][$value])){
							$data["mining_equipment_bought"][$key][$value] = 0;
						}
						$data["mining_equipment_bought"][$key][$value]++;
						
						if(!isset($data["mining_equipment_bought_total"][$key])){
							$data["mining_equipment_bought_total"][$key] = 0;
						}
						$data["mining_equipment_bought_total"][$key]++;
						
					}else{
						print "<br>Failed to parse: $valPair";
					}
				}
				
			}
				
			if( $i["var_name"] == "mining_voucher_redeemed" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					if(!isset($data["mining_voucher_redeemed"][$v])){
						$data["mining_voucher_redeemed"][$v] = 0;
					}
					$data["mining_voucher_redeemed"][$v]++;
						
					if(!isset($data["mining_voucher_redeemed_total"])){
						$data["mining_voucher_redeemed_total"] = 0;
					}
					$data["mining_voucher_redeemed_total"]++;
				}
				
			}
				
			if( $i["var_name"] == "jaunter" ){
				$val = $i["details"];
				//print "jaunter: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					if(!isset($data["jaunter"][$v])){
						$data["jaunter"][$v] = 0;
					}
					$data["jaunter"][$v]++;
						
					if(!isset($data["jaunter_total"])){
						$data["jaunter_total"] = 0;
					}
					$data["jaunter_total"]++;
				}
				
			}
				
			if( $i["var_name"] == "hivelord_core" ){
				$val = $i["details"];
				//print "hivelord_core: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $ii => $v){
					$valArray = explode("|", $v);
					if(count($valArray) == 2 || count($valArray) == 3){
						$key = $valArray[0];
						$value = $valArray[1];
						if(count($valArray) == 3){
							$value = "".$valArray[1] . " " . $valArray[2];
						}
						
						if(!isset($data["hivelord_core"][$key][$value])){
							$data["hivelord_core"][$key][$value] = 0;
						}
						$data["hivelord_core"][$key][$value]++;
						
						if(!isset($data["hivelord_core_total"][$key])){
							$data["hivelord_core_total"][$key] = 0;
						}
						$data["hivelord_core_total"][$key]++;
						
					}else{
						print "<br>Failed to parse: $valArray";
					}
				}
			}
			
			if( $i["var_name"] == "engine_started" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $key => $entry){
					if(!isset($data["engine_started"][$entry])){
						$data["engine_started"][$entry] = 0;
					}
					$data["engine_started"][$entry]++;
					
					if(!isset($data["engine_started_total"])){
						$data["engine_started_total"] = 0;
					}
					$data["engine_started_total"]++;
				}
			}
			
			if( $i["var_name"] == "wisp_lantern" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $key => $entry){
					if(!isset($data["wisp_lantern"][$entry])){
						$data["wisp_lantern"][$entry] = 0;
					}
					$data["wisp_lantern"][$entry]++;
					
					if(!isset($data["wisp_lantern_total"])){
						$data["wisp_lantern_total"] = 0;
					}
					$data["wisp_lantern_total"]++;
				}
			}
			
			if( $i["var_name"] == "immortality_talisman" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $key => $entry){
					if(!isset($data["immortality_talisman"][$entry])){
						$data["immortality_talisman"][$entry] = 0;
					}
					$data["immortality_talisman"][$entry]++;
					
					if(!isset($data["immortality_talisman_total"])){
						$data["immortality_talisman_total"] = 0;
					}
					$data["immortality_talisman_total"]++;
				}
			}
			
			if( $i["var_name"] == "warp_cube" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $key => $entry){
					if(!isset($data["warp_cube"][$entry])){
						$data["warp_cube"][$entry] = 0;
					}
					$data["warp_cube"][$entry]++;
					
					if(!isset($data["warp_cube_total"])){
						$data["warp_cube_total"] = 0;
					}
					$data["warp_cube_total"]++;
				}
			}
			
				//end stuff from https://github.com/tgstation/-tg-station/pull/17233
				
			
				//stuff from https://github.com/tgstation/-tg-station/pull/17728#issuecomment-220276218
				
			if( $i["var_name"] == "export_sold_amount" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					$valPair = explode("|", $v);
					if(count($valPair) == 2){
						$key = $valPair[0];
						$value = intval($valPair[1]);
					
						if(!isset($data["export_sold_amount"][$key])){
							$data["export_sold_amount"][$key] = 0;
						}
						$data["export_sold_amount"][$key] += $value;
					
						if(!isset($data["export_sold_amount_total"])){
							$data["export_sold_amount_total"] = 0;
						}
						$data["export_sold_amount_total"] += $value;
						
					}else{
						print "<br>Failed to parse: $valPair";
					}
				}
			}
				
			if( $i["var_name"] == "export_sold_cost" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					$valPair = explode("|", $v);
					if(count($valPair) == 2){
						$key = $valPair[0];
						$value = intval($valPair[1]);
					
						if(!isset($data["export_sold_cost"][$key])){
							$data["export_sold_cost"][$key] = 0;
						}
						$data["export_sold_cost"][$key] += $value;
					
						if(!isset($data["export_sold_cost_total"])){
							$data["export_sold_cost_total"] = 0;
						}
						$data["export_sold_cost_total"] += $value;
						
					}else{
						print "<br>Failed to parse: $valPair";
					}
				}
			}
				
			if( $i["var_name"] == "cargo_imports" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					$valPair = explode("|", $v);
					if(count($valPair) == 3){
						$key = $valPair[0];
						$value = intval($valPair[2]);
					
						if(!isset($data["cargo_imports"][$key])){
							$data["cargo_imports"][$key] = 0;
						}
						$data["cargo_imports"][$key]++;
					
						if(!isset($data["cargo_imports_total"])){
							$data["cargo_imports_total"] = 0;
						}
						$data["cargo_imports_total"]++;
					
						if(!isset($data["cargo_imports_cost"][$key])){
							$data["cargo_imports_cost"][$key] = 0;
						}
						$data["cargo_imports_cost"][$key] += $value;
					
						if(!isset($data["cargo_imports_cost_total"])){
							$data["cargo_imports_cost_total"] = 0;
						}
						$data["cargo_imports_cost_total"] += $value;
					}else{
						print "<br>Failed to parse: $v";
					}
				}
			}
			
			
			
				//end stuff from https://github.com/tgstation/-tg-station/pull/17233
				
			
				//stuff from https://github.com/tgstation/tgstation/pull/20289
				
			if( $i["var_name"] == "colonies_dropped" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					$valList = explode("|", $v);
					if(count($valList) == 3){
						$x = intval($valList[0]);
						$y = intval($valList[1]);
						$z = intval($valList[2]);
					
						if(!isset($data["colonies_dropped"]["$x, $y, $z"])){
							$data["colonies_dropped"]["$x, $y, $z"] = 0;
						}
						$data["colonies_dropped"]["$x, $y, $z"]++;
					
						if(!isset($data["colonies_dropped_total"])){
							$data["colonies_dropped_total"] = 0;
						}
						$data["colonies_dropped_total"]++;
					}else{
						print "<br>Failed to parse: $v";
					}
				}
			}
			
				//end stuff from https://github.com/tgstation/tgstation/pull/20289
				
				
			
				//stuff from Anonus on Tue Nov 01, 2016 5:53 pm 
				//A while ago, I added stat logging for clockcult scripture and cult runes, and apparently you're the guy who maintains the stats page, so could you update the stats page to include the logged stats for "clockcult_scripture_recited" and "cult_runes_scribed"?
				
			if( $i["var_name"] == "clockcult_scripture_recited" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					if(!isset($data["clockcult_scripture_recited"][$v])){
						$data["clockcult_scripture_recited"][$v] = 0;
					}
					$data["clockcult_scripture_recited"][$v] += 1;
					
					if(!isset($data["clockcult_scripture_recited_total"])){
						$data["clockcult_scripture_recited_total"] = 0;
					}
					$data["clockcult_scripture_recited_total"] += 1;
				}
			}
				
			if( $i["var_name"] == "cult_runes_scribed" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					if(!isset($data["cult_runes_scribed"][$v])){
						$data["cult_runes_scribed"][$v] = 0;
					}
					$data["cult_runes_scribed"][$v] += 1;
					
					if(!isset($data["cult_runes_scribed_total"])){
						$data["cult_runes_scribed_total"] = 0;
					}
					$data["cult_runes_scribed_total"] += 1;
				}
			}
			
				//end stuff from https://github.com/tgstation/tgstation/pull/20289
				
				//end stuff from https://tgstation13.org/phpBB/ucp.php?i=pm&mode=view&f=0&p=7603
				
			if( $i["var_name"] == "shuttle_purchase" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					if(!isset($data["shuttle_purchase"][$v])){
						$data["shuttle_purchase"][$v] = 0;
					}
					$data["shuttle_purchase"][$v] += 1;
					
					if(!isset($data["shuttle_purchase_total"])){
						$data["shuttle_purchase_total"] = 0;
					}
					$data["shuttle_purchase_total"] += 1;
				}
			}
				
			if( $i["var_name"] == "shuttle_manipulator" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					if(!isset($data["shuttle_manipulator"][$v])){
						$data["shuttle_manipulator"][$v] = 0;
					}
					$data["shuttle_manipulator"][$v] += 1;
					
					if(!isset($data["shuttle_manipulator_total"])){
						$data["shuttle_manipulator_total"] = 0;
					}
					$data["shuttle_manipulator_total"] += 1;
				}
			}
			
				//end stuff from https://tgstation13.org/phpBB/ucp.php?i=pm&mode=view&f=0&p=7603
				
				//stuff Kor asked 2017-01-12
				//could you please make the megafauna_kills stat appear on the page
				
			if( $i["var_name"] == "megafauna_kills" ){
				$val = $i["details"];
				//print "mining_voucher_redeemed: ".$val;
				$valTab = explode(" ", $val);
				
				foreach($valTab as $key => $v){
					if(!isset($data["megafauna_kills"][$v])){
						$data["megafauna_kills"][$v] = 0;
					}
					$data["megafauna_kills"][$v] += 1;
					
					if(!isset($data["megafauna_kills_total"])){
						$data["megafauna_kills_total"] = 0;
					}
					$data["megafauna_kills_total"] += 1;
				}
			}

				//end stuff Kor asked 2017-01-12
				
				//combat items
			if( $i["var_name"] == "item_used_for_combat" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					$entryPair = explode("|", $entry);
					$name = $entryPair[0];
					$amount = intval($entryPair[1]);
					
					if(strlen($name) > 50){
						$nameParts = str_split($name, 50);
						$name = implode("<br/>", $nameParts);
					}
					
					if(!isset($combat_items[$name])){
						$combat_items[$name]["TOTAL"] = 0;
						$combat_items[$name]["COUNT"] = 0;
					}
					$combat_items[$name]["TOTAL"] += $amount;
					$combat_items[$name]["COUNT"]++;
					
					if(!isset($combat_items[$name]["DISTRIBUTION"][$amount])){
						$combat_items[$name]["DISTRIBUTION"][$amount] = 0;
					}
					$combat_items[$name]["DISTRIBUTION"][$amount] += 1;
				}
			}
				//end combat items
			
				//Gun fired
			if( $i["var_name"] == "gun_fired" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					
					if(strlen($entry) > 50){
						$nameParts = str_split($entry, 50);
						$entry = implode("<br/>", $nameParts);
					}
					
					if(!isset($gun_fired[$entry])){
						$gun_fired[$entry] = 0;
					}
					$gun_fired[$entry]++;
				}
			}
				//end gun fired
			
				//combat target zone
			if( $i["var_name"] == "zone_targeted" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					if(!isset($combat_target_zone[$entry])){
						$combat_target_zone[$entry] = 0;
					}
					$combat_target_zone[$entry]++;
				}
			}
				//end combat target zone
			
				//mobs killed while mining
			if( $i["var_name"] == "mobs_killed_mining" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					if(!isset($mobs_killed_mining[$entry])){
						$mobs_killed_mining[$entry] = 0;
					}
					$mobs_killed_mining[$entry]++;
				}
			}
				//end mobs killed while mining
			
				//mobs killed while mining
			if( $i["var_name"] == "cell_used" ){
				$listOfEntries = explode(" ", $i["details"]);
				foreach($listOfEntries as $i => $entry){
					if(!isset($cell_used[$entry])){
						$cell_used[$entry] = 0;
					}
					$cell_used[$entry]++;
				}
			}
				//end mobs killed while mining
			
			
				//handcuffs
			if( $i["var_name"] == "handcuffs"){
				$cuffs_list = explode(" ",$i["details"]);
				foreach( $cuffs_list as $s ){
					if($s == "H"){
						$handcuffs++;
					}elseif($s == "C"){
						$cablecuffs++;
					}elseif($s == "B"){
						$beartraps++;
					}
				}
				$round_end_ghosts = $i["var_value"];
			}
				//end handcuffs
			
				//chemical reactions
			if( $i["var_name"] == "chemical_reaction"){
				$chemical_list = explode(" ",$i["details"]);
				foreach( $chemical_list as $chemical_pair ){
					
					$chemical = explode("|",$chemical_pair);
					$chem = $chemical[0];
					$amt = $chemical[1];
					if( isset($chemical_reactions[$chem]) ){
						$chemical_reactions[$chem] += intval($amt);
					}else{
						$chemical_reactions[$chem] = intval($amt);
					}
				}
				$round_end_ghosts = $i["var_value"];
			}
				//end chemical reactions
			
				//uptime
				
			if( $i["var_name"] == "round_start"){
				$round_start = $i["details"];
				$tmp_end_proper = 0;
				$tmp_game_mode = "";
				$clients = 0;
				$started = 1;
				$round_end_ghosts = 0;
				$survived_total = 0;
				$escaped_total = 0;
				$round_end_result = "";
				$total_applied = 0;
				$tmp_round_end_result = "";
			}
			if( $i["var_name"] == "round_end_clients"){
				$clients = $i["var_value"];
				
				if(isset($round_end_clients_num[$clients])){
					$round_end_clients_num[$clients]++;
				}else{
					$round_end_clients_num[$clients] = 1;
				}
			}
			if( $i["var_name"] == "round_end_ghosts"){
				$round_end_ghosts = $i["var_value"];
			}
			if( $i["var_name"] == "escaped_total"){
				$escaped_total = $i["var_value"];
			}
			if( $i["var_name"] == "survived_total"){
				$survived_total = $i["var_value"];
			}
			if( $i["var_name"] == "end_proper"){
				$tmp_end_proper = 1;
			}
			if( $i["var_name"] == "round_end"){
				$round_end = $i["details"];
				
				if($started == 1){ //If fails, this round doesn't have a round_start variable set.
					$started = 0;
				
					if($escaped_total > 0){
						$survival_percentage = $survived_total / ($survived_total + $round_end_ghosts);
						$escapee_percentage = $escaped_total / ($survived_total + $round_end_ghosts);
						$escaped_survivor_percentage = $escaped_total / $survived_total;
						
						$survival_percentage_m10 = min(floor(($survival_percentage*100) / 10) +1, 10);
						$escapee_percentage_m10 = min(floor(($escapee_percentage*100) / 10) +1, 10);
						$escaped_survivor_percentage_m10 = min(floor(($escaped_survivor_percentage*100) / 10) +1, 10);
						$players_m10 = min(floor((($survived_total + $round_end_ghosts)) / 10) +1, 10);
						
						$rounds_survival_percentage[$survival_percentage_m10]++;
						$rounds_escapee_percentage[$escapee_percentage_m10]++;
						$rounds_escaped_survivor_percentage[$escaped_survivor_percentage_m10]++;
						$rounds_survival_matrix[$survival_percentage_m10][$players_m10]++;
					}
					
					if($tmp_game_mode == "revolution"){
						$players_m10 = floor((($survived_total + $round_end_ghosts)) / 10) +1; 
						
						if($round_end_result == "win - heads killed"){
							$rounds_rev_bypop_success[$players_m10]++;
						}else if($round_end_result == "loss - rev heads killed"){
							$rounds_rev_bypop_fail[$players_m10]++;
						}else{
							$rounds_rev_bypop_failedround[$players_m10]++;
						}
					}
					if($tmp_game_mode == "cult"){
						$players_m10 = floor((($survived_total + $round_end_ghosts)) / 10) +1; 
						
						if($round_end_result == "win - cult win"){
							$rounds_cult_bypop_success[$players_m10]++;
						}else if($round_end_result == "loss - staff stopped the cult"){
							$rounds_cult_bypop_fail[$players_m10]++;
						}else{
							$rounds_cult_bypop_failedround[$players_m10]++;
						}
					}
					if($tmp_game_mode == "AI malfunction"){
						$players_m10 = floor((($survived_total + $round_end_ghosts)) / 10) +1; 
						
						if( startsWith($round_end_result, "win - AI win ")){
							$rounds_malf_bypop_success[$players_m10]++;
						}else if($round_end_result == "loss - staff win"){
							$rounds_malf_bypop_fail[$players_m10]++;
						}else{
							$rounds_malf_bypop_failedround[$players_m10]++;
						}
					}
					
					$start_hour = substr($round_start, 11, 2);
					$start_min = substr($round_start, 14, 2);
					$end_hour = substr($round_end, 11, 2);
					$end_min = substr($round_end, 14, 2);
					
					if($end_hour < $start_hour){ //Šlo èez polnoè
						$end_hour +=24;
					}
					
					
					$round_startends[count($round_startends)] = "s-".hourminute2minute(intval($start_hour),intval($start_min));
					$round_startends[count($round_startends)] = "e-".hourminute2minute((intval($end_hour)),intval($end_min));
					
					if( ($start_hour < $end_hour) || ($start_min == 0 && $start_hour <= $end_hour) ){
						$h = $start_hour;
						if($start_min != 0){
							$h++;
						}
						if($clients > 0){
							$daycode = substr($i["details"],0,10);
							for( $i = $h; $i <= $end_hour; $i++ ){
								$hm = ($i % 24) +1;
								$round_end_clients_hour[$hm] += $clients;
								$round_end_clients_hour_recordings[$hm]++;
								if(!isset($per_day_clients[$daycode])){
									$per_day_clients[$daycode] = $clients;
								}else{
									$per_day_clients[$daycode] += $clients;
								}
								if(!isset($per_day_clients_recordings[$daycode])){
									$per_day_clients_recordings[$daycode] = 1;
								}else{
									$per_day_clients_recordings[$daycode]++;
								}
							}
						}
					}
					
					$duration_hour = intval($end_hour) - intval($start_hour);
					$duration_min = intval($end_min) - intval($start_min);
					if($duration_min <= 0){
						$duration_hour--;
						$duration_min = 60 + $duration_min; //je negativno, zato odšteje
					}
					
					$duration_in_minutes = $duration_hour * 60 + $duration_min;
					
					$duration_m10 = floor(($duration_in_minutes) / 10); 
					if( !isset($rounds_duration[$duration_m10]) ){
						$rounds_duration[$duration_m10] = 1;
					}else{
						$rounds_duration[$duration_m10]++;
					}
					
					if($duration_in_minutes_max < $duration_in_minutes){
						$duration_in_minutes_max = $duration_in_minutes;
					}
					
					if($tmp_round_end_result == ""){
						$tmp_round_end_result = "UNSET";
					}
					
					if($tmp_round_end_result != "" && $tmp_game_mode != ""){
						$duration_m10 = floor(($duration_in_minutes) / 10); 
						$players_m10 = floor((($survived_total + $round_end_ghosts)) / 10);
						if(!isset($round_success_rating[$tmp_game_mode]["BYPOP"][$players_m10][$tmp_round_end_result])){
							$round_success_rating[$tmp_game_mode]["BYPOP"][$players_m10][$tmp_round_end_result] = 1;
						}else{
							$round_success_rating[$tmp_game_mode]["BYPOP"][$players_m10][$tmp_round_end_result]++;
						}
						if(!isset($round_success_rating[$tmp_game_mode]["BYDURATION"][$duration_m10][$tmp_round_end_result])){
							$round_success_rating[$tmp_game_mode]["BYDURATION"][$duration_m10][$tmp_round_end_result] = 1;
						}else{
							$round_success_rating[$tmp_game_mode]["BYDURATION"][$duration_m10][$tmp_round_end_result]++;
						}
						if(isset($round_success_rating[$tmp_game_mode]["MAXDURATION"])){
							if($round_success_rating[$tmp_game_mode]["MAXDURATION"] < $duration_in_minutes){
								$round_success_rating[$tmp_game_mode]["MAXDURATION"] = $duration_in_minutes;
							}
						}else{
							$round_success_rating[$tmp_game_mode]["MAXDURATION"] = $duration_in_minutes;
						}
						if(isset($round_success_rating[$tmp_game_mode]["MINDURATION"])){
							if($round_success_rating[$tmp_game_mode]["MINDURATION"] > $duration_in_minutes){
								$round_success_rating[$tmp_game_mode]["MINDURATION"] = $duration_in_minutes;
							}
						}else{
							$round_success_rating[$tmp_game_mode]["MINDURATION"] = $duration_in_minutes;
						}
					}
					
					if($tmp_game_mode == "nuclear emergency"){
						$duration_m10 = floor(($duration_in_minutes) / 10); 
						
						if( startsWith($round_end_result, "win")){
							if(isset($rounds_nuke_bydur_success[$duration_m10])){
								$rounds_nuke_bydur_success[$duration_m10]++;
							}else{
								$rounds_nuke_bydur_success[$duration_m10] = 1;
							}
						}else if(startsWith($round_end_result, "loss")){
							if(isset($rounds_nuke_bydur_fail[$duration_m10])){
								$rounds_nuke_bydur_fail[$duration_m10]++;
							}else{
								$rounds_nuke_bydur_fail[$duration_m10] = 1;
							}
						}else if(startsWith($round_end_result, "halfwin")){
							if(isset($rounds_nuke_bydur_halfwin[$duration_m10])){
								$rounds_nuke_bydur_halfwin[$duration_m10]++;
							}else{
								$rounds_nuke_bydur_halfwin[$duration_m10] = 1;
							}
						}else{
							if(isset($rounds_nuke_bydur_failedround[$duration_m10])){
								$rounds_nuke_bydur_failedround[$duration_m10]++;
							}else{
								$rounds_nuke_bydur_failedround[$duration_m10] = 1;
							}
						}
						$players_m10 = floor((($survived_total + $round_end_ghosts)) / 10) +1; 
						
						if( startsWith($round_end_result, "win")){
							$rounds_nuke_bypop_success[$players_m10]++;
						}else if(startsWith($round_end_result, "loss")){
							$rounds_nuke_bypop_fail[$players_m10]++;
						}else if(startsWith($round_end_result, "halfwin")){
							$rounds_nuke_bypop_halfwin[$players_m10]++;
						}else{
							$rounds_nuke_bypop_failedround[$players_m10]++;
						}
					}
					
					$uptime_minutes += $duration_in_minutes;
					$hours_played += $clients * ($duration_hour + ($duration_min/60));
					$total_rounds++;
					if($tmp_end_proper == 1){
						$successfully_completed_rounds++;
						switch($tmp_game_mode){
							case "nuclear emergency":
								$syndi_dur += $duration_in_minutes;
								if($duration_in_minutes > $syndi_dur_max){
									$syndi_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $syndi_dur_min){
									$syndi_dur_min = $duration_in_minutes;
								}
							break;
							case "revolution":
								$rev_dur += $duration_in_minutes;
								if($duration_in_minutes > $rev_dur_max){
									$rev_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $rev_dur_min){
									$rev_dur_min = $duration_in_minutes;
								}
							break;
							case "wizard":
								$wiz_dur += $duration_in_minutes;
								if($duration_in_minutes > $wiz_dur_max){
									$wiz_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $wiz_dur_min){
									$wiz_dur_min = $duration_in_minutes;
								}
							break;
							case "cult":
								$cult_dur += $duration_in_minutes;
								if($duration_in_minutes > $cult_dur_max){
									$cult_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $cult_dur_min){
									$cult_dur_min = $duration_in_minutes;
								}
							break;
							case "AI malfunction":
								$malf_dur += $duration_in_minutes;
								if($duration_in_minutes > $malf_dur_max){
									$malf_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $malf_dur_min){
									$malf_dur_min = $duration_in_minutes;
								}
							break;
							case "blob":
								$blob_dur += $duration_in_minutes;
								if($duration_in_minutes > $blob_dur_max){
									$blob_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $blob_dur_min){
									$blob_dur_min = $duration_in_minutes;
								}
							break;
							case "traitor":
								$traitor_dur += $duration_in_minutes;
								if($duration_in_minutes > $traitor_dur_max){
									$traitor_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $traitor_dur_min){
									$traitor_dur_min = $duration_in_minutes;
									/*$traitor_dur_min_date = $i["details"];*/
								}
							break;
							case "changeling":
								$chan_dur += $duration_in_minutes;
								if($duration_in_minutes > $chan_dur_max){
									$chan_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $chan_dur_min){
									$chan_dur_min = $duration_in_minutes;
								}
							break;
							case "traitor+changeling":
								$trch_dur += $duration_in_minutes;
								if($duration_in_minutes > $trch_dur_max){
									$trch_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $trch_dur_min){
									$trch_dur_min = $duration_in_minutes;
								}
							break;
							case "extended":
								$ext_dur += $duration_in_minutes;
								if($duration_in_minutes > $ext_dur_max){
									$ext_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $ext_dur_min){
									$ext_dur_min = $duration_in_minutes;
								}
							break;
							case "meteor":
								$meteor_dur += $duration_in_minutes;
								if($duration_in_minutes > $meteor_dur_max){
									$meteor_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $meteor_dur_min){
									$meteor_dur_min = $duration_in_minutes;
								}
							break;
							case "sandbox":
								$snd += $duration_in_minutes;
								if($duration_in_minutes > $snd_dur_max){
									$snd_dur_max = $duration_in_minutes;
								}
								if($duration_in_minutes < $snd_dur_min){
									$snd_dur_min = $duration_in_minutes;
								}
							break;
						
						}
						$tmp_end_proper = 0;
					}
				}
				
				/*print "<tr><td>$start_hour:$start_min to $end_hour:$end_min. Lasted $duration_hour:$duration_min. Running total: $uptime_minutes. Hours played running total: $hours_played</td></tr>";*/
				$tmp_end_proper = 0;
				$tmp_game_mode = "";
				$clients = 0;
				$started = 0;
				$round_end_ghosts = 0;
				$survived_total = 0;
				$escaped_total = 0;
				$round_end_result = "";
				$total_applied = 0;
				$tmp_round_end_result = "";
			}
			
				//end uptime
			
			/*END DATA MINING*/
		}
		
		//Now we reached the actual start of the output section
		//In this section, the data aggregated above will be printed out.
		//This goes in the same order as is displayed on the actual output page.
		
		
		
		function OutputStats($headerText, $linkID, $color0, $color1, $color2, $SumIdentifier, $DataIdentifier){
			global $data;
			
			//Mining vouchers https://github.com/tgstation/-tg-station/pull/17233 !
			print "<tr>";
			print "<td colspan='4' align='center'><a id='$linkID' name='$linkID'><b><br><br>In the last $time_in_days days, $headerText:</b> (<a href='#$linkID'>link</a>)</td>";
			print "</tr>";
			$sum = 0;
			
			$sum = $data[$SumIdentifier];
			
			arsort($data[$DataIdentifier]);
			print "<tr bgcolor='white'>";
			print "<td align='center' colspan='4'><font size='2'><b>Mining voucher redemtption (n = $sum)<br /></b>";
			$count = 1;
			foreach($data[$DataIdentifier] as $key => $val){
				$percent = 0;
				if($sum > 0){
					$percent = $val / $sum;
				}
				$percentTxt = floor($percent * 10000) / 100;
				$width = $percent * 800;
				print "<img src='bars/p$count.png' title='$key ($val; $percentTxt%)' style='width: ".$width."px; height: 10px;'>";
				$count++;
			}
			print "<br><b>Legend: </b>";
			$count = 1;
			foreach($data[$DataIdentifier] as $key => $val){
				$percent = 0;
				if($sum > 0){
					$percent = $val / $sum;
				}
				$percentTxt = floor($percent * 10000) / 100;
				print "<img src='bars/p$count.png' title='$key ($val; $percentTxt%)' style='width: 10px; height: 10px;'> = $key; ";
				$count++;
			}
			print "<br />Hover over a part of the bar for details<br><br></font></td>";
			print "</tr>";
			foreach($data[$DataIdentifier] as $key => $val){
				if($color == $color1){$color=$color2;}else{$color = $color1;}
				$percent = 0;
				if($sum > 0){
					$percent = $val / $sum;
				}
				$percent = floor($percent * 10000) / 100;
				
				print "<tr bgcolor='$color'>";
				print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td><td>$percent%</td>";
				print "</tr>";
			}
		}
		
		function OutputTwoTieredStats($headerText, $linkID, $color0, $color1, $color2, $SumIdentifier, $DataIdentifier){
			global $data;
			
			print "<tr>";
			print "<td colspan='4' align='center'><a id='$linkID' name='$linkID'><b><br><br>In the last $time_in_days days, $headerText</b> (<a href='#$linkID'>link</a>)</td>";
			print "</tr>";
			
			$sum = 0;
			
			foreach($data[$SumIdentifier] as $key => $val){
				$sum += $val;
			}
			
			foreach($data[$DataIdentifier] as $key => $value){
				print "<tr>";
				$thisVal = $data[$SumIdentifier][$key];
				$thisPercent = 0;
				if($sum > 0){
					$thisPercent = $thisVal / $sum;
				}
				$thisPercent = floor($thisPercent * 10000) / 100;
				
				print "<td colspan='4' bgcolor='$color0' align='center'><font size='5'><b>$key (Total: $thisVal/$sum ($thisPercent%))</b></font></td>";
				print "</tr>";
				
				arsort($value);
				print "<tr bgcolor='$color0'>";
				print "<td align='center' colspan='4'><font size='2'>";
				$count = 1;
				foreach($value as $key => $val){
					$percent = 0;
					if($sum > 0){
						$percent = $val / $thisVal;
					}
					$percentTxt = floor($percent * 10000) / 100;
					$width = $percent * 800;
					print "<img src='bars/p$count.png' title='$key ($val; $percentTxt%)' style='width: ".$width."px; height: 10px;'>";
					$count++;
				}
				print "<br><b>Legend: </b>";
				$count = 1;
				foreach($value as $key => $val){
					$percent = 0;
					if($sum > 0){
						$percent = $val / $thisVal;
					}
					$percentTxt = floor($percent * 10000) / 100;
					print "<img src='bars/p$count.png' title='$key ($val; $percentTxt%)' style='width: 10px; height: 10px;'> = $key; ";
					$count++;
				}
				print "<br />Hover over a part of the bar for details</font></td>";
				print "</tr>";
				foreach($value as $key2 => $val){
					if($color == $color1){$color=$color2;}else{$color = $color1;}
					$percent = 0;
					if($sum > 0){
						$percent = $val / $thisVal;
					}
					$percent = floor($percent * 10000) / 100;
					print "<tr bgcolor='$color'>";
					print "<td align='right'><b>$key2</b></td><td align='center'><b>$val</b></td><td>$percent%</td>";
					print "</tr>";
				}
			}
		}
		
		print "<h1 align='center'>Space Station 13 ingame statistics</h1>";
	
		print "<ul>";
		$dir = new DirectoryIterator(".");
		foreach ($dir as $fileinfo) {
			if (!$fileinfo->isDot()) {
				$fileName = $fileinfo->getFilename();
				if(startsWith($fileName, "stats_")){
					print "<li><a href='$fileName'>$fileName</a></li>";
				}
			}
		}
		print "</ul>";
		
		print "<div align='center'>Also view the <a href='deathimage.php'>Death image</a> page, the <a href='populationchange.php'>Player migration</a> page, the <a href='ingamepolls.php'>ingame poll results</a> page, the <a href='privacy.php'>privacy poll results</a> page and the <a href='banoverview.php'>admin activity</a> page.</div>";
		
		print "<p><table align='center' width='900' cellspacing='0' cellpadding='5'>";
		
		print "<tr>";
		print "<td colspan='4' align='center'><b> Statistics calculated from the data gathered from game rounds in a $time_in_days day period, so starting on $day - $month - $year and ending on $dayEnd - $monthEnd - $yearEnd.</b></td>";
		
		print "</tr>";
		
		$round_color1 = "#ffe476";
		$round_color2 = "#ffeb9a";
		$round_color = $round_color2;
		
		foreach($round_success_rating as $mode => $mode_lists){
			if($round_color==$round_color1){$round_color=$round_color2;}else{$round_color=$round_color1;}
			
			if(isset($round_success_rating[$mode]["TOTAL"]["total"])){
				$total = $round_success_rating[$mode]["TOTAL"]["total"];
			}else{
				$total = 0;
			}
			
			$win = 0;
			$halfwin = 0;
			$loss = 0;
			$other = 0;
			$proper = 0;
			$error = 0;
			if(isset($round_success_rating[$mode]["RESULTSUM"]["win"])){
				$win = $round_success_rating[$mode]["RESULTSUM"]["win"];
			}
			if(isset($round_success_rating[$mode]["RESULTSUM"]["halfwin"])){
				$halfwin = $round_success_rating[$mode]["RESULTSUM"]["halfwin"];
			}
			if(isset($round_success_rating[$mode]["RESULTSUM"]["loss"])){
				$loss = $round_success_rating[$mode]["RESULTSUM"]["loss"];
			}
			if(isset($round_success_rating[$mode]["ERROR"])){
				$error = array_sum($round_success_rating[$mode]["ERROR"]);
			}
			if(isset($round_success_rating[$mode]["PROPER"])){
				$proper = array_sum($round_success_rating[$mode]["PROPER"]);
			}
			$other = $total - $win - $loss - $halfwin;
			
			$drawgraph = 0;
			$ratio_win = 0;
			$ratio_halfwin = 0;
			$ratio_loss = 0;
			$width_win = 0;
			$width_halfwin = 0;
			$width_loss = 0;
			$width_other = 0;
			$ratio_error = 0;
			$ratio_proper = 0;
			$width_error = 0;
			$width_proper = 0;
			$width_unknown = 0;
			if($win + $halfwin + $loss > 0 && $total > 0){
				$ratio_win = $win / $total;
				$ratio_halfwin = $halfwin / $total;
				$ratio_loss = $loss / $total;
				
				$width_win = floor($ratio_win * 700);
				$width_halfwin = floor($ratio_halfwin * 700);
				$width_loss = floor($ratio_loss * 700);
				$width_other = 700-$width_win-$width_halfwin-$width_loss;
				$drawgraph = 1;
			}
			$drawgraph2 = 0;
			if($error + $proper > 0 && $total > 0){
				$drawgraph2 = 1;
				$ratio_error = $error / $total;
				$ratio_proper = $proper / $total;
				
				$width_error = floor($ratio_error * 700);
				$width_proper = floor($ratio_proper * 800);
				$width_unknown = 700-$width_error-$width_proper;
			}
		
			print "<tr bgcolor='$round_color'><td colspan='4' align='center'>";
			print "<a name='mode_".str_replace(" ","_",$mode)."'><font size='5'><b>$mode</b></font> (<a href='#mode_".str_replace(" ","_",$mode)."'>link</a>)<font size='2'><br>($total rounds; ";
			print "$proper properly completed rounds; $error rounds ended in an error)";
			if($drawgraph2 == 1){
				print "<br><div align='center'><font size='4'><b>Successful round completion (".percent2str($ratio_proper).")</b></font><font size='2'>
				<br><img src='unknown.png' width='10' height='10'> = Proper round completion (evacuation, nuke,...); <br>
				<img src='none.png' width='10' height='10'> = Round end error (Restart vote, admin restart,...); <br>
				<img src='errorous.png' width='10' height='10'> = Unaccounted round ending (Logging error);</font></div>";
				print "<img src='unknown.png' height='10' width='$width_proper'>";
				print "<img src='none.png' height='10' width='$width_error'>";
				print "<img src='errorous.png' height='10' width='$width_unknown'>";
				print "<br>";
				print "<table width='800' bgcolor='#ffffc9'><tr>";
				print "<td width='33%' align='center'><font size='2'><b>(PROPER: $proper / $total)</b></font></td>";
				print "<td width='33%' align='center'><font size='2'><b>(ERROR: $error / $total)</b></font></td>";
				print "<td width='33%' align='center'><font size='2'><b>(UNKNOWN: ".($total-($error+$proper))." / $total)</b></font></td>";
				print "<tr></table>";
			}
			if($drawgraph == 1){
				print "<br><div align='center'><font size='4'><b>Global success rate (".percent2str($ratio_win).")</b></font><font size='2'>
				<br><img src='good.png' width='10' height='10'> = Antag victory; 
				<img src='medi.png' width='10' height='10'> = Antag halfwin; 
				<img src='bad.png' width='10' height='10'> = Antag defeat;<br>
				<img src='none.png' width='10' height='10'> = Other ending (see round end error breakdown);<br>(See round end result breakdown for details on different types of victory, halfwin and defeat)</font></div>";
				print "<img src='good.png' height='10' width='$width_win'>";
				print "<img src='medi.png' height='10' width='$width_halfwin'>";
				print "<img src='bad.png' height='10' width='$width_loss'>";
				print "<img src='none.png' height='10' width='$width_other'>";
			
				print "<br>";
				print "<table width='800' bgcolor='#ffffc9'><tr>";
				print "<td width='25%' align='center'><font size='2'><b>(WIN: $win / $total)</b></font></td>";
				print "<td width='25%' align='center'><font size='2'><b>(HALFWIN: $halfwin / $total)</b></font></td>";
				print "<td width='25%' align='center'><font size='2'><b>(LOSS: $loss / $total)</b></font></td>";
				print "<td width='25%' align='center'><font size='2'><b>(OTHER: $other / $total)</b></font></td>";
				print "<tr></table>";
			}
			
			$color1="#fefeaf";
			$color2="#ffffc9";
			$colortitle = "#ffff89";
			$color = $color1;
			if(isset($mode_lists["RESULT"])){
				print "<br><div align='center'><font size='4'><b>Round end result breakdown</b></font></div>";
				krsort($mode_lists["RESULT"]);
				print "<table width='800' bgcolor='$color2'>";
				foreach($mode_lists["RESULT"] as $key => $val){
					if($color==$color1){$color=$color2;}else{$color=$color1;}
					print "<tr bgcolor='$color'>";
					print "<td align='right' width='50%'><b>$key</b></td>";
					print "<td align='left' width='50%'> $val</td>";
					print "</tr>";
				}
				print "</table>";
			}
			
			$color = $color1;
			if(isset($mode_lists["ERROR"])){
				print "<br><div align='center'><font size='4'><b>Round end error breakdown</b></font></div>";
				krsort($mode_lists["ERROR"]);
				print "<table width='800' bgcolor='$color2'>";
				foreach($mode_lists["ERROR"] as $key => $val){
					if($color==$color1){$color=$color2;}else{$color=$color1;}
					print "<tr bgcolor='$color'>";
					print "<td align='right' width='50%'><b>$key</b></td>";
					print "<td align='left' width='50%'> $val</td>";
					print "</tr>";
				}
				print "</table>";
			}
			
			if(isset($mode_lists["ANTAG"])){
				print "<br><div align='center'><font size='4'><b>Antagonist type success breakdown</b></font><font size='2'>
				<br><img src='good.png' width='10' height='10'> = Antagonist success; 
				<img src='bad.png' width='10' height='10'> = Antagonist failure;</font></div>";
				krsort($mode_lists["ANTAG"]);
				print "<table width='800' bgcolor='$color2'>";
				foreach($mode_lists["ANTAG"] as $antagtype => $success_array){
					$success = 0;
					$fail = 0;
					if(isset($success_array["SUCCESS"])){
						$success=$success_array["SUCCESS"];
					}
					if(isset($success_array["FAIL"])){
						$fail=$success_array["FAIL"];
					}
					$attempts = $success + $fail;
					$ratio = $success / $attempts;
					
					$width_good = floor(700*$ratio);
					$width_bad = 700-$width_good;
					
					print "<tr bgcolor='$color1'>";
					print "<td align='center' colspan='3'><b>$antagtype ($attempts attempts)</b></td>";
					print "</tr>";
					print "<tr>";
					print "<td align='center' colspan='3'><img src='good.png' width='$width_good' height='10'><img src='bad.png' width='$width_bad' height='10'></td>";
					print "</tr>";
					print "<tr>";
					print "<td align='center' width='33%'><b>(SUCCESS: $success / $attempts)</b></td>";
					print "<td align='center' width='33%'><b>(RATING: ".percent2str($ratio).")</b></td>";
					print "<td align='center' width='33%'><b>(FAIL: $fail / $attempts)</b></td>";
					print "</tr>";
				}
				print "</table>";
			}
			
			if(isset($mode_lists["OBJECTIVE"])){
				print "<br><div align='center'><font size='4'><b>Objectives by antagonist type success breakdown</b></font>
				<font size='2'>
				<br><img src='good.png' width='10' height='10'> = Objective success; 
				<img src='bad.png' width='10' height='10'> = Objective failure;</font></div>";
				krsort($mode_lists["OBJECTIVE"]);
				print "<table width='800' bgcolor='$color2' cellspacing='0' cellpadding='2'>";
				foreach($mode_lists["OBJECTIVE"] as $antagtype => $objective_array){
					print "<tr bgcolor='$colortitle'>";
					print "<td align='center' colspan='3'><b>$antagtype</b></td>";
					print "</tr>";
					foreach($objective_array as $objective => $success_array){
						$success = 0;
						$fail = 0;
						if(isset($success_array["SUCCESS"])){
							$success=$success_array["SUCCESS"];
						}
						if(isset($success_array["FAIL"])){
							$fail=$success_array["FAIL"];
						}
						$attempts = $success + $fail;
						$ratio = $success / $attempts;
						
						$width_good = floor(700*$ratio);
						$width_bad = 700-$width_good;
						
						print "<tr bgcolor='$color1'>";
						print "<td align='center' colspan='3'><b>$objective ($attempts attempts)</b></td>";
						print "</tr>";
						print "<tr>";
						print "<td align='center' colspan='3'><img src='good.png' width='$width_good' height='3'><img src='bad.png' width='$width_bad' height='3'></td>";
						print "</tr>";
						print "<tr>";
						print "<td align='center' width='33%'><b>(SUCCESS: $success / $attempts)</b></td>";
						print "<td align='center' width='33%'><b>(RATING: ".percent2str($ratio).")</b></td>";
						print "<td align='center' width='33%'><b>(FAIL: $fail / $attempts)</b></td>";
						print "</tr>";
					}
				}
				print "</table>";
			}
			
			if(isset($mode_lists["BYDURATION"])){
				print "<br><div align='center'><font size='4'><b>Round duration histogram (Number of rounds, duration in minutes)</b></font><font size='2'>
				<br><img src='good.png' width='10' height='10'> = Population indicator</font>
				</div>";
				print "<table width='800' bgcolor='$color2'><tr>";
				$maxdur = $mode_lists["MAXDURATION"];
				$mindur = $mode_lists["MINDURATION"];
				$maxdur_d10 = ceil($maxdur / 10) +1;
				print "<td bgcolor='$color1'>&nbsp;</td>";
				$bydur_rounds_max = 0;
				for($i = 0; $i < $maxdur_d10; $i++){
					$bydur_win = 0;
					$bydur_halfwin = 0;
					$bydur_loss = 0;
					$bydur_unset = 0;
					if(isset($mode_lists["BYDURATION"][$i]["win"])){
						$bydur_win = $mode_lists["BYDURATION"][$i]["win"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["halfwin"])){
						$bydur_halfwin = $mode_lists["BYDURATION"][$i]["halfwin"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["loss"])){
						$bydur_loss = $mode_lists["BYDURATION"][$i]["loss"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["UNSET"])){
						$bydur_unset = $mode_lists["BYDURATION"][$i]["UNSET"];
					}
					$bydur_total = $bydur_win + $bydur_halfwin + $bydur_loss + $bydur_unset;
					if($bydur_total > $bydur_rounds_max){
						$bydur_rounds_max = $bydur_total;
					}
				}
				
				for($i = 0; $i < $maxdur_d10; $i++){
					$bydur_win = 0;
					$bydur_halfwin = 0;
					$bydur_loss = 0;
					$bydur_unset = 0;
					if(isset($mode_lists["BYDURATION"][$i]["win"])){
						$bydur_win = $mode_lists["BYDURATION"][$i]["win"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["halfwin"])){
						$bydur_halfwin = $mode_lists["BYDURATION"][$i]["halfwin"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["loss"])){
						$bydur_loss = $mode_lists["BYDURATION"][$i]["loss"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["UNSET"])){
						$bydur_unset = $mode_lists["BYDURATION"][$i]["UNSET"];
					}
					$bydur_total = $bydur_win + $bydur_halfwin + $bydur_loss + $bydur_unset;
					$bydur_rounds_ratio = $bydur_total / $bydur_rounds_max;
					$height_bydur_rounds = floor(100 * $bydur_rounds_ratio);
					
					print "<td width='100' align='center' valign='bottom'>";
					print "<img src='good.png' width='10' height='$height_bydur_rounds'>";
					print "<br>$bydur_total";
					print "</td>";
					print "<td width='2' bgcolor='$color1'>&nbsp;</td>";
				}
				print "</tr><tr>";
				print "<td bgcolor='$color1'><font size='1'><b>Duration:</b></font></td>";
				for($i = 0; $i < $maxdur_d10; $i++){
					print "<td align='center'><font size='1'>".($i*10)." - ".(($i+1)*10)."</font></td><td bgcolor='$color1'></td>";
				}
				print "</tr></table>";
			}
			
			if(isset($mode_lists["BYDURATION"])){
				print "<br><div align='center'><font size='4'><b>Success rates by round duration (in minutes)</b></font><font size='2'>
				<br><img src='good.png' width='10' height='10'> = Antag victory; 
				<img src='medi.png' width='10' height='10'> = Antag halfwin; 
				<img src='bad.png' width='10' height='10'> = Antag defeat;
				<img src='none.png' width='10' height='10'> = No rounds played with this duration; <br>
				<img src='unset.png' width='10' height='10'> = Round type incompatible with clear 'victory' or 'defeat' (see antag and objective breakdown above); </font>
				</div>";
				print "<table width='800' bgcolor='$color2'><tr>";
				$maxdur = $mode_lists["MAXDURATION"];
				$mindur = $mode_lists["MINDURATION"];
				$maxdur_d10 = ceil($maxdur / 10) +1;
				print "<td bgcolor='$color1'>&nbsp;</td>";
				for($i = 0; $i < $maxdur_d10; $i++){
					$bydur_win = 0;
					$bydur_halfwin = 0;
					$bydur_loss = 0;
					$bydur_unset = 0;
					if(isset($mode_lists["BYDURATION"][$i]["win"])){
						$bydur_win = $mode_lists["BYDURATION"][$i]["win"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["halfwin"])){
						$bydur_halfwin = $mode_lists["BYDURATION"][$i]["halfwin"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["loss"])){
						$bydur_loss = $mode_lists["BYDURATION"][$i]["loss"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["UNSET"])){
						$bydur_unset = $mode_lists["BYDURATION"][$i]["UNSET"];
					}
					$bydur_total = $bydur_win + $bydur_halfwin + $bydur_loss + $bydur_unset;
					if($bydur_total){
						$bydur_ratio_win = $bydur_win / $bydur_total;
						$bydur_ratio_halfwin = $bydur_halfwin / $bydur_total;
						$bydur_ratio_loss = $bydur_loss / $bydur_total;
						$bydur_ratio_unset = $bydur_unset / $bydur_total;
						$bydur_height_win = floor($bydur_ratio_win * 100);
						$bydur_height_halfwin = floor($bydur_ratio_halfwin * 100);
						$bydur_height_loss = floor($bydur_ratio_loss * 100);
						$bydur_height_unset = floor($bydur_ratio_unset * 100);
						
						print "<td width='100' align='center' valign='bottom'>";
						if($bydur_height_loss > 0){
							print "<img src='bad.png' width='10' height='$bydur_height_loss'><br>";
						}
						if($bydur_height_halfwin > 0){
							print "<img src='medi.png' width='10' height='$bydur_height_halfwin'><br>";
						}
						if($bydur_height_win > 0){
							print "<img src='good.png' width='10' height='$bydur_height_win'><br>";
						}
						if($bydur_height_unset > 0){
							print "<img src='unset.png' width='10' height='$bydur_height_unset'><br>";
						}
						print "</td>";
						print "<td width='2' bgcolor='$color1'>&nbsp;</td>";
					}
					else{
						print "<td width='100' align='center' valign='bottom'>";
						print "<img src='none.png' width='10' height='100'><br>";
						print "</td>";
						print "<td width='2' bgcolor='$color1'>&nbsp;</td>";
					}
				}
				print "</tr><tr>";
				print "<td bgcolor='$color1'><font size='1'><b>Success:</b></font></td>";
				for($i = 0; $i < $maxdur_d10; $i++){
					$bydur_win = 0;
					$bydur_halfwin = 0;
					$bydur_loss = 0;
					$bydur_unset = 0;
					$bydur_ratio = 0;
					if(isset($mode_lists["BYDURATION"][$i]["win"])){
						$bydur_win = $mode_lists["BYDURATION"][$i]["win"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["halfwin"])){
						$bydur_halfwin = $mode_lists["BYDURATION"][$i]["halfwin"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["loss"])){
						$bydur_loss = $mode_lists["BYDURATION"][$i]["loss"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["UNSET"])){
						$bydur_unset = $mode_lists["BYDURATION"][$i]["UNSET"];
					}
					if(($bydur_loss+$bydur_halfwin+$bydur_win)>0){
						$bydur_ratio = $bydur_win / ($bydur_loss+$bydur_halfwin+$bydur_win+$bydur_unset);
					}
					print "<td align='center'><font size='1'>".percent2str($bydur_ratio)."</font></td><td bgcolor='$color1'></td>";
				}
				print "</tr><tr>";
				print "<td bgcolor='$color1'><font size='1'><b>Duration:</b></font></td>";
				for($i = 0; $i < $maxdur_d10; $i++){
					print "<td align='center'><font size='1'>".($i*10)." - ".(($i+1)*10)."</font></td><td bgcolor='$color1'></td>";
				}
				print "</tr><tr>";
				print "<td bgcolor='$color1'><font size='1'><b>Absolute:</b></font></td>";
				for($i = 0; $i < $maxdur_d10; $i++){
					$bydur_win = 0;
					$bydur_halfwin = 0;
					$bydur_loss = 0;
					$bydur_unset = 0;
					if(isset($mode_lists["BYDURATION"][$i]["win"])){
						$bydur_win = $mode_lists["BYDURATION"][$i]["win"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["halfwin"])){
						$bydur_halfwin = $mode_lists["BYDURATION"][$i]["halfwin"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["loss"])){
						$bydur_loss = $mode_lists["BYDURATION"][$i]["loss"];
					}
					if(isset($mode_lists["BYDURATION"][$i]["UNSET"])){
						$bydur_unset = $mode_lists["BYDURATION"][$i]["UNSET"];
					}
					$bydur_total = $bydur_win + $bydur_halfwin + $bydur_loss + $bydur_unset;
					print "<td align='center'><font size='1'>$bydur_win / $bydur_total</font></td><td bgcolor='$color1'></td>";
				}
				print "</tr></table>";
			}
			
			
			if(isset($mode_lists["BYPOP"])){
				print "<br><div align='center'><font size='4'><b>Success rates by round population</b></font><font size='2'>
				<br><img src='good.png' width='10' height='10'> = Antag victory; 
				<img src='medi.png' width='10' height='10'> = Antag halfwin; 
				<img src='bad.png' width='10' height='10'> = Antag defeat;
				<img src='none.png' width='10' height='10'> = No rounds played with this population; <br>
				<img src='unset.png' width='10' height='10'> = Round type incompatible with clear 'victory' or 'defeat' (see antag and objective breakdown above); </font></div>";
				print "<table width='800' bgcolor='$color2'><tr>";
				print "<td bgcolor='$color1'>&nbsp;</td>";
				for($i = 0; $i < 10; $i++){
					$bypop_win = 0;
					$bypop_halfwin = 0;
					$bypop_loss = 0;
					$bypop_unset = 0;
					if(isset($mode_lists["BYPOP"][$i]["win"])){
						$bypop_win = $mode_lists["BYPOP"][$i]["win"];
					}
					if(isset($mode_lists["BYPOP"][$i]["halfwin"])){
						$bypop_halfwin = $mode_lists["BYPOP"][$i]["halfwin"];
					}
					if(isset($mode_lists["BYPOP"][$i]["loss"])){
						$bypop_loss = $mode_lists["BYPOP"][$i]["loss"];
					}
					if(isset($mode_lists["BYPOP"][$i]["UNSET"])){
						$bypop_unset = $mode_lists["BYPOP"][$i]["UNSET"];
					}
					$bypop_total = $bypop_win + $bypop_halfwin + $bypop_loss + $bypop_unset;
					if($bypop_total){
						$bypop_ratio_win = $bypop_win / $bypop_total;
						$bypop_ratio_halfwin = $bypop_halfwin / $bypop_total;
						$bypop_ratio_loss = $bypop_loss / $bypop_total;
						$bypop_ratio_unset = $bypop_unset / $bypop_total;
						$bypop_height_win = floor($bypop_ratio_win * 100);
						$bypop_height_halfwin = floor($bypop_ratio_halfwin * 100);
						$bypop_height_loss = floor($bypop_ratio_loss * 100);
						$bypop_height_unset = floor($bypop_ratio_unset * 100);
						
						print "<td width='100' align='center' valign='bottom'>";
						if($bypop_height_loss > 0){
							print "<img src='bad.png' width='10' height='$bypop_height_loss'><br>";
						}
						if($bypop_height_halfwin > 0){
							print "<img src='medi.png' width='10' height='$bypop_height_halfwin'><br>";
						}
						if($bypop_height_win > 0){
							print "<img src='good.png' width='10' height='$bypop_height_win'><br>";
						}
						if($bypop_height_unset > 0){
							print "<img src='unset.png' width='10' height='$bypop_height_unset'><br>";
						}
						print "</td>";
						print "<td width='2' bgcolor='$color1'>&nbsp;</td>";
					}
					else{
						print "<td width='100' align='center' valign='bottom'>";
						print "<img src='none.png' width='10' height='100'><br>";
						print "</td>";
						print "<td width='2' bgcolor='$color1'>&nbsp;</td>";
					}
				}
				print "</tr><tr>";
				print "<td bgcolor='$color1'><font size='1'><b>Success:</b></font></td>";
				for($i = 0; $i < 10; $i++){
					$bypop_win = 0;
					$bypop_halfwin = 0;
					$bypop_loss = 0;
					$bypop_unset = 0;
					$bypop_ratio = 0;
					if(isset($mode_lists["BYPOP"][$i]["win"])){
						$bypop_win = $mode_lists["BYPOP"][$i]["win"];
					}
					if(isset($mode_lists["BYPOP"][$i]["halfwin"])){
						$bypop_halfwin = $mode_lists["BYPOP"][$i]["halfwin"];
					}
					if(isset($mode_lists["BYPOP"][$i]["loss"])){
						$bypop_loss = $mode_lists["BYPOP"][$i]["loss"];
					}
					if(isset($mode_lists["BYPOP"][$i]["UNSET"])){
						$bypop_unset = $mode_lists["BYPOP"][$i]["UNSET"];
					}
					if(($bypop_loss+$bypop_halfwin+$bypop_win)>0){
						$bypop_ratio = $bypop_win / ($bypop_loss+$bypop_halfwin+$bypop_win+$bypop_unset);
					}
					print "<td align='center'><font size='1'>".percent2str($bypop_ratio)."</font></td><td bgcolor='$color1'></td>";
				}
				print "</tr><tr>";
				print "<td bgcolor='$color1'><font size='1'><b>Population:</b></font></td>";
				for($i = 0; $i < 10; $i++){
					print "<td align='center'><font size='1'>".($i*10)." - ".(($i*10)+9)."</font></td><td bgcolor='$color1'></td>";
				}
				print "</tr><tr>";
				print "<td bgcolor='$color1'><font size='1'><b>Absolute:</b></font></td>";
				for($i = 0; $i < 10; $i++){
					$bypop_win = 0;
					$bypop_halfwin = 0;
					$bypop_loss = 0;
					$bypop_unset = 0;
					if(isset($mode_lists["BYPOP"][$i]["win"])){
						$bypop_win = $mode_lists["BYPOP"][$i]["win"];
					}
					if(isset($mode_lists["BYPOP"][$i]["halfwin"])){
						$bypop_halfwin = $mode_lists["BYPOP"][$i]["halfwin"];
					}
					if(isset($mode_lists["BYPOP"][$i]["loss"])){
						$bypop_loss = $mode_lists["BYPOP"][$i]["loss"];
					}
					if(isset($mode_lists["BYPOP"][$i]["UNSET"])){
						$bypop_unset = $mode_lists["BYPOP"][$i]["UNSET"];
					}
					$bypop_total = $bypop_win + $bypop_halfwin + $bypop_loss + $bypop_unset;
					print "<td align='center'><font size='1'>$bypop_win / $bypop_total</font></td><td bgcolor='$color1'></td>";
				}
				print "</tr></table>";
			}
			
			print "</font>";
			
			print "<br></td>";
			print "</tr>";
			print "<tr>";
			print "<td colspan='4' align='center'><br><br></td>";
			print "</tr>";
		}
		
		print "<tr>";
		print "<td colspan='4' align='center'><a name='job_popularity'><b>In the last $time_in_days days, the folowing jobs were popular: <a href='#job_popularity'>link</a><br>(based on player preferences at round start; sorted by number of high + medium preferences)</b></td>";
		print "</tr>";
		
		$job_popularity_tmp = Array();
		
		foreach($job_popularity as $job => $popularity_list){
			$high = 0;
			$medium = 0;
			$low = 0;
			if(isset($popularity_list["HIGH"])){
				$high = $popularity_list["HIGH"];
			}
			if(isset($popularity_list["MEDIUM"])){
				$medium = $popularity_list["MEDIUM"];
			}
			/*if(isset($popularity_list["LOW"])){
				$low = $popularity_list["LOW"];
			}*/
			$popularity = $high + $medium + $low;
			$job_popularity_tmp[$popularity][$job] = $popularity_list;
		}
		
		krsort($job_popularity_tmp, SORT_NUMERIC);
		//$job_popularity = $job_popularity_tmp;
		
		foreach($job_popularity_tmp as $popularity => $job_array){
			foreach($job_array as $job => $popularity_list){
				$high = 0;
				$medium = 0;
				$low = 0;
				$never = 0;
				$banned = 0;
				$young = 0;
				if(isset($popularity_list["HIGH"])){
					$high = $popularity_list["HIGH"];
				}
				if(isset($popularity_list["MEDIUM"])){
					$medium = $popularity_list["MEDIUM"];
				}
				if(isset($popularity_list["LOW"])){
					$low = $popularity_list["LOW"];
				}
				if(isset($popularity_list["NEVER"])){
					$never = $popularity_list["NEVER"];
				}
				if(isset($popularity_list["YOUNG"])){
					$young = $popularity_list["YOUNG"];
				}
				if(isset($popularity_list["BANNED"])){
					$banned = $popularity_list["BANNED"];
				}
				$total = $high + $medium + $low + $never + $young + $banned;
				$ratio_high = $high / $total;
				$ratio_medium = $medium / $total;
				$ratio_low = $low / $total;
				$ratio_never = $never / $total;
				$ratio_young = $young / $total;
				$ratio_banned = $banned / $total;
				
				$ratio_high_str = percent2str($ratio_high);
				$ratio_medium_str = percent2str($ratio_medium);
				$ratio_low_str = percent2str($ratio_low);
				$ratio_never_str = percent2str($ratio_never);
				$ratio_young_str = percent2str($ratio_young);
				$ratio_banned_str = percent2str($ratio_banned);
				
				$width_high = floor($ratio_high * 800);
				$width_medium = floor($ratio_medium * 800);
				$width_low = floor($ratio_low * 800);
				$width_never = floor($ratio_never * 800);
				$width_young = floor($ratio_young * 800);
				$width_banned = floor($ratio_banned * 800);
				
				print "<tr>";
				print "<td colspan='4' align='center' bgcolor='#000000'>";
				
				print "<b><font color='white'>$job (Popularity score: ".percent2str( $popularity/$total ).")</font></b><br>";
				print "<img src='bars/high.PNG' height='10' width='$width_high'>";
				print "<img src='bars/medium.PNG' height='10' width='$width_medium'>";
				print "<img src='bars/low.PNG' height='10' width='$width_low'>";
				print "<img src='bars/never.PNG' height='10' width='$width_never'>";
				print "<img src='bars/young.PNG' height='10' width='$width_young'>";
				print "<img src='bars/banned.PNG' height='10' width='$width_banned'>";
				
				print "<font size='1'>";
				print "<table width='800'><tr>";
				
				print "<td align='center' width='17%'><font size='1' color='white'>(HIGH: $high / $total = <b>$ratio_high_str</b>)</font></td>";
				print "<td align='center' width='17%'><font size='1' color='white'>(MEDIUM: $medium / $total = <b>$ratio_medium_str</b>)</font></td>";
				print "<td align='center' width='17%'><font size='1' color='white'>(LOW: $low / $total = <b>$ratio_low_str</b>)</font></td>";
				print "<td align='center' width='17%'><font size='1' color='white'>(NEVER: $never / $total = <b>$ratio_never_str</b>)</font></td>";
				print "<td align='center' width='17%'><font size='1' color='white'>(YOUNG: $young / $total = <b>$ratio_young_str</b>)</font></td>";
				print "<td align='center' width='17%'><font size='1' color='white'>(BANNED: $banned / $total = <b>$ratio_banned_str</b>)</font></td>";
				
				print "</tr></table>";
				print "</font>";
				print "</td>";
				print "</tr>";
			}
		}
		
			//Traitor, changeling, wizard success rates
			
		print "<tr>";
		print "<td colspan='4' align='center' bgcolor='#ffffff'><a name='antag_success'><br><br><b>In the last $time_in_days days, antagonists with objectives had the following success rates:</b> (<a href='#job_popularity'>link</a>)</td>";
		print "</tr>";
		
		foreach($antagonistsuccess as $mode => $mode_success_list){
			$success = $mode_success_list["SUCCESS"];
			$fail = $mode_success_list["FAIL"];
			$ratio = $success / $fail;
			$attempts = $success + $fail;
			
			print "<tr>";
			print "<td colspan='4' align='center' bgcolor='#ffdcaa'><font size='5'><b>$mode success rate ($attempts attempts)</b></font></td>";
			print "</tr>";
			
			$width_good = floor($ratio * 800);
			$width_bad = 800-$width_good;
			
			print "<tr bgcolor='#ffeacb'>";
			print "<td colspan='4' align='center'>
			
			<table align='center' width='800'><tr>
			<td colspan='3'><img src='good.png' width='$width_good' height='10'><img src='bad.png' width='$width_bad' height='10'></td>
			</tr><tr>
			<td width='33%' align='center'><b>SUCCESS = $success</b></td>
			<td width='33%' align='center'><b>(".percent2str($ratio).")</b></td>
			<td width='33%' align='center'><b>FAIL = $fail</b></td>
			
			</tr></table>
			
			</td>";
			print "</tr>";
		}
		
		/* STATS NO LONGER GATHERED
		
		//mining!
		print "<tr>";
		print "<td colspan='4' align='center'><b><br><br>In the last $time_in_days days, miners produced this many sheets of the following materials</b></td>";
		print "</tr>";
		print "<tr bgcolor='#dddddd'>";
		print "<td align='right'><b>Iron sheets produced:</b></td><td align='left'>$mining_iron</td>";
		print "<td width='50'><img src='iron.png'></td>";
		$s = strval( ($mining_iron / 21712.25) );
		print "<td width='50%'>Enough iron was produced to make ". substr($s , 0, strpos( $s, "." ) + 3 ) ." stations.</td>";
		print "</tr>";
		print "<tr bgcolor='#cccccc'>";
		print "<td align='right'><b>Steel sheets produced:</b></td><td align='left'>$mining_steel</td>";
		print "<td width='50'><img src='steel.png'></td>";
		$s = strval( ($mining_steel / 2706) );
		print "<td width='50%'>Enough steel was produced to reinforce ". substr($s , 0, strpos( $s, "." ) + 3 ) ." stations.</td>";
		print "</tr>";
		print "<tr bgcolor='#ddddff'>";
		print "<td align='right'><b>Glass panes produced:</b></td><td align='left'>$mining_glass</td>";
		print "<td width='50'><img src='glass.png'></td>";
		$s = strval( ($mining_glass / 2506) );
		print "<td width='50%'>Enough glass was produced to glaze ". substr($s , 0, strpos( $s, "." ) + 3 ) ." stations (with enough rods).</td>";
		print "</tr>";
		print "<tr bgcolor='#ccccff'>";
		print "<td align='right'><b>Reinforced glass panes produced:</b></td><td align='left'>$mining_rglass</td>";
		print "<td width='50'><img src='rglass.png'></td>";
		$s = strval( ($mining_rglass / 2506) );
		print "<td width='50%'>Enough r-glass was produced to glaze ". substr($s , 0, strpos( $s, "." ) + 3 ) ." stations.</td>";
		print "</tr>";
		print "<tr bgcolor='#ffeecc'>";
		print "<td align='right'><b>Gold ingots produced:</b></td><td align='left'>$mining_gold</td>";
		print "<td width='50'><img src='gold.png'></td>";
		print "<td width='50%'>At the current prices the amount of gold produced would be worth $". $mining_gold*30543.21 .".</td>";
		print "</tr>";
		print "<tr bgcolor='#eeeeee'>";
		print "<td align='right'><b>Silver ingots produced:</b></td><td align='left'>$mining_silver</td>";
		print "<td width='50'><img src='silver.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#eeeeff'>";
		print "<td align='right'><b>Diamonds produced:</b></td><td align='left'>$mining_diamond</td>";
		print "<td width='50'><img src='diamond.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ccffcc'>";
		print "<td align='right'><b>Uranium bars produced:</b></td><td align='left' >$mining_uranium</td>";
		print "<td width='50'><img src='uranium.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffccff'>";
		print "<td align='right'><b>Plasma bars produced:</b></td><td align='left'>$mining_plasma</td>";
		print "<td width='50'><img src='plasma.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffffcc'>";
		print "<td align='right'><b>Bananium produced:</b></td><td align='left'>$mining_clown</td>";
		print "<td width='50'><img src='bananium.png'></td>";
		$calories = $mining_clown * 105 ; //Average banana has 105 calories
		$days_fed = $calories / 1400; //needs 1400 calories per day
		print "<td width='50%'>Enough bananium was produced to feed a starving child for ". substr($days_fed , 0, strpos( $s, "." ) + 3 ) ." days.</td>";
		print "</tr>";
		print "<tr bgcolor='#bbbbbb'>";
		print "<td align='right'><b>Adamantine produced:</b></td><td align='left'>$mining_adamantine</td>";
		print "<td width='50'><img src='adama.png'></td>";
		print "</tr>";*/
		
		//mecha!
		print "<tr>";
		print "<td colspan='4' align='center'><a name='mech'><b><br><br>In the last $time_in_days days, roboticists managed to create the following numbers of mechs.</b> (<a href='#mech'>link</a>)</td>";
		print "</tr>";
		
		foreach($mecha_stats as $key => $val){
			$color = mecha2color($key);
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key made:</b></td><td align='left'>$val</td>";
			print "<td width='50'><img src='$key.png'></td>";
		
		}
		/*
		$eeeeee = odysseus
		print "</tr>";
		print "<tr bgcolor='#ffffcc'>";
		print "<td align='right'><b>Ripleys made:</b></td><td align='left'>$mecha_ripley_created</td>";
		print "<td width='50'><img src='ripley.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#daeccf'>";
		print "<td align='right'><b>Firefighters made:</b></td><td align='left'>$mecha_firefighter_created</td>";
		print "<td width='50'><img src='firefighter.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffeecc'>";
		print "<td align='right'><b>Gygaxes made:</b></td><td align='left'>$mecha_gygax_created</td>";
		print "<td width='50'><img src='gygax.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffcccc'>";
		print "<td align='right'><b>Honkers made:</b></td><td align='left'>$mecha_honker_created</td>";
		print "<td width='50'><img src='honk.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#cccccc'>";
		print "<td align='right'><b>Durands made:</b></td><td align='left'>$mecha_durand_created</td>";
		print "<td width='50'><img src='durand.png'></td>";
		print "</tr>";
		*/
		
		//cyborgs
		print "<tr>";
		print "<td colspan='4' align='center'><a name='aistat'><b><br><br>In the last $time_in_days days (or since Feb 11 2012), the following statistics was gathered from artificial intelligence machines:</b> (<a href='#aistat'>link</a>)</td>";
		print "</tr>";
		print "<tr>";
		print "<td colspan='4' align='center'><b>Module popularity:</b><br>";
		
		$borg_module_sum = 0;
		
		foreach($cyborg_stats_module_names as $abb){
			$borg_module_sum += $cyborg_stats[$abb];
		}
		foreach($cyborg_stats_module_names as $abb){
			$ratio = ($cyborg_stats[$abb] / $borg_module_sum);
			$width = 800 * $ratio;
			print "<img src='bars/$abb.PNG' width='$width' height='10' title='".abbreviation2cyborgstat($abb)." = ".percent2str($ratio)." (".$cyborg_stats[$abb]." / $borg_module_sum)'>";
		}
		
		print "<font size='2'><br><b>Legend: </b>";
		foreach($cyborg_stats_module_names as $abb){
			print "<img src='bars/$abb.PNG' width='10' height='10' title='".abbreviation2cyborgstat($abb)."'> = ".abbreviation2cyborgstat($abb)."; ";
		}
		print "<br>(Hover your mouse over a segment of the bar for specifics)</font>";
		
		print "</td>";
		print "</tr>";
		
		$color1 = "#fff1dc";
		$color2 = "#ffeacb";
		
		print "<tr bgcolor='#ffd9a3'><td align='center'><b>Description</b></td><td align='center'><b>Value</b></td><td align='center'><b>Img</b></td><td align='center'><b>Details</b></td></tr>";
		foreach ($cyborg_stats as $abb => $val){
			if($color == $color1){
				$color = $color2;
			}else{
				$color = $color1;
			}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>".abbreviation2cyborgstat($abb).":</b></td><td align='left'>$val</td>";
			print "<td bgcolor='#ffd9a3'><img src='borgs/$abb.gif'></td>";
			print "<td>That's ".num2print( ($val / $total_rounds), 3 )." per round. ($val / $total_rounds)<br>";
			print "Max in a round was ".$cyborg_stats_max[$abb];
			
			print"</td>";
			print "</tr>";
		}
		
		//alerts!
		print "<tr>";
		print "<td colspan='4' align='center'><a name='alertlevel'><b><br><br>In the last $time_in_days days, the security levels have been manually enacted the following number of times:</b> (<a href='#alertlevel'>link</a>)</td>";
		print "</tr>";
		print "<tr bgcolor='#ccffcc'>";
		print "<td align='right'><b>Green (comms):</b></td><td align='left'>$alert_comms_green</td>";
		print "<td width='50'><img src='greenalert.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ccccff'>";
		print "<td align='right'><b>Blue (comms):</b></td><td align='left'>$alert_comms_blue</td>";
		print "<td width='50'><img src='bluealert.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffcccc'>";
		print "<td align='right'><b>Red (auth device):</b></td><td align='left'>$alert_keycard_auth_red</td>";
		print "<td width='50'><img src='redalert.gif'></td>";
		print "</tr>";
		
		//escapes!
		print "<tr>";
		print "<td colspan='4' align='center'><a name='escapes'><b><br><br>In the last $time_in_days days, the following number of people have escaped the tyranny that is SS13:</b> (<a href='#escapes'>link</a>)</td>";
		print "</tr>";
		print "<tr bgcolor='#ccccff'>";
		print "<td align='right'><b>Escaped on shuttle:</b></td><td align='left'>$escaped_on_shuttle</td>";
		print "<td width='50'><img src='shuttle.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#eeeeff'>";
		print "<td align='right'><b>Escaped on pod 1 (arrivals left):</b></td><td align='left'>$escaped_on_pod_1</td>";
		print "<td width='50' rowspan='2'><img src='shuttle2.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#eeeeff'>";
		print "<td align='right'><b>Escaped on pod 2 (arrivals right):</b></td><td align='left'>$escaped_on_pod_2</td>";
		print "</tr>";
		print "<tr bgcolor='#ffcccc'>";
		print "<td align='right'><b>Escaped on pod 3 (security):</b></td><td align='left'>$escaped_on_pod_3</td>";
		print "<td width='50'><img src='shuttlesec.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffffcc'>";
		print "<td align='right'><b>Escaped on pod 5 (engineering):</b></td><td align='left'>$escaped_on_pod_5</td>";
		print "<td width='50'><img src='shuttleeng.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ccffcc'>";
		print "<td align='right'><b>Most escapees on one pod:</b></td><td align='left'>$escaped_on_pod_max</td>";
		print "<td width='50'><img src='shuttlepod.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffcccc'>";
		print "<td align='right'><b>Stations blown up with nuke:</b></td><td align='left'>$end_proper_nuke</td>";
		print "<td width='50'><img src='nuke.gif'></td>";
		print "</tr>";
		
		//radio use!
		print "<tr>";
		print "<td colspan='4' align='center'><a name='radiouse'><b><br><br>In the last $time_in_days days, the following means of communications were used:</b> (<a href='#radiouse'>link</a>)</td>";
		print "</tr>";
		print "<tr>";
		
		$radio_usage_sum = array_sum($radio_usage);
		$radio_usage_department_sum = 0;
		print "<td colspan='4' align='center'><font size='2'><b>Radio frequency popularity:</b> (n = $radio_usage_sum)</font><br>";
		
		foreach($radio_usage as $key => $val){
			$ratio = ($val / $radio_usage_sum);
			$width = floor(800 * $ratio);
			print "<img src='bars/$key.PNG' width='$width' height='10' title='".abbreviation2channel($key)." = ".percent2str($ratio)." ($val / $radio_usage_sum)'>";
			if( in_array($key, $radio_usage_department) ){
				$radio_usage_department_sum += $val;
			}
		}
		print "<font size='2'><br><b>Legend: </b>";
		foreach($radio_usage as $key => $val){
			print "<img src='bars/$key.PNG' width='10' height='10'> = ".abbreviation2channel($key)."; ";
		}
		print "<font size='2'><br><b>Department frequency popularity:</b> (n = $radio_usage_department_sum)</font><br>";
		foreach($radio_usage_department as $key){
			$ratio = ($radio_usage[$key] / $radio_usage_department_sum);
			$width = floor(800 * $ratio);
			print "<img src='bars/$key.PNG' width='$width' height='10' title='".abbreviation2channel($key)." = ".percent2str($ratio)." (".$radio_usage[$key]." / $radio_usage_department_sum)'>";
		}
		print "<font size='2'><br><b>Legend: </b>";
		foreach($radio_usage_department as $key){
			print "<img src='bars/$key.PNG' width='10' height='10'> = ".abbreviation2channel($key)."; ";
		}
		print "<font size='2'><br>(Hover your mouse over a segment of the bar for specifics)</font>";
		
		print "</td>";
		print "</tr>";
		
		foreach($radio_usage as $key => $val){
			print "<tr bgcolor='#ccccff'>";
			print "<td align='right'><b>".abbreviation2channel($key)."</b></td><td align='left'>$val</td>";
			if(file_exists("$key.png")){
				print "<td width='50'><img src='$key.png'></td>";
			}else if(file_exists("$key.gif")){
				print "<td width='50'><img src='$key.gif'></td>";
			}
			print "</tr>";
		}
		
		//newscasters!
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='newscasters'><br><br>In the last $time_in_days days, the station broke this much news:</b>(<a href='#newscasters'>link</a>)</td>";
		print "</tr>";
		print "<tr bgcolor='#f1ffee'>";
		print "<td align='right'><b>News channels:</b></td><td align='left'>$newscaster_channels</td>";
		print "<td width='50'><img src='NC.gif'></td>";
		print "</tr>";
		print "<tr bgcolor='#daffd3'>";
		print "<td align='right'><b>News stories:</b></td><td align='left'>$newscaster_stories</td>";
		print "<td width='50'><img src='NC.gif'></td>";
		print "</tr>";
		print "<tr bgcolor='#f1ffee'>";
		print "<td align='right'><b>Newspapers printed:</b></td><td align='left'>$newscaster_newspapers_printed</td>";
		print "<td width='50'><img src='newspaper.png'></td>";
		print "</tr>";
		
		//bans!
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='bans'><br><br>In the last $time_in_days days, the admins have applied this many bans:</b>(<a href='#bans'>link</a>)<br>Note: Unbans and edits no longer logged this way, sorry.</td>";
		print "</tr>";
		print "<tr bgcolor='#ffcccc'>";
		print "<td align='right'><b>Job bans:</b></td><td align='left'>$ban_job</td>";
		print "<td width='50'><img src='jobban.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ccffcc'>";
		print "<td align='right'><b>Jobbans removed:</b></td><td align='left'>$ban_job_unban</td>";
		print "<td width='50'><img src='jobunban.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffcccc'>";
		print "<td align='right'><b>Temp bans:</b></td><td align='left'>$ban_tmp</td>";
		print "<td width='50' rowspan='3'><img src='ban.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffcccc'>";
		print "<td align='right'><b>Permabans:</b></td><td align='left'>$ban_perma</td>";
		print "</tr>";
		print "<tr bgcolor='#ffffcc'>";
		print "<td align='right'><b>Bans edited:</b></td><td align='left'>$ban_edit</td>";
		print "</tr>";
		print "<tr bgcolor='#ccffcc'>";
		print "<td align='right'><b>Bans removed:</b></td><td align='left'>$ban_unban</td>";
		print "<td width='50'><img src='unban.png'></td>";
		print "</tr>";
		print "<tr bgcolor='#ffffcc'>";
		print "<td align='right'><b>Temp ban total:</b></td><td align='left'>".min2hour($ban_tmp_time)."h</td>";
		print "<td width='50'><img src='ban.png'></td>";
		print "<td width='50%'>That's ".min2duration($ban_tmp_time)."</td>";
		print "</tr>";
		
		$adm_color1 = "#cccccc";
		$adm_color2 = "#dddddd";
		$adm_i = 0;
		
		//Admin buttons!
		arsort($admin_secrets);
		print "<td colspan='4' align='center'><b><a name='adminbuttons'><br><br>In the last $time_in_days days, the admins have clicked the following number of buttons:</b> (<a href='#adminbuttons'>link</a>)<br>Note: The syntax here is: ID : Description. If you see the ID repeated after the colon, it means there is no description for it - that this page was not updated since the new ID was added to the game. Please look up the ID ingame and send me a description via a <a href='http://www.ss13.eu/phpbb/ucp.php?i=pm&mode=compose&u=2'>private message</a> on the forum. Your help is greately appreciated.</td>";
		print "</tr>";
		foreach( $admin_secrets as $key => $value ){
			if($adm_i == 0){
				$adm_i = 1;
				$adm_color = $adm_color1;
			}else{
				$adm_i = 0;
				$adm_color = $adm_color2;
			}
			print "<tr bgcolor='$adm_color'>";
			print "<td align='right'><b>$key : ".abbreviation2adminaction($key).":</b></td><td align='left'>".$admin_secrets[$key]."</td>";
			print "</tr>";
		}
		
		
		//Admin verbs!
		arsort($admin_verb_use);
		print "<td colspan='4' align='center'><b><a name='adminverbs'><br><br>In the last $time_in_days days, the admins have used the following number of verbs</b> (<a href='#adminverbs'>link</a>)<br>Note: The syntax here is: ID : Description. If you see the ID repeated after the colon, it means there is no description for it - that this page was not updated since the new ID was added to the game. Please look up the ID ingame and send me a description via a <a href='http://www.ss13.eu/phpbb/ucp.php?i=pm&mode=compose&u=2'>private message</a> on the forum. Your help is greately appreciated.<br>NOTE: pray and adminhelp used by both players and admins. Other examples may also be usable by players.</td>";
		print "</tr>";
		foreach( $admin_verb_use as $key => $value ){
			if($adm_i == 0){
				$adm_i = 1;
				$adm_color = $adm_color1;
			}else{
				$adm_i = 0;
				$adm_color = $adm_color2;
			}
			print "<tr bgcolor='$adm_color'>";
			print "<td align='right'><b>$key : ".abbreviation2adminverb($key).":</b></td><td align='left'>".$value."</td>";
			print "</tr>";
		}
		
		
		print "<td colspan='4' align='center'><b><a name='cuffs'><br><br>In the last $time_in_days days, the following cuff statistics were gathered:</b> (<a href='#cuffs'>link</a>)</td>";
		print "</tr>";
		print "<tr bgcolor='#fff4cd'>";
		print "<td align='right'><b>People handcuffed with handcuffs: </b></td><td align='center'><b>$handcuffs</b></td>";
		print "<td><img src='handcuffs.png'></td><td></td>";
		print "</tr>";
		print "<tr bgcolor='#ffea9e'>";
		print "<td align='right'><b>People handcuffed with cablecuffs: </b></td><td align='center'><b>$cablecuffs</b></td>";
		print "<td><img src='cablecuffs.png'></td><td></td>";
		print "</tr>";
		print "<tr bgcolor='#fff4cd'>";
		print "<td align='right'><b>People legcuffed with a beartrap: </b></td><td align='center'><b>$beartraps</b></td>";
		print "<td><img src='handcuffs.png'></td><td></td>";
		print "</tr>";
		
		$width_handcuffs = floor( $handcuffs / ($handcuffs + $cablecuffs + $beartraps) * 800 );
		$width_cablecuffs = floor( $cablecuffs / ($handcuffs + $cablecuffs + $beartraps) * 800 );
		$width_beartraps = floor( $beartraps / ($handcuffs + $cablecuffs + $beartraps) * 800 );
		
		print "<tr bgcolor='white'>";
		print "<td align='center' colspan='4'><img src='bars/handcuffs.PNG' height='10' width='$width_handcuffs'><img src='bars/cablecuffs.PNG' height='10' width='$width_cablecuffs'><img src='bars/beartraps.PNG' height='10' width='$width_beartraps'><br>
		<font size='2'><b>Legend:</b> <img src='bars/handcuffs.PNG' height='10' width='10'> = handcuffs; <img src='bars/cablecuffs.PNG' height='10' width='10'> = cablecuffs; <img src='bars/beartraps.PNG' height='10' width='10'> = beartraps;</font>
		</td>";
		print "</tr>";
		
		//Jobban popularity!
		
		$adm_color1 = "#ffcccc";
		$adm_color2 = "#ffdddd";
		$adm_i = 0;
		
		print "<td colspan='4' align='center'><b><a name='jobbanpopularity'><br><br>In the last $time_in_days days, the most likely jobs to get jobbanned from were:</b> (<a href='#jobbanpopularity'>link</a>)<br>Note: Unbans no longer logged this way, sorry.</td>";
		print "</tr>";
		print "<tr bgcolor='$adm_color1'>";
		print "<td align='center'><b>Job</b></td><td align='center'><b>Bans</b></td>";
		print "<td></td><td align='center'><b>Relative jobban 'popularity'</b></td>";
		print "</tr>";
		
		arsort($jobban_popularity);
		$jobban_popularity_max = max($jobban_popularity);
		
		foreach($jobban_popularity as $job => $val){
			if(!isset($jobban_unban_popularity[$job])){
				$jobban_unban_popularity[$job] = 0;
			}
		}
		foreach($jobban_unban_popularity as $job => $val){
			if(!isset($jobban_popularity[$job])){
				$jobban_popularity[$job] = 0;
			}
		}
		
		foreach($jobban_popularity as $job => $val){
			if($adm_i == 0){
				$adm_i = 1;
				$adm_color = $adm_color2;
			}else{
				$adm_i = 0;
				$adm_color = $adm_color1;
			}
			
			$val_unbans = $jobban_unban_popularity[$job];
			$val_yellow = $jobban_unban_popularity[$job];
			$val_red = $val - $val_yellow;
			$val_green = 0;
			$more_unbans_than_bans = 0;
			if($val_red < 0){
				$val_green = abs($val_red);
				$val_yellow = $val_yellow - (2*$val_green);
				$more_unbans_than_bans = 1;
			}
			
			$ratio_red = $val_red / $jobban_popularity_max;
			$ratio_yellow = $val_yellow / $jobban_popularity_max;
			$ratio_green = $val_green / $jobban_popularity_max;
			
			$width_red = floor($ratio_red * 300);
			$width_yellow = floor($ratio_yellow * 300);
			$width_green = floor($ratio_green * 300);
			
			print "<tr bgcolor='$adm_color'>";
			print "<td align='right'><b>$job:</b></td><td align='left'>$val</td>";
			if(file_exists("jobs/$job.png")){
				print "<td width='50' bgcolor='$adm_color1'><img src='jobs/$job.png'></td>";
			}else if(file_exists("jobs/$job.gif")){
				print "<td width='50' bgcolor='$adm_color1'><img src='jobs/$job.gif'></td>";
			}else{
				print "<td width='50' bgcolor='$adm_color1'></td>";
			}
			if($more_unbans_than_bans == 1){
				print "<td align='left'><img src='good.png' height='10' width='$width_green'><img src='medi.png' height='10' width='$width_yellow'>";
			}else{
				print "<td align='left'><img src='bad.png' height='10' width='$width_red'><img src='medi.png' height='10' width='$width_yellow'>";
			}
			print "<font size='2'><br><b>Bans: $val; Unbans: $val_unbans</b></td>";
			print "</tr>";
			
		}

		//Chemical reactions!
		arsort($chemical_reactions);
		$color0 = "#a19cff";
		$color1 = "#d1cdff";
		$color2 = "#cde0ff";
		print "<tr>";
		print "<td colspan='4' align='center'><a name='chemistry'><b><br><br>In the last $time_in_days days the station's chemistry has produced: (<a href='#chemistry'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Chemical</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($chemical_reactions as $chem => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$chem</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}
		
		

		//food made!
		arsort($food_made);
		$color0 = "#FFF94B";
		$color1 = "#FFFCAA";
		$color2 = "#FFFA7C";
		print "<tr>";
		print "<td colspan='4' align='center'><a name='foodmade'><b><br><br>In the last $time_in_days days the following food was produced: (<a href='#foodmade'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Dish</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($food_made as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}

		//objects made!
		arsort($objects_made);
		$color0 = "#4BFFFD";
		$color1 = "#B2FFFE";
		$color2 = "#CFFFFE";
		print "<tr>";
		print "<td colspan='4' align='center'><a name='objectsmade'><b><br><br>In the last $time_in_days days the following objects were made: (<a href='#objectsmade'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Object</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($objects_made as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}

		//Assemblies made!
		arsort($assembly_made);
		$color0 = "#B556FF";
		$color1 = "#E3BEFF";
		$color2 = "#EED8FF"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='assembliesmade'><b><br><br>In the last $time_in_days days the following assemblies were made: (<a href='#assembliesmade'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Assembly</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($assembly_made as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}
		
		

		//Events ran!
		arsort($events_ran);
		$color0 = "#FF5656";
		$color1 = "#FFC2C2";
		$color2 = "#FFD7D7"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='eventsran'><b><br><br>In the last $time_in_days days the following events were ran: (<a href='#eventsran'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Event</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($events_ran as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}

		//Food Harvested!
		arsort($food_harvested);
		$color0 = "#7D581D";
		$color1 = "#DEB473";
		$color2 = "#ECD2A8"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='foodharvested'><b><br><br>In the last $time_in_days days the following food was harvested: (<a href='#foodharvested'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Crop</b></td><td align='center'><b>Amt</b></td><td></td><td><b>Distribution</b></td></tr>";
		foreach($food_harvested as $key => $info){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>".$info["TOTAL"]."</b></td>";
			print "<td></td>";
			print "<td align='left'><b>";
			ksort($info["DISTRIBUTION"]);
			foreach($info["DISTRIBUTION"] as $amt => $num){
				print "Number of harvests which yielded $amt crops: $num<br />";
			}
			print "</b></td>";
			print "</tr>";
		}
		
		//Mining pick usage!
		arsort($mining_pick_usage);
		$color0 = "#DFAC8A";
		$color1 = "#ECC3A8";
		$color2 = "#F1DFD3"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='miningpickusage'><b><br><br>In the last $time_in_days days the miners reported they used these picks the following number of times: (<a href='#miningpickusage'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Pick</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($mining_pick_usage as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}

		//Minerals mined!
		arsort($minerals_mined);
		$color0 = "#7D581D";
		$color1 = "#DEB473";
		$color2 = "#ECD2A8"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='mineralsmined'><b><br><br>In the last $time_in_days days the following minerals were mined: (<a href='#mineralsmined'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Mineral</b></td><td align='center'><b>Amt</b></td><td></td><td><b>Distribution</b></td></tr>";
		foreach($minerals_mined as $key => $info){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>".$info["TOTAL"]."</b></td>";
			print "<td></td>";
			print "<td align='left'><b>";
			ksort($info["DISTRIBUTION"]);
			foreach($info["DISTRIBUTION"] as $amt => $num){
				print "Number of tiles which yielded $amt minerals: $num<br />";
			}
			print "</b></td>";
			print "</tr>";
		}
		
		//Combat items!
		arsort($combat_items);
		$color0 = "#A16868";
		$color1 = "#E0B8B8";
		$color2 = "#EFD0D0"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='combatitem'><b><br><br>In the last $time_in_days days the following items were used in combat, sorted by the combined force they caused, also available is the number of hits which were made with these items and how many hits were made with items of a certain force: (<a href='#combatitem'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Mineral</b></td><td align='center'><b>total<br /> force</b></td><td>total<br />hits</td><td><b>Distribution</b></td></tr>";
		foreach($combat_items as $key => $info){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>".$info["TOTAL"]."</b></td>";
			print "<td>".$info["COUNT"]."</td>";
			print "<td align='left'><b>";
			ksort($info["DISTRIBUTION"]);
			foreach($info["DISTRIBUTION"] as $amt => $num){
				print "Number of hits when the item had force $amt: $num<br />";
			}
			print "</b></td>";
			print "</tr>";
		}
		
		//Zone targeted in combat!
		arsort($gun_fired);
		$color0 = "#DFAC8A";
		$color1 = "#ECC3A8";
		$color2 = "#F1DFD3"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='gunsfired'><b><br><br>In the last $time_in_days days the following guns were fired this many times (<a href='#gunsfired'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Gun</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($gun_fired as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}
		
		//Zone targeted in combat!
		arsort($combat_target_zone);
		$color0 = "#DFAC8A";
		$color1 = "#ECC3A8";
		$color2 = "#F1DFD3"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='combattargetzone'><b><br><br>In the last $time_in_days days combattants targetted these body parts this many times: (<a href='#combattargetzone'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Pick</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($combat_target_zone as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}
		
		//Mobs killed while mining!
		arsort($mobs_killed_mining);
		$color0 = "#DFAC8A";
		$color1 = "#ECC3A8";
		$color2 = "#F1DFD3"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='mobskilledmining'><b><br><br>In the last $time_in_days days the following mobs were killed while mining: (<a href='#mobskilledmining'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Pick</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($mobs_killed_mining as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}
		
		//Cell used!
		arsort($cell_used);
		$color0 = "#DFAC8A";
		$color1 = "#ECC3A8";
		$color2 = "#F1DFD3"; 
		print "<tr>";
		print "<td colspan='4' align='center'><a name='cellused'><b><br><br>In the last $time_in_days days the following cells were used: (<a href='#cellused'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='$color0'><td align='center'><b>Pick</b></td><td align='center'><b>Amt</b></td></tr>";
		foreach($cell_used as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "</tr>";
		}
		
		//Antagonists!
		print "<tr>";
		print "<td colspan='4' align='center'><a id='antagobjetive' name='antagobjetive'><b><br><br>In the last $time_in_days days, the antagonists have managed to complete this many objectives:</b> (<a href='#antagobjective'>link</a>)</td>";
		print "</tr>";
		
		$color0 = "#ffa3a3";
		$color1 = "#ffcbcb";
		$color2 = "#ffdcdc";
		
		foreach($antagonist_objective_success as $objective => $antag_type_tab){
			print "<tr>";
			print "<td colspan='4' bgcolor='$color0' align='center'><font size='5'><b>".type2objective($objective)."</b></font></td>";
			print "</tr>";
			$color = "";
			foreach($antag_type_tab as $antag => $success_type_tab){
				if(!isset($success_type_tab["SUCCESS"]))
					$success_type_tab["SUCCESS"] = 0;
				if(!isset($success_type_tab["FAIL"]))
					$success_type_tab["FAIL"] = 0;
				print "<tr bgcolor='$color1'>";
				$success_type_sum = array_sum($success_type_tab);
				print "<td colspan='4' bgcolor='$color1' align='center'><font size='4'><b>$antag</b> ($success_type_sum attempts)</font></td>";
				print "</tr>";
				print "<tr bgcolor='$color1'>";
				print "<td colspan='4' bgcolor='$color1' align='center'>";
				$width_success = floor(800 * ($success_type_tab["SUCCESS"] / $success_type_sum));
				$width_fail = 800 - $width_success;
				
				print "<img src='good.png' height='5' width='$width_success'>";
				print "<img src='bad.png' height='5' width='$width_fail'>";
				
				print "</td>";
				print "</tr>";
				
				print "<tr bgcolor='$color2'>";
				print "<td colspan='4' bgcolor='$color2' align='center'>";
				
				print "<table width='800'>";
				print "<tr>";
				print "<td align='center' width='300'><b>SUCCESS = </b>".$success_type_tab["SUCCESS"]." / $success_type_sum</td>";
				print "<td width='200' align='center'><b>(".percent2str($success_type_tab["SUCCESS"]/$success_type_sum).")</b></td>";
				print "<td align='center' width='300'><b>FAIL = </b>".$success_type_tab["FAIL"]." / $success_type_sum</td>";
				print "</tr>";
				print "</table>";
				
				print "</td>";
				print "</tr>";
			}
			
		}
		
		//Mining vendors https://github.com/tgstation/-tg-station/pull/17233 !
		print "<tr>";
		print "<td colspan='4' align='center'><a id='miningvendors' name='miningvendors'><b><br><br>In the last $time_in_days days, the following mining equipment was bought from these vendors:</b> (<a href='#miningvendors'>link</a>)</td>";
		print "</tr>";
		
		$color0 = "#FFD5A3";
		$color1 = "#FFE9CE";
		$color2 = "#FFF4E7";
		$sum = 0;
		
		foreach($data["mining_equipment_bought_total"] as $key => $val){
			$sum += $val;
		}
		
		foreach($data["mining_equipment_bought"] as $vendor => $value){
			print "<tr>";
			$thisVendor = $data["mining_equipment_bought_total"][$vendor];
			$thisVendorPercent = 0;
			if($sum > 0){
				$thisVendorPercent = $thisVendor / $sum;
			}
			$thisVendorPercent = floor($thisVendorPercent * 10000) / 100;
			
			print "<td colspan='4' bgcolor='$color0' align='center'><font size='5'><b>$vendor (Total: $thisVendor/$sum ($thisVendorPercent%))</b></font></td>";
			print "</tr>";
			
			arsort($value);
			print "<tr bgcolor='$color0'>";
			print "<td align='center' colspan='4'><font size='2'>";
			$count = 1;
			foreach($value as $key => $val){
				$percent = 0;
				if($thisVendor > 0){
					$percent = $val / $thisVendor;
				}
				$percentTxt = floor($percent * 10000) / 100;
				$width = $percent * 800;
				print "<img src='bars/p$count.png' title='$key ($val; $percentTxt%)' style='width: ".$width."px; height: 10px;'>";
				$count++;
			}
			print "<br><b>Legend: </b>";
			$count = 1;
			foreach($value as $key => $val){
				$percent = 0;
				if($thisVendor > 0){
					$percent = $val / $thisVendor;
				}
				$percentTxt = floor($percent * 10000) / 100;
				print "<img src='bars/p$count.png' title='$key ($val; $percentTxt%)' style='width: 10px; height: 10px;'> = $key; ";
				$count++;
			}
			print "<br />Hover over a part of the bar for details</font></td>";
			print "</tr>";
			foreach($value as $item => $val){
				if($color == $color1){$color=$color2;}else{$color = $color1;}
				$percent = 0;
				if($thisVendor > 0){
					$percent = $val / $thisVendor;
				}
				$percent = floor($percent * 10000) / 100;
				print "<tr bgcolor='$color'>";
				print "<td align='right'><b>$item</b></td><td align='center'><b>$val</b></td><td>$percent%</td>";
				print "</tr>";
			}
		}
		
		//Mining vouchers https://github.com/tgstation/-tg-station/pull/17233 !
		
		OutputStats("the following mining vouchers were redeemed:", "miningvouchers", "#FFD5A3", "#FFE9CE", "#FFF4E7", "mining_voucher_redeemed_total", "mining_voucher_redeemed");
		
		//jaunter https://github.com/tgstation/-tg-station/pull/17233 !
		print "<tr>";
		print "<td colspan='4' align='center'><a id='jaunter' name='jaunter'><b><br><br>In the last $time_in_days days, the following types of jaunter activations happened:</b> (<a href='#jaunter'>link</a>)</td>";
		print "</tr>";
		
		$color0 = "#FFD5A3";
		$color1 = "#FFE9CE";
		$color2 = "#FFF4E7";
		$sum = 0;
		
		$sum = $data["jaunter_total"];
		
		arsort($data["jaunter"]);
		print "<tr bgcolor='white'>";
		print "<td align='center' colspan='4'><font size='2'><b>Jaunter statistics (n = $sum)<br /></b>";
		$count = 1;
		foreach($data["jaunter"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			$width = $percent * 800;
			print "<img src='bars/p$count.png' title='".JaunterID2Descriptor($key)." ($val; $percentTxt%)' style='width: ".$width."px; height: 10px;'>";
			$count++;
		}
		print "<br><b>Legend: </b>";
		$count = 1;
		foreach($data["jaunter"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			print "<img src='bars/p$count.png' title='".JaunterID2Descriptor($key)." ($val; $percentTxt%)' style='width: 10px; height: 10px;'> = ".JaunterID2Descriptor($key)."; ";
			$count++;
		}
		print "<br />Hover over a part of the bar for details<br><br></font></td>";
		print "</tr>";
		foreach($data["jaunter"] as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percent = floor($percent * 10000) / 100;
			
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key: ".JaunterID2Descriptor($key)."</b></td><td align='center'><b>$val</b></td><td>$percent%</td>";
			print "</tr>";
		}
		
		//hivelord core https://github.com/tgstation/-tg-station/pull/17233 !
		
		OutputTwoTieredStats("the following hivelord cores have been used", "hivelord", "#A3A3A3", "#CECECE", "#E7E7E7", "hivelord_core_total", "hivelord_core");
		
		//Engine started https://github.com/tgstation/-tg-station/pull/17233 !
		print "<tr>";
		print "<td colspan='4' align='center'><a id='enginestarted' name='enginestarted'><b><br><br>In the last $time_in_days days, the following types of engines were started:</b> (<a href='#enginestarted'>link</a>)</td>";
		print "</tr>";
		
		$color0 = "#FFD5A3";
		$color1 = "#FFE9CE";
		$color2 = "#FFF4E7";
		$sum = 0;
		
		$sum = $data["engine_started_total"];
		
		arsort($data["engine_started"]);
		print "<tr bgcolor='white'>";
		print "<td align='center' colspan='4'><font size='2'><b>Engine startup distribution (n = $sum)<br /></b>";
		$count = 1;
		foreach($data["engine_started"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			$width = $percent * 800;
			print "<img src='bars/p$count.png' title='$key ($val; $percentTxt%)' style='width: ".$width."px; height: 10px;'>";
			$count++;
		}
		print "<br><b>Legend: </b>";
		$count = 1;
		foreach($data["engine_started"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			print "<img src='bars/p$count.png' title='$key ($val; $percentTxt%)' style='width: 10px; height: 10px;'> = $key; ";
			$count++;
		}
		print "<br />Hover over a part of the bar for details<br><br></font></td>";
		print "</tr>";
		
		foreach($data["engine_started"] as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percent = floor($percent * 10000) / 100;
			
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td><td>$percent%</td>";
			print "</tr>";
		}
		
		//Wisp https://github.com/tgstation/-tg-station/pull/17233 !
		print "<tr>";
		print "<td colspan='4' align='center'><a id='wisp' name='wisp'><b><br><br>In the last $time_in_days days, the following things were done to wisps:</b> (<a href='#wisp'>link</a>)</td>";
		print "</tr>";
		
		$color0 = "#FFD5A3";
		$color1 = "#FFE9CE";
		$color2 = "#FFF4E7";
		$sum = 0;
		
		$sum = $data["wisp_lantern_total"];
		
		arsort($data["wisp_lantern"]);
		print "<tr bgcolor='white'>";
		print "<td align='center' colspan='4'><font size='2'><b>Wisp statistics (n = $sum)<br /></b>";
		$count = 1;
		foreach($data["wisp_lantern"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			$width = $percent * 800;
			print "<img src='bars/p$count.png' title='".Wisp2Action($key)." ($val; $percentTxt%)' style='width: ".$width."px; height: 10px;'>";
			$count++;
		}
		print "<br><b>Legend: </b>";
		$count = 1;
		foreach($data["wisp_lantern"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			print "<img src='bars/p$count.png' title='".Wisp2Action($key)." ($val; $percentTxt%)' style='width: 10px; height: 10px;'> = ".Wisp2Action($key)."; ";
			$count++;
		}
		print "<br />Hover over a part of the bar for details<br><br></font></td>";
		print "</tr>";
		foreach($data["wisp_lantern"] as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percent = floor($percent * 10000) / 100;
			
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key : ".Wisp2Action($key)."</b></td><td align='center'><b>$val</b></td><td>$percent%</td>";
			print "</tr>";
		}
		
		//Immortality Talisman https://github.com/tgstation/-tg-station/pull/17233 !
		print "<tr>";
		print "<td colspan='4' align='center'><a id='immortalitytalisman' name='immortalitytalisman'><b><br><br>In the last $time_in_days days, the following things were done to immortality talismans:</b> (<a href='#immortalitytalisman'>link</a>)</td>";
		print "</tr>";
		
		$color0 = "#FFD5A3";
		$color1 = "#FFE9CE";
		$color2 = "#FFF4E7";
		$sum = 0;
		
		$sum = $data["immortality_talisman_total"];
		
		arsort($data["immortality_talisman"]);
		print "<tr bgcolor='white'>";
		print "<td align='center' colspan='4'><font size='2'><b>Talisman statistics (n = $sum)<br /></b>";
		$count = 1;
		foreach($data["immortality_talisman"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			$width = $percent * 800;
			print "<img src='bars/p$count.png' title='".ImTalisman2Action($key)." ($val; $percentTxt%)' style='width: ".$width."px; height: 10px;'>";
			$count++;
		}
		print "<br><b>Legend: </b>";
		$count = 1;
		foreach($data["immortality_talisman"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			print "<img src='bars/p$count.png' title='".ImTalisman2Action($key)." ($val; $percentTxt%)' style='width: 10px; height: 10px;'> = ".ImTalisman2Action($key)."; ";
			$count++;
		}
		print "<br />Hover over a part of the bar for details<br><br></font></td>";
		print "</tr>";
		foreach($data["immortality_talisman"] as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percent = floor($percent * 10000) / 100;
			
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key : ".ImTalisman2Action($key)."</b></td><td align='center'><b>$val</b></td><td>$percent%</td>";
			print "</tr>";
		}
		
		//cargo exports https://github.com/tgstation/-tg-station/pull/17728 !
		
		OutputStats("the following exports were sold", "exportssold", "#A3A3A3", "#CECECE", "#E7E7E7", "export_sold_amount_total", "export_sold_amount");
		
		//cargo export costs https://github.com/tgstation/-tg-station/pull/17728 !
		
		OutputStats("the following number of credits were gained from exports", "exportscosts", "#A3A3A3", "#CECECE", "#E7E7E7", "export_sold_cost_total", "export_sold_cost");
		
		//cargo imports https://github.com/tgstation/-tg-station/pull/17728 !
		
		OutputStats("the following imports were ordered", "imports", "#A3A3A3", "#CECECE", "#E7E7E7", "cargo_imports_total", "cargo_imports");
		
		//cargo imports https://github.com/tgstation/-tg-station/pull/17728 !
		
		OutputStats("the following imports were ordered (price)", "importcost", "#A3A3A3", "#CECECE", "#E7E7E7", "cargo_imports_cost_total", "cargo_imports_cost");
		
		//colony drops https://github.com/tgstation/tgstation/pull/20289 !
		
		OutputStats("the following colonies were dropped", "colonies", "#A3A3A3", "#CECECE", "#E7E7E7", "colonies_dropped_total", "colonies_dropped");
		
		//clock cult scriptures
		
		OutputStats("the following clockcult scriptures were recited", "scriptures_recited", "#A3A3A3", "#CECECE", "#E7E7E7", "clockcult_scripture_recited_total", "clockcult_scripture_recited");
		OutputStats("the following cult runes were scribed", "runes_scribed", "#A3A3A3", "#CECECE", "#E7E7E7", "cult_runes_scribed_total", "cult_runes_scribed");

		
		OutputStats("the following shuttle manipulation happened", "shuttle_manipulator", "#A3A3A3", "#CECECE", "#E7E7E7", "shuttle_manipulator_total", "shuttle_manipulator");
		OutputStats("the following shuttles were purchased", "shuttle_purchase", "#A3A3A3", "#CECECE", "#E7E7E7", "shuttle_purchase_total", "shuttle_purchase");
		
		OutputStats("the station has killed these types of megafauna", "megafauna_kills", "#A3FFA3", "#CEFFCE", "#E7FFE7", "megafauna_kills_total", "megafauna_kills");
		
		//$headerText, $linkID, $color0, $color1, $color2, $SumIdentifier, $DataIdentifier
		
		//Warp Cube https://github.com/tgstation/-tg-station/pull/17233 !
		print "<tr>";
		print "<td colspan='4' align='center'><a id='warpcube' name='warpcube'><b><br><br>In the last $time_in_days days, the following warp cubes were used:</b> (<a href='#warpcube'>link</a>)</td>";
		print "</tr>";
		
		$color0 = "#FFD5A3";
		$color1 = "#FFE9CE";
		$color2 = "#FFF4E7";
		$sum = 0;
		
		$sum = $data["warp_cube_total"];
		
		arsort($data["warp_cube"]);
		print "<tr bgcolor='white'>";
		print "<td align='center' colspan='4'><font size='2'><b>Wisp statistics (n = $sum)<br /></b>";
		$count = 1;
		foreach($data["warp_cube"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			$width = $percent * 800;
			print "<img src='bars/p$count.png' title='".($key)." ($val; $percentTxt%)' style='width: ".$width."px; height: 10px;'>";
			$count++;
		}
		print "<br><b>Legend: </b>";
		$count = 1;
		foreach($data["warp_cube"] as $key => $val){
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percentTxt = floor($percent * 10000) / 100;
			print "<img src='bars/p$count.png' title='".($key)." ($val; $percentTxt%)' style='width: 10px; height: 10px;'> = ".($key)."; ";
			$count++;
		}
		print "<br />Hover over a part of the bar for details<br><br></font></td>";
		print "</tr>";
		foreach($data["warp_cube"] as $key => $val){
			if($color == $color1){$color=$color2;}else{$color = $color1;}
			$percent = 0;
			if($sum > 0){
				$percent = $val / $sum;
			}
			$percent = floor($percent * 10000) / 100;
			
			print "<tr bgcolor='$color'>";
			print "<td align='right'><b>$key</b></td><td align='center'><b>$val</b></td><td>$percent%</td>";
			print "</tr>";
		}
		
		//Religions!
		print "<tr><td colspan='4' align='center'><b><a name='religions'><br><br>In the last $time_in_days days the following religions have been spread on the station:</b> (<a href='#religions'>link</a>)</td>";
		print "</tr>";
		
		$religion_name_list_max = max($religion_name_list);
		$religion_deity_list_max = max($religion_deity_list);
		$religion_book_list_max = max($religion_book_list);
		$chaplain_weapons_list_max = max($chaplain_weapons_list);
		
		arsort($religion_name_list);
		arsort($religion_deity_list);
		arsort($religion_book_list);
		arsort($chaplain_weapons_list);
		
		$color1 = "#fffcaa";
		$color2 = "#fffedd";
		
		//religion names
		$color = $color1;
		print "<tr bgcolor='$color'>";
		print "<td align='center'><b>Religion name</b></td><td align='center'><b>Num</b></td>";
		print "<td></td><td align='center'><b>Relative religion popularity</b></td>";
		print "</tr>";
		$i = 0;
		foreach($religion_name_list as $key => $val){
			if($color == $color1){$color=$color2;}else{$color=$color1;}
			$ratio = $val / $religion_name_list_max;
			$width_green = floor(300 * $ratio);
			print "<tr bgcolor='$color'>";
			print "<td align='center'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "<td bgcolor='$color1'>&nbsp;</td><td align='left'><img src='good.png' height='10' width='$width_green'></td>";
			print "</tr>";
			$i++;
			if(!isset($_GET["allreligion"])){
				if($i > 50){
					//print "<tr><td colspan='4' align='center'><a href='?allreligion=1'>show all</a>";
					print "</td></tr>";
					break;
				}
			}
		}
		
		print "<tr><td colspan='4' align='center'><br><br></td>";
		print "<tr><td colspan='4' align='center'><b><a name='deities'><br><br>In the last $time_in_days days the following deities have been used in religions on the station:</b> (<a href='#deities'>link</a>)</td>";
		print "</tr>";
		
		//religion deities
		$color1 = "#ffedb7";
		$color2 = "#fff7dd";
		$color = $color1;
		print "<tr bgcolor='$color'>";
		print "<td align='center'><b>Deity</b></td><td align='center'><b>Num</b></td>";
		print "<td></td><td align='center'><b>Relative deity popularity</b></td>";
		print "</tr>";
		$i = 0;
		foreach($religion_deity_list as $key => $val){
			if($color == $color1){$color=$color2;}else{$color=$color1;}
			$ratio = $val / $religion_deity_list_max;
			$width_green = floor(300 * $ratio);
			print "<tr bgcolor='$color'>";
			print "<td align='center'><b>$key</b></td><td align='center'><b>$val</b></td>";
			print "<td bgcolor='$color1'>&nbsp;</td><td align='left'><img src='good.png' height='10' width='$width_green'></td>";
			print "</tr>";
			$i++;
			if(!isset($_GET["allreligion"])){
				if($i > 50){
					//print "<tr><td colspan='4' align='center'><a href='?allreligion=1'>show all</a>";
					print "</td></tr>";
					break;
				}
			}
		}
		print "<tr><td colspan='4' align='center'><br><br></td>";
		print "<tr><td colspan='4' align='center'><b><a name='holybook'><br><br>In the last $time_in_days days the following holy books have been used in religions:</b> (<a href='#holybook'>link</a>)</td>";
		print "</tr>";
		
		//religion book
		$color1 = "#ffdfc0";
		$color2 = "#ffeedd";
		$color = $color1;
		print "<tr bgcolor='$color'>";
		print "<td align='center'><b>Book style name</b></td><td align='center'><b>Num</b></td>";
		print "<td></td><td align='center'><b>Relative book style popularity</b></td>";
		print "</tr>";
		foreach($religion_book_list as $key => $val){
			if($color == $color1){$color=$color2;}else{$color=$color1;}
			$ratio = $val / $religion_book_list_max;
			$width_green = floor(300 * $ratio);
			print "<tr bgcolor='$color'>";
			print "<td align='center'><b>$key</b></td><td align='center'><b>$val</b></td>";
			
			if(file_exists("book/$key.png")){
				print "<td width='50' bgcolor='$color1'><img src='book/$key.png'></td>";
			}else if(file_exists("book/$key.gif")){
				print "<td width='50' bgcolor='$color1'><img src='book/$key.gif'></td>";
			}else{
				print "<td width='50' bgcolor='$color1'>&nbsp;</td>";
			}
			
			print "<td align='left'><img src='good.png' height='10' width='$width_green'></td>";
			print "</tr>";
		}
		print "<tr><td colspan='4' align='center'><br><br></td>";
		print "<tr><td colspan='4' align='center'><b><a name='chaplainweapon'><br><br>In the last $time_in_days days, chaplains on the station have opted for the following weapons.</b> (<a href='#chaplainweapon'>link</a>)</td>";
		print "</tr>";
		
		//chaplain weapon
		$color1 = "#FFC0C0";
		$color2 = "#FFE0E0";
		$color = $color1;
		print "<tr bgcolor='$color'>";
		print "<td align='center'><b>Weapon name</b></td><td align='center'><b>Num</b></td>";
		print "<td></td><td align='center'><b>Chaplain weapon popularity</b></td>";
		print "</tr>";
		foreach($chaplain_weapons_list as $key => $val){
			if($color == $color1){$color=$color2;}else{$color=$color1;}
			$ratio = $val / $chaplain_weapons_list_max;
			$width_green = floor(300 * $ratio);
			print "<tr bgcolor='$color'>";
			print "<td align='center'><b>$key</b></td><td align='center'><b>$val</b></td>";
			
			if(file_exists("book/$key.png")){
				print "<td width='50' bgcolor='$color1'></td>";
			}else if(file_exists("book/$key.gif")){
				print "<td width='50' bgcolor='$color1'><img src='book/$key.gif'></td>";
			}else{
				print "<td width='50' bgcolor='$color1'>&nbsp;</td>";
			}
			
			print "<td align='left'><img src='good.png' height='10' width='$width_green'></td>";
			print "</tr>";
		}
		
		
		
		
		//Traitor items!
		
		print "<tr>";
		print "<td colspan='4' align='center'><a name='uplinkitems'><b><br><br>In the last $time_in_days days, the syndicate collaborators have bought this many items: (sorted by telecrystals spent) (<a href='#uplinkitems'>link</a>)</b></td>";
		print "</tr>";
		
		$uplink_round_count = $syndi_round + $rev_round + $trch_round + $traitor_round;
		$traitor_items_sum_total = array_sum ( $traitor_uplink_items_total );
		$traitor_items_sum_traitor = array_sum ( $traitor_uplink_items_traitor );
		$traitor_items_sum_trch = array_sum ( $traitor_uplink_items_trch );
		$traitor_items_sum_rev = array_sum ( $traitor_uplink_items_rev );
		$traitor_items_sum_nuke = array_sum ( $traitor_uplink_items_nuke );
		
		$cost_sum_total = 0;
		$cost_sum_traitor = 0;
		$cost_sum_trch = 0;
		$cost_sum_rev = 0;
		$cost_sum_nuke = 0;
		
		$traitor_item_telecrystals_spent_total = array();
		
		foreach($traitor_uplink_items_total as $abb => $val){
			if(!isset($traitor_uplink_items_cost[$abb])){
				$traitor_uplink_items_cost[$abb] = 0;
			}if(!isset($traitor_uplink_items_rev[$abb])){
				$traitor_uplink_items_rev[$abb] = 0;
			}if(!isset($traitor_uplink_items_trch[$abb])){
				$traitor_uplink_items_trch[$abb] = 0;
			}if(!isset($traitor_uplink_items_traitor[$abb])){
				$traitor_uplink_items_traitor[$abb] = 0;
			}if(!isset($traitor_uplink_items_nuke[$abb])){
				$traitor_uplink_items_nuke[$abb] = 0;
			}
		}
		
		foreach($traitor_uplink_items_total as $abb => $val){
			$cost_sum_total = $cost_sum_total + ( $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_total[$abb] );
			$cost_sum_traitor = $cost_sum_traitor + ( $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_traitor[$abb] );
			$cost_sum_trch = $cost_sum_trch + ( $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_trch[$abb] );
			$cost_sum_rev = $cost_sum_rev + ( $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_rev[$abb] );
			$cost_sum_nuke = $cost_sum_nuke + ( $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_nuke[$abb] );
			
			$traitor_item_telecrystals_spent_total[$abb] = $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_total[$abb];
		};
		
		$color1_title = "00CCCC";
		$color1_row1 = "EEFFFF";
		$color1_row2 = "DDFFFF";
		$color2_title = "8888FF";
		$color2_row1 = "EEEEFF";
		$color2_row2 = "DDDDFF";
		
		$color_i = 1;
		
		arsort($traitor_item_telecrystals_spent_total);
		
		$max_tc_spent = 0;
		
		foreach($traitor_item_telecrystals_spent_total as $abb => $val){
		
			if($max_tc_spent == 0){
				$max_tc_spent = $traitor_item_telecrystals_spent_total[$abb];
			}
		
			$cost = $traitor_uplink_items_cost[$abb];
			$cost_total = $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_total[$abb];
			$cost_traitor = $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_traitor[$abb];
			$cost_trch = $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_trch[$abb];
			$cost_rev = $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_rev[$abb];
			$cost_nuke = $traitor_uplink_items_cost[$abb] * $traitor_uplink_items_nuke[$abb];
			
			if($color_i == 1){
				$color_i = 0;
				$color_title = $color1_title;
				$color_row1 = $color1_row1;
				$color_row2 = $color1_row2;
			}else{
				$color_i = 1;
				$color_title = $color2_title;
				$color_row1 = $color2_row1;
				$color_row2 = $color2_row2;
			}
			
			$rowspan = 5;
			if($traitor_uplink_items_total[$abb] == 0){
				$rowspan = 1;
			}
			
			$img_popularity_width_good = floor($cost_total / $max_tc_spent * 250);
			$img_popularity_width_bad = 250-$img_popularity_width_good;
			$relative_popularity = percent2str($cost_total / $max_tc_spent);
			
			print "<tr bgcolor='#$color_title'>";
			print "<td align='right'>
				<b>".abbreviation2itemname($abb)." (".$cost."TC) (Total):</b><br>
				$relative_popularity <img src='good.png' width='$img_popularity_width_good' height='10' title='Relative weighted popularity: $relative_popularity ($cost_total / $max_tc_spent)'><img src='bad.png' width='$img_popularity_width_bad' height='10' title='Relative weighted popularity: $relative_popularity ($cost_total TC / $max_tc_spent TC)'>
			</td>
			<td align='left'>
				".$traitor_uplink_items_total[$abb]."
			</td>";
			print "<td width='50' rowspan='$rowspan'><img src='$abb.gif'></td>";
			print "<td width='50%'>
				It is used in ". percent2str($traitor_uplink_items_total[$abb] / $uplink_round_count ) ." rounds with uplinks. (".$traitor_uplink_items_total[$abb]." / $uplink_round_count)<br>
				Item popularity: ". percent2str($traitor_uplink_items_total[$abb] / $traitor_items_sum_total ) ." (".$traitor_uplink_items_total[$abb]." / $traitor_items_sum_total)<br>
				Weighted popularity: ". percent2str($cost_total / $cost_sum_total) ." (".$cost_total."TC / ".$cost_sum_total."TC)
			</td>";
			print "</tr>";
			if($traitor_uplink_items_total[$abb] == 0){
				continue;
			}
			print "<tr bgcolor='#$color_row1'>";
			print "<td align='right'><b>Traitor:</b></td><td align='left'>".$traitor_uplink_items_traitor[$abb]."</td>";
			if($traitor_uplink_items_traitor[$abb] > 0){
				print "<td width='50%'>
					It is used in ". percent2str($traitor_uplink_items_traitor[$abb] / $traitor_round ) ." traitor rounds. (".$traitor_uplink_items_traitor[$abb]." / $traitor_round)<br>
					Item popularity: ". percent2str($traitor_uplink_items_traitor[$abb] / $traitor_items_sum_traitor ) ." (".$traitor_uplink_items_traitor[$abb]." / $traitor_items_sum_traitor)<br>
					Weighted popularity: ". percent2str($cost_traitor / $cost_sum_traitor) ." (".$cost_traitor."TC / ".$cost_sum_traitor."TC)
				</td>";
			}
			print "</tr>";
			print "<tr bgcolor='#$color_row2'>";
			print "<td align='right'><b>Traitorchan:</b></td><td align='left'>".$traitor_uplink_items_trch[$abb]."</td>";
			if($traitor_uplink_items_trch[$abb] > 0){
				print "<td width='50%'>
					It is used in ". percent2str($traitor_uplink_items_trch[$abb] / $trch_round ) ." traitorchan rounds. (".$traitor_uplink_items_trch[$abb]." / $trch_round)<br>
					Item popularity: ". percent2str($traitor_uplink_items_trch[$abb] / $traitor_items_sum_trch ) ." (".$traitor_uplink_items_trch[$abb]." / $traitor_items_sum_trch)<br>
					Weighted popularity: ". percent2str($cost_trch / $cost_sum_trch) ." (".$cost_trch."TC / ".$cost_sum_trch."TC)
				</td>";
			}
			print "</tr>";
			print "<tr bgcolor='#$color_row1'>";
			print "<td align='right'><b>Nuclear Emergency:</b></td><td align='left'>".$traitor_uplink_items_nuke[$abb]."</td>";
			if($traitor_uplink_items_nuke[$abb] > 0){
				print "<td width='50%'>
					It is used in ". percent2str($traitor_uplink_items_nuke[$abb] / $syndi_round ) ." nuke rounds. (".$traitor_uplink_items_nuke[$abb]." / $syndi_round)<br>
					Item popularity: ". percent2str($traitor_uplink_items_nuke[$abb] / $traitor_items_sum_nuke ) ." (".$traitor_uplink_items_nuke[$abb]." / $traitor_items_sum_nuke)<br>
					Weighted popularity: ". percent2str($cost_nuke / $cost_sum_nuke) ." (".$cost_nuke."TC / ".$cost_sum_nuke."TC)
				</td>";
			}
			print "</tr>";
			print "<tr bgcolor='#$color_row2'>";
			print "<td align='right'><b>Revolution:</b></td><td align='left'>".$traitor_uplink_items_rev[$abb]."</td>";
			if($traitor_uplink_items_rev[$abb] > 0){
				print "<td width='50%'>
					It is used in ". percent2str($traitor_uplink_items_rev[$abb] / $rev_round ) ." revolution rounds. (".$traitor_uplink_items_rev[$abb]." / $rev_round)<br>
					Item popularity: ". percent2str($traitor_uplink_items_rev[$abb] / $traitor_items_sum_rev ) ." (".$traitor_uplink_items_rev[$abb]." / $traitor_items_sum_rev)<br>
					Weighted popularity: ". percent2str($cost_rev / $cost_sum_rev) ." (".$cost_rev."TC / ".$cost_sum_rev."TC)
				</td>";
			}
			print "</tr>";
		
		}
		
		//Slimes!
		
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='slime'><br><br>In the last $time_in_days days, the following statistics about slimes were gathered</b> (<a href='#slime'>link</a>)</td>";
		print "</tr>";
		
		foreach($slimes_stats as $stattype => $slimesarray){
			print "<tr>";
			print "<td colspan='4'>$stattype</td>";
			print "</tr>";
			foreach($slimesarray as $slimecolor => $slimecount){
				print "<tr>";
				print "<td align='right'><b>$slimecolor</b></td>";
				print "<td align='left'><b>$slimecount</b></td>";
				print "</tr>";
			}
		}
		
		//Wizard spell learning
		
		$color_row1 = "99ff99";
		$color_row2 = "bbffbb";
		$i = true;
		
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='wizspell'><br><br>In the last $time_in_days days, wizards have attacked the station knowing the following spells. (number of times a spell has been learned)<br>Note that this only counts the amount of spells learned after the last use of 'unmemorize' (including it)</b> (<a href='#wizspell'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='#$color_row1'>";
		
		print "<td align='center'><b>Spell</b></td><td>&nbsp;</td><td>&nbsp;</td><td align='center'><b>Popularity</b></td>";
		
		print "</tr>";
		
		arsort($wizard_spell_learned_actual);
		
		$wizard_spell_learned_actual_max = max($wizard_spell_learned_actual);
		$wizard_spell_percentage_max = $wizard_spell_learned_actual_max/$wizard_spell_learned_actual_sum;
		
		foreach ($wizard_spell_learned_actual as $abb => $val){
			$i = !$i;
			if($i){
				print "<tr bgcolor='#$color_row1'>";
			}else{
				print "<tr bgcolor='#$color_row2'>";
			}
			
			print "<td align='right'><b>$abb : ".abbreviation2spellname($abb)."</b></td><td>$val</td>";
			
			$spell_popularity = ($wizard_spell_learned_actual[$abb] / $wizard_spell_learned_actual_max);
			$spell_popularity_absolute = ($wizard_spell_learned_actual[$abb] / $wizard_spell_learned_actual_sum);
			$img_popularity_width_good = floor(270 * ($wizard_spell_learned_actual[$abb] / $wizard_spell_learned_actual_max));
			$img_popularity_width_bad = 270 - $img_popularity_width_good;
			
			print "<td bgcolor='#$color_row1'></td>";
			
			print "<td>";
			print "0 <img src='good.png' width='$img_popularity_width_good' height='10'><img src='bad.png' width='$img_popularity_width_bad' height='10'> ";
			print percent2str($wizard_spell_percentage_max);
			
			print "<font size='2'>";
			print "<br>Relative popularity: ".percent2str($spell_popularity)." (".$wizard_spell_learned_actual[$abb]."/$wizard_spell_learned_actual_max)";
			print "<br>Absolute popularity: ".percent2str($spell_popularity_absolute)." (".$wizard_spell_learned_actual[$abb]."/$wizard_spell_learned_actual_sum)";
			print "</font>";
			
			print "</td>";
			
			print "</tr>";
		}
		
		//Changeling powers
		
		$color_row2 = "ffffd9";
		$color_row1 = "feffb3";
		$i = true;
		
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='changelingpower'><br><br>In the last $time_in_days days, the changelings who attacked the station used the following powers:</b> (<a href='#changelingpower'>link</a>)</td>";
		print "</tr>";
		
		print "<tr bgcolor='#$color_row1'>";
		
		print "<td align='center'><b>Power</b></td><td>&nbsp;</td><td>&nbsp;</td><td align='center'><b>Popularity</b></td>";
		
		print "</tr>";
		
		arsort($changeling_powers);
		
		$changeling_powers_sum = array_sum($changeling_powers);
		$changeling_powers_max = max($changeling_powers);
		
		foreach ($changeling_powers as $abb => $val){
			$i = !$i;
			if($i){
				print "<tr bgcolor='#$color_row1'>";
			}else{
				print "<tr bgcolor='#$color_row2'>";
			}
			
			print "<td align='right'><b>$abb : ".abbreviation2changelingpower($abb)."</b></td><td>$val</td>";

			$changeling_powers_popularity = ($changeling_powers[$abb] / $changeling_powers_max);
			$img_popularity_width_good = floor(270 * ($changeling_powers_popularity));
			
			print "<td bgcolor='#$color_row1'></td>";
			
			print "<td>";
			print "0 <img src='good.png' width='$img_popularity_width_good' height='10'>";
			
			print "<font size='2'>";
			print "<br>Popularity: ".percent2str($changeling_powers_popularity)." (".$changeling_powers[$abb]."/$changeling_powers_sum)";
			print "</font>";
			
			print "</td>";

			print "</tr>";
		}
		
		//uptime!
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='uptime'><br><br>In the last $time_in_days days (". $time_in_days*24 ." hours), the server has been up for:</b> (<a href='#uptime'>link</a>)</td>";
		print "</tr>";
		print "<tr>";
		print "<td align='right'><b>Uptime (hrs):</b></td><td align='left'>".min2hour($uptime_minutes)."</td>";
		print "<td width='50'></td>";
		print "<td width='50%'>That's ".min2duration($uptime_minutes)."</td>";
		print "</tr>";
		print "<tr>";
		print "<td align='right'><b>Uptime (%):</b></td><td align='left'>". percent2str( $uptime_minutes / ($time_in_days*24*60) ) ."</td>";
		print "<td></td>";
		print "<td>100% means 100% uptime on one server, 200% means 100% uptime on two servers, etc.</td>";
		print "</tr>";
		print "<tr>";
		print "<td align='right'><b>Player hours:</b></td><td align='left'>". floor($hours_played) ."h</td>";
		print "<td width='50'></td>";
		print "<td width='50%'>That's ".min2duration($hours_played * 60)."</td>";
		print "</tr>";
		print "<tr>";
		print "<td align='right'><b>Rounds played:</b></td><td align='left'>$total_rounds</td>";
		print "</tr>";
		print "<tr>";
		print "<td align='right'><b>Properly completed rounds:</b></td><td align='left'>$successfully_completed_rounds</td>";
		print "</tr>";
		print "<tr>";
		print "<td align='right'><b>Properly completed rounds percentage:</b></td><td align='left'>". percent2str( $successfully_completed_rounds / $total_rounds ) ."</td>";
		print "</tr>";
		
		//Survival rate
		
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='survivalrate'><br><br>In the last $time_in_days days the number of roudns with the survival rates were:</b> (<a href='#survivalrate'>link</a>)</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td colspan='4' align='center' bgcolor='#FFCCAA'><b>Survival percentage</b><br>#survivors / (#survivors + #ghosts)</td>";
		print "</tr>";
		
		//round survoval percentage
		$rounds_survival_percentage_max = max($rounds_survival_percentage);
		
		for($i = 1; $i <= 10; $i++){
			$width_good = floor(300 * ($rounds_survival_percentage[$i]/$rounds_survival_percentage_max) );
			
			print "<tr>";
			print "<td align='right'><b>Survival rate between ". (($i-1)*10) ."% and ". ($i*10) ."%:</b></td><td align='left'>". ($rounds_survival_percentage[$i]) ."</td><td></td><td><img src='good.png' width='$width_good' height='10'></td>";
			print "</tr>";
		}
		
		//Round survival matrix absolute!
		print "<tr>";
		print "<td colspan='4' align='center' bgcolor='#FFCCAA'><b>Survivor(pop) matrix (Absolute round value)</b><br>#survivors / (#survivors + #ghosts) at population</td>";
		print "</tr>";
		
		$rounds_survival_matrix_max = 1;
		for($i = 1; $i <= 10; $i++){
			for($j = 1;$j <= 10; $j++){
				if($rounds_survival_matrix[$i][$j] > $rounds_survival_matrix_max){
					$rounds_survival_matrix_max = $rounds_survival_matrix[$i][$j];
				}
			}
		}
		
		for($i = 10; $i >= 1; $i--){
			$rounds_survival_matrix_sum[$i] = 0;
			$rounds_survival_matrix_colmax[$i] = 0;
		}
		print "<tr>";
		print "<td colspan='4'>";
		print "<table>";
		for($i = 10; $i >= 1; $i--){
			print "<tr>";
			print "<td align='right' width='100'><font size='2'>".(($i-1)*10)."% - ".($i*10)."%</font></td>";
			for($j = 1;$j <= 10; $j++){
				$intensity_green = floor(($rounds_survival_matrix[$i][$j] / $rounds_survival_matrix_max)*255);
				$intensity_green = str_pad(dechex($intensity_green),2,"0",STR_PAD_LEFT);
				print "<td align='center' bgcolor='#ff".$intensity_green."00' height='40' width='40'>".$rounds_survival_matrix[$i][$j]."</td>";
				$rounds_survival_matrix_sum[$j] +=$rounds_survival_matrix[$i][$j];
				if($rounds_survival_matrix_colmax[$j] < $rounds_survival_matrix[$i][$j]){
					$rounds_survival_matrix_colmax[$j] = $rounds_survival_matrix[$i][$j];
				}
			}
			print "</tr>";
		}
		print "<tr>";
		print "<td></td>";
		for($i = 1; $i <= 10; $i++){
			print "<td align='center'><font size='2'>".(($i-1)*10)." - ".(($i-1)*10+9)."</font></td>";
		}
		print "</tr>";
		print "<tr>";
		print "<td colspan='11' align='center'><font size='2'><b>Round population</b></font></td>";
		print "</tr>";
		print "</table>";
		
		//Round survival matrix percentual!
		print "<tr>";
		print "<td colspan='4' align='center' bgcolor='#FFCCAA'><b>Survivor(pop) matrix (Procentual representation - sum(percentages) at each population level == 100%)</b><br>( #survivors / (#survivors + #ghosts) ) / sum(roudns at pop) at population</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td colspan='4'>";
		print "<table>";
		for($i = 10; $i >= 1; $i--){
			print "<tr>";
			print "<td align='right' width='100'><font size='2'>".(($i-1)*10)."% - ".($i*10)."%</font></td>";
			for($j = 1;$j <= 10; $j++){
				$ratio = 0;
				$ratio_colmax = 0;
				if($rounds_survival_matrix_sum[$j] > 0){
					$ratio = $rounds_survival_matrix[$i][$j]/$rounds_survival_matrix_sum[$j];
				}
				if($rounds_survival_matrix_colmax[$j] > 0){
					$ratio_colmax = $rounds_survival_matrix[$i][$j]/$rounds_survival_matrix_colmax[$j];
				}
				$intensity_green = floor($ratio_colmax*255);

				$intensity_green = str_pad(dechex($intensity_green),2,"0",STR_PAD_LEFT);
				print "<td align='center' bgcolor='#ff".$intensity_green."00' height='40' width='40'>".percent2str($ratio)."</td>";
				print "<td width='30'>&nbsp;</td>";
			}
			print "</tr>";
		}
		print "<tr>";
		print "<td></td>";
		for($i = 1; $i <= 10; $i++){
			print "<td align='center'><font size='2'>".(($i-1)*10)." - ".(($i-1)*10+9)."</font></td><td>&nbsp;</td>";
		}
		print "</tr>";
		print "<tr>";
		print "<td colspan='11' align='center'><font size='2'><b>Round population</b></font></td>";
		print "</tr>";
		print "</table>";
		
		//Round escapee percentage
		print "<tr>";
		print "<td colspan='4' align='center' bgcolor='#FFCCAA'><b>Escapee percentage:</b><br>#escapees / (#survivors + #ghosts)</td>";
		print "</tr>";
		
		$rounds_escapee_percentage_max = max($rounds_escapee_percentage);
		
		for($i = 1; $i <= 10; $i++){
			$width_good = floor(300 * ($rounds_escapee_percentage[$i]/$rounds_escapee_percentage_max) );
			
			print "<tr>";
			print "<td align='right'><b>Escaped survivors between ". (($i-1)*10) ."% and ". ($i*10) ."%:</b></td><td align='left'>". ($rounds_escapee_percentage[$i]) ."</td><td></td><td><img src='good.png' width='$width_good' height='10'></td>";
			print "</tr>";
		}
		print "<tr>";
		print "<td colspan='4' align='center' bgcolor='#FFCCAA'><b>Escapee percentage</b><br>#escapees / #survivors</td>";
		print "</tr>";
		
		//round escaped survivor percentage
		$rounds_escaped_survivor_percentage_max = max($rounds_escaped_survivor_percentage);
		
		for($i = 1; $i <= 10; $i++){
			$width_good = floor(300 * ($rounds_escaped_survivor_percentage[$i]/$rounds_escaped_survivor_percentage_max) );
			
			print "<tr>";
			print "<td align='right'><b>Escaped survivor percentage between ". (($i-1)*10) ."% and ". ($i*10) ."%:</b></td><td align='left'>". ($rounds_escaped_survivor_percentage[$i]) ."</td><td></td><td><img src='good.png' width='$width_good' height='10'></td>";
			print "</tr>";
		}
		
		//Roudn duration!
		
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='rounddur'><br><br>In the last $time_in_days days there have beem this many rounds which lasted for:</b> (<a href='#rounddur'>link</a>)</td>";
		print "</tr>";
			
		$rounds_duration_max = max($rounds_duration);
			
		for($i = 0; $i < (ceil($duration_in_minutes_max/10)+1); $i++){
			if(!isset($rounds_duration[$i])){
				$rounds_duration[$i] = 0;
			}
			$width_good = floor(300 * ($rounds_duration[$i]/$rounds_duration_max) );
			
			print "<tr>";
			print "<td align='right'><b>Duration from ". ($i*10) ." to ". (($i+1)*10) ." minutes:</b></td><td align='left'>". ($rounds_duration[$i]) ."</td><td></td><td><img src='good.png' width='$width_good' height='10'></td>";
			print "</tr>";
		}
		
		
		//Round population!
		print "<tr>";
		print "<td colspan='4' align='center'><a name='roundpop'><b><br><br>In the last $time_in_days days there have beem this many rounds with the round-end populations:</b> (<a href='#roundpop'>link</a>)</td>";
		print "</tr>";
			
		$round_end_clients_num_10 = Array();
		for($i = 0; $i < 10; $i++){
			
			$round_sum = 0;
			for($j = 0; $j < 10; $j++){
				$round_sum += $round_end_clients_num[$i * 10 + $j];
			}
			$round_end_clients_num_10[$i] = $round_sum;
		}
			
		$round_end_clients_num_10_max = max($round_end_clients_num_10);
			
		for($i = 0; $i < 10; $i++){
			$width_good = floor(300 * ($round_end_clients_num_10[$i]/$round_end_clients_num_10_max) );
			
			print "<tr>";
			print "<td align='right'><b>Population of ". ($i*10) ." - ". ($i*10 + 9) .":</b></td><td align='left'>". ($round_end_clients_num_10[$i]) ."</td><td></td><td><img src='good.png' width='$width_good' height='10'></td>";
			print "</tr>";
		}
		
		//Population per day!
		
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='daypop'><br><br>In the last $time_in_days days the following daily populations were recorded:<br>Graph shows the total number of clients recorded at each full hour.<br><img src='good.png'> indicates the relative number of clients on that day.<br><img src='medi.png'> indicates the normalized number of clients on that day. ( (#clients / #recordings) * 24)<br><img src='bad.png'> indicates the recordings for that day are not reliable.</b> (<a href='#daypop'>link</a>)</td>";
		print "</tr>";
		
		$per_day_clients_max = max($per_day_clients);
		foreach($per_day_clients as $daycode => $val){
			$ratio = $val / $per_day_clients_max;
			$recordings = $per_day_clients_recordings[$daycode];
			$uptime_indicator = $recordings / 24;
			$title_error = "";
			$width_bad = 0;
			$width_good = 0;
			$width_medi = 0;
			$average_hourly_population = $val / $recordings;
			$ratio_normalized = ($average_hourly_population * 24) / $per_day_clients_max;
			$average_hourly_population_str = "";
			$width_medi = floor($ratio_normalized*300);
			//if($recordings >= 20 && $recordings <= 24){
				$width_good = floor(300 * $ratio);
				$width_medi = max(0,$width_medi-$width_good);
				$uptime_indicator_str = percent2str($uptime_indicator);
				$average_hourly_population_str = "<font size='2'>".num2print($average_hourly_population)."</font>";
			/*}else{
				$width_bad = floor(300 * $ratio);
				$width_medi = max(0,$width_medi-$width_bad);
				$uptime_indicator_str = percent2str($uptime_indicator);
				$title_error = "<font color='red' size='1'><br><b>Reading not reliable - Too few recordings ($recordings). Suspected downtime</b></font>";
				$average_hourly_population_str = "<font color='red' size='2'>".num2print($average_hourly_population)."</font>";
			}*/
			print "<tr>";
			print "<td align='right'><b>$daycode:$title_error<font size='1'><br>uptime indicator = $uptime_indicator_str<br>Average hourly population: $average_hourly_population_str</font></b></td><td align='left'>$val</td><td></td><td><img src='good.png' width='$width_good' height='10'><img src='bad.png' width='$width_bad' height='10'><img src='medi.png' width='$width_medi' height='10'><font size='2'><br><b>Based on $recordings recordings.</b></font></td>";
			print "</tr>";
		}
		
		//Hourly population!
		
		print "<tr>";
		print "<td colspan='4' align='center'><b><a name='hourpop'><br><br>In the last $time_in_days days the average population by hour has been:</b>(<a href='#hourpop'>link</a>)</td>";
		print "</tr>";
		
		for($i = 1; $i <= 24; $i++){
			$round_end_clients_hour_ratio[$i] = $round_end_clients_hour[$i] / $round_end_clients_hour_recordings[$i];
		}
		
		$round_end_clients_hour_ratio_max = max($round_end_clients_hour_ratio);
		
		for($i = 1; $i <= 24; $i++){
		
			$width_good = floor(300 * ($round_end_clients_hour_ratio[$i]/$round_end_clients_hour_ratio_max) );
			//$width_bad = 300-$width_good;
		
			print "<tr>";
			print "<td align='right'><b>Average population at ".$i.":00 is:<b> <font size='2'><br>Calculated from ".$round_end_clients_hour_recordings[$i]." recordings.</td><td align='left'>". num2print($round_end_clients_hour_ratio[$i]) ."</font></td><td></td><td><img src='good.png' width='$width_good' height='10'></td>";
			print "</tr>";
		
		}

		
		print "</table>";
		
		$uptime_percentage = array();
		for($i = 0; $i < 1440; $i++){
			$uptime_percentage[$i] = 0;
			$uptime_measurments[$i] = 0;
		}
		
		
		/* Chronograph is broken :(
		$state = "none";
		print "<table align='center'>";
		print "<tr><td align='center'>";
		print "<a name='cronograph'>";
		print "<b>Server uptime chronograph:</b> (<a href='#cronograph'>link</a>)";
		print "</td></tr><tr><td>";
		foreach($round_startends as $val){
		
			$tab = explode("-",$val);
			
			$stat = $tab[0];
			$end = $tab[1] %1440;
			
			if($start > $end){
				$current_length = 1440 - $start;
				$remaining_length = $end;
				print "<img src='".$state.".png' height='10' width='$current_length'>";
				print "<br>";
				print "<img src='".$state.".png' height='10' width='$remaining_length'>";
				if($state != "none"){
					for($i = $start; $i < 1440; $i++){
						$uptime_measurments[$i]++;
					}
					for($i = 0; $i < $end; $i++){
						$uptime_measurments[$i]++;
					}
				}
				
				if($state == "good"){
					for($i = $start; $i < 1440; $i++){
						$uptime_percentage[$i]++;
					}
					for($i = 0; $i < $end; $i++){
						$uptime_percentage[$i]++;
					}
				}
				
			}else{
				$length = $end-$start;
				print "<img src='".$state.".png' height='10' width='$length'>";
				if($state != "none"){
					for($i = $start; $i < $end; $i++){
						$uptime_measurments[$i]++;
					}
				}
				if($state == "good"){
					for($i = $start; $i < $end; $i++){
						$uptime_percentage[$i]++;
					}
				}
			}
			
			if($stat == "s"){
				$state = "good";
			}else{
				$state = "bad";
			}
			$start = $end;
		}
		print "</td></tr>";
		
		for($i = 0; $i < 1440; $i++){
			$uptime_percentage_ratio[$i] = floor(100 * $uptime_percentage[$i] / $uptime_measurments[$i]);
		}
		
		$uptime_percentage_ratio_min = min($uptime_percentage_ratio);
		$uptime_percentage_ratio_max = max($uptime_percentage_ratio);
		$div_factor = ($uptime_percentage_ratio_max - $uptime_percentage_ratio_min) / 10;
		
		print "<tr><td align='center'>";
		print "<b>Average uptime at a time of day:</b>";
		print "<font size='2'><br>";
		print "<b>Legend: </b><img src='bars/0.PNG'> = ".$uptime_percentage_ratio_min."% average uptime; <img src='bars/10.PNG'> = ".$uptime_percentage_ratio_max." average uptime.</font>";
		
		print "</td></tr>";
		print "<tr><td>";
		
		for($i = 0; $i < 1440; $i++){
			$num = $uptime_percentage_ratio[$i] - $uptime_percentage_ratio_min;
			$num_normalized = floor($num / $div_factor);
			print "<img src='bars/$num_normalized.PNG' height='30' width='1'>";
		}
		
		print "</td></tr>";
		print "</table>";
		
		*/
		
		print '<script src="js/chartist.min.js"></script>';
		
		$out = ob_get_contents();
		//ob_end_clean();
		
		if(!file_exists("output/")){
			mkdir("output");
		}
		
		
		$out = "<script type='text/javascript'>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38190387-1']);
  _gaq.push(['_trackPageview']);

  (function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script><div align='center'>Page auto-generated by <b>$user</b> on <b>".date ( "j F Y")."</b> at <b>". date("H:i:s")."</b> in <b>". date("T") ." (GMT ".date("O").")</b></div>".$out;
		
		if($startDaysAgo == 0 && ($time_in_days == $maxAllowedNumberOfDays)){
			$f = fopen("latest_stats.html", 'w');
			fwrite($f, $out);
			fclose($f);
		}
		
		$f = fopen("stats_$year-$month-$day"."_to_$yearEnd-$monthEnd-$dayEnd".".html", 'w');
		fwrite($f, $out);
		fclose($f);
		
	}else{
		print "<div align='center'>Please log in.</div>";
	}
?>
	
</body>
</html>
