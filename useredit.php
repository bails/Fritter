<?php
/**
 * user has submitted form without errors and user's
 * account has been edited successfully.
 */
if(isset($_SESSION['useredit'])){
	unset($_SESSION['useredit']);

	echo "<h1>User Account Edit Success!</h1>";
	echo "<p><b>$session->username</b>, your account has been successfully updated. "
	."<a href=\"index.php\">Home</a>.</p>";
}
else{
	?>

	<?php
	/**
	 * if user is not logged in, the do not display anything.
	 * if user is logged in, then display the form to edit
	 * account informaiton, with the current email address
	 * already in the field.
	 */
	if($session->logged_in){
		?>

<h1>Account</h1>
		<?php
		if($form->num_errors > 0){
			echo "<td><font size=\"2\" color=\"##ff0000\">".$form->num_errors." error(s) found<font</td>";
		}
		?>
<form action="process.php" method="POST" enctype="multipart/form-data">
<table align="left" border="0" cellspacing="0" cellpadding="3">
	<tr>
		<td>Username:</td>
		<td><input type="text" name="username" maxlength="30"
			value="
				<?
				if($form->value("username") == ""){
				   echo $session->userinfo['username'];
				}else{
				   echo $form->value("username");
				}
				?>
		"></td>
		<td><? echo $form->error("username"); ?></td>
	</tr>
	<tr>
		<td>Current Password:</td>
		<td><input type="password" name="curpass" maxlength="30" value="<?php echo $form->value("curpass"); ?>"></td>
		<td><?php echo $form->error("curpass"); ?></td>
	</tr>
	<tr>
		<td>New Password:</td>
		<td><input type="password" name="newpass" maxlength="30" value="<? echo $form->value("newpass"); ?>"></td>
		<td><? echo $form->error("newpass"); ?></td>
	</tr>
	<tr>
		<td>Email:</td>
		<td><input type="text" name="email" maxlength="50"
			value="
				<?
				if($form->value("email") == ""){
				   echo $session->userinfo['email'];
				}else{
				   echo $form->value("email");
				}
				?>">
		</td>
		<td><? echo $form->error("email"); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="hidden" name="subedit" value="1"> <input type="submit" value="Save"></td>
	</tr>
	<tr>
		<td colspan="2" align="left"></td>
	</tr>
</table>
</form>
<h1>Picture</h1>
<table>
<tr>
		<td><a href='image.php'>Change Avatar </a></td>
	</tr>
</table>

<?php
}
}

?>
