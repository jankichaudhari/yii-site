<?php
require_once dirname(__FILE__) . "/../../../../config/config.admin.inc.php";
// start session
session_start();

/*
Username max length is 50
Password max length is 50
Salt max length is 50

ini_set('session.gc_maxlifetime',30);
ini_set('session.gc_probability',1);
ini_set('session.gc_divisor',1);
Super secure login page
Force https
Clean all input
Count number of retries and lock account for ($login_timeout) seconds
Check referer
Log all attepts, failed and successful, in database (log table)
Use salt from user table to hash the password



*/


// a better way of doing the lock, will be to flag the user's database row as locked with a timestamp,
// then compare current time to the timestamp and login_timeout value, unlocking and resetting the
// timestamp if the timeout is reached. this will prevent different sessions from attempting to log in
// with same user credentials.
// but if the login fails, we do not know the user's id, username or account credentials (or at least
// we cant be sure they are correct) so we have no way of knowing which acount to lock


// Limit to IE only until firefox stylesheets have been written
//require_once("../admin/inx/browser_detection.inc.php");
//if (browser_detection('browser') <> 'ie') {
//	echo "Internet Explorer Only";
//	exit;
//	}

// force https connection (this ensures http: sessions are never created)
if (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on') {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit();
}

// turn globals off
ini_set("register_globals", 0);
// assume user is not authenticated
$auth = false;
// url of admin area to redirect to
$admin_url = 'https://' . $_SERVER['HTTP_HOST'] . '/v3.0/live/admin/';
// url of login page
$login_url = 'https://' . $_SERVER['HTTP_HOST'] . '/v3.0/live/login/';
// maximum number of retries before lock-out
$max_login_attempts = 5;
// time delay for lock out (seconds)
$login_timeout = (20);
// message to show user when login fails
$message_fail = 'Your login failed';


session_register("login_attempts");
session_register("login_time");
session_register("login_delay");


// lock session if max_login_attepmts is reached
/**
 * logic incorrect
 * $login attempts are not defined
 * block is useless
 * @vit check this block. probabply remove
 */
if (isset($login_attempts) && ($login_attempts - $max_login_attempts) == 0) {
	// if times are not initiated
	if (!$_SESSION["login_time"]) {
		$_SESSION["login_time"]  = time();
		$_SESSION["login_delay"] = time() + $login_timeout;
	}
	// if login delay has expired, reset login attempts, else send to locked page
	if (time() > $_SESSION["login_delay"]) {
		$_SESSION["login_attempts"] = 0;
		unset($_SESSION["login_attempts"], $_SESSION["login_time"], $_SESSION["login_delay"]);
	} else {
		header("Location:locked.php");
		exit;
	}
}


