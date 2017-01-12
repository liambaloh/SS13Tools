<?php

//In this file you must edit DB connection data 
//and usernames + passwords for logins (like library)

session_start();
include("analytics.php");

//Edit database connection data.
mysql_connect("HOST:PORT", "USERNAME", 'PASSWORD');
mysql_select_db("DB_NAME") or die("Database selection failure");	
mysql_query('SET character_set_results="utf8"') or die("Database result type setting failure");	
mysql_set_charset ("utf8");

$authenticated = 0;
$auth = 0;
$user = "";


if( isset($_POST["un"]) ){
	$_SESSION["un"] = $_POST["un"];
}
if( isset($_POST["pw"]) ){
	$_SESSION["pw"] = $_POST["pw"];
}

//Change the usernames and passwords below here. This is used for logging into things like the library...

//Authenticate user
if( isset($_SESSION["un"]) && isset($_SESSION["pw"]) ){
	if( ($_SESSION["un"] == "username") && ($_SESSION["pw"] == "password") ){
		$user = "username";
		$auth = 1;
		$authenticated = 1;
	}
	if( ($_SESSION["un"] == "username2") && ($_SESSION["pw"] == "password2") ){
		$user = "username2";
		$auth = 1;
		$authenticated = 1;
	}
}

?>