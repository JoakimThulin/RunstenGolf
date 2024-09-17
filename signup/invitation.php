<?php
// Senast uppdaterad 2024-09-17 av joakim.thulin@outlook.com
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
  foreach($db->query("SELECT email FROM sig_players WHERE active=1 ORDER BY player") as $row) {
    $maillist .= $row['email'] . "; ";
  }
  $db = null;
} catch (PDOException $e) {
  print "Anslutning till DB kraschade med: " . $e->getMessage() . "<br/>";
  die();
}  ?>

<body>
<h2>Mall för inbjudan till ny tävling</h2>
<p>Markera, kopiera och klistra in i ett nytt inbjudningsmejl, redigera det som behövs. Epostadresserna tillhör de spelare som är markerade som aktiva.</p>
<table style='width:70%;'>
<tr><td>Till:</td><td style='vertical-align:top;'><?php echo $maillist; ?></td></tr>
<tr><td>Ämne:</td><td>Inbjudan till mästerskapen 20xx i yy</td></tr>
<tr><td>Text:</td><td style='vertical-align:top;'>Hjärtligt välkomna att spela Rune Runstens Minne yyyy!
  xx-zz september spelar vi årets mästerskap på qqq GK.
  Prisnivån ser ut så här:
  xxxxxx
  
  Var vänliga och gå in på anmälningsformuläret https://runstengolf.se/signup/roster.php?event=rrm2020 och kryssa i vilka dagar ni är med eller om ni inte kan delta.
    
  Välkomna!
  Eva, Kim, Mats och Micki</td></tr>
</table>

</body>
</html>