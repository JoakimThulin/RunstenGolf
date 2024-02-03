<?php
include "base.php";
playerboard();

//******************************************************************
function playerboard(){
// Senast uppdaterad 2021-07-17 av joakim.thulin@outlook.com
?>
<!doctype html>
<html lang="sv-se">
<head>
<title>RunstenGolf - Spelarlista</title>
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
<h2>RunstenGolf spelarlista</h2>

<?php

$yearlow = 2008;
$yearhigh = date("Y");

$header[0] = "Spelare";
$header[1] = "Status";
$header[2] = "GolfID";
$header[3] = "Epost";
$header[4] = "Startavgift";
$c=4;
for($k=$yearlow;$k<=$yearhigh;$k++){$c++; $header[$c] = $k;}
$c++;
$header[$c] = "Uppdaterad";

try {
  $dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
  $db = new PDO($dsn, DBUSER, DBPW);
  $sql = "SELECT id, player, golfid, email, basefee, updated, active FROM sig_players ORDER BY player";
  $r=-1;
  foreach($db->query($sql) as $row) {
    $player_name = $row['player'];
    if($player_name != "Rune"){
      $r++;
      $data[$r]['id'] = $row['id'];
      $data[$r]['player'] = $player_name;
      $data[$r]['golfid'] = $row['golfid'];
      $data[$r]['email'] = $row['email'];
      if($row['basefee']){
        $data[$r]['basefee'] = "Betalt";
      }else{
        $data[$r]['basefee'] = "&nbsp;";
      }
      if($row['active']){
        $data[$r]['active'] = "Aktiv";
      }else{
        $data[$r]['active'] = "Passiv";
      }
  
      $eer=5;
      for($k=$yearlow;$k<=$yearhigh;$k++){
        $eer++;
        $data[$r][$eer] = "&nbsp;";
      }
  
      $val = $row['updated'];
      if(substr($val,-9)==" 00:00:00"){$val = substr($val,0,10);}
      $eer++;
      $data[$r][$eer] = $val;
  
    }
  }
  $db = null;
} catch (PDOException $e) {
    print "Fråga till DB om senaste resultat kraschade med: " . $e->getMessage() . "<br/>";
    die();
}
$entries = $r + 1;

try {
  $dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
  $db = new PDO($dsn, DBUSER, DBPW);
  $c = 5;
  for($year=$yearlow;$year<=$yearhigh;$year++){
    $c++;
    $sql = "SELECT playerid FROM sig_annualfee WHERE feeyear=" . $year;
    foreach($db->query($sql) as $row) {
      for($r=0;$r<$entries;$r++){
        if($row['playerid']==$data[$r]['id']){
          $data[$r][$c] = "Betalt";
          break;
        }
      }    
    }
  }
  $db = null;
} catch (PDOException $e) {
  print "Det sket sig: " . $e->getMessage() . "<br/>";
  die();
}	

for($r=0;$r<$entries;$r++){
  if($data[$r]['player'] == "Lennart"){
    $c = 5;
    $data[$r][$c] = "Gratis";
    for($year=$yearlow;$year<=$yearhigh;$year++){
      $c++;
      $data[$r][$c] = "Gratis";
    }
    break;
  }
}

$yearspan = ($yearhigh-$yearlow)+1;
printtable($header, $data, $yearspan);

?>
</body>
</html>
<?php
}
//******************************************************************
function printtable($header, $data, $yearspan){
// Senast uppdaterad 2021-07-17 av joakim.thulin@outlook.com

echo "<table id='demo' class='ruler' border='1' cellpadding='2'>\n";

echo "<tr class='noselect'>\n";
foreach ($header as $value) {
  echo "<th>" . $value . "</th>\n";
}
echo "</tr>\n";

$entries = count($data);

for($r=0;$r<$entries;$r++){
  echo "<tr>\n";  
  echo "<td>" . $data[$r]['player'] . "</td>\n";
  echo "<td>" . $data[$r]['active'] . "</td>\n";
  echo "<td>" . $data[$r]['golfid'] . "</td>\n";
  echo "<td>" . $data[$r]['email'] . "</td>\n";
  echo "<td class='weekday'>" . $data[$r]['basefee'] . "</td>\n";
  $eer=5;
  for($k=1;$k<=$yearspan;$k++){
    $eer++;
    echo "<td class='weekday'>" . $data[$r][$eer] . "</td>\n";
   }
  $eer++;
  echo "<td class='weekday'>" . $data[$r][$eer] . "</td>\n";
  echo "</tr>\n";
}

echo "</table>\n";

}
//******************************************************************
?>
