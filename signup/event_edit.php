<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {editform();} else {no_access();}

//******************************************************************
function editform(){
// Senast uppdaterad 2018-10-20 av joakim.thulin@outlook.com
?>
<!doctype html>
<html lang="sv-se">
<head>
<title>Redigera tävling i RunstenGolf</title>
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
	$thisyear = date("Y");
	$newevent = true;
}
else
{
	try {
		$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
		$db = new PDO($dsn, DBUSER, DBPW);
		$sql = "SELECT eventyear, description, lockevent, championship FROM sig_events WHERE event='" . $thisevent . "'";
		$row = $db->query($sql)->fetch();
		$thisyear = $row['eventyear'];
		$thisdescription = $row['description'];
		$newevent = false;
		$lockevent = $row['lockevent'] + 0;
		$championship = $row['championship'] + 0;
		$db = null;
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
	echo "<p>Tävlingskod:<br /><input type='text' name='event' value='xxx" . $thisyear . "' maxlength='7' style='width:80px' /><i> amm" . $thisyear . "/rrm" . $thisyear . "</i></p>\n";
	echo "<p>Tävling:<br /><input type='text' name='description' value='Vårträningen/Mästerskapen xx-yy/z " . $thisyear . " i xxxxxxx' maxlength='100' style='width:500px' /></p>\n";
	echo "<p>Årtal:<br /><input type='text' name='eventyear' value='" . $thisyear . "' maxlength='4' style='width:50px' /></p>\n";
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
	echo "<form action='event_update.php?event=" . $thisevent . "' method='post'>\n";
	echo "<p>Tävling:<br /><input type='text' name='description' value='" . $thisdescription . "' maxlength='100' style='width:500px' /></p>\n";
	echo "<p>Årtal:<br /><input type='text' name='eventyear' value='" . $thisyear . "' maxlength='4' style='width:50px' /></p>\n";
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
