<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {addplayer();} else {no_access();}

//******************************************************************
function addplayer(){
// Senast uppdaterad 2021-07-17 av joakim.thulin@outlook.com
$player = trim($_POST["player"]);
if(strlen($player)>0)
{
	try {
		$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
		$db = new PDO($dsn, DBUSER, DBPW);
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$sql = "INSERT INTO sig_players (player, firstname, lastname, pw, updated) VALUES (:p, :f, :l, :w, :u)";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':p', $player, PDO::PARAM_STR);
		$stmt->bindParam(':f', $player, PDO::PARAM_STR);
		$stmt->bindParam(':l', $player, PDO::PARAM_STR);
		$stmt->bindParam(':w', $player, PDO::PARAM_STR);
		$stmt->bindParam(':u', date("Y-m-d H:i:s"), PDO::PARAM_STR);
		$stmt->execute();
		$db = null;
	} catch (PDOException $e) {
		print "Det sket sig: " . $e->getMessage() . "<br/>";
		die();
	}
}
die(header("Location: index.php"));
}
?>
