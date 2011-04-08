<?php
//session_start();
include('../include/session.php');
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)
include_once('functions.php');
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)

if(!$session->logged_in){ header("Location: ../main.php");}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Microblogging Application</title>
</head>

<body>

<?php
echo "Logged in as $session->username , $session->puserid<br>";
echo $_SESSION['username']." - ".$_SESSION['userid']."<br>";
// If a message is returned for the user, format and display it
if (isset($_SESSION['message'])){
	echo "<b>". $_SESSION['message']. "</b>";
	unset($_SESSION['message']);
}
?>

<?php
/*  First Display all relavant posts
 *  Form for submitting post
 *  'Following' list
 *  Link to see all users
 */
$page = $_GET['page'];
$display = $_GET['display'];
if(!$page){
	$page = 1;
}
if(!$display){
	$display = "all";
}

$users = show_users($session->puserid);	// get follower list for session user; to be used to retrieve posts
if(count($users) && ($display == "them" || $display == "all")){
	$myusers = array_keys($users);	// return all keys in the array of users
}else{
	$myusers = array(-1);	// return empty array if no users and -1 dummy user
}

if($display=="me" || $display=="all"){
	$myusers[] = $session->puserid;	// add current user to $myusers array
}

$posts = show_posts($myusers,10,$page);	// get list of 100 most recent posts from users in $myusers

// format and display posts if they exist
//if (count($posts) || ($display == "them" && count($posts)==0)){
?>
<table border='1' cellspacing='0' cellpadding='3' width='500'>
<?php
$nav = getNextPage($myusers,10,$page);
echo "<tr><td width='100px'><p align='left'><a href='index.php?page=".$nav['page']."&display=".$display."'>".$nav['text']."</a></td><td><p align='right'><small>";
echo "Show: <a href='index.php?page=1&display=".me."'>Just Me</a> | ";
echo "<a href='index.php?page=1&display=".them."'>Them</a> | ";
echo "<a href='index.php?page=1&display=".all."'>Everyone</a>";
echo "</small></p></td></tr>";
foreach ($posts as $key => $list){
	echo "<tr valign='top'>\n";
	echo "<td><img src='../".$list['pURL']."' /></td>\n";
	echo "<td><p>".stripslashes($list['body'])."</p>\n";
	echo "<small>by <b>".$list['username']."</b> on ".$list['stamp'] ."</small></td>\n";
	echo "</tr>\n";
}
?>
</table>
<?php
if (count($posts)<1){
	if($display=="them" || $display=="all"){
		$msg = "No one has posted anything yet!";
	}else{
		$msg = "You haven't posted anything yet!";
	}
	echo "<p><b>".$msg."</b></p>";
}
?>

<form action='add.php' method='POST'>
<p>Your status:</p>
<textarea name='body' rows='5' cols='40' wrap=VIRTUAL></textarea>
<p><input type='submit' value='submit' /></p>
</form>

<h2>Users you're following</h2>

<?php
$users = show_users($session->puserid); // get follower list for session user

// if there are users
if(count($users)){
	?>
<ul>
<?php
foreach ($users as $key => $value){	// iterate through array of users, format and display each user
	echo "<li>".$value."</li>\n";
}
?>
</ul>
<?php
}else{
	?>
<p><b>You're not following anyone yet!</b></p>
	<?php
}
?>
<p><a href='users.php'>see list of users</a></p>
</body>
</html>
