<?php
if (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on') {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit();
}
if (!$_SESSION["auth"]["use_id"]) {
	$errors[] = "use_id";
}
if (!$_SESSION["auth"]["use_loa"]) {
	$errors[] = "use_loa";
}
if (!$_SESSION["auth"]["use_username"]) {
	$errors[] = "use_username";
}
if (!$_SESSION["auth"]["use_email"]) {
	$errors[] = "use_email";
}
if (!$_SESSION["auth"]["use_fname"]) {
	$errors[] = "use_fname";
}
if (!$_SESSION["auth"]["use_sname"]) {
	$errors[] = "use_sname";
}

if (in_array('guest', $_SESSION['auth']['roles'])) {
	header("Location:" . WS_YII_GUEST_LOGIN_LINK);
	exit;
}

if ($errors) {
	header("Location:" . WS_YII_ADMIN_LOGIN_LINK . "?ref=" . urlencode($_SERVER['REQUEST_URI']));
	exit;
}

// check user has permission to access current page
// takes array of allowed role and compares to SESSION roles
function pageAccess($roles, $allowed = array())
{

	foreach ($allowed as $key => $val) {
		if (in_array($val, $roles)) {
			$access_allowed = 1;
		}
	}

	if ($access_allowed <> 1) {
		$errors[] = "You do not have sufficient permissions to access this page";
		echo error_message($errors);
		exit;
	}
}
