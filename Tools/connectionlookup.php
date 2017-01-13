<?php
include("db.php");

$minwage = Array();
$minwage['Afghanistan'] = 0.5;
$minwage['Albania'] = 1.2;
$minwage['Algeria'] = 1.29;
$minwage['Andorra'] = 7.47;
$minwage['Angola'] = 0.95;
$minwage['Antigua and Barbuda'] = 3.04;
$minwage['Argentina'] = 3.84;
$minwage['Armenia'] = 0.69;
$minwage['Australia'] = 15.58;
$minwage['Austria'] = 0;
$minwage['Azerbaijan'] = 0.77;
$minwage['The Bahamas'] = 4;
$minwage['Bahrain'] = 0;
$minwage['Bangladesh'] = 0.09;
$minwage['Barbados'] = 3.13;
$minwage['Belarus'] = 1.3;
$minwage['Belgium'] = 12.1;
$minwage['Belize'] = 1.65;
$minwage['Benin'] = 0.47;
$minwage['Bhutan'] = 0.35;
$minwage['Bolivia'] = 1.09;
$minwage['Bosnia and Herzegovina'] = 1.22;
$minwage['Botswana'] = 0.29;
$minwage['Brazil'] = 2.12;
$minwage['Brunei'] = 0;
$minwage['Bulgaria'] = 1.7;
$minwage['Burkina Faso'] = 0.4;
$minwage['Burundi'] = 0;
$minwage['Cambodia'] = 0;
$minwage['Cameroon'] = 0.42;
$minwage['Canada'] = 9.22;
$minwage['Cape Verde'] = 0.69;
$minwage['Central African Republic'] = 0.44;
$minwage['Chad'] = 0.72;
$minwage['Chile'] = 2.02;
$minwage['China'] = 0.8;
$minwage['Colombia'] = 1.66;
$minwage['Comoros'] = 0.86;
$minwage['Democratic Republic of the Congo'] = 0.2;
$minwage['Republic of the Congo'] = 1.05;
$minwage['Costa Rica'] = 1.84;
$minwage["C�te d'Ivoire"] = 0.43;
$minwage['Croatia'] = 3.04;
$minwage['Cuba'] = 0.05;
$minwage['Cyprus'] = 0;
$minwage['Czech Republic'] = 2.83;
$minwage['Denmark'] = 0;
$minwage['Djibouti'] = 0;
$minwage['Dominica'] = 1.48;
$minwage['Dominican Republic'] = 0.39;
$minwage['Ecuador'] = 2.38;
$minwage['Egypt'] = 0;
$minwage['El Salvador'] = 0.5;
$minwage['Equatorial Guinea'] = 2.64;
$minwage['Eritrea'] = 0;
$minwage['Estonia'] = 3.24;
$minwage['Ethiopia'] = 0;
$minwage['Federated States of Micronesia'] = 0;
$minwage['Fiji'] = 1.23;
$minwage['Finland'] = 0;
$minwage['France'] = 12.83;
$minwage['Gabon'] = 1.75;
$minwage['The Gambia'] = 0.12;
$minwage['Georgia'] = 0.07;
$minwage['Germany'] = 11.28;
$minwage['Ghana'] = 0.3;
$minwage['Greece'] = 5.23;
$minwage['Grenada'] = 0;
$minwage['Guatemala'] = 1.22;
$minwage['Guinea'] = 0;
$minwage['Guinea-Bissau'] = 0.2;
$minwage['Guyana'] = 0.98;
$minwage['Haiti'] = 0.35;
$minwage['Honduras'] = 1.01;
$minwage['Hong Kong'] = 4.19;
$minwage['Hungary'] = 2.52;
$minwage['Iceland'] = 0;
$minwage['India'] = 0.31;
$minwage['Indonesia'] = 0.53;
$minwage['Iran'] = 1.24;
$minwage['Iraq'] = 1.24;
$minwage['Ireland'] = 12.14;
$minwage['Israel'] = 6.99;
$minwage['Italy'] = 0;
$minwage['Jamaica'] = 1.26;
$minwage['Japan'] = 6.54;
$minwage['Jordan'] = 1.29;
$minwage['Kazakhstan'] = 0.69;
$minwage['Kenya'] = 0.25;
$minwage['Kiribati'] = 0;
$minwage['South Korea'] = 5.51;
$minwage['North Korea'] = 0;
$minwage['Kosovo'] = 1.3;
$minwage['Kuwait'] = 1.01;
$minwage['Kyrgyzstan'] = 0.1;
$minwage['Laos'] = 0.84;
$minwage['Latvia'] = 2.87;
$minwage['Lebanon'] = 2.15;
$minwage['Lesotho'] = 0.56;
$minwage['Liberia'] = 0.18;
$minwage['Libya'] = 2.04;
$minwage['Liechtenstein'] = 0;
$minwage['Lithuania'] = 2.41;
$minwage['Luxembourg'] = 14.75;
$minwage['Republic of Macedonia'] = 1.37;
$minwage['Madagascar'] = 0.26;
$minwage['Malawi'] = 0.16;
$minwage['Malaysia'] = 1.18;
$minwage['Maldives'] = 0;
$minwage['Mali'] = 0.28;
$minwage['Malta'] = 5.9;
$minwage['Marshall Islands'] = 2;
$minwage['Mauritania'] = 0.58;
$minwage['Mauritius'] = 0.44;
$minwage['Mexico'] = 0.62;
$minwage['Moldova'] = 0.41;
$minwage['Monaco'] = 13.47;
$minwage['Mongolia'] = 0.61;
$minwage['Montenegro'] = 1.48;
$minwage['Morocco'] = 0.94;
$minwage['Mozambique'] = 0.55;
$minwage['Myanmar'] = 0.46;
$minwage['Namibia'] = 0;
$minwage['Nauru'] = 0;
$minwage['Nepal'] = 0.39;
$minwage['Netherlands'] = 11.67;
$minwage['New Zealand'] = 12.24;
$minwage['Nicaragua'] = 0.5;
$minwage['Niger'] = 0.35;
$minwage['Nigeria'] = 0.65;
$minwage['Norway'] = 0;
$minwage['Oman'] = 4.33;
$minwage['Pakistan'] = 0.62;
$minwage['Palau'] = 3;
$minwage['Panama'] = 1.22;
$minwage['Papua New Guinea'] = 1.3;
$minwage['Paraguay'] = 1.97;
$minwage['Peru'] = 1.27;
$minwage['Philippines'] = 0.69;
$minwage['Poland'] = 3.2;
$minwage['Portugal'] = 4.51;
$minwage['Qatar'] = 0;
$minwage['Romania'] = 1.81;
$minwage['Russia'] = 0.9;
$minwage['Rwanda'] = 0;
$minwage['Saint Kitts and Nevis'] = 3.33;
$minwage['Saint Lucia'] = 0;
$minwage['Saint Vincent and the Grenadines'] = 1.16;
$minwage['Samoa'] = 0.86;
$minwage['San Marino'] = 12.92;
$minwage['S�o Tom� and Pr�ncipe'] = 0;
$minwage['Saudi Arabia'] = 3.85;
$minwage['Senegal'] = 0.37;
$minwage['Serbia'] = 1.84;
$minwage['Seychelles'] = 2.09;
$minwage['Sierra Leone'] = 0.64;
$minwage['Singapore'] = 0;
$minwage['Slovakia'] = 3.09;
$minwage['Slovenia'] = 6.05;
$minwage['Solomon Islands'] = 0.43;
$minwage['Somalia'] = 0;
$minwage['South Africa'] = 0;
$minwage['Spain'] = 5.79;
$minwage['Sri Lanka'] = 0.29;
$minwage['Sudan'] = 0.43;
$minwage['Suriname'] = 0;
$minwage['Swaziland'] = 0.33;
$minwage['Sweden'] = 0;
$minwage['Switzerland'] = 0;
$minwage['Syria'] = 1.02;
$minwage['Taiwan'] = 4.05;
$minwage['Tajikistan'] = 0.29;
$minwage['Tanzania'] = 0.12;
$minwage['Thailand'] = 1.15;
$minwage['Timor-Leste'] = 0.6;
$minwage['Togo'] = 0.41;
$minwage['Tonga'] = 0;
$minwage['Trinidad and Tobago'] = 2.34;
$minwage['Tunisia'] = 0.59;
$minwage['Turkey'] = 2.82;
$minwage['Turkmenistan'] = 1.08;
$minwage['Tuvalu'] = 0;
$minwage['Uganda'] = 0.01;
$minwage['Ukraine'] = 0.66;
$minwage['United Arab Emirates'] = 0;
$minwage['United Kingdom'] = 11.02;
$minwage['United States'] = 7.25;
$minwage['Uruguay'] = 2.15;
$minwage['Uzbekistan'] = 0.27;
$minwage['Vanuatu'] = 1.75;
$minwage['Venezuela'] = 0.23;
$minwage['Vietnam'] = 0.59;
$minwage['Yemen'] = 0;
$minwage['Zambia'] = 0.41;
$minwage['Zimbabwe'] = 0;


