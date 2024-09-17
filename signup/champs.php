<?php
// Senast uppdaterad 2024-09-16 av joakim.thulin@outlook.com
include "base.php";

try {
	$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME;
	$db = new PDO($dsn, DBUSER, DBPW);

/*
	//Här kommer en lista med årtalen som allt skall hängas upp på, enbart mästerskap
	$sql = "SELECT DISTINCT sig_events.eventyear AS eventyear";
	$sql .= " FROM sig_victories";
	$sql .= " INNER JOIN sig_events ON sig_victories.event = sig_events.event";
	$sql .= " WHERE sig_events.championship = 1";
	$sql .= " ORDER BY sig_events.eventyear DESC";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$ssd[$r]['eventyear'] = $row['eventyear'];
	}
	$years = $r + 1;
*/

	//Samling med alla segrare i en enda hög
	$sql = "SELECT sig_events.eventyear AS eventyear, sig_players.player AS player, sig_events.location AS location";
	$sql .= " FROM sig_victories";
	$sql .= " INNER JOIN sig_events ON sig_victories.event = sig_events.event";
	$sql .= " INNER JOIN sig_players ON sig_victories.playerid = sig_players.id";
	$sql .= " WHERE sig_events.championship = 1";
	$sql .= " ORDER BY sig_events.eventyear DESC";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$annualfallwinner[$r]['eventyear'] = $row['eventyear'];
		$annualfallwinner[$r]['player'] = utf8_encode($row['player']);
		$annualfallwinner[$r]['location'] = utf8_encode($row['location']);
	}
	//$events = $r + 1;

	$sql = "SELECT sig_events.eventyear AS eventyear, sig_players.player AS player, sig_events.location AS location";
	$sql .= " FROM sig_victories";
	$sql .= " INNER JOIN sig_events ON sig_victories.event = sig_events.event";
	$sql .= " INNER JOIN sig_players ON sig_victories.playerid = sig_players.id";
	$sql .= " WHERE sig_events.championship = 0";
	$sql .= " ORDER BY sig_events.eventyear DESC";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$annualspringwinner[$r]['eventyear'] = $row['eventyear'];
		$annualspringwinner[$r]['player'] = utf8_encode($row['player']);
		$annualspringwinner[$r]['location'] = utf8_encode($row['location']);
	}

/*
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
*/

	//Här kommer det totala antalet individuella segrare
	//$sql = "SELECT count(x) as hits from (select distinct playerid as x from sig_victories) as sd";
	//$result = mysql_query($sql);
	//$row = mysql_fetch_array($result)
	//$winners = $row[0];

/*
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
*/

	//Här kommer alla segrare i mästerskap sorterade efter den person med mest segrar
	$sql = "SELECT sig_players.player AS player, count(sig_victories.playerid) AS hits FROM sig_victories";
	$sql .= " INNER JOIN sig_players ON sig_players.id=sig_victories.playerid";
	$sql .= " INNER JOIN sig_events ON sig_events.event=sig_victories.event";
	$sql .= " WHERE sig_events.championship = 1";
	$sql .= " GROUP BY sig_victories.playerid";
	$sql .= " ORDER BY hits DESC, player";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$fall[$r]['player'] = utf8_encode($row['player']);
		$fall[$r]['wins'] = $row['hits'];
	}
	//$fallwinners = $r + 1;

	$sql = "SELECT sig_players.player AS player, count(sig_victories.playerid) AS hits FROM sig_victories";
	$sql .= " INNER JOIN sig_players ON sig_players.id=sig_victories.playerid";
	$sql .= " INNER JOIN sig_events ON sig_events.event=sig_victories.event";
	$sql .= " WHERE sig_events.championship = 0";
	$sql .= " GROUP BY sig_victories.playerid";
	$sql .= " ORDER BY hits DESC, player";
	$r = -1;
	foreach($db->query($sql) as $row) {
		$r++;
		$spring[$r]['player'] = utf8_encode($row['player']);
		$spring[$r]['wins'] = $row['hits'];
	}

/*
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
*/

/*
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
*/

	$db = null;
} catch (PDOException $e) {
	print "Anslutning till DB kraschade med: " . $e->getMessage() . "<br/>";
	die();
}
?>
<!doctype html>
<html lang="sv-se">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>RunstenGolf Hall of Fame</title>
  <link rel="icon" type="image/svg+xml" href="media/favicon.svg" />
  <link rel="apple-touch-icon" sizes="180x180" href="media/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="192x192" href="media/android-chrome-192x192.png" />
  <link rel="icon" type="image/png" sizes="512x512" href="media/android-chrome-512x512.png" />
	<link rel='stylesheet' media='screen' type='text/css' href='signup.css' />
	<link rel='stylesheet' media='print' type='text/css' href='print.css' />
</head>
<body>

<fieldset>
<legend>Mästerskapen</legend>
<table border='0' cellpadding='2'>
<thead>
	<tr>
		<td><h4>Hall of Fame</h4></td>
		<td><h4>Meste segrare</h4></td>
	</tr>
</thead>
<tbody>
<tr style='vertical-align:top;'>
<td>
	<table id='demo' class='ruler' border='1' cellpadding='2'>
	<thead>
	<tr class='noselect'>
	<th>År</th>
	<th>Spelare</th>
	<th>Golfklubb</th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($annualfallwinner as $p) {
		echo "<tr>\n";
		echo "<td class='event'>" . $p['eventyear'] . "</td>\n";
		echo "<td class='event'>" . $p['player'] . "</td>\n";
		echo "<td class='event'>" . $p['location'] . "</td>\n";
		echo "</tr>\n";
	}
	?>
	</tbody>
	</table>
</td>
<td>
	<table id='demo' class='ruler' border='1' cellpadding='2'>
	<thead>
	<tr class='noselect'>
	<th>Spelare</th>
	<th>Segrar</th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($fall as $p)
	{
		echo "<tr>\n";
		echo "<td class='event'>" . $p['player'] . "</td>\n";
		echo "<td class='weekday'>" . $p['wins'] . "</td>\n";
		echo "</tr>\n";
	}
	?>
	</tbody>
	</table>
</td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset>
<legend>Vårträningen</legend>
<table border='0' cellpadding='2'>
<thead>
	<tr>
		<td><h4>Hall of Fame</h4></td>
		<td><h4>Meste segrare</h4></td>
	</tr>
</thead>
<tbody>
<tr style='vertical-align:top;'>
<td>
	<table id='demo' class='ruler' border='1' cellpadding='2'>
	<thead>
	<tr class='noselect'>
	<th>År</th>
	<th>Spelare</th>
	<th>Golfklubb</th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($annualspringwinner as $p) {
		echo "<tr>\n";
		echo "<td class='event'>" . $p['eventyear'] . "</td>\n";
		echo "<td class='event'>" . $p['player'] . "</td>\n";
		echo "<td class='event'>" . $p['location'] . "</td>\n";
		echo "</tr>\n";
	}
	?>
	</tbody>
	</table>
</td>
<td>
	<table id='demo' class='ruler' border='1' cellpadding='2'>
	<thead>
	<tr class='noselect'>
	<th>Spelare</th>
	<th>Segrar</th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($spring as $p)
	{
		echo "<tr>\n";
		echo "<td class='event'>" . $p['player'] . "</td>\n";
		echo "<td class='weekday'>" . $p['wins'] . "</td>\n";
		echo "</tr>\n";
	}
	?>
	</tbody>
	</table>
</td>
</tr>
</tbody>
</table>
</fieldset>

</body>
</html>