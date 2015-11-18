<?php
require_once dirname(__FILE__) . "/../../../config/config_feed.inc.php";
error_reporting(0);
$link = "http://www." . str_replace("www.", "", WS_HOSTNAME) . "/mailshot.php";

if (!is_int(intval($_SERVER['argv'][1])) || intval($_SERVER['argv'][1]) == 0) {
	exit;
} else {
	$mai_id = $_SERVER['argv'][1];
}

$additionalText = $_SERVER['argv'][2];
$includeLink    = (bool)$_SERVER['argv'][3];
$attachFile     = $_SERVER['argv'][4];
$debugEmail     = '';
if (isset($_SERVER['argv'][5]) && $_SERVER['argv'][5]) {
	$debugEmail = $_SERVER['argv'][5];
}

$db_data["mai_status"] = 'Sending';
db_query($db_data, "UPDATE", "mailshot", "mai_id", $mai_id);
// get property details
$sql = "SELECT
mai_id,mai_type,mai_user,mai_deal,use_email,
dea_marketprice,dea_type,dea_strapline,dea_bedroom,dea_ptype,dea_psubtype,
pro_area,CONCAT(pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode,4)) AS pro_addr,
bra_title,bra_email,bra_tel
FROM mailshot
LEFT JOIN deal ON mailshot.mai_deal = deal.dea_id
LEFT JOIN property ON dea_prop = property.pro_id
LEFT JOIN branch ON dea_branch = branch.bra_id
LEFT JOIN area ON pro_area = area.are_id
LEFT JOIN user ON mailshot.mai_user = user.use_id
WHERE mai_id = " . $mai_id;
$q   = $db->query($sql);

if (DB::isError($q)) {
	die("db error 1: " . $q->getMessage());
}

$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$mai_type  = $row["mai_type"];
	$mai_deal  = $row["mai_deal"];
	$use_email = $row["use_email"];

	$dea_type = $row["dea_type"];
	if ($row["dea_type"] == 'Sales') {
		$price = format_price($row["dea_marketprice"]);
	} elseif ($row["dea_type"] == 'Lettings') {
		$price = format_price($row["dea_marketprice"]) . " per week / " . format_price(pw2pcm($row["dea_marketprice"])) . " per month";
	}
	$price = str_replace("&pound;", "£", $price);

	$strap     = $row["dea_strapline"];
	$pro_addr  = trim(str_replace("  ", " ", $row["pro_addr"]));
	$bra_title = $row["bra_title"];
	$bra_email = $row["bra_email"];
	$bra_tel   = $row["bra_tel"];

	// construct the sql
	// matching to: type, emailalert is yes and email is present, price, beds, ptype+psubtype, area

	if ($row["dea_marketprice"]) {
		$sql_inner_sales .= " ((cli_salemin * 0.9) < " . round($row["dea_marketprice"]) . " AND (cli_salemax *1.1) > " . round($row["dea_marketprice"]) . ") AND ";
		$sql_inner_lettings .= " ((cli_letmin * 0.9) < " . round($row["dea_marketprice"]) . " AND (cli_letmax * 1.1) > " . round($row["dea_marketprice"]) . ") AND ";
	}
	if ($row["dea_bedroom"]) {
		$sql_inner_sales .= " cli_salebed <= " . $row["dea_bedroom"] . " AND ";
		$sql_inner_lettings .= " cli_letbed <= " . $row["dea_bedroom"] . "	AND ";
	}
	if ($row["dea_ptype"]) {
		$sql_ptype_sales    = " CONCAT('|',cli_saleptype,'|') LIKE '%|" . $row["dea_ptype"] . "|%' ";
		$sql_ptype_lettings = " CONCAT('|',cli_letptype,'|') LIKE '%|" . $row["dea_ptype"] . "|%' ";
	}
	if ($row["dea_psubtype"]) {
		$sql_psubtype_sales    = " CONCAT('|',cli_saleptype,'|') LIKE '%|" . $row["dea_psubtype"] . "|%' ";
		$sql_psubtype_lettings = " CONCAT('|',cli_letptype,'|') LIKE '%|" . $row["dea_psubtype"] . "|%' ";
	}

	if ($sql_ptype_sales && $sql_psubtype_sales) {
		$sql_inner_sales .= "($sql_ptype_sales OR $sql_psubtype_sales) AND ";
	}
	if ($sql_ptype_lettings && $sql_psubtype_lettings) {
		$sql_inner_lettings .= "($sql_ptype_lettings OR $sql_psubtype_lettings) AND ";
	}
}

