<?php
//requestied username error checking

$req_user = trim($_GET['user']);
if(!$req_user || strlen($req_user) ==0 ||
!eregi("^([0-9a-z])+$", $req_user) ||
!$database->usernameTaken($req_user)){
	die("Username not registered");
}

// logged in user iewing own account
//if(strcmp($session->username, $req_user) == 0){
//	echo "<h1>User Info</h1>";
//}
//visitor not viewing own account
//else{
$req_user_info = $database->getUserInfo($req_user);
echo "<h1><img src='".$req_user_info['pURL']."' /> User Info</h1>";
//echo "<h1>User Info</h1>";
//}

//display requested user information
//$req_user_info = $database->getUserInfo($req_user);

//username
echo "<b>Username: ".$req_user_info['username']."</b><br>";

//email
echo "<b>Email:</b> ".$req_user_info['email']."<br>";

/**
 * Note: when you add your own fields to th users table
 * to hold more information, like homepage, location, etc.
 * they can easily be accessed by the user info array.
 *
 * $session->user_info['location']; (for logged in users)
 *
 * ..and for this page
 *
 * $req_user_info['location']; (for any user)
 */

//if logged in user viewing own account, give link to edit
if(strcmp($session->username, $req_user) == 0){
	echo "<br><a href=\"useredit.php\">Edit Account Information</a><br>";
}

//link back to main
echo "<br>Back to [<a href=\"index.php\">Home</a>]<br>";

?>