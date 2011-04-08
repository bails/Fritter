<?php
	//echo "<div id='top' class='status'><span class='status-img'><a href='image.php'><img src='$session->useravatar' /></a></span>";
	echo "<div id='top'>";
	echo "<span class='status-entry'><form class='message' action='add.php' method='POST'>";
	echo "<a href='image.php'><img src='$session->useravatar' /></a><textarea name='body' rows='2' cols='10' wrap='VIRTUAL'></textarea>";
	echo "<p><input class='submit' type='submit' value='submit' />";
	
	// If a message is returned for the user, format and display it
	if (isset($_SESSION['message'])){
		echo $_SESSION['message'];
		unset($_SESSION['message']);
	}
	echo "</p></form></span>";
	echo "<span id='msg'>";
	unset($_SESSION['page']);
	echo "</span>";
	echo "</div>";
?>