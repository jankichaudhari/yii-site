<?php
header("Location:../v3.0/live/admin");
exit;
session_start();

/*

if (!$PHPSESSID) {

	echo "The following error(s) have occoured:\nNo Session ID - you may need to enable cookies in your web browser";

	exit;

	}

	*/

require_once("global.php");



// login

// check credentials against admin table

// store login details in login table



if ($_POST["username"]) { // only work if username field is populated

	if (!$_POST["username"]) {

		echo '<p>Invalid username</p>';

		exit;

		}

	if (!$_POST["password"]) {

		echo '<p>Invalid password</p>';

		exit;

		}

	$user = trim($_POST["username"]);

	$pass = trim($_POST["password"]);

	

	$sql = "SELECT * FROM admin WHERE admin.adm_user = '$user' AND admin.adm_pass = '$pass' AND admin.adm_status = 'Active' LIMIT 1";

	//echo $sql;

	$q = $db->query($sql);

	$numrows = $q->numRows();

	if (DB::isError($q)) {  die("error: ".$q->getMessage()); }

	if ($numrows == 0) {

		echo '<p>login failed, please try again';

		exit;

		}

	while ($row = $q->fetchRow()) {

		$_SESSION["s_userid"] = $row["adm_id"];

		$_SESSION["s_user"] = $row["adm_user"];		

		$_SESSION["s_name"] = $row["adm_name"];

		$_SESSION["s_loa"] = $row["adm_loa"];

		$_SESSION["s_auth"] = $auth_secret;	
		
		$_SESSION["s_email"] = $row["adm_email"];

		}

	if (!$_POST["ref"]) { $ref = "/admin/index.php"; } else  { $ref = urldecode($_GET["ref"]); }

	

	$userid = $_SESSION["s_userid"];

	$session = $PHPSESSID;

	$ip = $_SERVER["REMOTE_ADDR"];

	

	$sql_login = "INSERT INTO login	

	(log_userid,log_session,log_ip,log_ref)

	VALUES

	('$userid','$session','$ip','$ref')

	";	

	//echo $sql_login;

	$q_login = $db->query($sql_login);

	if (DB::isError($q_login)) {  die("login error: ".$q_login->getMessage()); }	

	$query_login = 'SELECT LAST_INSERT_ID()'; 

	$result_login = mysql_query($query_login); 

	$rec_login = mysql_fetch_array($result_login); 

	$insert_id = $rec_login[0]; 	

	$_SESSION["s_id"] = $insert_id;

	//print_r($_SESSION);

	header("Location:$ref");

	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"

"http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<title>woosterstock :: login</title>

<style type="text/css">

<!--

td.greyForm { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; background-color: #E9E9E9}

th.greyForm { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; color: #FFFFFF; background-color: #666666}

-->

</style>

</head>



<body OnLoad="document.login.username.focus();">

<div align="center">

<img src="../images/logo300x115.gif" width="300" height="115" vspace="10">

<form name="login" method="post">

<table border="0" cellspacing="4" cellpadding="3">

      <tr> 

        <th class="greyForm" colspan="2">Administration Area</th>

      </tr>

      <tr> 

        <td align="right" class="greyForm">Name</td>

        <td class="greyForm"> 

          <input name="username" type="text" id="username">

        </td>

      </tr>

      <tr> 

        <td align="right" class="greyForm">Password</td>

        <td class="greyForm"> 

          <input name="password" type="password" id="password">

        </td>

      </tr>

      <tr> 

        <td colspan="2" align="center"> 

          <input type="submit" value="Log In" name="Submit">

        </td>

      </tr>

  </table>	

<input type="hidden" name="ref" value="<?php echo $_GET["ref"]; ?>">

<input type="hidden" name="action" value="login">

</form>

</div>

</body>

</html>