if (isset($_POST["username"]) && isset($_POST["password"]) && $_POST["action"] == "login") {

	// Host names from where the form is authorized to be posted from:
	//	$authHosts = array("woosterstock.co.uk", "new.wooster-1.titaninternet.co.uk", "wsvitaly.acp.local");
	//@lookup
	$authHosts = WS::getAuthHosts();
	// if no authhosts defined - we just ignore this. and allow to login from any host.
	// this logic is not logic, don't know why it is needed here.
	// @vitaly
	if ($authHosts) {
		// Where have we been posted from?
		$fromArray = parse_url(strtolower($_SERVER['HTTP_REFERER']));

		// Test to see if the $fromArray used www to get here.
		$wwwUsed = strpos($fromArray['host'], "www.");

		// Make sure the form was posted from an approved host name.
		if (!in_array(($wwwUsed === false ? $fromArray['host'] : substr(stristr($fromArray['host'], '.'), 1)), $authHosts)) {
			echo '
			<p>Invalid Referer</p>';
			exit;
		}
	}


	// db connection only
	require_once("../admin/inx/db.inc.php");

	// formatting functions, required to clean input and make random strings
	require_once("../admin/inx/format.inc.php");


	// username is always lower case, max 50 chars
	$username = substr(clean_input(strtolower($_POST["username"])), 0, 50);
	// password is case sensitive, max 50 chars
	$password = substr(clean_input($_POST["password"]), 0, 50);

	if (!$username || !$password) {
		// insert row into login table
		$db_data["log_ip"]           = $_SERVER['REMOTE_ADDR'];
		$db_data["log_session"]      = $PHPSESSID;
		$db_data["log_agent"]        = $_SERVER['HTTP_USER_AGENT'];
		$db_data["log_result"]       = 'Fail';
		$db_data["log_use_username"] = $username;
		$db_data["log_errmsg"]       = 'missing username or password';
		db_query($db_data, "INSERT", "login", "log_id");

		$login_attempts++;
		header("Location:$login_url");
		exit;
	}


	// get the user's salt, or make one if one does not exist
	// if no salt exists, the user will not be able to log on, so not much point in creating a new salt
	$sql_salt = "SELECT use_salt FROM user WHERE use_username = '$username' LIMIT 1";
	$q_salt   = $db->query($sql_salt);
	if (DB::isError($q_salt)) {
		die("error: " . $q->getMessage());
	}

	if ($q_salt->numRows() == 0) { // username not found
		// insert row into login table
		$db_data["log_ip"]           = $_SERVER['REMOTE_ADDR'];
		$db_data["log_session"]      = $PHPSESSID;
		$db_data["log_agent"]        = $_SERVER['HTTP_USER_AGENT'];
		$db_data["log_result"]       = 'Fail';
		$db_data["log_use_username"] = $username;
		$db_data["log_errmsg"]       = 'username not found';
		db_query($db_data, "INSERT", "login", "log_id");

		$login_attempts++;
		header("Location:$login_url");
		exit;
	} else {
		while ($row_salt = $q_salt->fetchRow()) {
			if ($row_salt["use_salt"] == "") { // user has no salt
				// insert row into login table
				$db_data["log_ip"]           = $_SERVER['REMOTE_ADDR'];
				$db_data["log_session"]      = $PHPSESSID;
				$db_data["log_agent"]        = $_SERVER['HTTP_USER_AGENT'];
				$db_data["log_result"]       = 'Fail';
				$db_data["log_use_username"] = $username;
				$db_data["log_errmsg"]       = 'user has no salt';
				db_query($db_data, "INSERT", "login", "log_id");

				$login_attempts++;
				header("Location:$login_url");
				exit;
			} else {
				$salt = $row_salt["use_salt"];
			}
		}
	}

	unset($sql_salt, $row_salt, $q_salt);

	// user is found, double check password and salt before continuing
	if (!$password || !$salt) {
		// insert row into login table
		$db_data["log_ip"]           = $_SERVER['REMOTE_ADDR'];
		$db_data["log_session"]      = $PHPSESSID;
		$db_data["log_agent"]        = $_SERVER['HTTP_USER_AGENT'];
		$db_data["log_result"]       = 'Fail';
		$db_data["log_use_username"] = $username;
		$db_data["log_errmsg"]       = 'final check, either password or salt missing';
		db_query($db_data, "INSERT", "login", "log_id");

		$login_attempts++;
		header("Location:$login_url");
		exit;
	}


	// hash the password using salt
	$password = encrypt_password($password, $salt);

	// authorise and get user roles
	$sql = "SELECT
	use_id,use_loa,use_username,use_password,use_email,use_fname,use_sname,use_branch,use_scope,role.rol_id,rol_title
	FROM user
	LEFT JOIN link_user_to_role ON user.use_id = link_user_to_role.u2r_use
	LEFT JOIN role ON link_user_to_role.u2r_rol = role.rol_id
	WHERE
	use_username = '$username' AND
	use_password = '$password' AND
	use_status = 'Active'
	";

	$q = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();

	if ($numRows != 0) {
		$auth = true;
		while ($row = $q->fetchRow()) {
			$use_id                = $row["use_id"];
			$use_loa               = $row["use_loa"];
			$use_username          = $row["use_username"];
			$use_email             = $row["use_email"];
			$use_fname             = $row["use_fname"];
			$use_sname             = $row["use_sname"];
			$use_branch            = $row["use_branch"];
			$roles[$row["rol_id"]] = $row["rol_title"];
			$use_scope             = $row["use_scope"];
		}
	} else {

		// insert row into login table
		$db_data["log_ip"]           = $_SERVER['REMOTE_ADDR'];
		$db_data["log_session"]      = $PHPSESSID;
		$db_data["log_agent"]        = $_SERVER['HTTP_USER_AGENT'];
		$db_data["log_result"]       = 'Fail';
		$db_data["log_use_username"] = $username;
		$db_data["log_errmsg"]       = 'password did not match';
		db_query($db_data, "INSERT", "login", "log_id");

		$login_attempts++;
		header("Location:$login_url");
		exit;
	}
}


