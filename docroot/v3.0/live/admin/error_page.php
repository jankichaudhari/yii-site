<?php
require_once("inx/global.inc.php");

if ($_GET["error"] == '404') {
	$errors[] = "Page not found (404)";
	}
elseif ($_GET["error"] == '500') {
	$errors[] = "Internal Server Error (500)";
	}
else {
	$errors[] = "Unknown Error";
	}

echo error_message($errors);
exit;
?>