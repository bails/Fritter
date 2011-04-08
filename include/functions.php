<?php
include_once("constants.php");
/*
 *add_post
 * Add the post(body) submitted by user (user_id) with the current time
 * construct SQL insert statement and execute
 * mysql_real_escape_string used to escape special characters in a string such as \n, \r, etc
 * this is used ot make data safe before sending the sql query
 */
// include database.php in fritter

function add_post($userid, $body){	//change userid to username in fritter
	$sql = "insert into posts (user_id, body, stamp)
					values ($userid, '" . mysql_real_escape_string($body). "', now())";
	$result = mysql_query($sql);
}

/*
 * add_lol
 * increments the lol score for the given user 
 */
function add_lol($userid){
	$sql = "UPDATE ".TBL_LOLS." SET lol = lol + 1 where user_id = $userid";
	$result = mysql_query($sql);
}
/* getNextPage
 * returns next page
 * DEPRECATE THIS 12/29/2009
 */
function getNextPage($userid, $limit=0, $page){
	$text = "Older Posts";	//default

	$user_string = implode(',', $userid);
	$sql = "SELECT count(*) as numrows from posts P LEFT JOIN users U ON P.user_id = U.id where P.user_id in ($user_string)";
	$result = mysql_query($sql);
	$rows = mysql_fetch_array($result);
	$numrows = $rows['numrows'];
	$maxPage = ceil($numrows/$limit);

	if(($page+1)>$maxPage || $page<0){
		$page = 1;
		$text = "Back to Start";
	}
	else{
		$page++;
		$text = "Older Posts";
	}
	$info = array('text' => $text, 'page' => $page);

	return $info;
}


/* show_posts
 * return array with most recent posts from users in array $userid,
 * limited to the most recent $limit posts
 */
function show_posts($userid, $limit=0, $page=1){//change userid to username in fritter
	$posts = array();

	$user_string = implode(',', $userid); // create comma delimited list of users in single string
	if(!$user_string){
		return $posts;	//return empty array
	}


	//if  limit is specified, construct limit portion of sql statement
	if($limit>0){
		/* $sql = "SELECT count(*) as numrows from posts P LEFT JOIN users U ON P.user_id = U.id where P.user_id in ($user_string)";
		 $result = mysql_query($sql);
		 $rows = mysql_fetch_array($result);
		 $numrows = $rows['numrows'];
		 $maxPage = ceil($numrows/$limit);

		 if($page>$maxPage || $page<0){
			$page = 1;
			}*/
		$offset = ($page - 1) * $limit;
		$extra = "LIMIT ".$offset.", ".$limit;
	}else{
		$extra='';
	}

	// construct and execute sql statement to retreive posts
	$sql = "select P.id, P.user_id, U.username, P.body, P.stamp, I.pURL from posts P LEFT JOIN users U ON P.user_id = U.id LEFT JOIN ".TBL_USER_IMAGES." I ON U.id = I.user_id where P.user_id in ($user_string) order by P.stamp desc $extra";
	//echo $sql;	// for debugging
	$result = mysql_query($sql);

	// iterate through sql query result set and store records
	// into multi array with associative keys stamp, user_id, body
	while($data = mysql_fetch_object($result)){
		$posts[] = array(	'stamp' => $data->stamp,
							'id' => $data->id,
							'user_id' => $data->user_id,
							'username' => $data->username, 
							'body' => $data->body,
							'pURL' => $data->pURL
		);
	}
	return $posts;	// return array of user posts
}

//show list of all users and user info
function show_user_info($userid=0){
	$users = array();

	if($userid > 0){
		$user_string = implode(',', $userid); // create comma delimited list of users in single string
		if(!$user_string){
			return $users;	//return empty array
		}
		else{
			$extra = "AND U.id in ($user_string)";
		}
	}



	// construct and execute sql statement to retreive posts
	$sql = "select U.id, U.username, I.pURL, I.tURL from users U LEFT JOIN ".TBL_USER_IMAGES." I ON U.id = I.user_id where U.status = 'active' ".$extra." order by U.id";
	
	//echo $sql;	// for debugging
	$result = mysql_query($sql);

	// iterate through sql query result set and store records
	// into multi array with associative keys stamp, user_id, body
	while($data = mysql_fetch_object($result)){
		$users[$data->id] = array(	'id' => $data->id,
							'username' => $data->username, 
							'tURL' => $data->tURL,
							'pURL' => $data->pURL
		);
	}
	return $users;	// return array of user posts
}


/* show_users
 * if $user_id specified, return array of users 'followed' by user identified with $user_id
 * if $user_id unspecified, return array of all system users
 */
function show_users($user_id=0){

	if ($user_id > 0){	// user is specified
		$follow = array();
		$fsql = "select user_id from following where follower_id = '$user_id'";	//update this to reflect new db strucutre in fritter
		$fresult = mysql_query($fsql);	// get list of users followed by $user_id

		while($f = mysql_fetch_object($fresult)){	// iterate through sql query record set
			array_push($follow, $f->user_id);	// populate $follow array with user id's of follow list
		}

		// if $user_id does not follow anyone, return empty array and exit
		// otherwise form $id_string
		if(count($follow)){	// follow list is not empty
			$id_string = implode(',', $follow);	// create comma delimited list of users followed by $user_id
			$extra = " and id in ($id_string)"; // set $extra portion of sql statement
		}else{	// follow list is empty
			return array();
		}
	}

	$users = array();
	// sql statement string:
	// select id, name of all active users that are in the follow list
	$sql = "select id, username from users where status = 'active' $extra order by username";
	$result = mysql_query($sql);	//change to use methods in database.php

	// iterate through query result set and populate $users array with keys,usernames
	while ($data = mysql_fetch_object($result)){
		$users[$data->id] = $data->username;
	}
	return $users;
}

/* following
 * returns list of user id's for all users being followed by &userid
 */
function following($userid){
	$users = array();
	$sql = "select distinct user_id from following where follower_id = '$userid'";

	$result = mysql_query($sql);

	while($data = mysql_fetch_object($result)){
		array_push($users, $data->user_id);
	}

	return $users;
}

/* check_count
 * helper method for function follow_user and unfollow_user
 *  returns number of occurrences of user $first following user $second in the 'following' table
 */
function check_count($first, $second){
	$sql = "select count(*) from following where user_id='$second' and follower_id='$first'";
	$result = mysql_query($sql);

	$row = mysql_fetch_row($result);
	return $row[0];
}

/* follow_user
 * stores a following relationship between two users if the relationship does not already exist
 */
function follow_user($me, $them){
	$count = check_count($me, $them);	// use check_count to see if relationship exists

	if($count == 0){	// relationship does not exist, add the relationship, else do nothing
		$sql = "insert into following (user_id, follower_id) values ($them, $me)";
		$result = mysql_query($sql);
	}
}

/* unfollow_user
 * removes 'following' relationship between two users if it exists
 */
function unfollow_user($me, $them){
	$count = check_count($me, $them);	// use check_count to see if relationship exists

	if($count !=0){	// relationship exists, proceed to delete the relatinship
		$sql = "delete from following where user_id='$them' and follower_id='$me' limit 1";
		$result = mysql_query($sql);
	}
}



?>