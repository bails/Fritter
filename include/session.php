<?php
/**
 * Session.php
 * 
 * the Session class is meant to simplify the task of kepeing
 * track of logged in users and also guests.
 */

include("database.php");
include("mailer.php");
include("form.php");

class Session
{
	var $username;		//Username given on sign-up
	var $userid;		//Random value generated on current login
	var $userlevel;		//The level to which the user pertains
	var $time;			//Time user was last active (page loaded)
	var $logged_in;		//True if user is logged in, false otherwise
	var $userinfo = array();		//The array holding all user ingo
	var $url;			//The page url currently beign viewed
	var $referrer;		//Last recorded site page viewed
	var $puserid;		//User id for posting system
	var $useravatar;
	var $home;
	/*
	 * Note: referrer should really onle be considered the actual
	 * page referrer in process.php, any other time it may be
	 * inaccurate
	 */
	
	//Class constructor
	function Session(){
		$this->time = time();
		$this->startSession();
	}
	
	/**
	 * startSession - erforms all the actions necessary to
	 * initialize this session object. Tries to determine if the
	 * user has logged in already, and sets the variables
	 * accordingle. Also takes advantage of this page load to 
	 * update the active visitors tables.
	 */
	function startSession(){
		global $database;		//The database connection
		session_start();		//Tell php to start the session
		
		//determine if user is logged in
		$this->logged_in  = $this->checkLogin();
		
		/**
		 * Set guest value to users not logged in, and update
		 * actiive guests table accordingly.
		 */
		if(!$this->logged_in){
			$this->username = $_SESSION['username'] = GUEST_NAME;
			$this->userlevel = GUEST_LEVEL;
			$database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
		}
		//Update users last active timestamp
		else{
			$database->addActiveUser($this->username,$this->time);
		}
		
		// Remove inactive visitors from database
		$database->removeInactiveUsers();
		$database->removeInactiveGuests();
		
		//Set referrer page
		if(isset($_SESSION['url'])){
			$this->referrer = $_SESSION['url'];
		}else{
			$this->referrer = "/";
		}
		
		// Set current url
		$this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
		$this->home = "index.php";
	}
	
