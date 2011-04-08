<?php
/**
 * Index.php
 *
 * this is an example of the main page of a website. Here
 * users will be able to login. however, like on most sites
 * the login form doesn't just have to be on the main page,
 * but re-appear on subsequent pages, depending on whether
 * the user has logged in or not.
 */
include_once("include/session.php");
include_once("include/functions.php");


		
/*************************blog section*****************************/

//echo "Logged in as $session->username , $session->puserid<br>";
//echo $_SESSION['username']." - ".$_SESSION['userid']."<br>";
// If a message is returned for the user, format and display it




/*  First Display all relavant posts
 *  Form for submitting post
 *  'Following' list
 *  Link to see all users
 */
if($_SESSION['page'] == "maxed out"){
	return;
} else{
		$page = $_SESSION['page'];
		if(!$page){
			$page = 1;
		} else{
			$page++;
		}
		$_SESSION['page'] = $page;
		
		$users = show_users($session->puserid);	// get follower list for session user; to be used to retrieve posts
		if(count($users)){
			$myusers = array_keys($users);	// return all keys in the array of users
		}else{
			$myusers = array(-1);	// return empty array if no users and -1 dummy user
		}
		$myusers[] = $session->puserid;	// add current user to $myusers array
		$posts = show_posts($myusers,10,$page);	// get list of 100 most recent posts from users in $myusers
		
		if (count($posts)<1){
			//echo "<p><b> no posts: ".$page."</b></p>";
			$_SESSION['page'] = "maxed out";
			return;
		}
		
		foreach ($posts as $key => $list){
			echo "<li class='status' onMouseOver='toggleOn(\"".$list['id']."\")' onMouseOut='toggleOff(\"".$list['id']."\")'>";
			echo "<span class = 'status-img'><img src='".$list['pURL']."' /></span>";
			echo "<span class = 'status-body'>";
			echo "<span class = 'message'><span class = 'username'>".$list['username']."</span> ".stripslashes($list['body'])."</span>";
			echo "<span class = 'by'>".$list['stamp'] ."<span id ='hoot_".$list['id']."' class='button'><a href='hoot.php?id=".$list['user_id']."&name=".$list['username']."'>hoot!</a></span>";
			echo "</span>";
			echo "</span>";
			echo "</li>";
		}
}	
		
?>





