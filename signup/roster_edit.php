<?php
include "base.php";
editform();
//******************************************************************
function editform(){
// Senast uppdaterad 2018-10-21 av joakim.thulin@outlook.com
?>
<!doctype html>
<html lang="sv-se">
<head>
<title>Redigera anmälning till RunstenGolf</title>
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
<?php $thisevent = $_GET["event"]; ?>
<script type='text/javascript'>
function signupList(){window.location='roster.php?event=<?php echo $thisevent; ?>'}
</script>
</head>
<body>

<?php

$idplayer = $_GET["idplayer"];

try {
	$cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
	
	$sql = "SELECT absent, thursday, friday, saturday, sunday, notes FROM sig_roster WHERE idplayer=" . $idplayer . " AND event='" . $thisevent . "'";
    $row = $cn->query($sql)->fetch();
	$absent = $row['absent'];
	$thursday = $row['thursday'];
	$friday = $row['friday'];
	$saturday = $row['saturday'];
	$sunday = $row['sunday'];
	$notes = $row['notes'];

	$sql = "SELECT description, lockevent FROM sig_events WHERE event='" . $thisevent . "'";
    $row = $cn->query($sql)->fetch();
	$thistitle = $row['description'];
	$lockevent = $row['lockevent'];
	
	$sql = "SELECT player FROM sig_players WHERE id=" . $idplayer;
    $row = $cn->query($sql)->fetch();
	$player = $row['player'];

	$cn = null;
} catch (PDOException $e) {
    print "Det sket sig: " . $e->getMessage() . "<br/>";
    die();
}

if($lockevent == 1)
{
	echo "<h4>" . $thistitle . " är låst mot vidare ändringar</h4>\n";
	echo "<p class='printnoshow'>\n";
	echo "<input type='button' value='Gå tillbaka' onclick='signupList()' />\n";
	echo "</p>\n";
}
else
{
	echo "<fieldset style='width:600px'>\n";
	echo "<legend>" . $player . " deltagande i " . $thistitle . "</legend>\n";
	echo "<form action='roster_update.php?event=" . $thisevent . "&amp;idplayer=" . $idplayer . "' method='post'>\n";

	if($absent){$ff=" checked='checked'";}else{$ff="";}
	echo "<p><input type='checkbox' name='absent'" . $ff . " />Kan inte delta\n";

	if($thursday){$ff=" checked='checked'";}else{$ff="";}
	echo "<br /><br /><input type='checkbox' name='thursday'" . $ff . " />Spelar torsdag\n";

	if($friday){$ff=" checked='checked'";}else{$ff="";}
	echo "<br /><input type='checkbox' name='friday'" . $ff . " />Spelar fredag\n";

	if($saturday){$ff=" checked='checked'";}else{$ff="";}
	echo "<br /><input type='checkbox' name='saturday'" . $ff . " />Spelar lördag\n";

	if($sunday){$ff=" checked='checked'";}else{$ff="";}
	echo "<br /><input type='checkbox' name='sunday'" . $ff . " />Spelar söndag\n";

	echo "</p>\n";

	echo "<p>Kommentar:<br /><input type='text' name='notes' value='" . str_replace("<br />", " ", $notes) . "' maxlength='100' style='width:500px' /></p>\n";

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
