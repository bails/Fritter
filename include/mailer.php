<?php
/**
 * mailer.php
 *
 * The mailer class is meant to simplify the task of sending
 * emails to users. Note: this email system will not work
 * if your server is not setup to send mail.
 *
 *
 */

class Mailer
{
	/** sendWelcome - sends a welcome message to the newly
	 * registered user, also supplying the username and password
	 */
	function sendWelcome($user, $email, $pass){
		$from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
		$subject = "Fritter - Welcome!";
		$body = $user.",\n\n"
		."Welcome! You've just registered at Fritter "
		."with the following information: \n\n"
		."Username: ".$user."\n"
		."Password: ".$pass."\n\n"
		."If you ever lose or forget your password, a new "
		."password will be generated for you and sent to this "
		."email address, if you would like to change your "
		."email address you can do so by going to the "
		."My Account page agter signing in. \n\n"
		." - Fritter";

		return mail($email, $subject, $body, $from);
	}

	/**
	 * sendNewPass - sends the newly generated password
	 * to the user's email address that was specified at
	 * sign-up
	 */
	function sendNewPass($user, $email, $pass){
		$from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
		$subject = "Fritter - Your new password";
		$body = $user."\n\n"
		."We've generated a new password for you at your "
		."request, you can use this new password with your "
		."username to log in to Fritter.\n\n"
		."New Password: ".$pass."\n\n"
		."It is recommended that you change your password "
		."to something that is easier to remember, which "
		."can be done by going to the My Account page "
		."after signing in. \n\n"
		."- Fritter";

		return mail($email, $subject, $body, $from);

	}
};

//Initialize mailer object
$mailer = new Mailer;


?>