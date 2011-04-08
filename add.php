<?php


include("include/session.php");
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)
include_once("include/functions.php");
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)

$puserid = $session->puserid;	// get userid from form post
$body = substr($_POST['body'],0, 140); // get body form post; limited to firsrt 400 characters

add_post($puserid,$body);	// submit the post
$_SESSION['message'] = "Your post has been added!";	//set session message


header("Location: index.php");	// return main page to browser
?>