if ($dea_type == 'Sales') {
	$sql = "SELECT cli_id, CONCAT(cli_fname,' ',cli_sname) AS cli_name, cli_email FROM client WHERE
	cli_status != 'Archived' AND cli_sales = 'Yes' AND cli_saleemail = 'Yes' AND cli_email != '' AND
	" . rtrim($sql_inner_sales, " AND") . "
	";

} elseif ($dea_type == 'Lettings') {
	$sql = "SELECT cli_id, CONCAT(cli_fname,' ',cli_sname) AS cli_name, cli_email FROM client WHERE
	cli_status != 'Archived' AND cli_lettings = 'Yes' AND cli_letemail = 'Yes' AND cli_email != '' AND
	" . rtrim($sql_inner_lettings, " AND") . "
	";
}

file_put_contents(dirname(__FILE__) . "/../../../../logs/" . date("Y-m-d") . "_mailshot_sql_dealID_" . $mai_deal . ".log", $sql);

$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}

$count_attempts = $q->numRows();

$started = time();
include_once("Zend/Mail.php");
$count_failed = 0;
while ($row = $q->fetchRow()) {
	$msg     = '';
	$subject = $mailshot_types[$mai_type];
	$email_addresses .= $row["cli_email"] . "\n";

	//$msg = "Dear ".$row["cli_name"].",\n\n";
	if ($mai_type == 'new') {
		$msg .= "Further to your recent enquiry, a new property which may be of interest to you has been added to our website.\n\n";
	} elseif ($mai_type == 'reduced') {
		$msg .= "Further to your recent enquiry, a property which may be of interest to you has been reduced in price.\n\n";
	} elseif ($mai_type == 'back') {
		$msg .= "Further to your recent enquiry, a property which may be of interest to you has come back on the market.\n\n";
	} elseif ($mai_type == 'resend') {
		$msg .= "Did you miss out on this one last time? Take a look before it goes!\n\n";
	} else {
		$errors[] = "Invalid mailshot type";
		echo error_message($errors);
		exit;
	}

	// used in sender notfy email
	$msg2 = $msg;
	$msg  = '';

	if ($additionalText) {
		$msg .= $additionalText . "\n\n";
	}
	if ($includeLink) {
		$msg .= "$strap\n$pro_addr\n$price\n$link?id=" . $mai_id . "&c=" . $row["cli_id"] . "\n\n";
	}

//	$msg .= "To arrange a viewing please call our $bra_title office on $bra_tel\n\n\n";

	$msg .= "\n\nTo unsubscribe from all future mailings, please follow this link:\n";
	$msg .= "$link?a=unsub&id=" . $mai_id . "&e=" . trim($row["cli_email"]) . "&c=" . $row["cli_id"];

//	if (!mail($row["cli_email"], $subject, $msg, "From: Wooster & Stock<$bra_email>\r\nContent-Type: text/plain; charset=\"UTF-8\"")) {
//
//	}

	if ($debugEmail) {
		$row['cli_email'] = $debugEmail;
	}
	try {
		$mailToClient = new Zend_Mail('UTF-8');
		$mailToClient->addTo($row['cli_email']);
		$mailToClient->setFrom($bra_email, "Wooster & Stock");
		$mailToClient->setSubject($subject);
		$mailToClient->setBodyText($msg);
		if ($attachFile) {
			echo "attaching\n";
			$at           = $mailToClient->createAttachment(file_get_contents($attachFile));
			$at->filename = basename($attachFile);
			$at->type     = mime_content_type($attachFile);
		}
		echo "sending... \n";
		$mailToClient->send();
	} catch (Exception $e) {
		echo "FAILED \n";
		$count_failed++;
	}
	if ($debugEmail) {
		break;
	}
//	break;
}

$ended = time();

// count the number of sucessful sends, then update database and send notification to sender
$db_data["mai_failed"] = intval($count_failed);
$db_data["mai_status"] = 'Sent';
db_query($db_data, "UPDATE", "mailshot", "mai_id", $mai_id);

$msg = "

Mailshot Report for $pro_addr ($mai_deal)

Attepted to send: " . intval($count_attempts) . "
Emails failed:    " . intval($count_failed) . "
Started:          " . date('H:i:s', $started) . "
Ended:            " . date('H:i:s', $ended) . "
Duration:         " . date('H:i:s', ($ended - $started)) . "

The content of this mailshot follows:

$msg2";
mail($use_email, "Mailshot Report ($pro_addr)", $msg);
exit;


