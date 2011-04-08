<?php
include("include/session.php");
include_once('include/functions.php');

$id = $_GET['id'];
$name = $_GET['name'];
add_lol($id);

if($name == $_SESSION['username']){
	$name = "yourself";
}
$msg = "You Hoot'd @ $name.";
$_SESSION['message'] = $msg;

//if($session->logged_in){
//	echo $session->logged_in;
//}
//echo $session->logged_in;
header("Location: index.php");
//exit();
?>