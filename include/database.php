<?php
/**
 * database.php
 * 
 * The Dabatase class is meant to simplify the task
 * of accessign information from the site's database
 * 
 */
include_once("constants.php");

class MySQLDB
{
	var $connection;	//the MySQL database connection
	var $num_active_users;	//number of active users viewing site
	var $num_active_guests;	//number of active guests viewing the site
	var $num_members;	//number of signed-up users
	/* Note: call getNumMembers() to access $num_members! */
	
	// Class constructor
	function MySQLDB(){
		// Make connection to db
		$this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
		
		/**
		 * only query database to find out number of members
		 * when getNumMmembers() is called for teh first time,
		 * until then, default value set.
		 */
		$this->num_members = -1;
		
		if(TRACK_VISITORS){
			//Calculate number of users on the site
			$this->calcNumActiveUsers();
			
			//Calculate number of guests on the site
			$this->calcNumActiveGuests();
		}
	}
	
	/**
	 * confirmUserPass - Checks whther or not the given username is in the database,
	 * if so it checks if the given password is the same in the db for that user.
	 * If the user doesn't exist or it the passwords don't mathc up, it returens an error code
	 * (1 or 2). onsuccess it returns 0
	 */
	function confirmUserPass($username, $password){
		//add slaches if necessary for query
		if(!get_magic_quotes_gpc()){
			$username = addslashes($username);
		}
		
		//verify that user is in db
		$q = "SELECT password FROM ".TBL_USERS." WHERE username = '$username'";
		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){
			return 1;	//indicates username failure
		}
		
		// Retreive pq from result, strip slashes
		$dbarray = mysql_fetch_array($result);
		$dbarray['password'] = stripslashes($dbarray['password']);
		$password = stripslashes($password);
		
