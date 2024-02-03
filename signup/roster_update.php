<?php
include "base.php";
updateplayer();
//******************************************************************
function updateplayer(){
// Senast uppdaterad 2018-10-21 av joakim.thulin@outlook.com

if (!isset($_GET["event"])) {$_GET["event"] = "";} 
if (!isset($_GET["idplayer"])) {$_GET["idplayer"] = "";} 
if (!isset($_POST["absent"])) {$_POST["absent"] = "";} 
if (!isset($_POST["thursday"])) {$_POST["thursday"] = "";} 
if (!isset($_POST["friday"])) {$_POST["friday"] = "";} 
if (!isset($_POST["saturday"])) {$_POST["saturday"] = "";} 
if (!isset($_POST["sunday"])) {$_POST["sunday"] = "";} 
if (!isset($_POST["notes"])) {$_POST["notes"] = "";} 

$thisevent = $_GET["event"];
$idplayer = $_GET["idplayer"];
$absent = $_POST["absent"];
$thursday = $_POST["thursday"];
$friday = $_POST["friday"];
$saturday = $_POST["saturday"];
$sunday = $_POST["sunday"];
$notes = $_POST["notes"];

try {
	$cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
  $sql = "SELECT count(idplayer) FROM sig_roster WHERE idplayer = " . $idplayer . " AND event = '" . $thisevent . "'";
	$q = $cn->query($sql);
	$hits = $q->fetchColumn() + 0;
	$cn = null;
} catch (PDOException $e) {
	print "Det sket sig: " . $e->getMessage() . "<br/>";
	die();
}

if($hits == 0){
  try {
    $cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
    $sql = "INSERT INTO sig_roster (idplayer, event) VALUES (:i, :e)";
    $stmt = $cn->prepare($sql);
    $stmt->bindParam(':i', $idplayer, PDO::PARAM_INT);
    $stmt->bindParam(':e', $thisevent, PDO::PARAM_STR);
    $stmt->execute();
    $cn = null;
  } catch (PDOException $e) {
    print "Det sket sig: " . $e->getMessage() . "<br/>";
    die();
  }
}

try {
  $cn = new PDO("mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;", DBUSER, DBPW);
  $ticks = ResolveBoolean($absent) + ResolveBoolean($thursday) + ResolveBoolean($friday) + ResolveBoolean($saturday) + ResolveBoolean($sunday);
  if($ticks == 0){
    $sql = "DELETE FROM sig_roster WHERE idplayer = :i AND event = :e";
    $stmt2 = $cn->prepare($sql);
    $stmt2->bindParam(':i', $idplayer, PDO::PARAM_INT);
    $stmt2->bindParam(':e', $thisevent, PDO::PARAM_STR);
    $stmt2->execute();
  } else {
    $sql = "UPDATE sig_roster SET absent = :a, thursday = :t, friday = :f, saturday = :sa, sunday = :su, notes = :n, updated = :u WHERE idplayer = :i AND event = :e";
    $stmt2 = $cn->prepare($sql);
    $stmt2->bindParam(':a', ResolveBoolean($absent), PDO::PARAM_INT);
    $stmt2->bindParam(':t', ResolveBoolean($thursday), PDO::PARAM_INT);
    $stmt2->bindParam(':f', ResolveBoolean($friday), PDO::PARAM_INT);
    $stmt2->bindParam(':sa', ResolveBoolean($saturday), PDO::PARAM_INT);
    $stmt2->bindParam(':su', ResolveBoolean($sunday), PDO::PARAM_INT);
    $stmt2->bindParam(':n', str_replace("\n", "<br />", $notes), PDO::PARAM_STR);
    $stmt2->bindParam(':u', date("Y-m-d H:i:s"), PDO::PARAM_STR);
    $stmt2->bindParam(':i', $idplayer, PDO::PARAM_INT);
    $stmt2->bindParam(':e', $thisevent, PDO::PARAM_STR);
    $stmt2->execute();
  }
  $cn = null;
} catch (PDOException $e) {
  print "Det sket sig: " . $e->getMessage() . "<br/>";
  die();
}

/*
echo "<p>idplayer: ". $idplayer . "</p>\n";
echo "<p>thisevent: ". $thisevent . "</p>\n";
echo "<p>hits: ". $hits . "</p>\n";
echo "<p>ticks: ". $ticks . "</p>\n";
*/

die(header("Location: roster.php?event=" . $thisevent));
}
//******************************************************************
function ResolveBoolean($checkboxvalue){
$ret=0;
if($checkboxvalue=="on"){$ret=1;}
return $ret;
}
?>
