<?php
// Senast uppdaterad 2024-09-16 av joakim.thulin@outlook.com
include "base.php";
?>
<!doctype html>
<html lang="sv-se">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Runstengolf inbjudan</title>
  <link rel="icon" type="image/svg+xml" href="media/favicon.svg" />
  <link rel="apple-touch-icon" sizes="180x180" href="media/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="192x192" href="media/android-chrome-192x192.png" />
  <link rel="icon" type="image/png" sizes="512x512" href="media/android-chrome-512x512.png" />
	<link rel='stylesheet' media='screen' type='text/css' href='signup.css' />
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