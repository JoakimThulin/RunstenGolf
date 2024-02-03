<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {updateplayer();} else {no_access();}

//******************************************************************
function updateplayer(){
// Senast uppdaterad 2021-07-17 av joakim.thulin@outlook.com

$player = $_POST["player"];
$golfid = $_POST["golfid"];
$email = $_POST["email"];
$basefee = $_POST["basefee"];
$annualfee = $_POST["annualfee"];
$active = $_POST["active"];
$firstname = $_POST["firstname"];
$lastname = $_POST["lastname"];
$pw = $_POST["pw"];

try {
  $dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
  $db = new PDO($dsn, DBUSER, DBPW);
  $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  $sql = "UPDATE sig_players SET player =:p, firstname=:f, lastname=:l, pw=:w, golfid=:g, email=:e, basefee=:b, active=:a, updated=:u WHERE id=:i";
  $stmt = $db->prepare($sql);
  $stmt->bindParam(':p', $player, PDO::PARAM_STR);
  $stmt->bindParam(':f', $firstname, PDO::PARAM_STR);
  $stmt->bindParam(':l', $lastname, PDO::PARAM_STR);
  $stmt->bindParam(':w', $pw, PDO::PARAM_STR);
  $stmt->bindParam(':g', $golfid, PDO::PARAM_STR);
  $stmt->bindParam(':e', $email, PDO::PARAM_STR);
  $stmt->bindParam(':b', ResolveBoolean($basefee), PDO::PARAM_INT);
  $stmt->bindParam(':a', ResolveBoolean($active), PDO::PARAM_INT);
  $stmt->bindParam(':u', date("Y-m-d H:i:s"), PDO::PARAM_STR);
  $stmt->bindParam(':i', $_SESSION['playerid'], PDO::PARAM_INT);
  $stmt->execute();
  $db = null;
} catch (PDOException $e) {
  print "Det sket sig: " . $e->getMessage() . "<br/>";
  die();
}

$currentyear = date("Y");
try {
  $dsn = "mysql:host=" . DBSERVER . ";port=3306;dbname=" . DBNAME . ";charset=utf8;";
  $db = new PDO($dsn, DBUSER, DBPW);
  if(ResolveBoolean($annualfee)==1){

    $sql = "SELECT count(playerid) FROM sig_annualfee WHERE (feeyear = :y) AND (playerid = :i)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':y', $currentyear, PDO::PARAM_INT);
    $stmt->bindParam(':i', $_SESSION['playerid'], PDO::PARAM_INT);
    $stmt->execute();
    $r = $stmt->fetch(PDO::FETCH_NUM);
    $alreadypaid = 0;
    if($r[0] > 0){$alreadypaid = 1;} 

    if($alreadypaid==0){
      $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      $sql = "INSERT INTO sig_annualfee (feeyear, playerid) VALUES (:y, :i)";
      $stmt2 = $db->prepare($sql);
      $stmt2->bindParam(':y', $currentyear, PDO::PARAM_INT);
      $stmt2->bindParam(':i', $_SESSION['playerid'], PDO::PARAM_INT);
      $stmt2->execute();
    }

  }else{

      $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      $sql = "DELETE FROM sig_annualfee WHERE feeyear=:y AND playerid=:i";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':y', $currentyear, PDO::PARAM_INT);
      $stmt->bindParam(':i', $_SESSION['playerid'], PDO::PARAM_INT);
      $stmt->execute();

  }
  $db = null;
} catch (PDOException $e) {
  print "Det sket sig: " . $e->getMessage() . "<br/>";
  die();
}

die(header("Location: index.php"));
}
//******************************************************************
function ResolveBoolean($checkboxvalue){
//Uppdaterad 2018-10-20 av Joakim [joakim.thulin@outlook.com]
//Översätter textvärde till något som liknar en boolean
  $ret=0;
  if($checkboxvalue=="on"){$ret=1;}
  return $ret;
}
?>
