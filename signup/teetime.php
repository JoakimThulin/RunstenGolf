<?php
// Senast uppdaterad 2021-07-17 av joakim.thulin@outlook.com
include "base.php";
?>
<!doctype html>
<html lang="sv-se">
<head>
<title>Runstengolf inbjudan</title>
<meta charset="utf-8">
<meta name='viewport' content='width=device-width, initial-scale=1.0'> 
<meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1" >
<!-- For IE 9 and below. ICO should be 32x32 pixels in size -->
<!--[if IE]><link rel="shortcut icon" href="media/rg32.ico"><![endif]-->
<!-- Touch Icons - iOS and Android 2.1+ 180x180 pixels in size. --> 
<link rel="apple-touch-icon-precomposed" href="media/rg180.png">
<!-- Firefox, Chrome, Safari, IE 11+ and Opera. 196x196 pixels in size. -->
<link rel="icon" href="media/rg196.png">
</head>

<?php
try {
  $dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME;
  $db = new PDO($dsn, DBUSER, DBPW);
  foreach($db->query("SELECT firstname, lastname, golfid FROM sig_players WHERE active=1 ORDER BY firstname, lastname") as $row) {
    $maillist = $row['firstname'];
    $firstname = utf8_encode($row['firstname']);
    $lastname = utf8_encode($row['lastname']);
    $golfid = $row['golfid'];
    $playerlist .= $firstname . " " . $lastname . ", " . $golfid . "</br>\n";
    }
  $db = null;
} catch (PDOException $e) {
  print "Anslutning till DB kraschade med: " . $e->getMessage() . "<br/>";
  die();
}  ?>

<body>
<h2>Mall för mejl till golfklubb</h2>
<p>Markera, kopiera och klistra in i ett mejl till den golfklubb vi skall besöka, redigera det som behövs. Spelarna i listan är markerade som aktiva.</p>

<p>Hej,</br>vi skulle önska starttider hos er för följande spelare:</p>
<p><?php echo $playerlist; ?></p>

</body>
</html>