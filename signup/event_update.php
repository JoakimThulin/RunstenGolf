<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {updateplayer();} else {no_access();}

//******************************************************************
function updateplayer(){
// Senast uppdaterad 2024-09-16 av joakim.thulin@outlook.com

if (!isset($_GET["newevent"])) {$_GET["newevent"] = "";} 
if (!isset($_GET["id"])) {$_GET["id"] = "";} 
if (!isset($_POST["playdate"])) {$_POST["playdate"] = "";} 
if (!isset($_POST["eventyear"])) {$_POST["eventyear"] = "";} 
if (!isset($_POST["lockevent"])) {$_POST["lockevent"] = "";} 
if (!isset($_POST["championship"])) {$_POST["championship"] = "";} 
if (!isset($_POST["location"])) {$_POST["location"] = "";} 

$newevent = $_GET["newevent"];
$id = $_GET["id"];
$thisplaydate = $_POST["playdate"];
$thisyear = $_POST["eventyear"];
$lockevent = $_POST["lockevent"];
$championship = $_POST["championship"];
$location = $_POST["location"];

$thisevent = "amm";
if(ResolveBoolean($championship) == 1) {
	$thisevent = "rrm";
}
$thisevent = $thisevent . $thisyear;

try {
	$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
	$db = new PDO($dsn, DBUSER, DBPW);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
if($newevent == "true")
{
	//$thisevent = $_POST["event"];
	$sql = "INSERT INTO sig_events (event, eventyear, playdate, location, championship, updated) VALUES (:e, :y, :d, :o, :c, :u)";
	$stmt = $db->prepare($sql);
	$stmt->bindParam(':e', $thisevent, PDO::PARAM_STR);
	$stmt->bindParam(':y', $thisyear, PDO::PARAM_INT);
	$stmt->bindParam(':d', $thisplaydate, PDO::PARAM_STR);
	$stmt->bindParam(':o', $location, PDO::PARAM_STR);
	$stmt->bindParam(':c', ResolveBoolean($championship), PDO::PARAM_INT);
	$stmt->bindParam(':u', date("Y-m-d"), PDO::PARAM_STR);
	$stmt->execute();
}
else
{
	$sql = "UPDATE sig_events SET eventyear=:y, playdate=:d, location=:o, lockevent=:l, championship=:c, updated=:u, event=:e WHERE id=:i";  
	$stmt = $db->prepare($sql);
	$stmt->bindParam(':y', $thisyear, PDO::PARAM_INT);
	$stmt->bindParam(':d', $thisplaydate, PDO::PARAM_STR);
	$stmt->bindParam(':o', $location, PDO::PARAM_STR);
	$stmt->bindParam(':l', ResolveBoolean($lockevent), PDO::PARAM_INT);
	$stmt->bindParam(':c', ResolveBoolean($championship), PDO::PARAM_INT);
	$stmt->bindParam(':u', date("Y-m-d"), PDO::PARAM_STR);
	$stmt->bindParam(':e', $thisevent, PDO::PARAM_STR);
	$stmt->bindParam(':i', $id, PDO::PARAM_INT);
	$stmt->execute();
}
$db = null;
} catch (PDOException $e) {
  print "Det sket sig: " . $e->getMessage() . "<br/>";
  die();
}
  
die(header("Location: events.php"));
}
//******************************************************************
function ResolveBoolean($checkboxvalue){
$ret=0;
if($checkboxvalue=="on"){$ret=1;}
return $ret;
}
?>