	/**
	 * checkLogin - Checks if the user has already previously
	 * logged in, and a session with the user has already been
	 * established. Also checks to see if user has been remembered.
	 * if so, the database is queried ot make sure of ther users's
	 * authenticity. Returns true if the user has logged in.
	 */
	function checkLogin(){
		global $database;		//the database connection
		// check if user has been remembered
		if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])){
			$this->username = $_SESSION['username'] = $_COOKIE['cookname'];
			$this->userid = $_SESSION['userid'] = $_COOKIE['cookid'];
		}
		
		//username and userid have been set and not guest
		if(isset($_SESSION['username']) && isset($_SESSION['userid']) && $_SESSION['username'] != GUEST_NAME){
			//confirm that username and userid are valid
			if($database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0){
				//Variables are incorrect, user not logged in
				unset($_SESSION['username']);
				unset($_SESSION['userid']);
				return false;
			}

		//user is logged in, set class variables
		$this->userinfo = $database->getUserInfo($_SESSION['username']);
		$this->username = $this->userinfo['username'];
		$this->userid = $this->userinfo['userid'];
		$this->userlevel = $this->userinfo['userlevel'];
		$this->puserid = $this->userinfo['id'];
		$this->useravatar = $this->userinfo['pURL'];
		return true;
		}
		//User not logged in
		else{
			return false;
		}
	}
	
	/**
	 * login - The user has submitted his username and password
	 * through the login form, this function checks the authenticity
	 * of that information in the database and creates th session.
	 * effectively logging in the user if all goes well
	 */
	function login($subuser, $subpass, $subremember){
		global $database, $form;	//The database and form object
		
		//username error checking
		$field = "user";	//Use field name for username
		if(!$subuser || strlen($subuser = trim($subuser)) == 0){
			$form->setError($field, "* Username not entered");
		}
		else{
			//check if username is not alphanumeric
			if(!eregi("^([0-9a-z])*$", $subuser)){
					$form->setError($field, "* Username not alphanumeric");
			}
		}
		
		//password error checking
		$field = "pass";	//user field name for password
		if(!$subpass){
			$form->setError($field, "* Password not entered");	
		}
		
		//return if form errors exist
		if($form->num_errors > 0){
			return false;
		}
		
		//Checks that username is in database and password is correct
		$subuser = stripslashes($subuser);
		$result = $database->confirmUserPass($subuser, md5($subpass)); 
		
		//check error codes
		if($result == 1){
			$field = "user";
			$form->setError($field, "* Username not found");
		}
		else if($result == 2){
			$field = "pass";
			$form->setError($field, "* Invalid password");
		}
		
		// return if form errors exist
		if($form->num_errors > 0){
			return false;
		}
		
		//Username and password are correct, register session variables
		$this->userinfo = $database->getUserInfo($subuser);
		$this->username = $_SESSION['username'] = $this->userinfo['username'];
		$this->userid = $_SESSION['userid'] = $this->generateRandID();
		$this->userlevel = $this->userinfo['userlevel'];
		$this->useravatar = $this->userinfo['pURL'];
		
		//Insert userid into database and update active users table
		$database->updateUserField($this->username, "userid", $this->userid);
		$database->addActiveUser($this->username, $this->time);
		$database->removeActiveGuest($_SERVER['REMOTE_ADDR']);
		
		/**
		 * this is the cool part: the user has requested that we remember that
		 * he's logged in, so we set two cookies. one to hold his username,
		 * and one to hold his random value userid. it expires by the time 
		 * specified in contsants.php.Now, next time he coems to our site, we will
		 * log him in automatically, but only if he didnt log out before he left
		 */
		
		if($subremember){
			setcookie("cookname", $this->username, time()+COOKIE_EXPIRE, COOKIE_PATH);
			setcookie("cookid", $this->userid, time()+COOKIE_EXPIRE, COOKIE_PATH);
		}
		
		//login completed successfully
		return true;
	}
	
	/**
	 * logout - Gets called when the user wants to be logged out of the
	 * website. it deletes any cookies that were stoerd on the users 
	 * computer as a result of him wanting to be remembered, and also
	 * unsets session variables and demotes his user level to guest
	 */
	function logout(){
		global $database;	//the database connection
		/**
		 * Delete cookies - the time must be in the past, 
		 * so just negate what you added when creating
		 * the cookie
		 */
		if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])){
			setcookie("cookname","",time() - COOKIE_EXPIRE, COOKIE_PATH);
			setcookie("cookid", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
		}
		
		//unset php session variables
		unset($_SESSION['username']);
		unset($_SESSION['userid']);
		
		//reflect fact that user has logged out
		$this->logged_in = false;
		
		/*
		 * remove from active users table and add to 
		 * active guests tables
		 */
		$database->removeActiveUser($this->username);
		$database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
		
		//set user level to guest
		$this->username = GUEST_NAME;
		$this->userlevel = GUEST_LEVEL;
	}
	
	/**
	 * register - Gets called when the user has just subtmitted the 
	 * registration form. determins if there were any errors with
	 * the entry fields, if so, it records the errors and returns
	 * 1. if no errors were found, it registeres the new user and 
	 * returns 0. returns 2 if refistration failed. 
	 */
	function register($subuser, $subpass, $subemail){
		global $database, $form, $mailer;	//teh database, form and mailer object

		//username error cehcking
		$field = "user";	//use field name for username
		if(!$subuser || strlen($subuser = trim($subuser)) == 0){
			$form->setError($field, "* Username not entered");
		}
		else{
			// spruce up username, check length
			$subuser = stripslashes($subuser);
			if(strlen($subuser) < 5){
				$form->setError($field, "* Username below 5 characters");
			}
			else if(strlen($subuser) > 30){
				$form->setError($field, "* Username above 30 characters");
			}
			// check if username not alphanumeric
			else if(!eregi("^([0-9a-z])+$", $subuser)){
				$form->setError($field, "* Username not alphanumeric");
			}
			//checkif username is reserved
			else if(strcasecmp($subuser, GUEST_NAME) == 0){
				$form->setError($field, "* Username reserved word");
			}
			//check if username is already in use
			else if($database->usernameTaken($subuser)){
				$form->setError($field, "* Username already in use");
			}
			//check if username is banned
			else if($database->usernameBanned($subuser)){
				$form->setError($field, "* Username banned");
			}
		}
		
		//Password error checking
		$field = "pass";	//use field name for password
		if(!$subpass){
			$form->setError(Field, "* Password not entered");
		}
		else{
			//spruce up password and check length
			$subpass = stripslashes($subpass);
			if(strlen($subpass) < 4){
				$form->setError($field, "* Password too short");
			}
			//check if password not alphanumeric
			else if(!eregi("^([0-9a-z])+$", ($subpass = trim($subpass)))){
				$form->setError($field, "* Password not alphanumeric");
			}
			/**
			 * Note: i trimmed the password only after i checked the length
			 * becaise if you fill the password field up with spaces
			 * it looks like a lot more characters than 4, so it looks
			 * kind of stupid to report "password too short"
			 */
		}
		
		//Email error checking
		$field = "email";
		if (!$subemail || strlen($subemail = trim($subemail)) == 0){
			$form->setError($field, "* Email not entered");
		}
		else{
			//check if valid email address
			$regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
					."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
					."\.([a-z]{2,}){1}$";
			if(!eregi($regex,$subemail)){
				$form->setError($field, "* Email invalid");
			}
			$subemail = stripslashes($subemail);
		}
		
		//errors exist, have user correct them
		if($form->num_errors > 0){
			return 1;	//errors with form 
		}
		//no errors, add the new account to the database
		else{
			if($database->addNewUser($subuser, md5($subpass), $subemail)){
				if(EMAIL_WELCOME){
					$mailer->sendWelcome($subuser, $subemail, $subpass);
				}
				return 0;	//new user added successfully
			}
			else{
				return 2;	//Registration attempt faild
			}
		}
	}
	
	/** 
	 * edit Account - attempts to edit the users's accoutn information
	 * including the password, which it first makes sure is correct
	 * if entered, if so and the new password is in the right
	 * format, the change is made. all other fields are changed
	 * automatically
	 */
	function editAccount($subcurpass, $subnewpass, $subemail){
		global $database, $form;	//the database and form object
		//new password entered
		if($subnewpass){
			//CURRENT password error checking)
			$field = "curpass";  //Use field name for current password
			if(!$subcurpass){
				$form->setError($field, "* Current Password not entered");
			}else{
				//check if password too short or is not alphanumerc
				$subcurpass = stripslashes($subcurpass);
				if(strlen($subcurpass) < 4 ||
					!eregi("^([0-9a-z])+$", ($subcurpass = trim($subcurpass)))){
						$form->setError($field, "* Current Password incorrect");
				}
				//password entered is incorrect
				if($database->confirmUserPass($this->username, md5($subcurpass)) != 0){
					$form->setError($field, "* Current password incorrect");
				}
			}
			
			//new password error checking
			$field = "newpass";	//use field name for new password
			//spruce up password and check length
			$subpass = stripslashes($subnewpass);
			if(strlen($subnewpass) < 4){
				$form->setError($field, "* New Password too short");
			}
			//check if password is not alphanumeric
			else if(!eregi("^([0-9a-z])+$", ($subnewpass = trim($subnewpass)))){
				$form->setError($field, "* New Password not alphanumeric");
			}
		}
		//change password attempted
		else if($subcurpass){
			//new password error reporting
			$field = "newpass";	//use field name for new password
			$form->setError($field, "* New Password not entered");
		}
		
		//email error checking
		$field = "email";	//use field name for email
		if($subemail && strlen($subemail = trim($subemail)) > 0){
			//check if valid email address
			$regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
					."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
					."\.([a-z]{2,}){1}$";
			if(!eregi($regex, $subemail)){
				$form->setError($field, "* Email invalid");
			}
			$subemail = stripslashes($subemail);
		}
		
		//errors exist, have user correc them
		if($form->num_errors > 0){
			return false;	//errors with form
		}
		
		//update password since there were no errors
		if($subcurpass && $subnewpass){
			$database->updateUserField($this->username, "password", md5($subnewpass));
		}
		
		//change email
		if($subemail){
			$database->updateUserField($this->username, "email", $subemail);
		}
		
		//user image
		//if($subimg){
			
		
		//success
		return true;
	}
	
	function editPic(){
	if(isset($_FILES['new_image'])){
	
			switch ($_FILES['new_image']['type']){
				case "image/jpeg":
					$ext = "jpg";
					break;
				case "image/png":
					$ext = "png";
					break;
				case "image/gif":
					$ext = "gif";
					break;
				default:
					echo "<p>* Image must be jpeg, png, or gif.</p>";
					exit;
			}
	
			$imagename = md5(rand() * time()).".".$ext;
			$source = $_FILES['new_image']['tmp_name'];
			$target = "images/".$imagename;
			move_uploaded_file($source, $target);
	
			$imagepath = $imagename;
			$save = "images/".$imagepath;	//this is the new file you're saving
			$file = "images/".$imagepath;	//original
	
			list($width, $height) = getimagesize($file);
	
			$modwidth = min(250,$width);
			$diff = $width/$modwidth;
			$modheight = $height/$diff;
			$tn = imagecreatetruecolor($modwidth, $modheight);
	
			switch($_FILES['new_image']['type']){
				case "image/jpeg":
					$image = @imagecreatefromjpeg($file);
					break;
				case "image/png":
					$image = @imagecreatefrompng($file);
					break;
				case "image/gif":
					$image = @imagecreatefromgif($file);
					break;
			}
	
			if(!$image){
				echo "Oops, this picture file seems to be corrupted. Try using a different image.";
				echo $_FILES['new_image']['type'];
				exit;
			}
	
			imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
	
			switch($_FILES['new_image']['type']){
				case "image/jpeg":
					imagejpeg($tn, $save, 100);
					break;
				case "image/png":
					imagepng($tn, $save, 100);
					break;
				case "image/gif":
					imagegif($tn, $save, 100);
					break;
			}
			$sql = "UPDATE ".TBL_USER_IMAGES." SET originalURL = '$save' WHERE user_id=$this->puserid";
			mysql_query($sql);
	
			$save_p = "images/p_".$imagepath;
			$save_t = "images/t_".$imagepath;
			$file = "images/".$imagepath;
	
			switch($_FILES['new_image']['type']){
				case "image/jpeg":
					$image = imagecreatefromjpeg($file);
					break;
				case "image/png":
					$image = imagecreatefrompng($file);
					break;
				case "image/gif":
					$image = imagecreatefromgif($file);
					break;
			}
	
			if(!$image){
				echo "Oops, this picture file seems to be corrupted. Try using a different image.";
				echo $_FILES['new_image']['type'];
				exit;
			}
	
			$scale_p = 48;
			$scale_t = 24;
			list($width, $height) = getimagesize($file);
			$src_w = min($width*0.90, $height*0.90);	//capture a square area
			$src_h = min($width*0.90, $height*0.90);
			$src_x = ($width - $src_w)/2;	//center horizontally
			$src_y = $height * 0.05;
	
			$tn_p = imagecreatetruecolor($scale_p, $scale_p);
			$tn_t = imagecreatetruecolor($scale_t, $scale_t);
			imagecopyresampled($tn_p, $image, 0, 0, $src_x, $src_y, $scale_p, $scale_p, $src_w, $src_h);
			imagecopyresampled($tn_t, $image, 0, 0, $src_x, $src_y, $scale_t, $scale_t, $src_w, $src_h);
	
			switch($_FILES['new_image']['type']){
				case "image/jpeg":
					imagejpeg($tn_p, $save_p, 100);
					imagejpeg($tn_t, $save_t, 100);
					break;
				case "image/png":
					imagepng($tn_p, $save_p, 100);
					imagepng($tn_t, $save_t, 100);
					break;
				case "image/gif":
					imagegif($tn_p, $save_p, 100);
					imagegif($tn_t, $save_t, 100);
					break;
			}
			$sql = "UPDATE ".TBL_USER_IMAGES." SET pURL = '$save_p' WHERE user_id=$this->puserid";
			mysql_query($sql);
			$sql = "UPDATE ".TBL_USER_IMAGES." SET tURL = '$save_t' WHERE user_id=$this->puserid";
			mysql_query($sql);
	
		}
		//}
	}
	
	/**
	 * isAdmin - returns true if currently logged in user is
	 * 	an administrator, flase otherwise.
	 */
	function isAdmin(){
		return ($this->userlevel == ADMIN_LEVEL || $this->username == ADMIN_NAME);
	}
	
	/**
	 * generateRandID - genereates a string made up of randomized
	 * letters (lower and upper case) and digits and returns
	 * the md5 hash of it to be used as a userid
	 */
	function generateRandID(){
		return md5($this->generateRandStr(16));
	}
	
	/**
	 * generateRandStr - generates a string made up of randomized 
	 * letters (lower and upper case) and digits, the length
	 * is a specified parameter
	 */
	function generateRandStr($length){
		$randstr = "";
		for($i=0; $i<$length; $i++){
			$randnum = mt_rand(0,61);
			if($randnum < 10){
				$randstr .= chr($randnum+48);
			}else if($randnum < 36){
				$randstr .= chr($randnum+55);
			}else{
				$randstr .= chr($randnum+61);
			}
		}
		return $randstr;
	}
};

/**
 * Initialize the session object - this must be initialized before
 * the form object because the form user session variables,
 * which cannot be accessed unless the session has started
 */
$session = new Session;

// Initialize form object
$form = new Form;

?>