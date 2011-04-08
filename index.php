<?php
/**
 * Index.php
 *
 * this is an example of the main page of a website. Here
 * users will be able to login. however, like on most sites
 * the login form doesn't just have to be on the main page,
 * but re-appear on subsequent pages, depending on whether
 * the user has logged in or not.
 */
include_once("include/session.php");
include_once("include/functions.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Hoot!</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="ajax_fn.js" type="text/javascript"></script>
<script type="text/javascript">
   var myReq = getXMLHTTPRequest();
  </script>
</head>

<body>

<?php
/**
 * User has already logged in, so display relevant links, including
 * a ling to the admin centr if the user is an administrator
 */
//echo $session->logged_in;
if($session->logged_in){
	include("header.php");
	include("entry.php");
	include("content.php");
	include("footer.php");
}
else{
	include("login.php");
}

?>

</body>
</html>

