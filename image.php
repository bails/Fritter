<?php
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)
include_once("include/session.php");
// Ensures that include file contents are included once and not again in subsequent include statements (in this or other php scripts)
include_once("include/functions.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Hoot!</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
	include("header.php");
	include("entry.php");

/**
 * user has submitted form without errors and user's
 * account has been edited successfully.
 */
if(isset($_SESSION['picedit'])){
	unset($_SESSION['picedit']);

	echo "<h1>Profile Picture Edit Success!</h1>";
	echo "<p><b>$session->username</b>, your profile picture has been successfully updated. "
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
<table>
<tr>
		<td><a href='edit.php'>Edit Account</a></td>
	</tr>
</table>
<h1>Picture</h1>
		<?php
		if($form->num_errors > 0){
			echo "<td><font size=\"2\" color=\"##ff0000\">".$form->num_errors." error(s) found<font</td>";
		}
		?>
<form action="process.php" method="POST" enctype="multipart/form-data">
<table align="left" border="0" cellspacing="0" cellpadding="3">

	<tr>
		<td>Avatar:</td>
		<td><img src='<?php echo $session->userinfo['pURL']; ?>'></td>
	</tr>
	<tr>
		<td></td>
		<td><input name="new_image" id="new_image" size="10" type="file" class="fileUpload" /></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="hidden" name="subeditpic" value="1"> <input type="submit" value="Save"></td>
	</tr>
</table>
</form>

<?php
}
}
	include("footer.php");
?>
</body>
</html>
