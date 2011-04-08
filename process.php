<?php
/**
 * Process.php
 * 
 * The process class is maent to simplify the task of processing
 * user submitted forms, redirecting the user to the correct 
 * pages if errors are found, or if form is successful, either 
 * way. Also handles the logout procedure.
 * 
 */
include("include/session.php");

class Process{
	//class constructor
	
	function Process(){
		global $session;
		//user submitted login form
		if(isset($_POST['sublogin'])){
			$this->procLogin();
		}
		//user submitted registration form
		else if(isset($_POST['subjoin'])){
			$this->procRegister();
		}
		//user submitted forgot password form
		else if(isset($_POST['subforgot'])){
			$this->procForgotPass();
		}
		//user submitted edit account form
		else if(isset($_POST['subedit'])){
			$this->procEditAccount();
		}
		else if(isset($_POST['subeditpic'])){
			$this->procEditPic();
		}
		/**
		 * The only other reason user should be directed here
		 * is if he wants to logout, which means user is
		 * logged in currently.
		 */
		else if($session->logged_in){
			$this->procLogout();
		}
		/**
		 * should not get here, which means user is viewing thsi page
		 * by mistake and therefore is redicrected
		 */
		else{
			header("Location: index.php");
		}
	}
	
	/**
	 * procLogin - Processes the user submitted lgin form, if errors
	 * are found, the user is redirected to correc the information,
	 * if not, the user is effectively logged in to the system.
	 */
	function procLogin(){
		global $session, $form;
		//login attempt
		$retval = $session->login($_POST['user'], $_POST['pass'], isset($_POST['remember']));
		
		//login successful
		if($retval){
			header("Location: ".$session->referrer);
		}
		//login failed
		else{
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
	}
	
	/**
	 * procLogout - Simply attempts to log the user out of the system
	 * given that there is no logout form to process
	 */
	function procLogout(){
		global $session;
		$retval = $session->logout();
		header("Location: index.php");
	}
	/**
	 * procRegister - processes the user submittred registration form, 
	 * if errors are found, the user is redirected to correct the
	 * information, if not, the user is effectively registered with
	 * the system and an email is (optionall) sent to the newly
	 * created user.
	 */
	function procRegister(){
		global $session, $form;
		//convert username to all lowercase (by option)
		if(ALL_LOWERCASE){
			$_POST['user'] = strtolower($_POST['user']);
		}
		//registration attempt
		$retval = $session->register($_POST['user'], $_POST['pass'], $_POST['email']);
		
		//registration successful
		if($retval==0){
			$_SESSION['reguname'] = $_POST['user'];
			$_SESSION['regsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		//error found with form
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			
		}
		
	}
	
	/**
	 * procForgotPass - validates the given username then if
	 * everythign is fine, a new password is generated and
	 * emailed to teh addresss the user gave on sign-up
	 */
	function procForgotPass(){
		global $database, $session, $mailer, $form;
		//username erroerchecking
		$subuser = $_POST['user'];
		$field = "user";	//user field name for username
		if(!$subuser || strlen($subuser = trim($subuser)) ==0){
			$form->setError($field, "* Username does not exist<br>");
		}
		else{
			//make sure username is in database
			$subuser = stripslashes($subuser);
			if(strlen($subuser) < 5 || strlen($subuser) > 30 || !eregi("^([0-9a-z])+$", $subuser)
				|| (!$database->usernameTaken($subuser))){
					$form->setError($field, "* Username does not exist >br>");
				}
		}
		
		//errors exist, have user correct them
		if($form->num_errors > 0){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
		}
		//generate new password and email it to user
		else{
			//generate new password
			$newpass = $session->generateRandStr(8);
			
			//get email of user
			$usrinf = $database->getUserInfo($subuser);
			$email = $usrinf['email'];
			
			//attempt to send the email with new password
			if($mailer->sendNewPass($subuser,$email,$newpass)){
				//email sent, update database
				$database->updateUserField($subuser, "password", md5($newpass));
				$_SESSION['forgotpass'] = true;
			}
			//email failure
			else{
				$_SESSION['forgotpass'] = false;
			}
		}
		header("Location: ".$session->referrer);
	}
	
	/**
	 * procEditAccount - attempts to edit the users's account
	 * information, including the password, which must be verified
	 * before a change is made.
	 */
	function procEditAccount(){
		global $session, $form;
		//account edit attempt
		$retval = $session->editAccount($_POST['curpass'], $_POST['newpass'], $_POST['email'], $_POST['subimg']);
		
		//account edit successful
		if($retval){
			$_SESSION['useredit'] = true;
			header("Location: ".$session->referrer);
		}
		//error found with form
		else{
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
	}
	
	function procEditPic(){
		global $session, $form;
		//account edit attempt
		$retval = $session->editPic();
		
		//account edit successful
		if($retval){
			$_SESSION['picedit'] = true;
			header("Location: ".$session->referrer);
		}
		//error found with form
		else{
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
	}
};

//initialize process
$process = new Process;
?>