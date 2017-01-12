<?php
include("db.php");
$ip = $_SERVER['REMOTE_ADDR'];

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

print "<h1 align='center'>/tg/ station 13 Book Club</h1>";

$viewadminlog = 0;

$color1 = "#f8f8f8";
$color2 = "#f4f4f4";
$color = "";

if($auth == 0){
	print "<div align='center'>";
	print "<form method='POST'>";
	print "<b>Log in</b>";
	print "<br>Not a member? Currently not accepting new members, sorry. Go back to the index <a href='../index.html'>here</a>";
	print "<br><b>Username:</b> <input type='text' name='un'>";
	print "<br><b>Password:</b> <input type='password' name='pw'>";
	print "<br><input type='submit' name='Log in'>";
	print "</form>";
	print "</div>";
}else{

	if(isset($_POST["deleteid"])){
		$deleteid = intval($_POST["deleteid"]);
		
		$d0 = mysql_query("
					SELECT id FROM ss13library WHERE id = $deleteid AND NOT isnull(deleted);
		");
		
		if($iv = mysql_fetch_array($d0)){
			print "<div align='center'><font color='red'><b>Book at id $deleteid is already deleted</b></font></div>";
		}else{
			
			$d1 = mysql_query("
						INSERT INTO erro_library_log (id, username, ip, datetime, bookid, description)
						VALUES (null, '$user', '$ip', Now(), $deleteid, 'User $user deleted book $deleteid');
			") or die(mysql_error());
			
			$d2 = mysql_query("
						UPDATE ss13library SET deleted = 1 WHERE id = $deleteid
			") or die(mysql_error());
			
			print "<div align='center'><font color='red'><b>You deleted book id $deleteid</b></font></div>";
		}
	}
	
	if(isset($_POST["undeleteid"])){
		$undeleteid = intval($_POST["undeleteid"]);
		
		$d0 = mysql_query("
					SELECT id FROM ss13library WHERE id = $undeleteid AND isnull(deleted);
		");
		
		if($iv = mysql_fetch_array($d0)){
			print "<div align='center'><font color='red'><b>Book at id $undeleteid is not deleted</b></font></div>";
		}else{
		
			$d1 = mysql_query("
						INSERT INTO erro_library_log(id,username,ip,datetime,bookid,description)
						VALUES (null,'$user','$ip',Now(),$undeleteid,'User $user undeleted book $undeleteid');
			") or die(mysql_error());
			
			$d2 = mysql_query("
						UPDATE ss13library SET deleted = null WHERE id = $undeleteid
			") or die(mysql_error());
			
			print "<div align='center'><font color='red'><b>You undeleted book id $undeleteid</b></font></div>";
		}
	}
	
	$dv = mysql_query("SELECT * FROM erro_library_veto WHERE username = '$user'") or die(mysql_error());
				
	$vetos = 0;
	if($iv = mysql_fetch_array($dv)){
		$vetos = intval($iv["vetos"]);
	}
	print "<div align='center'>Logged in as $user. You have $vetos deletion-stopping veto(s) available.</div><p>";

	print "<div align='center'>";
	print "<form method='POST'>";
	print "<input type='hidden' name='un' value='foo'>";
	print "<input type='hidden' name='pw' value='bar'>";
	print "<input type='submit' value='Log out'>";
	print "</form>";
	print "</div>";
	
	print "<div align='center'>";
	print "<form method='GET'>";
	print "<b>Search books by content:</b> <input type='text' name='filter' size='50' width='500'>";
	print "<input type='submit' value='Search'>";
	print "</form>";
	print "</div>";
	
	if(isset($_POST["bookidsave"])){
		$bookid = intval($_POST["bookidsave"]);
		$mark = "";
		for($j = 1; $j <= 15; $j++){
			if(isset($_POST["r$j"])){
				switch($j){
					case 1:
						$p = "GOOD";
					break;
					case 2:
						$p = "COPIED";
					break;
					case 3:
						$p = "OUTDATED";
					break;
					case 4:
						$p = "BADCONTENT";
					break;
					case 5:
						$p = "OVEROFFENSIVE";
					break;
					case 6:
						$p = "OVERSEXUAL";
					break;
					case 7:
						$p = "COPYRIGHTED";
					break;
					case 8:
						$p = "BROKEN";
					break;
					case 9:
						$p = "ABUSESHTML";
					break;
					case 10:
						$p = "WRONGCATEGORY";
					break;
					case 11:
						$p = "OTHER";
					break;
					case 12:
						$p = "FUNNY";
					break;
					case 13:
						$p = "CLASSIC";
					break;
					case 14:
						$p = "UNFINISHED";
					break;
					case 15:
						$p = "HISTORICAL";
					break;
				}
				if($mark == ""){
					$mark = $p;
				}else{
					$mark = $mark."|".$p;
				}
			}
		}
		$a = "";
		if(isset($_POST["a1"])){
			switch(intval($_POST["a1"])){
				case 1:
					$a = "KEEP";
				break;
				case 2:
					$a = "UPDATE";
				break;
				case 3:
					$a = "DELETE";
				break;
			}
		}
		$fail = 0;
		if($a == ""){
			$fail = 1;
			print "<div align='center'>No action selected</div>";
		}
		if($mark == ""){
			$fail = 1;
			print "<div align='center'>No review marks selected</div>";
		}
		
		if($fail == 0){
			$df = mysql_query("SELECT id FROM erro_library_review WHERE bookid = $bookid AND username = '$user'") or die(mysql_error());
			
			if($if = mysql_fetch_array($df)){
				$fail = 1;
				if(!isset($_POST["update"])){
					print "<div align='center'>";
					print "<form method='POST'>";
					print "<input type='hidden' name='bookidsave' value='$bookid'>";
					for($j = 1; $j <= 15; $j++){
						if(isset($_POST["r$j"])){
							print "<input type='hidden' name='r$j' value='1'>";
						}
					}
					print "<input type='hidden' name='a1' value='".$_POST["a1"]."'>";
					print "<input type='hidden' name='update' value='1'>";
					print "<div align='center'><b>Error: You have already logged a review for this book.</b></div>";
					print "<br><b>Update old review with new values?</b> <input type='submit' value='Update'>";
					print "</form>";
					print "</div>";
				}
			}
		}
		
		if($fail == 0){
			print "Reviers are now closed.";
			//$di = mysql_query("INSERT INTO `erro_library_review`
			//(`id`,`datetime`,`bookid`,`username`,`mark`,`suggested_action`)
			//VALUES(null,Now(),$bookid,'$user','$mark','$a');") or die(mysql_error());
			//print "<div align='center'><b>Success: Review saved.</b></div>";
		}else if(isset($_POST["update"])){
			//print "UPDATE erro_library_review SET mark = '$mark', suggested_action = '$a' WHERE bookid = $bookid AND username = '$user'";
			print "Reviers are now closed.";
			/*$du = mysql_query("UPDATE erro_library_review SET mark = '$mark', suggested_action = '$a' WHERE bookid = $bookid AND username = '$user'") or die(mysql_error());
			print "<div align='center'><b>Success: Review updated.</b></div>";*/
		}
		
		$_GET["bookid"] = $bookid;
	}

	if(isset($_GET["bookid"])){
		$bookid = intval($_GET["bookid"]);
		$db = mysql_query("SELECT l.*, a.action, v.id AS vetoid FROM (ss13library l LEFT JOIN erro_library_action a ON l.id = a.id) LEFT JOIN erro_library_veto_list v ON v.bookid = l.id WHERE l.id = $bookid") or die(mysql_error());
		$dor = mysql_query("SELECT * FROM erro_library_review WHERE bookid = $bookid") or die(mysql_error());
		if($ib = mysql_fetch_array($db)){
			$author = $ib["author"];
			$title = $ib["title"];
			$content = $ib["content"];
			$category = $ib["category"];
			$action = $ib["action"];
			$vetoid = $ib["vetoid"];
			
			$category = str_replace("\"","&#34;",$category);
			$category = str_replace("\x91","&#8216;",$category);
			$category = str_replace("'","&#8216;",$category);
			$title = str_replace("\"","&#34;",$title);
			$title = str_replace("\x91","&#8216;",$title);
			$title = str_replace("'","&#8216;",$title);
			$author = str_replace("\"","&#34;",$author);
			$author = str_replace("\x91","&#8216;",$author);
			$author = str_replace("'","&#8216;",$author);
		
			print "<table align='center' width='600' cellspacing='0' cellpadding='5'><tr bgcolor='$color2'><td align='center'>";
			//if($action == "UPDATE"){
				if(isset($_POST["updatehead"])){
					$newcategoryint = intval($_POST["category"]);
					$newauthor = $_POST["author"];
					$newtitle = $_POST["title"];
					$newcategory;
					switch ($newcategoryint){
						case 1:
							$newcategory = "Fiction";
						break;
						case 2:
							$newcategory = "Non-Fiction";
						break;
						case 3:
							$newcategory = "Reference";
						break;
						case 4:
							$newcategory = "Religion";
						break;
						case 5:
							$newcategory = "Adult";
						break;
					}
					
					
					$newtitle = str_replace("\"","&#34;",$newtitle);
					$newtitle = str_replace("\x91","&#8216;",$newtitle);
					$newtitle = str_replace("'","&#8216;",$newtitle);
					$newauthor = str_replace("\"","&#34;",$newauthor);
					$newauthor = str_replace("\x91","&#8216;",$newauthor);
					$newauthor = str_replace("'","&#8216;",$newauthor);
					$newcategory = str_replace("\"","&#34;",$newcategory);
					$newcategory = str_replace("\x91","&#8216;",$newcategory);
					$newcategory = str_replace("'","&#8216;",$newcategory);
					
					if($title != $newtitle || $category != $newcategory || $author != $newauthor){
						mysql_query("UPDATE ss13library SET category = '$newcategory', author = '$newauthor', title = '$newtitle' WHERE id = $bookid") or die(mysql_error());
						mysql_query("INSERT INTO erro_library_update_log VALUES (null, Now(), '$user', '$ip', 'HEAD', $bookid,'category-$category|author-$author|title-$title','category-$newcategory|author-$newauthor|title-$newtitle')") or die(mysql_error());
						print "Info updated successfully.";
						$title = $newtitle;
						$category = $newcategory;
						$author = $newauthor;
					}else{
						print "No change detected, update cancelled";
					}
				}else{
					print "<b><form method='POST'><input type='hidden' name='updatehead' value='$bookid'>";
					print "<select name='category'>";
					if($category == "Fiction"){
						print "<option value='1' selected>Fiction *</option>";
					}else{
						print "<option value='1'>Fiction</option>";
					}
					if($category == "Non-Fiction"){
						print "<option value='2' selected>Non-Fiction *</option>";
					}else{
						print "<option value='2'>Non-Fiction</option>";
					}
					if($category == "Reference"){
						print "<option value='3' selected>Reference *</option>";
					}else{
						print "<option value='3'>Reference</option>";
					}
					if($category == "Religion"){
						print "<option value='4' selected>Religion *</option>";
					}else{
						print "<option value='4'>Religion</option>";
					}
					if($category == "Adult"){
						print "<option value='5' selected>Adult *</option>";
					}else{
						print "<option value='5'>Adult</option>";
					}
					print "</select>";
					
					print " Author: <input type='text' name='author' value='$author'>";
					print " Title: <input type='text' name='title' value='$title'>";
					print " <input type='submit' value='Update'>";
				}
				print "<br>Current info: [$category] $author: <i>\"$title\"</i></b></form></td></tr>";
			//}else{
			//	print "<b>[$category] $author: <i>\"$title\"</i></b></td></tr>";
			//}
			print "<tr bgcolor='$color2'><td align='center'>Other reviewers have marked this book as: ";
			while($ior = mysql_fetch_array($dor)){
				print "".$ior["mark"]." (A: ".$ior["suggested_action"]."); ";
			}
			print "</td></tr>";
			$fcolor = "#000000";
			if($action == "KEEP"){
				$color = "#eeffee";
				$fcolor = "#000000";
			}else if($action == "UPDATE"){
				$color = "#ffff80";
				$fcolor = "#ff0000";
			}else if($action == "DELETE"){
				$color = "#222222";
				$fcolor = "#ffffff";
			}
		
			print "<tr bgcolor='$color'><td align='left'><font color='$fcolor'><b>This book is marked with: $action";
			if($vetoid != null){
				print "<br><font color='#ccffcc'><b>[DELETION VETO: book will be kept in the library]</b></font>";
			}else{
				if($action == "DELETE"){
					if(isset($_POST["useveto"])){
						if ($vetos > 0){
							$dveto = mysql_query("INSERT INTO erro_library_veto_list(`id`,`datetime`,`bookid`,`username`,`ip`)
							VALUES(null,Now(),$bookid,'$user','$ip');") or die(mysql_error());
							$dvetou = mysql_query("UPDATE erro_library_veto SET vetos = vetos - 1 WHERE username = '$user'") or die(mysql_error());
							print " <b>Veto used successfully. You have ".($vetos-1)." vetos left.</b>";
						}else{
							print " <b>Veto failed. You don't have any vetos left.</b>";
						}
					}else{
						print "<br><form method='POST'><input type='hidden' name='useveto' value='$bookid'>You can veto this book's deletion by clicking: <input type='submit' name='action' value='Use Veto'> ($vetos available)</form>";
					}
				}
			}
			print "</i></b></font></td></tr>";
			//if($action == "UPDATE"){
				if(isset($_POST["newcontentfinal"])){
					$newcontentfinal = $_POST["newcontentfinal"];
					$newcontentfinal = str_replace("\"","&#34;",$newcontentfinal);
					$newcontentfinal = str_replace("\x91","&#8216;",$newcontentfinal);
					$newcontentfinal = str_replace("'","&#8216;",$newcontentfinal);
					//print "ncf = ". $newcontentfinal;
					//if(strpos($newcontentfinal,'\"') != false){
					//	print "I'm sorry, you can't use the \" symbol due to it being a huge pain to deal with.";
					//}else{
						mysql_query("UPDATE ss13library SET content = '$newcontentfinal' WHERE id = $bookid") or die(mysql_error());
						
						mysql_query("INSERT INTO erro_library_update_log VALUES (null, Now(), '$user', '$ip', 'CONTENT',$bookid,'$content','$newcontentfinal')") or die(mysql_error());
						$content = $newcontentfinal;
						print "Content successfully updated.";
					//}
				}
				print "<tr bgcolor='$color1'><td align='left'>";
				print "<h2>Formatted content</h2>";
				print "$content";
				print "</td></tr>";
				if(isset($_POST["newcontentpreview"])){
					$newcontentpreview = $_POST["newcontentpreview"];
					$newcontentpreview = str_replace("\"","&#34;",$newcontentpreview);
					$newcontentpreview = str_replace("'","&#8216;",$newcontentpreview);
					print "<tr bgcolor='$color1'><td align='left'>";
					print "<h2>Preview</h2>";
					print "$newcontentpreview";
					print "<br>";
					print "<form method='POST'><input type='hidden' name='updatecontentsave' value='$bookid'><input type='hidden' name='newcontentfinal' value='$newcontentpreview'><input type='submit' value='Save preview to database'> (NOTE! This will save the PREVIEW you see above, not the source below. If you edited the source, hit preview again and then save!)</form>";
					print "</td></tr>";
				}
				
				print "<tr bgcolor='$color1'><td align='left'>";
				print "<h2>Source</h2>";
				print "Please don't abuse the editing freedom this tool offers you. Update content only as needed.";
				print "<form method='POST'><input type='hidden' name='updatecontent' value='$bookid'><textarea name='newcontentpreview' cols='80' rows='50'>";
				if(isset($newcontentpreview)){
					print $newcontentpreview;
				}else{
					print $content;
				}
				//print "</textarea><br>If you with to update this book's content, please upload it to <a href='http://pastebin.com/'>pastebin</a> and <a href='http://forums.nanotrasen.com/ucp.php?i=pm&mode=compose&u=479'>send me a PM</a>";
				print "
				<input type='submit' value='Preview changes'>
				</form></td></tr>";
			//}else{
			//	print "<tr bgcolor='$color1'><td align='left'><b>$content</b></td></tr>";
			//}
			print "</table>";
			
			/*
			print "<table align='center'><tr><td>";
			print "<form method='post'>";
			print "<input type='hidden' name='bookidsave' value='$bookid'>";
			print "<b>Your review: </b>";
			print "<br><input type='checkbox' name='r1' value='1'> Good";
			print "<br><input type='checkbox' name='r2' value='1'> Copied (identical exists in library)";
			print "<br><input type='checkbox' name='r3' value='1'> Outdated";
			print "<br><input type='checkbox' name='r4' value='1'> Terrible content";
			print "<br><input type='checkbox' name='r5' value='1'> Overly offensive";
			print "<br><input type='checkbox' name='r6' value='1'> Overly sexual";
			print "<br><input type='checkbox' name='r7' value='1'> Copyright violation";
			print "<br><input type='checkbox' name='r8' value='1'> Broken (formatting code broken)";
			print "<br><input type='checkbox' name='r9' value='1'> Abuses HTML";
			print "<br><input type='checkbox' name='r10' value='1'> Wrong category";
			print "<br><input type='checkbox' name='r11' value='1'> Other negative property";
			print "<br><input type='checkbox' name='r12' value='1'> Funny";
			print "<br><input type='checkbox' name='r13' value='1'> A Classic (Please use this very sparingly)";
			print "<br><input type='checkbox' name='r14' value='1'> Unfinished (book or series)";
			print "<br><input type='checkbox' name='r15' value='1'> Historical (stuff that records round events)";
			
			print "<p><b>Your proposed action regarding this book:</b>";
			print "<br><input type='radio' name='a1' value='1'> Keep unchanged";
			print "<br><input type='radio' name='a1' value='2'> Update (ONLY if it requires minimal updating!)";
			print "<br><input type='radio' name='a1' value='3'> Delete from library";
			
			print "<br><input type='submit' value='Save review'>";
			
			print "</td></tr></table>";
			
			print "</form>";
			*/
			
		}else{
			print "Id doesn't match any book.";
		}
	}

	$reviewnumber = Array();
	$dr = mysql_query("SELECT bookid, count(id) AS reviews FROM erro_library_review GROUP BY bookid") or die(mysql_error());
	while($ir = mysql_fetch_array($dr)){
		$bookid = intval($ir["bookid"]);
		$reviews = intval($ir["reviews"]);
		$reviewnumber[$bookid] = $reviews;
	}

	print "<table width='1000' cellspacing='0' cellpadding='5' align='center'><tr bgcolor='$color2'><td align='center' colspan='2'><b>Books</b></td></tr>";
	
	$filter_sql = "";
	if(isset($_GET["filter"])){
		$filter = strtolower($_GET["filter"]);
		if(strpos($filter, ";") > 0 || strpos($filter, "(") > 0 || strpos($filter, ")") > 0 || strpos($filter, "\"") > 0 || strpos($filter, "'") > 0 || strpos($filter, "\\") > 0){
			print "<div align='center'>Sorry, cannot search for the characters: ; ( ) \" ' or \\</div>";
			$filter = "";
		}else{
			$filter_sql = "WHERE LOWER(content) LIKE '%$filter%' OR  LOWER(author) LIKE '%$filter%' OR  LOWER(title) LIKE '%$filter%'";
		}
	}
	
	$d = mysql_query("SELECT l.id, author, title, category, l.deleted, l.ckey, date(l.datetime) as datetime, r.username, mark, suggested_action, action, v.id AS vetoid FROM (((SELECT * FROM ss13library $filter_sql) l LEFT JOIN (SELECT * FROM erro_library_review WHERE username = '$user') r ON l.id = r.bookid) LEFT JOIN erro_library_action a ON a.id = l.id) LEFT JOIN erro_library_veto_list v ON v.bookid = l.id ORDER BY l.id") or die(mysql_error());
	$colori = 0;
	
	$color_keep = ArraY("#eeffee","#e8ffe8");
	$color_vkeep = ArraY("#f0ffee","#f4ffe8");
	$color_upd = ArraY("#ffff80","#ffff90");
	$color_del = ArraY("#222222","#333333");
	
	while($i = mysql_fetch_array($d)){
		if($colori == 1){$colori = 0;}else{$colori = 1;}

		$id = $i["id"];
		$author = $i["author"];
		$title = $i["title"];
		$category = $i["category"];
		$mark = $i["mark"];
		$saction = $i["suggested_action"];
		$action = $i["action"];
		$vetoid = $i["vetoid"];
		$deleted = $i["deleted"];
		
		$ckey = $i["ckey"];
		$datetime = $i["datetime"];
		
		if($ckey == null){
			$ckey = "ckey unknown";
		}
		if($datetime == null){
			$datetime = "before May 2013";
		}

		$fcolor = "#000000";
		if($deleted){
			$color = $color_del[$colori];
			$fcolor = "#ffffff";
		}else if($action == "KEEP"){
			$color = $color_keep[$colori];
			$fcolor = "#000000";
		}else if($action == "UPDATE"){
			$color = $color_upd[$colori];
			$fcolor = "#ff0000";
		}else if($action == "DELETE"){
			if($vetoid != null){
				$color = $color_vkeep[$colori];
				$fcolor = "#000000";
			}else{
				$color = $color_del[$colori];
				$fcolor = "#ffffff";
			}
		}else{
			//Default to green (keep) color
			$color = $color_keep[$colori];
			$fcolor = "#000000";
		}
		
		print "<tr bgcolor='$color'><td align='center'>";
		if($deleted){
			print "<form method='POST'><input type='hidden' name='undeleteid' value='$id'><input type='submit' value='Undelete'></form>";
		}else{
			print "<form method='POST'><input type='hidden' name='deleteid' value='$id'><input type='submit' value='Delete'></form>";
		}
		print "</td>";
		
		print "<td align='left'>";
		print "<li>";
		
		if($deleted){
			print "<font color='red'><b>BOOK IS DELETED</b></font><font color='$fcolor'> ";
		}else if($vetoid != null){
			print "<font color='$fcolor'><b>VETO KEEP</b> ";
		}else{
			print "<font color='$fcolor'><b>$action</b> ";
		}
		if($mark){
			print "(<font color='green'>Reviewed</font>) ";
		}
		print "<font size='2'><b>$id</b>: $datetime</font> [$category] $author ($ckey): <b><i>\"$title\"</i></b> (<a href='library.php?bookid=$id'>Read and review</a>)";
		if(!isset($reviewnumber[$id])){
			print " (<font color='#800000'><b>No reviews yet</b></font>) ";
		}else{
			if($reviewnumber[$id] > 1){
				$s = "s";
			}else{
				$s = "";
			}
			print " (<font color='#008000'><b>".$reviewnumber[$id]." review$s</b></font>) ";
		}
		if($mark){
			print "(Your review: $mark, suggested action is $saction) </font>";
		}
		
		print "</li>";
		print "</td></tr>";
	}
}


