		// Validate that password is correct
		if($password == $dbarray['password']){
			return 0; //success, uname and pw confirmed
		}
		else{
			return 2;	//indicates password failure
		}
	}
	
	/**
	 * confirmUserID
	 * checks whether or not given username is in db, if so it checks
	 * if the given userid is the same userid in the db for that user.
	 * if the user doesn't exist or if the userids don't match up, it returns
	 * an error code (1 or 2). On success it returns 0.
	 */
	function confirmUserID($username, $userid){
		//add slashes if necessary
		if(!get_magic_quotes_gpc()){
			$username = addslashes($username);
		}
		
		//verify that user is in db
		$q = "SELECT userid FROM ".TBL_USERS." WHERE username = '$username'";
		$result = mysql_query($q, $this->connection);
		
		if(!$result || (mysql_numrows($result) < 1)){
			return 1;	//indicates username failure
		}
		
		//retrieve userid from result, strip slashses
		$dbarray = mysql_fetch_array($result);
		$dbarray['userid'] = stripslashes($dbarray['userid']);
		$userid = stripslashes($userid);
		
		//validate that userid is correct 
		if($userid == $dbarray['userid']){
			return 0;	//sucess, uname and uid confirmed
		}
		else{
			return 2;	//indeicates uid invalid
		}
	}
	
	/**
	 * usernameTaken
	 * returns true if the username has been taken by another user, false otherwise
	 */
	function usernameTaken($username){
		if(!get_magic_quotes_gpc()){
			$username = addslashes($username);
		}
		$q = "SELECT username FROM ".TBL_USERS." WHERE username = '$username'";
		$result = mysql_query($q, $this->connection);
		return (mysql_numrows($result) > 0); 
	}
	
	/**
	 * usernameBanned
	 * returnns true of the username has been bannned by administrator
	 */
	function usernameBanned($username){
		if(!get_magic_quotes_gpc()){
			$username = addslashes($username);
		}
		$q = "SELECT username FROM ".TBL_BANNED_USERS." WHERE username= '$username'";
		$result = mysql_query($q, $this->connection);
		return (mysql_numrows($result) > 0);
	}
	
	/**
	 * addNewUser - Inserts the given (username, password, email)
	 * info into the database. Appropriate user level is set.\
	 * Returns true on success, false otherwise.
	 */
	function addNewUser($username, $password, $email){
		$time = time();
		//if admin sign up, give admin user level
		if(strcasecmp($username, ADMIN_NAME) == 0){
			$ulevel = ADMIN_LEVEL;
		}
		else{
			$ulevel = USER_LEVEL;
		}
		$q = "INSERT INTO ".TBL_USERS." VALUES ('$username', '$password', '0', $ulevel, '$email', $time, 'active', null)";
		$result_users = mysql_query($q, $this->connection);
		$id = mysql_insert_id();
		$q = "INSERT INTO ".TBL_USER_IMAGES."(user_id) VALUES($id)";
		$result_images = mysql_query($q, $this->connection);
		return $result_users && $result_images;
	}
	
	/**
	 * updateUserField - Updates a field, specified by the field parameter,
	 * in the users row of the database
	 */
	function updateUserField($username, $field, $value){
		$q = "UPDATE ".TBL_USERS." SET ".$field." = '$value' WHERE username = '$username'";
		return mysql_query($q, $this->connection);
	}
	
	/**
	 * getUserInfo - erturns the result array from a mysql
	 * query asking for all information stored regarding
	 * the given username. If query fails, NULL is returned.
	 */
	function getUserInfo($username){
		$q = "SELECT u.*, i.pURL FROM ".TBL_USERS." AS u INNER JOIN user_images AS i ON u.id = i.user_id AND u.username = '$username'";
		$result = mysql_query($q, $this->connection);
		// Error occured, return given name by default
		if(!$result || (mysql_numrows($result) < 1 )){
			return NULL;
		}
		// return result array
		$dbarray = mysql_fetch_array($result);
		return $dbarray;
	}
	
	/**
	 * getNumMembers - Returns the number of signed-up users
	 * of the website, banned members not included. The first 
	 * time the function is called on page load, the database
	 * is queried, on subsequent calls, the stored result is 
	 * is returned. This is to improve teh efficiency, effectiely
	 * not querying the databse when no cal is made. 
	 */
	function getNumMembers(){
		if($this->num_members < 0){
			$q = "SELECT * FROM ".TBL_USERS;
			$result = mysql_query($q, $this->connection);
			$this->num_members = mysql_numrows($result);
		}
		return $this->num_members;
	}
	
	/**
	 * calcNumActiveUsers - Finds out how many active users 
	 * are viewing the site and sets class cariable accordingly.
	 */
	function calcNumActiveUsers(){
		//calculate number of useres on site
		$q = "SELECT * FROM ".TBL_ACTIVE_USERS;
		$result = mysql_query($q, $this->connection);
		$this->num_active_users = mysql_numrows($result);
	}
	
	/**
	 * calcNumActiveGuests - Finds out how many active guests
	 * are viewing the site and sets calss variable accordingly.
	 */
	function calcNumActiveGuests(){
		$q = "SELECT * FROM ".TBL_ACTIVE_GUESTS;
		$result = mysql_query($q, $this->connection);
		$this->num_active_guests = mysql_numrows($result);
	}
	
	/**
	 * addActiveUser - Updates username's last active timestamp
	 * in the database, and also adds him to the table of 
	 * active users, or updates timestamp if already there
	 */
	function addActiveUser($username, $time){
		$q = "UPDATE ".TBL_USERS." SET timestamp = '$time' WHERE username = '$username'";
		mysql_query($q, $this->connection);
		
		if(!TRACK_VISITORS) return;
		$q = "REPLACE INTO ".TBL_ACTIVE_USERS." VALUES ('$username', '$time')";
		mysql_query($q, $this->connection);
		$this->calcNumActiveUsers();
	}
	
	/**
	 * addActiveGuest - Adds guest to actuve guests table
	 */
	function addActiveGuest($ip, $time){
		if(!TRACK_VISITORS) return;
		$q = "REPLACE INTO ".TBL_ACTIVE_GUESTS." VALUES ('$ip', '$time')";
		mysql_query($q, $this->connection);
		$this->calcNumActiveGuests();
	}
	
	//removeActiveUser
	function removeActiveUser($username){
		if(!TRACK_VISITORS) return;
		$q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE username = '$username'";
		mysql_query($q, $this->connection);
		$this->calcNumActiveUsers();
	}
	
	//removeActiveGuest
	function removeActiveGuest($ip){
		if(!TRACK_VISITORS) return;
		$q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE ip = '$ip'";
		mysql_query($q, $this->connection);
		$this->calcNumActiveGuests();
	}
	
	//removeInactiveUsers
	function removeInactiveUsers(){
		if(!TRACK_VISITORS) return;
		$timeout = time() - USER_TIMEOUT * 60;
		$q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE timestamp < $timeout";
		mysql_query($q, $this->connection);
		$this->calcNumActiveUsers();
	}
	
	//removeInactiveGuests(){
	function removeInactiveGuests(){
		if(!TRACK_VISITORS) return;
		$timeout = time() - GUEST_TIMEOUT * 60;
		$q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE timestamp < $timeout";
		mysql_query($q, $this->connection);
		$this->calcNumActiveGuests();
	}
	
	/**
	 * query - Performs the given query onthe database and
	 * returns the result, which may be false, true, or a
	 * resource identifier
	 */
	function query($query){
		return mysql_query($query, $this->connection);
	}
};

//Create database connection
$database = new MySQLDB;

?>