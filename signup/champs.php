<?php
// Senast uppdaterad 2018-06-04 av joakim.thulin@outlook.com
include "base.php";

?>
<!doctype html>
<html lang="sv-se">
<head>
<title>RunstenGolf Hall of Fame</title>
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


<?php
try {
	$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME;
	$db = new PDO($dsn, DBUSER, DBPW);

	//Här kommer en lista med årtalen som allt skall hängas upp på
	$sql = "SELECT DISTINCT sig_events.eventyear AS eventyear";
	$sql .= " FROM sig_victories";
	$sql .= " INNER JOIN sig_events";
	$sql .= " ON sig_victories.event = sig_events.event";
	$sql .= " ORDER BY sig_events.eventyear DESC";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$ssd[$r]['eventyear'] = $row['eventyear'];
	}
	$years = $r + 1;

	//Samling med alla segrare i en enda hög
	$sql = "SELECT sig_events.eventyear AS eventyear, sig_events.championship AS championship, sig_players.player AS player";
	$sql .= " FROM sig_victories";
	$sql .= " INNER JOIN sig_events";
	$sql .= " ON sig_victories.event = sig_events.event";
	$sql .= " INNER JOIN sig_players";
	$sql .= " ON sig_victories.playerid=sig_players.id";
	$sql .= " ORDER BY sig_events.eventyear, sig_events.championship";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$raw[$r]['eventyear'] = $row['eventyear'];
		$raw[$r]['championship'] = $row['championship'];
		$raw[$r]['player'] = $row['player'];
	}
	$events = $r + 1;

	for ( $r = 0; $r < $years; $r++) {
		$currentyear = $ssd[$r]['eventyear'];
		$ssd[$r]['springwinner'] = "-";
		$ssd[$r]['fallwinner'] = "-";
		for ( $v = 0; $v < $events; $v++) {
			if($raw[$v]['eventyear'] == $currentyear) {
				if($raw[$v]['championship']==0) {
					$ssd[$r]['springwinner'] = utf8_encode($raw[$v]['player']);	
				}
				if($raw[$v]['championship']==1) {
					$ssd[$r]['fallwinner'] = utf8_encode($raw[$v]['player']);	
				}
			}
		}
	}
	
	//Här kommer det totala antalet individuella segrare
	//$sql = "SELECT count(x) as hits from (select distinct playerid as x from sig_victories) as sd";
	//$result = mysql_query($sql);
	//$row = mysql_fetch_array($result)
	//$winners = $row[0];

	//Här kommer alla segrare, sorterad efter den person med flest segrar
	$sql = "SELECT sig_players.player AS player, count(sig_victories.playerid) AS hits FROM sig_victories";
	$sql .= " INNER JOIN sig_players ON sig_players.id=sig_victories.playerid";
	$sql .= " GROUP BY sig_victories.playerid";
	$sql .= " ORDER BY hits DESC";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$ch[$r]['player'] = utf8_encode($row['player']);
		$ch[$r]['hits'] = $row['hits'];
	}
	$winners = $r + 1;

	//Här kommer alla segrare i vårträningar sorterade efter den person med mest segrar
	$sql = "SELECT sig_players.player AS player, count(sig_victories.playerid) AS hits FROM sig_victories";
	$sql .= " INNER JOIN sig_players ON sig_players.id=sig_victories.playerid";
	$sql .= " INNER JOIN sig_events ON sig_events.event=sig_victories.event";
	$sql .= " WHERE sig_events.championship=0";
	$sql .= " GROUP BY sig_victories.playerid";
	$sql .= " ORDER BY hits DESC";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$spring[$r]['player'] = utf8_encode($row['player']);
		$spring[$r]['wins'] = $row['hits'];
	}
	$springwinners = $r + 1;

	//Här kommer alla segrare i mästerskap sorterade efter den person med mest segrar
	$sql = "SELECT sig_players.player AS player, count(sig_victories.playerid) AS hits FROM sig_victories";
	$sql .= " INNER JOIN sig_players ON sig_players.id=sig_victories.playerid";
	$sql .= " INNER JOIN sig_events ON sig_events.event=sig_victories.event";
	$sql .= " WHERE sig_events.championship=1";
	$sql .= " GROUP BY sig_victories.playerid";
	$sql .= " ORDER BY hits DESC";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$fall[$r]['player'] = utf8_encode($row['player']);
		$fall[$r]['wins'] = $row['hits'];
	}
	$fallwinners = $r + 1;

	for ( $r = 0; $r < $winners; $r++)
	{
		$currentwinner = $ch[$r]['player'];
		$ch[$r]['springwins'] = "-";
		$ch[$r]['fallwins'] = "-";
		for ( $v = 0; $v < $springwinners; $v++)
		{
			if($spring[$v]['player'] == $currentwinner)
			{
				$ch[$r]['springwins'] = $spring[$v]['wins'];
			}
		}
		for ( $v = 0; $v < $fallwinners; $v++)
		{
			if($fall[$v]['player'] == $currentwinner)
			{
				$ch[$r]['fallwins'] = $fall[$v]['wins'];
			}
		}
	}

	$db = null;
} catch (PDOException $e) {
	print "Anslutning till DB kraschade med: " . $e->getMessage() . "<br/>";
	die();
}
?>

<table border='0' cellpadding='2'>
<tr>
<td>
<h2>Hall of Fame</h2>
</td>
<td>
<h2>Meste segrare</h2>
</td>
</tr>
<tr style='vertical-align:top;'>
<td>

<table id='demo' class='ruler' border='1' cellpadding='2'>
<tr class='noselect'>
<th>År</th>
<th>Vår</th>
<th>Höst</th>
</tr>

<?php
foreach($ssd as $p) {
	echo "<tr>\n";
	echo "<td class='event'>" . $p['eventyear'] . "</td>\n";
	echo "<td class='weekday'>" . $p['springwinner'] . "</td>\n";
	echo "<td class='weekday'>" . $p['fallwinner'] . "</td>\n";
	echo "</tr>\n";
}
?>

</table>
</td>
<td>

<table id='demo' class='ruler' border='1' cellpadding='2'>
<tr class='noselect'>
<th>Spelare</th>
<th>Vår</th>
<th>Höst</th>
<th>Segrar</th>
</tr>

<?php
foreach($ch as $p)
{
	echo "<tr>\n";
	echo "<td class='event'>" . $p['player'] . "</td>\n";
	echo "<td class='weekday'>" . $p['springwins'] . "</td>\n";
	echo "<td class='weekday'>" . $p['fallwins'] . "</td>\n";
	echo "<td class='weekday'>" . $p['hits'] . "</td>\n";
	echo "</tr>\n";
}
?>

</table>

</td>
</tr>
</table>

</body>
</html>