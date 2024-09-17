<?php
include "base.php";
eventboard();

//******************************************************************
function eventboard(){
// Senast uppdaterad 2024-09-17 av joakim.thulin@outlook.com
?>
<!doctype html>
<html lang="sv-se">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>RunstenGolf tävlingslista</title>
  <link rel="icon" type="image/svg+xml" href="media/favicon.svg" />
  <link rel="apple-touch-icon" sizes="180x180" href="media/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="192x192" href="media/android-chrome-192x192.png" />
  <link rel="icon" type="image/png" sizes="512x512" href="media/android-chrome-512x512.png" />
	<link rel='stylesheet' media='screen' type='text/css' href='signup.css' />
	<link rel='stylesheet' media='print' type='text/css' href='print.css' />
	<script src=signup.js></script>
</head>
<body>
<h2>RunstenGolf tävlingslista</h2>
<p class='printnoshow'>Klicka på den rad du vill ändra.</p>
<p class='printnoshow'>
<input type='button' value='Lägg till ny tävling' onclick='location.href="event_edit.php?event=new";' />
<input type='button' value='Till Registerstartsidan' onclick='location.href="index.php";' />
</p>

<?php

$header[0] = "Tävling";
$header[1] = "Tävlingskod";
$header[2] = "Låst";
$header[3] = "Mästerskap";

try {
	$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
	$db = new PDO($dsn, DBUSER, DBPW);
	$sql = "SELECT eventyear, playdate, location, event, lockevent, championship FROM sig_events ORDER BY eventyear DESC, event DESC";
	$r=-1;
	foreach($db->query($sql) as $row) {
		$r++;
		$dbyear = $row['eventyear'];
		$dbdate = $row['playdate'];
		$dblocation = $row['location'];
		$dbchampionship = $row['championship'];
		$dbeventtype = "Vårträningen";
		if($dbchampionship == 1) {
			$dbeventtype = "Mästerskapen";
		}
		$ssd[$r][0] = $dbeventtype . " " . $dbdate . " " . $dbyear . " på " . $dblocation;
		$ssd[$r][1] = $row['event'];
		if($row['lockevent'] == 1){$ssd[$r][2] = "Låst";}else{$ssd[$r][2] = "Öppen";}
		if($dbchampionship == 1){$ssd[$r][3] = "Ja";}else{$ssd[$r][3] = "-";}
	}
	$db = null;
} catch (PDOException $e) {
	print "Fråga till DB om senaste resultat kraschade med: " . $e->getMessage() . "<br/>";
	die();
}
  
printtable($header, $ssd);
?>

</body>
</html>
<?php
}
//******************************************************************
function printtable($header, $data){
// Senast uppdaterad 2018-10-20 av joakim.thulin@outlook.com

$dc = htmlspecialchars_decode('&quot;');//dubbelt citationstecken

echo "<table id='demo' class='ruler' border='1' cellpadding='2'>\n";

echo "<tr class='noselect'>\n";
foreach ($header as $value) { echo "<th>" . $value . "</th>\n"; }
echo "</tr>\n";

$entries = count($data);

for($r=0;$r<$entries;$r++)
{
	$lnk = $dc . "event_edit.php?event=" . $data[$r][1] . $dc;
	echo "<tr style='cursor:pointer;' onclick='location.href=" . $lnk . "'>\n";
	echo "<td class='event'>" . $data[$r][0] . "</td>\n";
	echo "<td class='event'>" . $data[$r][1] . "</td>\n";
	echo "<td class='weekday'>" . $data[$r][2] . "</td>\n";
	echo "<td class='weekday'>" . $data[$r][3] . "</td>\n";
	echo "</tr>\n";
}

echo "</table>\n";

}
//******************************************************************
?>