$ip = $_SERVER['REMOTE_ADDR'];

function days_diff($d1, $d2) {
    $x1 = days($d1);
    $x2 = days($d2);
    
    if ($x1 && $x2) {
        return abs($x1 - $x2);
    }
}

$d = mysql_query("SELECT ckey FROM ss13player WHERE ip = '$ip' ") or die(mysql_error());

$limit = 10;
if(mysql_num_rows($d) > 0){
	while($i = mysql_fetch_array($d)){	
		$ckey = $i["ckey"];
		$limit--;
		
		
		if(isset($_GET["test"])){ 
			$ckey = $_GET["test"];
		}
		
		$d2 = mysql_query("SELECT COUNT(1) as 'abc', MIN(datetime) as mintime FROM ss13connection_log WHERE ckey = '$ckey' GROUP BY ckey ") or die(mysql_error());

		print "The average round length is 66.48 minutes.<br>";
		print "On average, people connect 1.90 times per round.<br><br><br>";
		
		while($i2 = mysql_fetch_array($d2)){	
			$connections = $i2["abc"];
			$rounds = intval($connections / 1.90);
			$time = intval($rounds * 66.48);
			
			$mintime = $i2["mintime"];
			
			$phpdate = strtotime( $mintime );
			$mysqldate = date( 'Y-m-d H:i:s', $phpdate );
			
			$now = time(); // or your date as well
			$your_date = strtotime($mintime);
			$datediff = $now - $your_date;
			$daydiff = floor($datediff/(60*60*24));
			
			$percentage = (intval(($time * 60 * 10000) / $datediff) * 1.0) / 100;
			$tags = json_decode(file_get_contents('http://getcitydetails.geobytes.com/GetCityDetails?fqcn='. $ip), true);
			$country = $tags["geobytescountry"];
			$hours = (intval(($time * 10) / 60)/ 10);
			
			print "Hello <b>$ckey</b>, since logging started in January 2013, you connected to TGStation <b>".$connections."</b> times. Your first connection was on <b>$mysqldate</b> (<b>$daydiff</b> days ago). You probably played in about <b>$rounds</b> rounds, or somewhere in the ballpark of <b>$time</b> minutes, which is <b>$hours</b> hours, or <b>".(intval(($time * 10) / 60 / 24)/ 10)."</b> full days, or <b>".(intval(($time*100) / 60 / 24 /30)/100)."</b> full months. In this time span, you've spent <b>$percentage%</b> of your time alive playing TGStation13! Looking up your IP on geobytes.com indicates that you live in <b>".$country."</b>. ";
			
			if(isset($minwage[$country]) && $minwage[$country] != 0){
				$wage = $minwage[$country];
				$earnings = $wage * $hours;
				print "According to Wikipedia, the minimum hourly wage in this counry is <b>$".$wage."</b>. Had you spent this time working for minimum wage, you'd have been <b>$".$earnings."</b> richer. ";
			}else{
				print "Unfortunately I don't know what your country's minimum wage is to let you know how much money you could have made if you worked for minimum wage. I can't make you sad... Dang. ";
			}
			
			$ects = intval(($hours * 10) / 25) / 10;
			$courses = intval(($ects * 100) / 6) / 100;
			$uniYears = intval(($courses * 100) / 10) / 100;
			print "Had you spent this time studying, you would have earned <b>$ects</b> <a href='https://en.wikipedia.org/wiki/European_Credit_Transfer_and_Accumulation_System'>ECTS credits</a>, which is enough to pass <b>$courses</b> university level courses. This would have gotten you through <b>$uniYears</b> years of university.";
			
			print "<br>";
			
			//print_r($tags);
		}

		if($limit <= 0){
			break;
		}
	}
}else{
	print "Sorry, I don't know who you are. Connect to any <a href='https://tgstation13.org'>TGStation13</a> server and then come back.";
}

?>