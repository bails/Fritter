<?php
/**
 * adminprocess.php
 *
 * the adminprocess class is meant tp simplify the task of processing
 * admin submitted forms from the admin center, these deal with
 * member system adjustments.
 *
 **/
include("../include/session.php");

class AdminProcess
{
	//class constructor
	function AdminProcess(){
		global $session;
		//make sure administrator is acessing page
		if(!$session->isAdmin()){
			header("Location: ../main.php");
			return;
		}
		//admin submitted update user level form
		if(isset($_POST['subupdlevel'])){
			$this->procUpdateLevel();
		}
		//admin submitted delte user form
		else if(isset($_POST['subdeluser'])){
			$this->procDeleteUser();
		}
		else if(isset($_POST['subdelinact'])){
			$this->procDeleteInactive();
		}
		//admin submitted ban user
		else if(isset($_POST['subbanuser'])){
			$this->procBanUser();
		}
		//admin submitted delete banned user form
		else if(isset($_POST['subdelbanned'])){
			$this->procDeleteBannedUser();
		}
		//should not get here, redirec tto home page
		else{
			header("Location: ../main.php");
		}
	}

	/**
	 * procUpdateLevel - if the submitted username is correct,
	 * their user level is updated according to the admin's
	 * request
	 */
	function procUpdateLevel(){
		global $session, $database, $form;
		//username error checking
		$subuser = $this->checkUsername("upduser");

		//errors exist, have user correct them
		if($form->num_errors > 0){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		//update user level
		else{
			$database->updateUserField($subuser, "userlevel", (int)$_POST['updlevel']);
			header("Location: ".$session->referrer);
		}
	}

	/**
	 * procDeleteUser - if the submitted username is correct,
	 * the user is deleted from teh database
	 */
	function procDeleteUser(){
		global $session, $database, $form;
		//username error checking
		$subuser = $this->checkUsername("deluser");

		//errors exist, have user correct them
		if($form->num_errors > 0){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		//delete user from database
		else{
			$q = "DELETE FROM ".TBL_USERS." WHERE username = '$subuser'";
			$database->query($q);
			header("Location: ".$session->referrer);
		}
	}

	/**
	 * procDeleteInactive - All inactive users are deleted from
	 * the database, not including administrator. Inactivity
	 * is defined by the number of data specified that have
	 * fone by that the user has not logged in.
	 */
	function procDeleteInactive(){
		global $session, $database;
		$inact_time = $session->time - $_POST['inactdays']*24*60*60;
		$q = "DELETE FROM ".TBL_USERS." WHERE timestamp < $inact_time "
		."AND userlevel != ".ADMIN_LEVEL;
		$database->query($q);
		header("Location: ".$session->referrer);
	}

	/**
	 * procBanUser - if the submitted username is correct,
	 * the user is banned from the member system, which entails
	 * removign the usrname from teh users table and adding
	 * it to the banned users table
	 */
	function procBanUser(){
		global $session, $database, $form;
		//username error checking
		$subuser = $this->checkUsername("banuser");

		//errors exist, have user correcnt them
		if($form->num_errors > 0){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		//ban user from member system
		else{
			$q = "DELETE FROM ".TBL_USERS." WHERE username = '$subuser'";
			$database->query($q);
				
			$q = "INSERT INTO ".TBL_BANNED_USERS." VALUES ('$subuser', $session->time)";
			$database->query($q);
			header("Location: ".$session->referrer);
		}
	}

	/**
	 * procDeleteBannedUser - if the submitted username is correct,
	 * the user is deleted from the baned users table, which
	 * enables someone to register with that username again
	 */
	function procDeleteBannedUser(){

		global $session, $database, $form;
		//username error checking
		$subuser = $this->checkUsername("delbanuser", true);

		//errors exist, have user correcnt them
		if($form->num_errors > 0){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		//delete user from database
		else{
			$q = "DELETE FROM ".TBL_BANNED_USERS." WHERE username = '$subuser'";
			$database->query($q);
			header("Location: " .$session->referrer);
		}
	}

	/**
	 * checkUsername - helper function for the above processing,
	 * it makes sure the submitted username is valid, if not,
	 * it adds the appropriate error to the form.
	 */
	function checkUsername($uname, $ban=false){
		global $database, $form;
		//username error checking
		$subuser = $_POST[$uname];
		$field  = $uname;	//user field name for username
		if(!$subuser || strlen($subuser = trim($subuser)) == 0){
			$form->setError($field, "* Username not entered<br>");
		}
		else{
			//make sure username is in database
			$subuser = stripslashes($subuser);
			if(strlen($subuser)<5 || strlen($subuser) > 30 ||
			!eregi("^([0-9a-z])+$", $subuser) ||
			(!$ban && !$database->usernameTaken($subuser))){
				$form->setError($field, "* Username does not exist<br>");
			}
		}
		return $subuser;
	}
};

//initialize process
$adminprocess = new AdminProcess;
?>