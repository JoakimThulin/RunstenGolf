<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {editform();} else {no_access();}

//******************************************************************
function editform(){
// Senast uppdaterad 2023-09-10 av joakim.thulin@outlook.com
?>
<!doctype html>
<html lang="sv-se">
<head>
<title>Redigera segrare i RunstenGolf</title>
<meta charset="utf-8">
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

<?php


if (!isset($_GET["event"])) {$_GET["event"] = "";}
if (!isset($_POST["event"])) {$_POST["event"] = "";}
$event = $_POST["event"];
if($event == ""){$event = $_GET["event"];}
if($event == ""){
	try {
		$cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
		$sql = "SELECT event FROM sig_events ORDER BY eventyear DESC, event DESC LIMIT 0,1";
		$q = $cn->query($sql);
		$event = $q->fetchColumn();
		$cn = null;
	} catch (PDOException $e) {
		print "Det sket sig: " . $e->getMessage() . "<br/>";
		die();
	}
}

try {
	$cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
	$sql = "SELECT playerid FROM sig_victories WHERE event ='" . $event . "'";
	$q = $cn->query($sql);
	$rowcount = $q->num_rows + 0;
	$oldplayer = $q->fetchColumn() + 0;
	$cn = null;
} catch (PDOException $e) {
	print "Det sket sig: " . $e->getMessage() . "<br/>";
	die();
}
if($rowcount == 0){$newentry=true;}else{$newentry=false;}

if (!isset($_POST["player"])){
	$_POST["player"] = "";
	$player = 0;
	}
else{
	$player = $_POST["player"] + 0;
	}

//echo "<p>+++++++++++++++++++++++++++++++++++++</p>\n";
//echo "<p>oldplayer: " . $oldplayer . "</p>\n";
//echo "<p>player: " . $player . "</p>\n";
//echo "<p>newentry: " . $newentry . "</p>\n";
//echo "<p>Event: " . $event . "</p>\n";
//echo "<p>Antal rader: " . $rowcount . "</p>\n";
//echo "<p>+++++++++++++++++++++++++++++++++++++</p>\n";

if($player == 0)
{
	$player = $oldplayer;
}
else
{
	if($player != $oldplayer){
		try {
			$cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
			$cn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			if($newentry){
				$sql = "INSERT INTO sig_victories (playerid, updated, event) VALUES (:i, :u, :e)";
				$stmt = $cn->prepare($sql);
				$stmt->bindParam(':i', $player, PDO::PARAM_INT);
				$stmt->bindParam(':u', date("Y-m-d"), PDO::PARAM_STR);
				$stmt->bindParam(':e', $event, PDO::PARAM_STR);
				$stmt->execute();
			} else {
				$sql = "UPDATE sig_victories SET playerid = :i, updated = :u WHERE event = :e";  
				$stmt = $cn->prepare($sql);
				$stmt->bindParam(':i', $player, PDO::PARAM_INT);
				$stmt->bindParam(':u', date("Y-m-d"), PDO::PARAM_STR);
				$stmt->bindParam(':e', $event, PDO::PARAM_STR);
				$stmt->execute();
			}
			$cn = null;
		} catch (PDOException $e) {
			print "Det sket sig: " . $e->getMessage() . "<br/>";
			die();
		}
	}
}


//TODO: 2021-07-17: Om man väljer spelare 0 skall den gamla tilldelningen raderas

echo "<form action='champ_edit.php' method='post'>\n";
echo "<p class='printnoshow'>\n";
echo "Tävling: <select name='event' onchange='submit()'>\n";
try {
	$cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
	$sql = "SELECT event, description FROM sig_events ORDER BY eventyear DESC, event DESC";
	foreach($cn->query($sql) as $row) {
		$dbid = $row['event'];
		$dbname = $row['description'];
		$sSel = "";
		if($dbid == $event){
			$sSel = " selected='selected'";
			$chosendbname = $dbname;
		}
		echo "<option value='" . $dbid . "'" . $sSel . ">" . $dbname . "</option>\n";
	}
	$cn = null;
} catch (PDOException $e) {
	print "Fråga till DB om senaste resultat kraschade med: " . $e->getMessage() . "<br/>";
	die();
}
echo "</select>\n";
echo "</p>\n";
echo "</form>\n";

echo "<form action='champ_edit.php?event=" . $event . "' method='post'>\n";
echo "<p class='printnoshow'>\n";
echo "Spelare: <select name='player' onchange='submit()'>\n";
if($player == 0){ $sSel = " selected='selected'";} else {$sSel = "";}
echo "<option value='0'" . $sSel . ">** Inte angivet ännu **</option>\n";
try {
	$cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
	$sql = "SELECT id, player FROM sig_players ORDER BY player";
	foreach($cn->query($sql) as $row) {
		$dbid = $row['id'];
		$dbname = $row['player'];
		$sSel = "";
		if($dbid == $player){
			$sSel = " selected='selected'";
			$chosendbname = $dbname;
		}
		echo "<option value='" . $dbid . "'" . $sSel . ">" . $dbname . "</option>\n";
	}
	$cn = null;
} catch (PDOException $e) {
	print "Fråga till DB om senaste resultat kraschade med: " . $e->getMessage() . "<br/>";
	die();
}
echo "</select>\n";
echo "</p>\n";
echo "</form>\n";

?>
<p class='printnoshow'>
<input type='button' value='Till Registerstartsidan' onclick='location.href="index.php";' />
</p>

</body>
</html>
<?php
}
//******************************************************************
?>
