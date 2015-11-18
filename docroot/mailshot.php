<?php
// mailshot
require_once(dirname(__FILE__) . "/../config/config.public.inc.php");
/*
$a = '' - count hit and forward to property
$a = prefs - ask for email address, log in to edit reqs
$a = unsub

$id is the mailshot id
$c is the client id

*/
if (!$_GET["id"]) {
	header("Location:/");
	exit;
} else {
	$mai_id = $_GET["id"];
}
if (!$_GET["c"]) {
	header("Location:/");
	exit;
} else {
	$cli_id = $_GET["c"];
}


// get the mailshot record, and property id
$sql = "SELECT mailshot.*, deal.dea_id,deal.dea_type FROM mailshot
LEFT JOIN deal ON mailshot.mai_deal = deal.dea_id
WHERE mai_id = $mai_id";
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage() . $sql);
}
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		$dea_id   = $row["dea_id"];
		$dea_type = $row["dea_type"];
	}
}

// no action defined, count hit and forward to details
if (!$_GET["a"]) {

	// update hits
	$sql = "INSERT INTO hit
	(hit_mailshot,hit_deal,hit_client,hit_action)
	VALUES
	('$mai_id','$dea_id','$cli_id','View')";
	$q   = $db->query($sql);

	header("Location:/details/$dea_id.html");
	exit;

} elseif ($_GET["a"] == 'unsub') {

	if (!$_GET["e"]) {
		header("Location:/");
		exit;
	} else {
		$cli_email = trim($_GET["e"]);
	}

	// update hits
	$sql = "INSERT INTO hit
	(hit_mailshot,hit_deal,hit_client,hit_action)
	VALUES
	('$mai_id','$dea_id','$cli_id','Unsub')";
	$q   = $db->query($sql);

	if ($dea_type == 'Sales') {
		$sql = "UPDATE client SET cli_sales = 'No', cli_saleemail = 'No' WHERE cli_id = $cli_id AND cli_email = '$cli_email'";
	} elseif ($dea_type == 'Lettings') {
		$sql = "UPDATE client SET cli_lettings = 'No', cli_letemail = 'No' WHERE cli_id = $cli_id AND cli_email = '$cli_email'";
	}
	$q      = $db->query($sql);
	$render = "<h2>Thank you</h2><p>You have been unsubscribed from any future $dea_type mailshots from <a href=/>Wooster &amp; Stock</a></p>";

} elseif ($_GET["a"] == 'prefs') {

	// prefs disabled
	header("Location:/");
	exit;

	if (!$_GET["e"]) {
		header("Location:/");
		exit;
	} else {
		$cli_email = trim($_GET["e"]);
	}

	// update hits
	$sql = "INSERT INTO hit
	(hit_mailshot,hit_deal,hit_client,hit_action)
	VALUES
	('$mai_id','$dea_id','$cli_id','Prefs')";
	$q   = $db->query($sql);

	$sql = "SELECT cli_email FROM client WHERE cli_id = $cli_id AND cli_email = '$cli_email'";
	$q   = $db->query($sql);
	//if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {
			$cli_email = $row["cli_email"];
		}

		$_SESSION["Client_ID"] = $cli_id;
		$_SESSION["Email"]     = $cli_email;

		header("Location:Edit.php");

	} else {

		$render = '<p>Sorry, something went wrong</p>
		<p><a href="mailto:webmaster@woosterstock.co.uk">Please contact support for assistance</a>';

	}
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Wooster &amp; Stock</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="content-language" content="en">
	<meta name="robots" content="noindex,nofollow">
	<link rel="stylesheet" href="ws.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#0000CC" vlink="#0000CC" alink="#CC0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="140" border="0" cellspacing="0" cellpadding="2" align="left">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td height="150">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="leftLinks">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="625" border="0" cellspacing="0" cellpadding="2" align="left">
	<tr>
		<td width="100%" colspan="2" valign="top"><?php echo $render; ?></td>
	</tr>
</table>


</body>
</html>