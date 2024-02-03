<?php
session_start();
include "general.php";
include "base.php";
if($_SESSION['loggedin']) {editform();} else {no_access();}

//******************************************************************
function editform(){
// Senast uppdaterad 2021-07-17 av joakim.thulin@outlook.com
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
<title>RunstenGolf - Ny spelare</title>
<meta http-equiv='content-type' content='text/html;charset=utf-8' />
<meta http-equiv='Content-Script-Type' content='text/javascript' />
<meta http-equiv='Content-Style-Type' content='text/css' />
<link rel='stylesheet' media='screen' type='text/css' href='signup.css' />
<link rel='stylesheet' media='print' type='text/css' href='print.css' />
<script type='text/javascript'>
function playerList(){window.location='index.php'}
</script>
</head>
<body>
<fieldset style='width:300px'>
<legend>Lägg till ny spelare</legend>
<form action='adduserupdate.php' method='post'>
<p>Spelarens smeknamn:&nbsp;<input type='text' name='player' value='' maxlength='15' style='width:180px' /></p>
<p>
<input type='submit' value='Lägg till' />
<input type='button' value='Avbryt' onclick='playerList()' />
</p>
</form>
</fieldset>
</body>
</html>
<?php
}
//******************************************************************
?>
