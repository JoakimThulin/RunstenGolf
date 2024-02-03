<?php
include "base.php";
eventboard();

//******************************************************************
function eventboard(){
// Senast uppdaterad 2018-10-20 av joakim.thulin@outlook.com
?>
<!doctype html>
<html lang="sv-se">
<head>
<head>
<title>RunstenGolf tävlingslista</title>
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
	$sql = "SELECT description, event, lockevent, championship FROM sig_events ORDER BY eventyear DESC, event DESC";
	$r=-1;
	foreach($db->query($sql) as $row) {
		$r++;
		$event_name = $row['description'];
		$event_code = $row['event'];
		$ssd[$r][0] = $event_name;
		$ssd[$r][1] = $event_code;
		if($row['lockevent'] == 1){$ssd[$r][2] = "Låst";}else{$ssd[$r][2] = "Öppen";}
		if($row['championship'] == 1){$ssd[$r][3] = "Ja";}else{$ssd[$r][3] = "-";}
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
