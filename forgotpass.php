<?php
/**
 * Forgotpass.php
 *
 * this page is for those users who have forgotten their
 * password and want to have a new password generated for
 * them and sent to the email address attached to their
 * account in the database, the new password is not
 * displayed on the website for security purposes.
 *
 * Note: server must be properly setup for mail
 */
include("include/session.php");
?>

<html>
<title>Fritter</title>
<body>

<?php
/**
 * forgot password ofrm as been submitted and no errors
 * were found withthe form (the username as in the database)
 */
if(isset($_SESSION['forgotpass'])){
	/**
	 * new password was generated for user and sent to user's
	 * email address.
	 */
	if($_SESSION['forgotpass']){
		echo "<h1>New Password Genereated</h1>";
		echo "<p>Your new password has been generated "
		."and sent to the email <br> associated with your account. "
		."<a href=\"index.php\">Home</a>.</p>";
	}
	/**Email could not be sent, therefore password was not
	 * edited in the database
	 */
	else{
		echo "<h1>New Password Failure</h1>";
		echo "<p>there was an error sending you the "
		."email with the new password, <br> so your password has not been changed. "
		."<a href=\"index.php\">Home</a>.</p>";
	}
	unset($_SESSION['forgotpass']);
}
else{
	/**
	 * forgot password form ids displayed, if error found
	 * it is displayed
	 */
	?>

<h1>Forgot Password</h1>
a new password will be generated for you and sent to the email address
<br>
associated with your account, all you have to do is enter your username.
<br>
<br>
	<?php echo $form->error("user"); ?>
<form action="process.php" method="POST"><b>Username:</b> <input
	type="text" name="user" maxlength="30"
	value="<?php echo $form->value("user"); ?>"> <input type="hidden"
	name="subforgot" value="1"> <input type="submit"
	value="Get New Password"></form>

	<?php
}
?>

</body>
</html>
