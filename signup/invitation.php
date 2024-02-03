<?php
// Senast uppdaterad 2021-07-17 av joakim.thulin@outlook.com
include "base.php";

?>
<!doctype html>
<html lang="sv-se">
<head>
<meta charset="utf-8">
<title>RunstenGolf inbjudan</title>
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
  
  Var vänliga och gå in på anmälningsformuläret http://runstengolf.se/signup/roster.php?event=rrm2020 och kryssa i vilka dagar ni är med eller om ni inte kan delta.
    
  Välkomna!
  Eva, Kim, Mats och Micki</td></tr>
</table>

</body>
</html>