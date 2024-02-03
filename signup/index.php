<?php
session_start();
include "base.php";
list_players();

//******************************************************************
function list_players(){
//Uppdaterad 2021-07-17 av Joakim [joakim.thulin@outlook.com]

	try {
		$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME;
		$db = new PDO($dsn, DBUSER, DBPW);
		$playerid = $db->query("select id from sig_players order by player limit 0,1")->fetchColumn();
		if(!isset($_SESSION['loggedin']))	{$_SESSION['loggedin'] = false;}
		if(!isset($_SESSION['playerid']))	{$_SESSION['playerid'] = $playerid;}
		else
		{
			if(isset($_POST['playerid']))
			{
				$playerid = $_POST['playerid'];
				$_SESSION['playerid'] = $playerid;
			}
			else {$playerid = $_SESSION['playerid'];}
		}
		$player_name = $db->query("select player from sig_players where id = $playerid")->fetchColumn();
		$db = null;
	} catch (PDOException $e) {
		print "Det sket sig: " . $e->getMessage() . "<br/>";
		die();
	}

?>
<!DOCTYPE html>
<html lang='sv'>
<head>
    <title>Register i RunstenGolf</title>
	<meta charset='utf-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0'> 
    <meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1" >
    <!-- For IE 9 and below. ICO should be 32x32 pixels in size -->
    <!--[if IE]><link rel="shortcut icon" href="media/rg32.ico"><![endif]-->
    <!-- Touch Icons - iOS and Android 2.1+ 180x180 pixels in size. --> 
    <link rel="apple-touch-icon-precomposed" href="media/rg180.png">
    <!-- Firefox, Chrome, Safari, IE 11+ and Opera. 196x196 pixels in size. -->
    <link rel="icon" href="media/rg196.png">
        <link rel='stylesheet' media='screen' type='text/css' href='signup.css' />
    <link rel='stylesheet' media='print' type='text/css' href='print.css' />
</head>

<body>

<h1>Register i RunstenGolf</h1>


<?php

	echo "<section>\n";
	echo "<fieldset style='width:300px'>\n";
	echo "<legend>Logga in spelare</legend>\n";
	if(!$_SESSION['loggedin'])
	{
		echo "<form action='index.php' method='post'>\n";
		echo "<p>Spelare: <select id='playerid' name='playerid' onchange='submit()'>\n";
		try {
			$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME;
			$db = new PDO($dsn, DBUSER, DBPW);
			foreach($db->query("select id, player from sig_players order by player") as $row) {
				$pid = $row['id'];
				$pname = $row['player'];
				$sSel = "";
				if($playerid == $pid){$sSel = " selected='selected'";}
				echo "<option value='" . $pid . "'" . $sSel . ">" . utf8_encode($pname) . "</option>\n";
			}
			$db = null;
		} catch (PDOException $e) {
			print "Det sket sig: " . $e->getMessage() . "<br/>";
			die();
		}
		echo "</select></p>\n";
		echo "</form>\n";

		echo "<form action='login.php' method='post'>\n";
		echo "<p>Lösenord: <input type='password' style='max-width:150px;' id='playerpw' name='playerpw' maxlength='200' /></p>\n";
		echo "<p><input type='submit' value='" . sprintf("Logga in %s", utf8_encode($player_name)) . "' /></p>\n";
		echo "</form>\n";
	}

	if($_SESSION['loggedin'])
	{
		echo "<form action='login.php' method='post'>\n";
		echo "<p>Inloggad spelare: " . utf8_encode($player_name) . "</p>\n";
		echo "<p><input type='button' value='Tävlingsregistret' onclick='location.href=\"events.php\";' /></p>\n";
		echo "<p><input type='button' value='Tilldela segrar' onclick='location.href=\"champ_edit.php\";' /></p>\n";
		echo "<p><input type='button' value='Lägg till ny spelare' onclick='location.href=\"adduser.php\";' /></p>\n";
		
		$genitiv = "s";
		$last_char = substr($player_name, strlen($player_name)-1, 1);
		if($last_char == "s"){$genitiv = "";}
		echo "<p><input type='button' value='Redigera " . utf8_encode($player_name) . $genitiv . " profil' onclick='location.href=\"user_edit.php\";' /></p>\n";
		echo "<p><input type='button' value='" . sprintf("Logga ut %s", utf8_encode($player_name)) . "' onclick='location.href=\"logout.php\";' /></p>\n";
		echo "</form>\n";
	}

	echo "</fieldset>\n";
	echo "</section>\n";

	echo "<section>\n";
	echo "<fieldset style='width:300px'>\n";
	echo "<legend>Allmänna delar</legend>\n";
	echo "<p><input type='button' value='Anmälningsformulären' onclick='location.href=\"roster.php\";' /></p>\n";
	echo "<p><input type='button' value='Hall of Fame' onclick='location.href=\"champs.php\";' /></p>\n";
	echo "<p><input type='button' value='Spelarregistret' onclick='location.href=\"users.php\";' /></p>\n";
	echo "<p><input type='button' value='Inbjudningsmall' onclick='location.href=\"invitation.php\";' /></p>\n";
	echo "<p><input type='button' value='Anmälningsmall till mottagande GK' onclick='location.href=\"teetime.php\";' /></p>\n";
	echo "</fieldset>\n";
	echo "</section>\n";

	echo "</body>\n";
	echo "</html>\n";

}

//******************************************************************
?>
