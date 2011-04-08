<?php
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)
include('include/session.php');
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)
include_once("functions.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Microdebating Application - Users</title>
</head>
<body>

<h1>List of Users</h1>
<?php
$users = show_users();	// get list of all users
$following = following($session->puserid); // get follow list for session user

if(count($users)){	// if users exist, format and display user id and usernames
	?>
<table border='1' cellspacing='0' cellpadding='5' width='500'>
<?php
foreach ($users as $key => $value){
	echo "<tr valign='top'>\n";
	echo "<td>".$key ."</td>\n";
	echo "<td>".$value." ";
	if ($value != $session->username){
		if (in_array($key,$following)){	// if user is followed by session user then display unfollow option, else display follow option
			echo "<small><a href='action.php?id=$key&do=unfollow'>unfollow</a></small>";
		}else{
			echo "<small><a href='action.php?id=$key&do=follow'>follow</a></small>";
		}
	}
	else{
		echo "<small>(This is you)</small>";
	}
	echo "</td>\n";
	echo "</tr>\n";
}
?>
</table>
<?php
}else{
	?>
<p><b>There are no users in the system!</b></p>
	<?php
}
?>
</body>
</html>
