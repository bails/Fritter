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
	include("userinfo.php");
	include("footer.php");
?>

</body>
</html>
