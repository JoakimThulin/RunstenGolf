<?php
// Senast uppdaterad 2018-06-04 av joakim.thulin@outlook.com
include "base.php";

?>
<!doctype html>
<html lang="sv-se">
<head>
<title>Runstengolf anmälningsformulär</title>
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
<script src=signup.js></script>
</head>
<body>

<?php

if (!isset($_GET["event"])) {$_GET["event"] = "";} 
if (!isset($_POST["event"])) {$_POST["event"] = "";}
$thisevent = $_POST["event"];
if($thisevent == ""){$thisevent = $_GET["event"];}

try {
  $dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME;
  $db = new PDO($dsn, DBUSER, DBPW);

  if($thisevent == "") {
    $thisevent = $db->query("SELECT event FROM sig_events ORDER BY eventyear DESC, event DESC LIMIT 0,1")->fetchColumn();
  }

  $q_event = $db->prepare("SELECT eventyear, description FROM sig_events WHERE event=:e");
  $q_event->bindParam(':e', $thisevent, PDO::PARAM_STR);
  $q_event->execute();
  $result_event = $q_event->fetchObject();
  $thisyear = $result_event->eventyear;					
  $thistitle = "Anmälning till " . utf8_encode($result_event->description);

  $sql = "SELECT";
  $sql .= " sig_players.player";
  $sql .= ", sig_players.golfid";
  $sql .= ", sb.absent";
  $sql .= ", sb.thursday";
  $sql .= ", sb.friday";
  $sql .= ", sb.saturday";
  $sql .= ", sb.sunday";
  $sql .= ", sb.notes";
  $sql .= ", sb.updated";
  $sql .= ", sig_players.id";
  $sql .= " FROM sig_players LEFT JOIN";
  $sql .= " (SELECT idplayer, absent, thursday, friday, saturday, sunday, notes, updated FROM sig_roster WHERE event=:e) AS sb";
  $sql .= " ON ( sig_players.id = sb.idplayer )";
  $sql .= " WHERE sig_players.active=1";//visa bara aktiva spelare
  $sql .= " ORDER BY sig_players.player";
  $q_main = $db->prepare($sql);
  $q_main->bindParam(':e', $thisevent, PDO::PARAM_STR);
  $q_main->execute();
  $r=-1;
	foreach($q_main as $row) {
		$r++;
    $player_name = utf8_encode($row['player']);
    if(is_null($row['golfid'])){$golfid = "-";}else{$golfid = $row['golfid'];}
    $r++;
    $ssd[$r]['idplayer']=$row['id'];
    $ssd[$r]['player']="<td>" . $player_name . "</td>\n";
    $ssd[$r]['golfid']="<td>" . $golfid . "</td>\n";
    if(is_null($row['absent'])) {
      $ssd[$r]['absent']="<td class='noanswer' title='Ännu inget svar från " . $player_name . "'>?</td>\n";
      $ssd[$r]['thursday']="<td class='weekday'>&nbsp;</td>\n";
      $ssd[$r]['friday']="<td class='weekday'>&nbsp;</td>\n";
      $ssd[$r]['saturday']="<td class='weekday'>&nbsp;</td>\n";
      $ssd[$r]['sunday']="<td class='weekday'>&nbsp;</td>\n";
      $ssd[$r]['notes']="<td class='weekday'>&nbsp;</td>\n";
      $ssd[$r]['updated']="<td class='weekday'>&nbsp;</td>\n";
    } else {
      if($row['absent']){$ssd[$r]['absent']="<td class='weekday'>X</td>\n";}else{$ssd[$r]['absent']="<td class='weekday'>&nbsp;</td>\n";}
      if($row['thursday']){$ssd[$r]['thursday']="<td class='weekday'>X</td>\n";}else{$ssd[$r]['thursday']="<td class='weekday'>-</td>\n";}
      if($row['friday']){$ssd[$r]['friday']="<td class='weekday'>X</td>\n";}else{$ssd[$r]['friday']="<td class='weekday'>-</td>\n";}
      if($row['saturday']){$ssd[$r]['saturday']="<td class='weekday'>X</td>\n";}else{$ssd[$r]['saturday']="<td class='weekday'>-</td>\n";}
      if($row['sunday']){$ssd[$r]['sunday']="<td class='weekday'>X</td>\n";}else{$ssd[$r]['sunday']="<td class='weekday'>-</td>\n";}
      $ssd[$r]['notes']="<td>" . utf8_encode($row['notes']) . "</td>\n";
      $ssd[$r]['updated']="<td>" . utf8_encode($row['updated']) . "</td>\n";
    }
  }

  $q_playerfee = $db->prepare("SELECT playerid FROM sig_annualfee WHERE feeyear=:y");
  $q_playerfee->bindParam(':y', $thisyear, PDO::PARAM_INT);
  $q_playerfee->execute();
  $r=-1;
	foreach($q_main as $row) {
		$r++;
    $feeid[$r]=$row['playerid'];
  }

//  $playerCount = $db->query("select count(player) from sig_players WHERE active=1")->fetchColumn();
//  for($r=0;$r<$playerCount;$r++){
//    $ssd[$r]['annualfee']="<td class='weekday'>-</td>\n";
//    foreach($feeid as $t){
//      if($ssd[$r]['idplayer']==$t){
//        $ssd[$r]['annualfee']="<td class='weekday'>Ja</td>\n";
//        break;
//      }
//    }
//  }
  
  $f=-1;
	foreach($db->query("SELECT event, description FROM sig_events ORDER BY eventyear DESC, event DESC") as $row) {
    $dbid = $row['event'];
    $dbname = $row['description'];
    $sSel = "";
    if($dbid == $thisevent){
      $sSel = " selected='selected'";
      $chosendbname = utf8_encode($dbname);
    }
    $f++;
    $cbobox[$f] = "<option value='" . $dbid . "'" . $sSel . ">" . utf8_encode($dbname) . "</option>\n";
	}

  $db = null;
} catch (PDOException $e) {
  print "Anslutning till DB kraschade med: " . $e->getMessage() . "<br/>";
  die();
}

?>

<form action='roster.php' method='post'>
<p class='printnoshow'>
Tävling: <select name='event' onchange='submit()'>
<?php foreach($cbobox as $f){ echo $f;} ?>
</select>
</p>
</form>
<h2><?php echo $thistitle; ?></h2>
<table id='demo' class='ruler' border='1' cellpadding='2'>
<tr class='noselect'>
<th>Spelare</th>
<th>GolfID</th>
<th>Deltar<br />inte</th>
<th>Golf<br />Torsdag</th>
<th>Golf<br />Fredag</th>
<th>Golf<br />Lördag</th>
<th>Golf<br />Söndag</th>
<th>Kommentar</th>
<th>Uppdaterad</th>
</tr>

<?php
//<th>Erlagt<br />årsavgift</th>
$dc = htmlspecialchars_decode('&quot;');//dubbelt citationstecken
foreach($ssd as $p){
	$lnk = $dc . "roster_edit.php?event=" . $thisevent . "&amp;idplayer=" . $p['idplayer'] . $dc;
  echo "<tr style='cursor:pointer;' onclick='location.href=" . $lnk . "'>\n";
  echo $p['player'];
  echo $p['golfid'];
  echo $p['absent'];
  echo $p['thursday'];
  echo $p['friday'];
  echo $p['saturday'];
  echo $p['sunday'];
  echo $p['notes'];
  echo $p['updated'];
  //$p['annualfee'];
  echo "</tr>\n";
}
?>

</table>
<p class='printnoshow'>Klicka på den rad du vill ändra</p>
</body>
</html>