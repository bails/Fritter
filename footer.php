<div id='footer'>
<?php
 $users = show_users($session->puserid); // get follower list for session user
 // if there are users
if(count($users)){
	echo "Cohoot!s: ";
	$i = 0; 
	foreach ($users as $key => $value){	// iterate through array of users, format and display each user
		echo "<a href=\"info.php?user=$value\">$value</a>, ";
		if (++$i > 2) break;
 	}
}else{
?>
 <p><b>You're not following anyone yet!</b></p>
<?php
 }
?>
 <a href='users.php?display=following'>...</a>
<div>
<?php
if(!defined('TBL_ACTIVE_USERS')) {
	die("Error processing page");
}

$q = "SELECT username FROM ".TBL_ACTIVE_USERS." ORDER BY timestamp DESC, username";
$result = $database->query($q);
// error occured
$num_rows = mysql_numrows($result);
if(!$result || ($num_rows < 0)){
	echo "Error displaying info";
} else if($num_rows > 0){
  /**
  * Just a little page footer, tells how many registered members
  * there are, how many users currently logged in and viewing site,
  * and how many guests viewing site. Active users are displayed,
  * with link to their user information.
  */
     echo "<b>Live: $database->num_active_users</b> <a href='users.php'>hoot!ers</a>, <b>$database->num_active_guests</b> guests.";	
 }
?>
</div>
<?php 
	echo "$session->username: ";
 	echo "<a href=\"index.php\">Home</a> | "
		."<a href=\"info.php?user=$session->username\">My Info</a> | "
		."<a href=\"edit.php\">Edit</a> | ";

if($session->isAdmin()){
	echo "<a href=\"admin/admin.php\">Admin Center</a> | ";
}

echo "<a href=\"process.php\">Logout</a>";
?>
</div>