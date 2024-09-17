<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {editform();} else {no_access();}

//******************************************************************
function editform(){
// Senast uppdaterad 2024-09-16 av joakim.thulin@outlook.com
?>
<!doctype html>
<html lang="sv-se">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Redigera tävling i RunstenGolf</title>
  <link rel="icon" type="image/svg+xml" href="media/favicon.svg" />
  <link rel="apple-touch-icon" sizes="180x180" href="media/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="192x192" href="media/android-chrome-192x192.png" />
  <link rel="icon" type="image/png" sizes="512x512" href="media/android-chrome-512x512.png" />
	<link rel='stylesheet' media='screen' type='text/css' href='signup.css' />
	<link rel='stylesheet' media='print' type='text/css' href='print.css' />
	<script type='text/javascript'>
	function signupList(){window.location='events.php'}
	</script>
</head>
<body>

<?php

if (!isset($_GET["event"])) {$_GET["event"] = "";} 
$thisevent = $_GET["event"];

if($thisevent == "new")
{
	$eventyear = date("Y");
	$newevent = true;
}
else
{
	try {
		$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
		$db = new PDO($dsn, DBUSER, DBPW);
		$sql = "SELECT id, eventyear, playdate, location, championship, lockevent FROM sig_events WHERE event='" . $thisevent . "'";
		$row = $db->query($sql)->fetch();
		$eventid = $row['id'];
		$eventyear = $row['eventyear'];
		$playdate = $row['playdate'];
		$location = $row['location'];
		$championship = $row['championship'] + 0;
		$lockevent = $row['lockevent'] + 0;
		$db = null;
		$newevent = false;
	} catch (PDOException $e) {
		print "Det sket sig: " . $e->getMessage() . "<br/>";
		die();
	}
}

if($newevent)
{
	echo "<fieldset style='width:600px'>\n";
	echo "<legend>Skapa ny tävling i RunstenGolf</legend>\n";
	echo "<form action='event_update.php?newevent=true' method='post'>\n";
	echo "<p>Årtal:<br /><input type='text' name='eventyear' value='" . $eventyear . "' maxlength='4' style='width:50px' /></p>\n";
	echo "<p>Speldatum:<br /><input type='text' name='playdate' value='' maxlength='20' style='width:80px' /> <i>Ange datum utan årtal</i></p>\n";
	echo "<p>Golfklubb:<br /><input type='text' name='location' value='' maxlength='100' style='width:150px' /> <i>Ange golfklubbens namn</i></p>\n";
	echo "<p><input type='checkbox' name='championship' checked='checked' />Mästerskap</p>\n";
	echo "<p>\n";
	echo "<input type='submit' value='Skapa' />\n";
	echo "<input type='button' value='Avbryt' onclick='signupList()' />\n";
	echo "</p>\n";
	echo "</form>\n";
	echo "</fieldset>\n";
}
else
{
	echo "<fieldset style='width:600px'>\n";
	echo "<legend>Redigera tävling i RunstenGolf [" . $thisevent . "]</legend>\n";
	echo "<form action='event_update.php?id=" . $eventid . "' method='post'>\n";
	echo "<p>Årtal:<br /><input type='text' name='eventyear' value='" . $eventyear . "' maxlength='4' style='width:50px' /></p>\n";
	echo "<p>Speldatum:<br /><input type='text' name='playdate' value='" . $playdate . "' maxlength='20' style='width:80px' /> <i>Ange datum utan årtal</i></p>\n";
	echo "<p>Golfklubb:<br /><input type='text' name='location' value='" . $location . "' maxlength='100' style='width:150px' /> <i>Ange golfklubbens namn</i></p>\n";
	if($championship == 1){$ff=" checked='checked'";}else{$ff="";}
	echo "<p><input type='checkbox' name='championship'" . $ff . " />Mästerskap</p>\n";
	if($lockevent == 1){$ff=" checked='checked'";}else{$ff="";}
	echo "<p><input type='checkbox' name='lockevent'" . $ff . " />Tävlingen är låst, inga ändringar kan göras i anmälningsformuläret</p>\n";
	echo "<p>\n";
	echo "<input type='submit' value='Uppdatera' />\n";
	echo "<input type='button' value='Avbryt' onclick='signupList()' />\n";
	echo "</p>\n";
	echo "</form>\n";
	echo "</fieldset>\n";
}

?>

</body>
</html>
<?php
}
//******************************************************************
?>
