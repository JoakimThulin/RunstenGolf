<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {updateplayer();} else {no_access();}

//******************************************************************
function updateplayer(){
// Senast uppdaterad 2018-10-20 av joakim.thulin@outlook.com

if (!isset($_GET["newevent"])) {$_GET["newevent"] = "";} 
if (!isset($_GET["event"])) {$_GET["event"] = "";} 
if (!isset($_POST["description"])) {$_POST["description"] = "";} 
if (!isset($_POST["eventyear"])) {$_POST["eventyear"] = "";} 
if (!isset($_POST["lockevent"])) {$_POST["lockevent"] = "";} 
if (!isset($_POST["championship"])) {$_POST["championship"] = "";} 

$newevent = $_GET["newevent"];
$thisevent = $_GET["event"];
$thisdescription = $_POST["description"];
$thisyear = $_POST["eventyear"];
$lockevent = $_POST["lockevent"];
$championship = $_POST["championship"];

try {
	$dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
	$db = new PDO($dsn, DBUSER, DBPW);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
if($newevent=="true")
{
	$thisevent = $_POST["event"];
	$sql = "INSERT INTO sig_events (event, eventyear, description, championship, updated) VALUES (:e, :y, :d, :c, :u)";
	$stmt = $db->prepare($sql);
	$stmt->bindParam(':e', $thisevent, PDO::PARAM_STR);
	$stmt->bindParam(':y', $thisyear, PDO::PARAM_INT);
	$stmt->bindParam(':d', $thisdescription, PDO::PARAM_STR);
	$stmt->bindParam(':c', ResolveBoolean($championship), PDO::PARAM_INT);
	$stmt->bindParam(':u', date("Y-m-d"), PDO::PARAM_STR);
	$stmt->execute();
}
else
{
	$sql = "UPDATE sig_events SET eventyear=:y, description=:d, lockevent=:l, championship=:c, updated=:u WHERE event=:e";  
	$stmt = $db->prepare($sql);
	$stmt->bindParam(':y', $thisyear, PDO::PARAM_INT);
	$stmt->bindParam(':d', $thisdescription, PDO::PARAM_STR);
	$stmt->bindParam(':l', ResolveBoolean($lockevent), PDO::PARAM_INT);
	$stmt->bindParam(':c', ResolveBoolean($championship), PDO::PARAM_INT);
	$stmt->bindParam(':u', date("Y-m-d"), PDO::PARAM_STR);
	$stmt->bindParam(':e', $thisevent, PDO::PARAM_STR);
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
