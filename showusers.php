<?php
$display = $_GET['display'];
if ($display){$msg = "Cohoot!ers";} else{$msg = "All hoot!ers";}

$users = show_users();	// get list of all users
$following = following($session->puserid); // get follow list for session user
$userData = show_user_info();
if(count($users)){	// if users exist, format and display user id and usernames
	?>
<div id="content">	

<?php
echo "<h1> $msg </h1>";
foreach ($userData as $key => $list){
	
	if ($display && !in_array($key,$following)) continue;
	echo "<li>";
	echo "<span><img src='".$list['tURL']."' /></span>";
	echo "<span class='message'>".$list['username'];
	if ($list['username'] != $session->username){
		if (in_array($key,$following)){	// if user is followed by session user then display unfollow option, else display follow option
			echo "<small><a href='action.php?id=$key&do=unfollow'>unfollow</a></small>";
		}else{
			echo "<small><a href='action.php?id=$key&do=follow'>follow</a></small>";
		}
	}
	else{
		echo "<small>(This is you)</small>";
	}
	echo "</span></li>";
}
?>

</div>
<?php
}else{
	?>
<p><b>There are no users in the system!</b></p>
	<?php
}
?>