// if user is not authorised show the login form
if (!$auth) {

	if ($_SESSION["login_attempts"] > 0) {
		$warning = '<div id="inset">' . $message_fail . "<br>Login attempt " . $_SESSION["login_attempts"] . " of " . $max_login_attempts . '</div>';
	}

	echo '<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3c.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>Login</title>
<link href="' . $admin_url . 'css/styles.css" rel="stylesheet" type="text/css" />
<link rel="SHORTCUT ICON" href="' . $admin_url . 'favicon.ico" />
</head>
<body onLoad=document.forms[0].username.focus();>
<script type="text/javascript" language="javascript">
if (parent.frames.length > 0) { parent.location.href = self.document.location }
</script>
<div style="width:700px">
<form id="" method="POST" action="' . $_SERVER["PHP_SELF"] . '" enctype="application/x-www-form-urlencoded">
<div id="standard_form">
<fieldset>
<div class="block-header">Login</div>
<div>
<label for="username" class="formLabel" id="label">Username</label>
<input type="text" name="username" maxlength="50" style="width:250px" />
</div>
<div>
<label for="password" class="formLabel" id="label">Password</label>
<input type="password" name="password" maxlength="50" style="width:250px" />
</div>
<div>
<input type="submit" name="" id="" value="Login" class="submit" />
<input type="hidden" name="action" value="login" />
<input type="hidden" name="ref" value="' . (isset($_GET["ref"]) ? $_GET["ref"] : "") . '" />
</div>
' . (isset($warning) ? $warning : "") . '
</fieldset>
</div>

</form>
</div>
</body>
</html>
';


	exit;

} else {

	/*
	if ($use_id <> 1) {
		echo "closed for maintenance";
		exit;
		}
	*/

	$_SESSION["auth"]                  = array();
	$_SESSION["auth"]["use_id"]        = $use_id;
	$_SESSION["auth"]["use_loa"]       = $use_loa;
	$_SESSION["auth"]["use_username"]  = $use_username;
	$_SESSION["auth"]["use_email"]     = $use_email;
	$_SESSION["auth"]["use_fname"]     = $use_fname;
	$_SESSION["auth"]["use_sname"]     = $use_sname;
	$_SESSION["auth"]["use_branch"]    = $use_branch;
	$_SESSION["auth"]["session_id"]    = $PHPSESSID;
	$_SESSION["auth"]["roles"]         = $roles;
	$_SESSION["auth"]["default_scope"] = $use_scope;

	// values used by old admin for compatibilty
	$_SESSION["s_userid"] = $use_id;
	$_SESSION["s_user"]   = $use_username;
	$_SESSION["s_name"]   = $use_fname . ' ' . $use_sname;
	// end


	session_unregister("login_attempts");
	session_unregister("login_time");
	session_unregister("login_delay");

	session_write_close(); // save session

	// insert row into login table
	$db_data["log_ip"]           = $_SERVER['REMOTE_ADDR'];
	$db_data["log_session"]      = $PHPSESSID;
	$db_data["log_agent"]        = $_SERVER['HTTP_USER_AGENT'];
	$db_data["log_result"]       = 'Success';
	$db_data["log_use_id"]       = $_SESSION["auth"]["use_id"];
	$db_data["log_use_username"] = $_SESSION["auth"]["use_username"];
	db_query($db_data, "INSERT", "login", "log_id");

	if ($_POST["ref"]) {
		// dont go to old admin screen
		if (!strstr($_POST["ref"], 'live')) {

			header("Location:" . $admin_url . "index.php");
			exit;
		}
		// calendar fix
		$_POST["ref"] = str_replace("calendar_day.php", "calendar.php", $_POST["ref"]);
		header("Location:" . $_POST["ref"]);
	} else {
		header("Location:" . $admin_url . "index.php");
	}
}


?>
