<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {editform();} else {no_access();}

//******************************************************************
function editform(){
// Senast uppdaterad 2021-07-17 av joakim.thulin@outlook.com
?>
<!doctype html>
<html lang="sv-se">
<head>
<title>Redigera spelare</title>
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
function playerList(){window.location='index.php'}
</script>
</head>
<body>

<?php

try {
    $dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
    $db = new PDO($dsn, DBUSER, DBPW);

    $sql = "SELECT player, golfid, email, basefee, active, firstname, lastname, pw FROM sig_players WHERE id=" . $_SESSION['playerid'];
    $row = $db->query($sql)->fetch();
    $player = $row['player'];
    $golfid = $row['golfid'];
    $email = $row['email'];
    $basefee = $row['basefee'];
    $active = $row['active'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $pw = $row['pw'];

    $currentyear = date("Y");
    $sql = "SELECT count(playerid) AS player_id_count FROM sig_annualfee WHERE feeyear=" . $currentyear . " AND playerid=" . $_SESSION['playerid'];
    $row = $db->query($sql)->fetch();
    $annualfee = $row['player_id_count'];

    $db = null;
} catch (PDOException $e) {
    print "Det sket sig: " . $e->getMessage() . "<br/>";
    die();
}

echo "<fieldset style='width:300px'>\n";
echo "<legend>Redigera spelare</legend>\n";
echo "<form action='user_update.php' method='post'>\n";

if($active){$ff=" checked='checked'";}else{$ff="";}
echo "<p><input type='checkbox' name='active'" . $ff . " />Aktiv spelare</p>\n";
echo "<p>Smeknamn:&nbsp;<input type='text' name='player' value='" . $player . "' maxlength='15' style='width:180px' /></p>\n";
echo "<p>Förnamn:&nbsp;<input type='text' name='firstname' value='" . $firstname . "' maxlength='40' style='width:180px' /></p>\n";
echo "<p>Efternamn:&nbsp;<input type='text' name='lastname' value='" . $lastname . "' maxlength='40' style='width:180px' /></p>\n";
echo "<p>Lösenord:&nbsp;<input type='text' name='pw' value='" . $pw . "' maxlength='200' style='width:180px' /></p>\n";
echo "<p>GolfID:&nbsp;<input type='text' name='golfid' value='" . $golfid . "' maxlength='10' style='width:100px' /></p>\n";
echo "<p>Epost:&nbsp;<input type='text' name='email' value='" . $email . "' maxlength='50' style='width:250px' /></p>\n";
if($basefee){$ff=" checked='checked'";}else{$ff="";}
echo "<p><input type='checkbox' name='basefee'" . $ff . " />Har betalat startavgift (300 kr)</p>\n";
if($annualfee>0){$ff=" checked='checked'";}else{$ff="";}
echo "<p><input type='checkbox' name='annualfee'" . $ff . " />Har betalat årsavgiften för " . $currentyear . " (250 kr)</p>\n";

echo "<p>\n";
echo "<input type='submit' value='Uppdatera' />\n";
echo "<input type='button' value='Avbryt' onclick='playerList()' />\n";
echo "</p>\n";

echo "</form>\n";
echo "</fieldset>\n";

?>

</body>
</html>
<?php
}
//******************************************************************
?>
