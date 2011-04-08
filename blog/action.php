<?php
include('include/session.php');
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)
include_once("functions.php");
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)

$id = $_GET['id'];	// get url variable id (user) variable
$do = $_GET['do'];	// get url variable do (requested action)

switch ($do){
	case "follow":	// request to follow the given user
		follow_user($session->puserid,$id);	// store follow relationship
		//change $_['userid'] to $_SESSION['username' on fritter
		$msg = "You have followed a user!";
		break;

	case "unfollow":	// request to unfollow given user
		unfollow_user($session->puserid,$id);	// remove follow relationship
		//change $_['userid'] to $_SESSION['username' on fritter
		$msg = "You have unfollowed a user!";
		break;
}
$_SESSION['message'] = $msg; // set session message

header("Location: index.php");	// return main page to browser

?>