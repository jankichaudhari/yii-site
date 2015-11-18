<?php
include_once dirname(__FILE__) . "/../components/functions/details_function.php";
// show correct phone numbers depending on area (cam,syd, other)
function propertyDetailHeader()
{

	global $db, $CONFIG, $pageInfo, $CONSTANT, $pageContents, $additionalJs, $intPropID;
	$queryString = $pageInfo['queryString'];
	parse_str($queryString, $string);
	foreach ($string as $key => $val) {
		$path = $val;
	}

	if (!is_numeric($path)) {
		propertyNotFound();
	} else {
		$dea_id = intval($path);
	}

	$sql = "SELECT dea_branch FROM deal
	WHERE dea_id = $dea_id";

	$q = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$branch = $row['dea_branch'];
	}

	//sydenham
	if ($branch == 2 || $branch == 4) {
		return '<strong>sales:</strong> 020 8613 0060 <strong>lettings:</strong> 020 8613 0070';
	} else {
		return '<strong>sales:</strong> 020 7708 6700 <strong>lettings:</strong> 020 7708 6710';
	}

}

function sendToFriend()
{

	global $db, $CONFIG, $pageInfo, $CONSTANT, $pageContents, $additionalJs, $intPropID;
	$queryString = $pageInfo['queryString'];
	parse_str($queryString, $string);
	foreach ($string as $key => $val) {
		$path = $val;
	}

	if (!is_numeric($path)) {
		propertyNotFound();
	} else {
		$dea_id = intval($path);
	}

	$CONSTANT['robots'] = 'noindex,follow';

	//
	$sql = "SELECT
	pro_addr3,pro_addr4,pro_addr5,LEFT(pro_postcode, 4) AS pro_postcode,pro_north,pro_east,
	deal.*,
	area.are_title
	FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN area ON property.pro_area = area.are_id
	WHERE
	(deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Exchanged') AND
	dea_id = $dea_id";
	$q   = $db->query($sql);
	if ($q->numRows() == 0) {
		propertyNotFound();
	}
	while ($row = $q->fetchRow()) {
		$render = '<p>' . $row['dea_strapline'] . '</p><p>' . $row['pro_addr3'] . ', ' . $row['are_title'] . ', ' . $row['pro_postcode'] . ' - ' . format_price($row['dea_marketprice']);
		if ($row['dea_type'] == 'Lettings') {
			$render .= "p/w";
		}
		$render .= '</p><p><a href="' . $CONFIG['SITE_URL'] . 'details/' . $dea_id . '.html">' . $CONFIG['SITE_URL'] . 'details/' . $dea_id . '.html</a></p>';

		$renderEmail = $row['dea_strapline'] . "\n" . $row['pro_addr3'] . ', ' . $row['are_title'] . ', ' . $row['pro_postcode'] . ' - ' . format_price($row['dea_marketprice']);
		if ($row['dea_type'] == 'Lettings') {
			$renderEmail .= "p/w";
		}
		$renderEmail .= "\n" . '' . $CONFIG['SITE_URL'] . 'details/' . $dea_id . '.html';

	}

	if ($_POST['action'] != 'sendtofriend') {

		$render = '
<p>I\'m visiting the Wooster and Stock Web Site and I thought this property might be of interest to you:</p>
' . $render . '

<form method="post" action="">
<div class="row">
<label>Your Email Address</label>
<input type="text" name="sender_email" class="text" />
</div>
<div class="row">
<label>Your Friend\'s Email Address *</label>
<input type="text" name="recipient_email" class="text" />
</div>
<div class="row">
<label>Optional Comment</label>
<textarea name="comment" rows="4" cols="50"></textarea>
</div>
<input type="submit" value="Send" />
<input type="hidden" name="action" value="sendtofriend" />
</form>
';

	} else {

		if (!isset($_SERVER['HTTP_USER_AGENT'])) {
			die("Forbidden - You are not authorized to view this page");
			exit;
		}
		$authHosts = array("woosterstock.co.uk", "woosterstock.com", "wsvitaly.acp.local");

		$fromArray = parse_url(strtolower($_SERVER['HTTP_REFERER']));
		$wwwUsed   = strpos($fromArray['host'], "www.");
		if (!in_array(($wwwUsed === false ? $fromArray['host'] : substr(stristr($fromArray['host'], '.'), 1)), $authHosts)) {
			pageNotFound();
		}

		// Attempt to defend against header injections:
		$badStrings = array("Content-Type:", "MIME-Version:", "Content-Transfer-Encoding:", "bcc:", "cc:");
		foreach ($_POST as $k => $v) {
			foreach ($badStrings as $v2) {
				if (strpos($v, $v2) !== false) {
					pageNotFound();
				}
			}
		}

		if (!clean_input($_POST['recipient_email'])) {
			$errors[] = 'Your Friend\'s Email Address';
		} else {
			$recipient = clean_input($_POST['recipient_email']);
		}

		$comment = clean_input($_POST['comment']);

		if ($errors) {
			$render = "<h3>Error</h3>\n<p>The following fields are mandatory:</p>\n<ul>\n";
			foreach ($errors as $error) {
				$render .= "<li>$error</li>\n";
			}
			$render .= "</ul>\n<p>Please <a href=\"javascript:goback();\">go back</a> and try again</p>\n";
		} else {

			// send the email
			if ($_POST['sender_email']) {
				$from = $_POST['sender_email'];
			} else {
				$from = 'post@woosterstock.co.uk';
			}

			$emailBody = "I'm visiting the Wooster and Stock Web Site and I thought this property might be of interest to you:\n\n";
			$emailBody .= str_replace('&pound;', 'GBP ', $renderEmail) . "\n\n";
			$emailBody .= $comment;
			$emailHeaders = "From: $from\r\n";
			mail($recipient, 'Web Site recommendation from your friend or colleague', $emailBody, $emailHeaders);

			header("Location:" . $CONFIG['SITE_URL'] . 'details/' . $dea_id . '.html');

		}

	}

	return $render;
}

function callBackForm($clientId)
{

	global $db, $CONFIG;

	if ($_GET['sent'] == 'true') {
		return "<p>We'll call you shortly</p>";
	}
	$render = '
<h4>Call us</h4>
<p>If you wish to discuss your requirements with us, please call one of <a href="' . $CONFIG['SITE_URL'] . 'contact.html">our offices</a></p>

<form method="post" action="' . $CONFIG['SITE_URL'] . 'callback" class="callback">
<h4>Call me back</h4>
<p>If you would like us to call you, please enter your telephone number and click the button:</p>
<p><input type="text" name="callbackTelephone" value="' . $_SESSION['register']['tel'] . '" /> <input type="submit" name="callback" value="Call me back" /></p>
<input type="hidden" name="cid" value="' . $clientId . '" />
<input type="hidden" name="ref" value="' . urlencode($_SERVER['REQUEST_URI']) . '" />
</form>';
	return $render;
}

function callBackFunction($clientId = null, $tel = null)
{

	global $db;
	// get client info and send email to branch
	if (intval($clientId) > 0) {
		$sql = "SELECT client.*,branch.bra_title, branch.bra_email, CONCAT(user.use_fname,' ',user.use_sname) AS neg
		FROM client
		LEFT JOIN branch ON cli_branch = bra_id
		LEFT JOIN user ON cli_neg = use_id
		WHERE cli_id = $clientId";

		$q = $db->query($sql);
		while ($row = $q->fetchRow()) {
			$name        = $row['cli_fname'] . ' ' . $row['cli_sname'];
			$email       = $row['cli_email'];
			$branchTitle = $row['bra_title'];
			$branchEmail = $row['bra_email'];
			$neg         = $row['neg'];
			$sales       = $row['cli_sales'];
			$lettings    = $row['cli_lettings'];
		}

		if ($branchEmail) {
			$recipient     = $branchEmail;
			$recipientNote = "The client is assigned to branch $branchTitle";
		} else {

			if ($sales == 'Yes' && $lettings == 'No') {
				$recipient     = 'cam.sale@woosterstock.co.uk';
				$recipientNote = "The client is not assigned to any branch, is registered for sales and not for lettings";
			} elseif ($sales == 'No' && $lettings == 'Yes') {
				$recipient     = 'cam.let@woosterstock.co.uk';
				$recipientNote = "The client is not assigned to any branch, is registered for lettings and not for sales";
			} else {
				$recipient     = 'post@woosterstock.co.uk';
				$recipientNote = "The client is not assigned to any branch, is registered for lettings and for sales";
			}

		}

		$emailBody = "
		A client has requested a callback.

		- Open client record: https://www.woosterstock.co.uk/admin4/client/update/id/$clientId
		- Check the Contact Log to make sure no one else has already made the call
		- Make the call (note that the phone number in this email may differ from the one on record)
		- Make a record of the call in the Contact Log system

		Client:      $name
		Telephone:   $tel
		Date:        " . date('r') . "

		They were viewing the following page when the request was sent:
		" . $_SERVER['HTTP_REFERER'] . "


		**********************************************************************************************
		This email was sent to $recipient
		$recipientNote
		**********************************************************************************************
		";

	} else {

		$emailBody = "
		A client has requested a callback.

		- Call the client and register them
		- Make a record of the call in the Contact Log system

		Telephone:   $tel
		Date:        " . date('r') . "
		(no further information on this client is available)


		They were viewing the following page when the request was sent:
		" . $_SERVER['HTTP_REFERER'] . "


		**********************************************************************************************
		This email was sent to $recipient
		$recipientNote
		**********************************************************************************************
		";

	}

	$emailHeaders = "From: noreply@woosterstock.co.uk\r\n";
	mail($recipient, 'A client has requsted a callback', $emailBody, $emailHeaders);

	return "<p>We'll call you shortly</p>";
}

function register()
{

	global $db, $CONFIG, $CONSTANT, $intPropID, $pageId;
	$CONSTANT['robots'] = 'noindex,follow';

	if ($_SESSION['register']['clientId']) {
		return '<p>You are already registered with us.</p>' . callBackForm($_SESSION['register']['clientId']);
	}

	if (!$_POST) {

		if (!$_SESSION['register']['fname']) {
			$fname = 'First Name(s)';
		} else {
			$fname = $_SESSION['register']['fname'];
		}
		if (!$_SESSION['register']['sname']) {
			$sname = 'Surname';
		} else {
			$sname = $_SESSION['register']['sname'];
		}
		if ($_SESSION['register']['scope'] == 'sales') {
			$checksales = ' checked="checked"';
		}
		if ($_SESSION['register']['scope'] == 'lettings') {
			$checklettings = ' checked="checked"';
		}

		$render = '

<form method="post" action="" class="register">

<div class="row">
<label>Name</label>
<input type="text" name="r_fname" value="' . $fname . '" class="text" id="fname" style="width:140px;margin-right:5px;" /><input type="text" name="r_sname" id="sname" value="' . $sname . '" class="text" style="width:150px" />
</div>
<div class="row">
<label>Telephone</label>
<input type="text" name="r_tel" id="tel" class="text" value="' . $_SESSION['register']['tel'] . '" />
</div>
<div class="row">
<label>Email</label>
<input type="text" name="r_email" id="email" class="text" value="' . $_SESSION['register']['email'] . '" />
</div>
<div class="row">
<label>Sales or Lettings?</label>
<input type="radio" name="r_scope" value="sales"' . $checksales . '> Sales &nbsp; <input type="radio" name="r_scope" value="lettings"' . $checklettings . '> Lettings
</div>
<div class="row">
<input type="submit" value="Register" />
</div>

</form>
';
	} else {

		if (!clean_input($_POST['r_fname']) || $_POST['r_fname'] == 'First Name(s)') {
			$errors[] = 'First Name(s)';
		} else {
			$fname = clean_input($_POST['r_fname']);
		}
		if (!clean_input($_POST['r_sname']) || $_POST['r_sname'] == 'Surname') {
			$errors[] = 'Surname';
		} else {
			$sname = clean_input($_POST['r_sname']);
		}
		if (!clean_input($_POST['r_tel'])) {
			$errors[] = 'Telephone';
		} else {
			$tel = clean_input($_POST['r_tel']);
		}
		if (!clean_input($_POST['r_email'])) {
			$errors[] = 'Email';
		} else {
			$email = clean_input($_POST['r_email']);
		}
		if (!clean_input($_POST['r_scope'])) {
			$errors[] = 'Sales or Lettings';
		} else {
			$scope = clean_input($_POST['r_scope']);
		}

		if ($scope == 'sales') {
			$recipient = 'cam.sale@woosterstock.co.uk';
		} elseif ($scope == 'lettings') {
			$recipient = 'cam.let@woosterstock.co.uk';
		}

		// set up session variables for use in other forms
		$_SESSION['register']['fname'] = $fname;
		$_SESSION['register']['sname'] = $sname;
		$_SESSION['register']['name']  = $fname . ' ' . $sname;
		$_SESSION['register']['tel']   = $tel;
		$_SESSION['register']['email'] = $email;
		$_SESSION['register']['scope'] = $scope;

		if ($errors) {
			$render = "<h3>Error</h3>\n<p>The following fields are mandatory:</p>\n<ul>\n";
			foreach ($errors as $error) {
				$render .= "<li>$error</li>\n";
			}
			$render .= "</ul>\n<p>Please <a href=\"javascript:goback();\">go back</a> and try again</p>\n";
		} else {

			// check email exists
			$sql     = "SELECT client.*,branch.bra_title,branch.bra_tel FROM client
			LEFT JOIN branch ON client.cli_branch = branch.bra_id
			WHERE cli_email = '$email' ORDER BY cli_id DESC LIMIT 1";
			$q       = $db->query($sql);
			$numRows = $q->numRows();
			if ($numRows > 0) {
				while ($row = $q->fetchRow()) {

					$clientId = $row['cli_id'];

					$_SESSION['register']['clientId'] = $clientId;
					/*
					is active within scope = show branch contact number, callback button and email form
					is active out of scope = re-register as new
					is inactive = re-register as new
					*/

					if ($scope == 'sales') {
						if ($row['cli_sales'] == 'Yes') {
							$message = '<p>You are already registered with us for Sales.</p>';
							/*
							if ($row['bra_tel']) {
								$message .= '<h4>Call us</h4>';
								$message .= '<p>If you wish to discuss your requirements with us, please call our <strong>'.$row['bra_title'].'</strong> branch on <strong>'.$row['bra_tel'].'</strong></p>';
								} else {
								$message .= '<h4>Call us</h4>';
								$message .= '<p>If you wish to discuss your requirements with us, please call one of <a href="'.$CONFIG['SITE_URL'].'contact.html">our offices</a></p>';
								}
							*/
							$message .= callBackForm($clientId);

						} else {

							$db_data['cli_status'] = 'Pending_New_Client_Sales';
							$dbq                   = new dbQuery('client', 'UPDATE', 'cli_id', $clientId, $db_data);
							$message               = '<p>You have succesfully registered. A member of our team will be in touch with you shortly to discuss your requirements further</p>';
							$debug                 = '<p>(existing client, inactive for sales, cli_id = ' . $clientId . ')</p>';
							// send email to branch
							$emailSubject = 'New Client Registration';
							$emailBody    = "New client resigtration, log into to admin to activate:\n\nhttps://www.woosterstock.co.uk/v3.0/live/admin/new_client_questionaire.php?cli_id=" . $clientId;
							$emailHeaders = "From: noreply@woosterstock.co.uk\r\n";
							mail($recipient, $emailSubject, $emailBody, $emailHeaders);
						}
					} elseif ($scope == 'lettings') {
						if ($row['cli_lettings'] == 'Yes') {
							$message = '<p>You are already registered with us for Lettings.</p>';
							/*
							if ($row['bra_tel']) {
								$message .= '<h4>Call us</h4>';
								$message .= '<p>If you wish to discuss your requirements with us, please call our <strong>'.$row['bra_title'].'</strong> branch on <strong>'.$row['bra_tel'].'</strong></p>';
								} else {
								$message .= '<h4>Call us</h4>';
								$message .= '<p>If you wish to discuss your requirements with us, please call one of <a href="'.$CONFIG['SITE_URL'].'contact.html">our offices</a></p>';
								}
							*/
							$message .= callBackForm($clientId);

						} else {

							$db_data['cli_status'] = 'Pending_New_Client_Lettings';
							$dbq                   = new dbQuery('client', 'UPDATE', 'cli_id', $clientId, $db_data);
							$message               = '<p>You have succesfully registered. A member of our team will be in touch with you shortly to discuss your requirements further</p>';
							$debug                 = '<p>(existing client, inactive for lettings, cli_id = ' . $clientId . ')</p>';
							// send email to branch
							$emailSubject = 'New Client Registration';
							$emailBody    = "New client resigtration, log into to admin to activate:\n\nhttps://www.woosterstock.co.uk/v3.0/live/admin/new_client_questionaire.php?cli_id=" . $clientId;
							$emailHeaders = "From: noreply@woosterstock.co.uk\r\n";
							mail($recipient, $emailSubject, $emailBody, $emailHeaders);
						}
					}

				}
			} else {

				// insert client
				$db_data['cli_method']  = 'Website';
				$db_data['cli_created'] = date('Y-m-d H:i:s');
				$db_data['cli_fname']   = $fname;
				$db_data['cli_sname']   = $sname;
				$db_data['cli_email']   = $email;
				if ($scope == 'sales') {
					$db_data['cli_status'] = 'Pending_New_Client_Sales';
					$db_data['cli_sales']  = 'Yes';
				} elseif ($scope == 'lettings') {
					$db_data['cli_status']   = 'Pending_New_Client_Lettings';
					$db_data['cli_lettings'] = 'Yes';
				}
				$dbq                              = new dbQuery('client', 'INSERT', 'id', '', $db_data);
				$clientId                         = $dbq->row;
				$_SESSION['register']['clientId'] = $clientId;
				unset($db_data);

				// insert telephone number
				if (substr($tel, 0, 2) == '07') {
					$db_data['tel_type'] = 'Mobile';
				} else {
					$db_data['tel_type'] = 'Home';
				}
				$db_data['tel_number'] = $tel;
				$db_data['tel_cli']    = $clientId;
				$db_data['tel_ord']    = 0;
				$dbq                   = new dbQuery('tel', 'INSERT', 'id', '', $db_data);

				$message = '<p>You have succesfully registered. A member of our team will be in touch with you shortly to discuss your requirements further</p>';
				$debug   = '<p>(new client, cli_id = ' . $clientId . ') </p>';

				// send email to branch
				$emailSubject = 'New Client Registration';
				$emailBody    = "New client resigtration, log into to admin to activate:\n\nhttps://www.woosterstock.co.uk/v3.0/live/admin/new_client_questionaire.php?cli_id=" . $clientId;
				$emailHeaders = "From: noreply@woosterstock.co.uk\r\n";
				mail($recipient, $emailSubject, $emailBody, $emailHeaders);

			}

			$render = $message;
		}

	}
	return $render;
}

function nearest_transport($dea_id)
{

	global $db, $CONFIG;

	// get the coordinates of teh property in question
	$sql = "SELECT pro_east,pro_north
	FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	WHERE deal.dea_id = $dea_id";
	$q   = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$intOSX = $row['pro_east'];
		$intOSY = $row['pro_north'];
	}

	if (strlen($intOSX) != 6 || strlen($intOSY) != 6) {
		return;
	}

	$basex = $intOSX;
	$basey = $intOSY;

	$sql = "SELECT
	places.place_ID, places.place_type, places.place_title, places.place_desc, places.place_osx, places.place_osy, pl_type.pl_type_id, pl_type_title
	FROM places, pl_type
	WHERE
	(places.place_type = 1 OR places.place_type = 2) AND
	places.place_type = pl_type.pl_type_id  AND
	sqrt((abs(places.place_osx-" . $intOSX . ")*abs(places.place_osx-" . $intOSX . "))+(abs(places.place_osy-" . $intOSY . ")*abs(places.place_osy-" . $intOSY . "))) < 2000
	ORDER BY sqrt((abs(places.place_osx-" . $intOSX . ")*abs(places.place_osx-" . $intOSX . "))+(abs(places.place_osy-" . $intOSY . ")*abs(places.place_osy-" . $intOSY . "))) LIMIT 1";
	$q   = $db->query($sql);
	if ($q->numRows() == 0) {
		return;
	}
	while ($row = $q->fetchRow()) {

		$intTransType  = $row['place_type'];
		$strTransType  = $row['pl_type_title'];
		$strTransTitle = $row['place_title'];
		$strTransDesc  = $row['place_desc'];
		$intTransOSX   = $row['place_osx'];
		$intTransOSY   = $row['place_osy'];

		$intTransDistance = round(sqrt((abs($intTransOSX - $intOSX) * abs($intTransOSX - $intOSX)) + (abs($intTransOSY - $intOSY) * abs($intTransOSY - $intOSY))));

		if ($intTransType == 1) {
			$strTrans = '<p class="nearest"><img src="' . WS_URL_IMAGES . '/sys/rail.gif" alt="Train" width="17" height="10" /> ' . $strTransTitle . ' <span>(approx. ' . $intTransDistance . ' meters away)</span></p>';
		} elseif ($intTransType == 2) {
			$strTrans = '<p class="nearest"><img src="' . WS_URL_IMAGES . '/sys/tube.gif" alt="Tube" width="12" height="10" /> ' . $strTransTitle . ' <span>(approx. ' . $intTransDistance . ' meters away)</span></p>';
		}

	}

	$render .= '
<h5>Nearest Transport</h5>
<div class="box">
' . $strTrans . '
</div>
';
	return $render;
}

function nearest_property($dea_id)
{

	global $db, $CONFIG;

	// get the coordinates of teh property in question
	$sql = "SELECT dea_type,pro_east,pro_north
	FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	WHERE deal.dea_id = $dea_id";
	$q   = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$intOSX  = $row['pro_east'];
		$intOSY  = $row['pro_north'];
		$SaleLet = $row['dea_type'];
	}
	if (strlen($intOSX) != 6 || strlen($intOSY) != 6) {
		return;
	}
	$basex         = $intOSX;
	$basey         = $intOSY;
	$intProxCount  = 1;
	$intLayerCount = 1;

	$sql = "SELECT
	dea_id,dea_type,dea_marketprice,dea_bedroom,pro_addr3,pro_postcode,pro_east,pro_north,T.pty_title AS ptype,ST.pty_title AS psubtype,ST.pty_id AS psubtypeid,media.med_file
	FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
	LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id
	LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal' AND med_order = 1 AND med_type= 'Photograph'

	WHERE deal.dea_type = '" . $SaleLet . "' AND (deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other') AND
	LENGTH(pro_east) = 6 AND LENGTH(pro_north) = 6 AND pro_east != $intOSX AND pro_north != $intOSY
	AND dea_id != $dea_id
	AND sqrt((abs(property.pro_east-$intOSX)*abs(property.pro_east-$intOSX))+(abs(property.pro_north-$intOSY)*abs(property.pro_north-$intOSY))) < 2000
	GROUP BY dea_id
	ORDER BY
	sqrt((abs(property.pro_east-$intOSX)*abs(property.pro_east-$intOSX))+(abs(property.pro_north-$intOSY)*abs(property.pro_north-$intOSY)))
	LIMIT 7";

	$q = $db->query($sql);
	if ($q->numRows() == 0) {
		return;
	}

	while ($row = $q->fetchRow()) {

		// added 31/07/08 to fix shorter postcodes not displaying correctly
		$pcSplit             = explode(" ", $row['pro_postcode']);
		$row['pro_postcode'] = $pcSplit[0];

		$intProxOSX = $row['pro_east'];
		$intProxOSY = $row['pro_north'];

		$intDistance = round(sqrt((abs($intProxOSX - $intOSX) * abs($intProxOSX - $intOSX)) + (abs($intProxOSY - $intOSY) * abs($intProxOSY - $intOSY))));

		$strProx .= '<li>
<a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_thumb2.jpg', $row['med_file']) . '" alt="' . $row['pro_addr3'] . ' ' . $row['pro_postcode'] . '" /></a>
<p><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html">' . $row['pro_addr3'] . '</a></p>
';
		if ($row['ptype'] == "Other") {
			$strType2 = $row['psubtype'];
		} else {
			if ($row['psubtypeid'] == 13) {
				$strType2 = 'Studio';
			} elseif ($row['psubtypeid'] == 26) {
				$strType2 = 'House/Flat Share';
			} else {
				$strType2 = $row['dea_bedroom'] . " Bed " . $row['ptype'];
			}
		}
		$strProx .= '<p>' . $strType2 . '</p>';
		if ($row['dea_type'] == 'Lettings') {
			$suffix = ' p/w';
		} else {
			unset($suffix);
		}

		$strProx .= '<p>' . format_price($row['dea_marketprice']) . $suffix . '</p>';
		//$strProx .= '<p>approx. '.$intDistance.' meters</p>';
		$strProx .= '</li>' . "\n";

		$intProxCount++;
	}
	if ($intProxCount <> 0) {
		$strProxDisplay = "";
		$strProxDisplay .= $strProx;
	}

	$render .= '
<h5>More Property</h5>
<div class="box">
<ul class="moreProperty">
' . $strProx . '</ul>
</div>
';

	return $render;
}

function propertyHitCounter($dea_id)
{

	global $db, $PHPSESSID;
	$sql = "INSERT INTO
	`propertyviews` (`dea_id` , `datetime`, `session`,`ip` )
	VALUES
	('$dea_id', '" . date('Y-m-d H:i:s') . "', '" . $PHPSESSID . "','" . $_SERVER['REMOTE_ADDR'] . "');";
	$q   = $db->query($sql);

	$sql = "UPDATE deal SET dea_hits = (dea_hits + 1) WHERE dea_id = '$dea_id'";
	$q   = $db->query($sql);

}

// show correct phone numbers for selected property
function telephoneNumbers()
{

	global $db, $CONFIG, $pageInfo;
	$queryString = $pageInfo['queryString'];
	parse_str($queryString, $string);
	foreach ($string as $key => $val) {
		$path = $val;
	}

	if (!is_numeric($path)) {
		propertyNotFound();
	} else {
		$intPropID = intval($path);
	}

	$sql = "SELECT dea_branch  FROM deal WHERE dea_id = $intPropID";
	$q   = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$branch = $row['dea_branch'];
	}
	// get all branch

}

function viewingForm($propId, $strState, $SaleLet)
{

	global $CONFIG, $db;

	if ($SaleLet == 'Lettings') {
		$statusMsgUO   = 'Let S.T.C';
		$statusMsgSold = 'Let';
	} else {
		$statusMsgUO   = 'Under Offer';
		$statusMsgSold = 'Sold';
	}

	// viewing form
	if ($strState == 'Available') {

		if ($_POST['action'] == 'arrangeviewing') {

			if (!trim($_POST['name'])) {
				$errors['name'] = true;
			} else {
				$name                        = trim(strip_html($_POST['name']));
				$_SESSION['viewing']['name'] = $name;
			}
			if (!trim($_POST['email'])) {
				$errors['email'] = true;
			} else {
				$email                        = trim(strip_html($_POST['email']));
				$_SESSION['viewing']['email'] = $email;
			}
			if (!trim($_POST['telephone'])) {
				$errors['telephone'] = true;
			} else {
				$telephone                        = trim(strip_html($_POST['telephone']));
				$_SESSION['viewing']['telephone'] = $telephone;
			}
			$datetime = trim(strip_html($_POST['datetime']));
		} else {
			$name      = $_SESSION['viewing']['name'];
			$email     = $_SESSION['viewing']['email'];
			$telephone = $_SESSION['viewing']['telephone'];
		}

		if (!$name) {
			$name = $_SESSION['register']['name'];
		}
		if (!$email) {
			$email = $_SESSION['register']['email'];
		}
		if (!$telephone) {
			$telephone = $_SESSION['register']['tel'];
		}

		$viewing_form = '
<form method="post" action="">
<label>Name</label>
<input type="text" name="name" value="' . $name . '" />
<label>Email</label>
<input type="text" name="email" value="' . $email . '" />
<label>Telephone</label>
<input type="text" name="telephone" value="' . $telephone . '" />
<label>Preferred Date and Time</label>
<textarea name="datetime" rows="3" cols="10">' . $datetime . '</textarea>
<input type="submit" value="Send &raquo;" class="submit" />
<input type="hidden" name="action" value="arrangeviewing" />
</form>';

		if ($_POST['action'] == 'arrangeviewing' && !$errors) {

			// set up session variables for use in other forms
			$_SESSION['register']['fname'] = $fname;
			$_SESSION['register']['sname'] = $sname;
			$_SESSION['register']['name']  = $fname . ' ' . $sname;
			$_SESSION['register']['tel']   = $telephone;
			$_SESSION['register']['email'] = $email;

			$sql = "SELECT pro_addr3,pro_addr4,pro_addr5,pro_postcode,pro_postcode AS pro_fullpostcode,bra_title,bra_email,bra_tel,deal.*,CONCAT(use_fname,' ',use_sname) AS use_name
		FROM deal
		LEFT JOIN property ON deal.dea_prop = property.pro_id
		LEFT JOIN branch ON dea_branch = bra_id
		LEFT JOIN user ON dea_neg = use_id
		WHERE dea_id = $propId";

			$q = $db->query($sql);
			while ($row = $q->fetchRow()) {
				$pcSplit             = explode(" ", $row['pro_postcode']);
				$row['pro_postcode'] = $pcSplit[0];
				$address             = $row['pro_addr3'] . ', ' . $row['pro_postcode'];
				$description         = $row['dea_strapline'];
				$price               = $row['dea_marketprice'];
				$branch              = $row['bra_title'];
				$branchtel           = $row['bra_tel'];
				$branchemail         = $row['bra_email'];
				$use_name            = $row['use_name'];
			}

			// send email

			$EmailSubject = "Arrange viewing: " . $address;

			// Message to Client
			$html_body = '<html>
<head></head>
<body>
<span style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000">
<p>Hi ' . $name . ',</p>
<p>Many thanks for your interest. We will be getting back to you shortly to confirm your viewing. Please do call us at any time if you would like to speak to a negotiator to discuss your requirements further.</p>
<p>Contact our ' . $branch . ' office on ' . $branchtel . '</p>
<p>' . $description . '<br />' . $address . '<br />' . format_price($price) . '<br />
<a href="' . $CONFIG['SITE_URL'] . 'details/' . $propId . '.html">' . $CONFIG['SITE_URL'] . 'details/' . $propId . '.html</a></p>
</span>
' . email_footer("html", $email, $name);
			$text_body = '
Hi ' . $strName . ',

Many thanks for your interest. We will be getting back to you shortly to confirm your viewing. Please do call us at any
time if you would like to speak to a negotiator to discuss your requirements further.

Contact our ' . $branch . ' office on ' . $branchtel . '

' . $description . '
' . $address . '
' . format_price($price) . '<br />
' . $CONFIG['SITE_URL'] . 'details/' . $propId . '.html
' . email_footer("text", $email, $name);

			$text = $text_body;
			$html = $html_body;
			$crlf = "\r\n";
			$hdrs = array(
				'From'    => $branchemail,
				'Subject' => $EmailSubject,
			);
			$mime = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
			$mail =& Mail::factory('mail');
			$mail->send($email, $hdrs, $body);

			// message to Office
			$EmailBody = 'Name:        ' . $name . '
Tel:         ' . $telephone . '
Email:       ' . $email . '

Would like to arrange a viewing of:
Address:     ' . $address . '
Price:       ' . $price . '
Link:        ' . $CONFIG['SITE_URL'] . 'details/' . $propId . '.html
Date/Time:   ' . $datetime . '

Property ID: ' . $propId . '
Sent:        ' . date('r') . '
';

			$text    = $EmailBody;
			$subject = "(" . $use_name . ") - " . $EmailSubject;
			$crlf    = "\r\n";
			$hdrs    = array(
				'From'    => $email,
				'Subject' => $subject,
			);
			$mime    = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
			$mail =& Mail::factory('mail');
			$mail->send($branchemail, $hdrs, $body);

			return '<p>Thank you for your message</p>';
		} elseif ($_POST['action'] == 'arrangeviewing' && $errors) {
			// error messgae
			return '<p class="error">Please fill in all the fields</p>' . $viewing_form;
		} else {
			// view form
			return $viewing_form;
		}

	} elseif ($strState == 'Under Offer' || $strStatus == 'Under Offer with Other') {

		if ($_POST['action'] == 'arrangeviewing') {

			if (!trim($_POST['name'])) {
				$errors['name'] = true;
			} else {
				$name                        = trim($_POST['name']);
				$_SESSION['viewing']['name'] = $name;
			}
			if (!trim($_POST['email'])) {
				$errors['email'] = true;
			} else {
				$email                        = trim($_POST['email']);
				$_SESSION['viewing']['email'] = $email;
			}
			if (!trim($_POST['telephone'])) {
				$errors['telephone'] = true;
			} else {
				$telephone                        = trim($_POST['telephone']);
				$_SESSION['viewing']['telephone'] = $telephone;
			}
		} else {
			$name      = $_SESSION['viewing']['name'];
			$email     = $_SESSION['viewing']['email'];
			$telephone = $_SESSION['viewing']['telephone'];
		}
		if (!$name) {
			$name = $_SESSION['register']['name'];
		}
		if (!$email) {
			$email = $_SESSION['register']['email'];
		}
		if (!$telephone) {
			$telephone = $_SESSION['register']['tel'];
		}

		$viewing_form = '
<form method="post" action="">
<label>Name</label>
<input type="text" name="name" value="' . $name . '" />
<label>Email</label>
<input type="text" name="email" value="' . $email . '" />
<label>Telephone</label>
<input type="text" name="telephone" value="' . $telephone . '" />
<input type="submit" value="Send &raquo;" class="submit" />
<input type="hidden" name="action" value="arrangeviewing" />
</form>';

		if ($_POST['action'] == 'arrangeviewing' && !$errors) {

			// set up session variables for use in other forms
			$_SESSION['register']['fname'] = $fname;
			$_SESSION['register']['sname'] = $sname;
			$_SESSION['register']['name']  = $fname . ' ' . $sname;
			$_SESSION['register']['tel']   = $telephone;
			$_SESSION['register']['email'] = $email;

			// send email
			$sql = "SELECT pro_addr3,pro_addr4,pro_addr5,pro_postcode,pro_postcode AS pro_fullpostcode,bra_title,bra_email,bra_tel,deal.*,CONCAT(use_fname,' ',use_sname) AS use_name
		FROM deal
		LEFT JOIN property ON deal.dea_prop = property.pro_id
		LEFT JOIN branch ON dea_branch = bra_id
		LEFT JOIN user ON dea_neg = use_id
		WHERE dea_id = $propId";

			$q = $db->query($sql);
			while ($row = $q->fetchRow()) {
				$pcSplit             = explode(" ", $row['pro_postcode']);
				$row['pro_postcode'] = $pcSplit[0];
				$address             = $row['pro_addr3'] . ', ' . $row['pro_postcode'];
				$description         = $row['dea_strapline'];
				$price               = $row['dea_marketprice'];
				$branch              = $row['bra_title'];
				$branchtel           = $row['bra_tel'];
				$branchemail         = $row['bra_email'];
				$use_name            = $row['use_name'];
			}

			// send email

			$EmailSubject = "Register interest: " . $address;

			// Message to Client
			$html_body = '<html>
<head></head>
<body>
<span style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000">
<p>Hi ' . $name . ',</p>
<p>Many thanks for your interest. We will let you know if this property comes back on the market. Please do call us at any time if you would like to speak to a negotiator to discuss your requirements further.</p>
<p>Contact our ' . $branch . ' office on ' . $branchtel . '</p>
<p>' . $description . '<br />' . $address . '<br />' . format_price($price) . '<br />
<a href="' . $CONFIG['SITE_URL'] . 'details/' . $propId . '.html">' . $CONFIG['SITE_URL'] . 'details/' . $propId . '.html</a></p>
</span>
' . email_footer("html", $email, $name);
			$text_body = '
Hi ' . $strName . ',

Many thanks for your interest. We will let you know if this property comes back on the market. Please do call us at any
time if you would like to speak to a negotiator to discuss your requirements further.

Contact our ' . $branch . ' office on ' . $branchtel . '

' . $description . '
' . $address . '
' . format_price($price) . '<br />
' . $CONFIG['SITE_URL'] . 'details/' . $propId . '.html
' . email_footer("text", $email, $name);

			$text = $text_body;
			$html = $html_body;
			$crlf = "\r\n";
			$hdrs = array(
				'From'    => $branchemail,
				'Subject' => $EmailSubject,
			);
			$mime = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
			$mail =& Mail::factory('mail');
			$mail->send($email, $hdrs, $body);

			// message to Office
			$EmailBody = 'Name:        ' . $name . '
Tel:         ' . $telephone . '
Email:       ' . $email . '

Would like to register interest in:
Address:     ' . $address . '
Price:       ' . $price . '
Link:        ' . $CONFIG['SITE_URL'] . '/details/' . $propId . '
Date/Time:   ' . $datetime . '

Property ID: ' . $propId . '
Sent:        ' . date('r') . '
';

			$text    = $EmailBody;
			$subject = "(" . $use_name . ") - " . $EmailSubject;
			$crlf    = "\r\n";
			$hdrs    = array(
				'From'    => $email,
				'Subject' => $subject,
			);
			$mime    = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
			$mail =& Mail::factory('mail');
			$mail->send($branchemail, $hdrs, $body);

			return '<p>Thank you for your message</p>';
		} elseif ($_POST['action'] == 'arrangeviewing' && $errors) {
			// error messgae

			return '<p><span style="color:#FF0000;">This property is currently ' . $statusMsgUO . '</span></p>
<p>It is not possible to view this property but if you fill in the form below we will let you know if this property becomes available again.</p>
<p class="error">Please fill in all the fields</p>
' . $viewing_form;
		} else {
			// view form
			return '<p><span style="color:#FF0000;">This property is currently ' . $statusMsgUO . '</span></p>
<p>It is not possible to view this property but if you fill in the form below we will let you know if this property becomes available again.</p>
' . $viewing_form;
		}

	} else {

		return '
<p>This property is ' . $statusMsgSold . '</p>
<p>It is not possible to view this property.</p>
<p>Please <a href="' . $CONFIG['SITE_URL'] . 'register">register with us</a> for up to the minute updates by email</p>
';
	}

}

function propertySearchSales()
{

	global $db, $CONFIG;

	// from search box
	$strPropType = $_GET['t'];
	$strPageLink = 't=' . $_GET['t'] . '&amp;';
	if ($strPropType == "House") {
		$sql .= " (deal.dea_ptype = 1) AND ";
		$strPropType = "House";
	} elseif ($strPropType == "Apartment") {
		$sql .= " (deal.dea_ptype = 2) AND ";
		$strPropType = "Apartment";
	} elseif ($strPropType == "LiveWork") {
		$sql .= " (deal.dea_psubtype = 10) AND ";
		$strPropType = "LiveWork";
	} elseif ($strPropType == "Commercial") {
		$sql .= " (deal.dea_ptype = 3) AND ";
		$strPropType = "Commercial";
	} else {
		$strPropType = "Any";
	}

	$minp = intval($_GET['minp']);
	$strPageLink .= 'minp=' . $minp . '&amp;';
	if ($minp) {
		$strMinPrice = $minp;
		$sql .= " deal.dea_marketprice >= " . $minp . " AND ";
	}

	$maxp = intval($_GET['maxp']);
	$strPageLink .= 'maxp=' . $maxp . '&amp;';
	if ($maxp) {
		$strMaxPrice = $maxp;
		$sql .= " deal.dea_marketprice <= " . $maxp . " AND ";
	}

	$minbeds = $_GET['minbeds'];
	$strPageLink .= 'minbeds=' . $minbeds . '&amp;';
	if ($minbeds) {
		$strMinBedrooms = $minbeds;
		$sql .= " deal.dea_bedroom >= " . $minbeds . " AND ";
	}
	$maxbeds = $_GET['maxbeds'];
	if ($_GET['maxbeds'] == '') {
		$maxbeds = 99;
	}
	$strPageLink .= 'maxbeds=' . $maxbeds . '&amp;';
	if ($maxbeds || $maxbeds == 0) {
		$strMaxBedrooms = $maxbeds;
		$sql .= " deal.dea_bedroom <= " . $maxbeds . " AND ";
	} else {
		$strMaxBedrooms = 99;
	}

	$strKeywords = $_GET['kw'];
	if ($strKeywords) {
		$sqlKeyword = " ( ";
		$strKeyword = explode(",", $strKeywords);
		$kwCount    = count($strKeyword);

		for ($i = 0; $i <= $kwCount; $i++) {

			if ($strKeyword[$i]) { // if keyword exists

				// fix for SE1 returning SE11, SE12 etc
				if (trim(strtolower($strKeyword[$i])) == "se1") {
					$sqlKeyword .= " property.pro_postcode LIKE 'SE1 %' OR ";
				} else {
					$sqlKeyword .= "
			property.pro_id LIKE '%" . trim($strKeyword[$i]) . "%' OR
			property.pro_addr1 LIKE '%" . trim($strKeyword[$i]) . "%' OR
			property.pro_addr3 LIKE '%" . trim($strKeyword[$i]) . "%' OR
			deal.dea_strapline LIKE '%" . trim($strKeyword[$i]) . "%' OR
			property.pro_postcode LIKE '%" . $strKeyword[$i] . "%' OR
			area.are_title LIKE '%" . trim($strKeyword[$i]) . "%' OR
			deal.dea_keywords LIKE '%" . trim($strKeyword[$i]) . "%' OR ";
				}

			}
		}
		$sqlKeyword = substr($sqlKeyword, 0, -3);
		$sqlKeyword .= ") ";
		$strPageLink .= 'kw=' . $strKeywords . '&amp;';
		$sql .= $sqlKeyword . " AND ";
	}

	$area_id = intval($_GET['area_id']);
	if ($area_id) {
		$strAreaID = $area_id;
		$sql .= " deal.dea_area = " . $area_id . " AND ";
	}

	// Show by Status
	$strStatus = $_GET['s'];
	$strPageLink .= 's=' . $_GET['s'] . '&amp;';
	if ($strStatus == "Available") {
		$sql .= " (deal.dea_status = 'Available') AND ";
		$strStatus = "Available";
	} else {
		$sql .= " (deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other' OR deal.dea_status = 'Exchanged') AND ";
		$strStatus = "All";
	}

	if ($_GET['branch'] == '1' || $_GET['branch'] == '2' || $_GET['branch'] == '5' || $_GET['branch'] == '7') {
		$sql .= " deal.dea_branch = " . intval($_GET['branch']) . " AND ";
		$strPageLink .= 'branch=' . $_GET['branch'] . '&amp;';
	}

	if ($_GET['special'] == "map") {
		$sql .= " property.pro_east =" . intval($_GET['osx']) . " AND property.pro_north =" . intval($_GET['osy']) . " AND ";
	}

	// Order records
	$strOrderBy = $_GET['o'];
	if ($strOrderBy == "Date") {
		$order      = " dea_launchdate DESC";
		$strOrderBy = "Date";
	} elseif ($strOrderBy == "Street") {
		$order      = " property.pro_addr3 ASC, dea_launchdate DESC";
		$strOrderBy = "Street";
	} elseif ($strOrderBy == 'PriceASC') {
		$order      = " deal.dea_marketprice ASC, dea_launchdate DESC";
		$strOrderBy = "PriceASC";
	} else {
		$order      = " deal.dea_marketprice DESC, dea_launchdate DESC";
		$strOrderBy = "Price";
	}
	$strPageLink .= 'o=' . $strOrderBy . '&amp;';

	$sql = remove_lastchar(trim($sql), "AND");
	$sql = remove_lastchar(trim($sql), "OR");

	$sql = "SELECT
CONCAT(pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
pro_addr3,pro_postcode,are_title,
dea_id,dea_type,dea_marketprice,dea_strapline,dea_status,dea_launchdate,
med_file,med_title
FROM deal


LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal' AND med_order = 1 AND med_type= 'Photograph'
WHERE
$sql
AND deal.dea_type = 'Sales' AND underTheRadar <> 1
GROUP BY deal.dea_id
ORDER BY $order";
	//echo $sql;
	$q = $db->query($sql);
	//if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {

			// added 31/07/08 to fix shorter postcodes not displaying correctly
			$pcSplit             = explode(" ", $row['pro_postcode']);
			$row['pro_postcode'] = $pcSplit[0];

			if ($row['dea_status'] == 'Under Offer' || $row['dea_status'] == 'Under Offer with Other') {
				$sot = '<span style="color:#FF0000;">UNDER OFFER</span>';

				$price = format_price($row['dea_marketprice']);
			} elseif ($row['dea_status'] == 'Exchanged') {
				$sot   = 'SOLD';
				$price = 'SOLD';
			} else {
				$sot   = 'FOR SALE';
				$price = format_price($row['dea_marketprice']);
			}

			$propTitle = trim($row['pro_addr3'] . ', ' . $row['are_title'] . ' ' . $row['pro_postcode']);
			// flag new properties
			$newdate = strtotime(date('Y-m-j H:i:s')) - (18 * 24 * 60 * 60);
			if ($row['dea_launchdate']) {
				if (strtotime($row['dea_launchdate']) > $newdate) {
					$class = ' class="new"';
					$propTitle .= ' (New)';
				}
			}

			$row['dea_strapline'] = str_replace(
				array(
					 'OSP',
					 'CHAIN FREE'
				),
				array(
					 '<abbr title="Off-street Parking">OSP</abbr>',
					 '<abbr title="Offered with no forward chain">CHAIN FREE</abbr>'
				),
				$row['dea_strapline']
			);

			$data[] = '
<li>
<div class="thumbnail">
<a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_thumb1.jpg', $row['med_file']) . '" alt="' . $row['pro_addr3'] . '" /></a>
</div>
<div class="details">
<h2' . $class . '><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html" title="' . $row['pro_addr3'] . '"><span class="price">' . $price . '</span> ' . $propTitle . ' </a></h2>
<p class="sot">' . $sot . '</p>
<p class="strapline">' . $row['dea_strapline'] . '</p>
<p class="link"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html">Full details</a></p>
</div>
</li>

';
			unset($class);
		}
	}

	require_once 'Pager/Pager.php';
	$params = array(
		'mode'     => 'Jumping',
		'perPage'  => 15,
		'delta'    => 100,
		'itemData' => $data,
		'path'     => $CONFIG['SITE_URL'] . 'sales.html',
		'append'   => true,
		'fileName' => '?pageID=%d'
	);
	$pager  = & Pager::factory($params);
	$data   = $pager->getPageData();
	$links  = $pager->getLinks();

	if ($pager->numItems()) {

		if (!$links['back']) {
			$back = "";
		} else {
			$back = '<a href="' . $CONFIG['SITE_URL'] . 'sales.html?pageID=' . $pager->getPreviousPageID() . '&amp;' . $strPageLink . '">&laquo; Prev Page</a>';
		}
		if (!$links['next']) {
			$next = "";
		} else {
			$next = '<a href="' . $CONFIG['SITE_URL'] . 'sales.html?pageID=' . $pager->getNextPageID() . '&amp;' . $strPageLink . '">Next Page &raquo;</a>';
		}

		$pages = '
<p>Found ' . $pager->numItems() . ' Properties</p>
';
		if ($pager->numPages() > 1) {
			$pages .= '<p>Page: ' . str_replace(array('&nbsp;', '/index.php'), array(
																					' ', ''
																			   ), $links['pages']) . '</p>';
		}

		$top = '
<div class="top">
<p class="right">' . $back . ' &nbsp; ' . $next . '</p>
<p>Page: ' . $pager->getCurrentPageID() . ' of ' . $pager->numPages() . '</p>
</div>
';

		foreach ($data as $output) {
			$results .= $output;
		}

		$bottom = '
<div class="bottom">
<p class="right">' . $back . ' &nbsp; ' . $next . '</p>
<p>Page: ' . $pager->getCurrentPageID() . ' of ' . $pager->numPages() . '</p>
</div>
';

	} else { // no results

		// no results

		// pages table cells
		$pages = '<p>Found 0 Properties</p>';

		// no results message
		$results = '
<li>
<h2>No Records Found</h2>
<p>Your search returned no records.</p>
<p>Please refine your search and try again.</p>
<p>Click <a href="' . $CONFIG['SITE_URL'] . 'sales.html">here</a> to view all property
for sale, or click <a href="javascript:history.go(-1);">here</a>
to go back.</p>
</li>';
	}

	$pageLink               = $CONFIG['SITE_URL'] . 'sales.html?pageID=' . $_GET['pageID'] . '&amp;' . $strPageLink;
	$_SESSION['searchPage'] = $pageLink;

	// build the form fields.
	$branches = array(
		1 => 'Camberwell',
		2 => 'Sydenham',
		5 => 'Whitstable',
		7 => 'Brixton'
	);
	foreach ($branches as $key => $val) {
		$render_branches .= '<option value="' . $key . '"';
		if ($key == $_GET['branch']) {
			$render_branches .= ' selected="selected"';
		}
		$render_branches .= '>' . $val . '</option>' . "\n";
	}

	$propertytypes = array(
		'Any'        => 'Any',
		'House'      => 'House',
		'Apartment'  => 'Apartment',
		'LiveWork'   => 'Live/Work',
		'Commercial' => 'Commercial/Mixed Use'
	);
	foreach ($propertytypes as $key => $val) {
		$render_propertytypes .= '<option value="' . $key . '"';
		if ($key == $strPropType) {
			$render_propertytypes .= ' selected="selected"';
		}
		$render_propertytypes .= '>' . $val . '</option>' . "\n";
	}

	$minprices = array(
		75000, 100000, 125000, 150000, 175000, 200000, 225000, 250000, 275000, 300000, 325000, 350000, 375000, 400000,
		425000, 450000, 475000, 500000, 550000, 600000, 650000,
		700000, 750000, 800000, 850000, 900000, 950000, 1000000
	);
	foreach ($minprices as $key) {
		$render_minprices .= '<option value="' . $key . '"';
		if ($key == $strMinPrice) {
			$render_minprices .= ' selected="selected"';
		}
		$render_minprices .= '>' . format_price($key) . '</option>' . "\n";
	}

	$maxprices = array(
		125000, 150000, 175000, 200000, 225000, 250000, 275000, 300000, 325000, 350000, 375000, 400000, 425000, 450000,
		475000, 500000, 550000, 600000, 650000, 700000, 750000,
		800000, 850000, 900000, 950000, 1000000, 2000000, 3000000
	);
	foreach ($maxprices as $key) {
		$render_maxprices .= '<option value="' . $key . '"';
		if ($key == $strMaxPrice) {
			$render_maxprices .= ' selected="selected"';
		}
		$render_maxprices .= '>' . format_price($key) . '</option>' . "\n";
	}

	$minbedrooms = array(
		0 => 'Studio',
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5
	);
	foreach ($minbedrooms as $key => $val) {
		$render_minbedrooms .= '<option value="' . $key . '"';
		if ($key == $strMinBedrooms) {
			$render_minbedrooms .= ' selected="selected"';
		}
		$render_minbedrooms .= '>' . $val . '</option>' . "\n";
	}
	$maxbedrooms = array(
		99 => 'Max',
		0  => 'Studio',
		1  => 1,
		2  => 2,
		3  => 3,
		4  => 4,
		5  => 5
	);
	foreach ($maxbedrooms as $key => $val) {
		$render_maxbedrooms .= '<option value="' . $key . '"';
		if ($key == $strMaxBedrooms) {
			$render_maxbedrooms .= ' selected="selected"';
		}
		$render_maxbedrooms .= '>' . $val . '</option>' . "\n";
	}

	$sortbys = array(
		'Price'    => 'Price (highest first)',
		'PriceASC' => 'Price (lowest first)',
		'Date'     => 'Date',
		'Street'   => 'Street'
	);
	foreach ($sortbys as $key => $val) {
		$render_sortbys .= '<option value="' . $key . '"';
		if ($key == $strOrderBy) {
			$render_sortbys .= ' selected="selected"';
		}
		$render_sortbys .= '>' . $val . '</option>' . "\n";
	}

	$shows = array(
		'All'       => 'All',
		'Available' => 'Available'
	);
	foreach ($shows as $key => $val) {
		$render_shows .= '<label for="' . $key . '"><input type="radio" name="s" value="' . $key . '" id="' . $key . '"';
		if ($key == $strStatus) {
			$render_shows .= ' checked="checked"';
		}
		$render_shows .= ' />' . $val . '</label> ' . "\n";
	}

	$form = '<form method="get" name="WS_Form" action="sales.html">
<p>Branch<br/>
<select name="branch" class="formwide">
<option value="">Any</option>
' . $render_branches . '
</select> </p>
<p>Type of Property<br/>
<select name="t" class="formwide">
' . $render_propertytypes . '
</select> </p>
<p>Minimum Price<br/>
<select class="formwide" onchange="WSMax(this.form,0);" name="minp" id="minp">
<option value="0">No Minimum</option>
' . $render_minprices . '
</select></p>
<p>Maximum Price<br/>
<select class="formwide" onchange="WSMin(this.form,0);" name="maxp" id="maxp">
<option value="999999999">No Maximum</option>
' . $render_maxprices . '
</select></p>
<p>Bedrooms<br/>
<select name="minbeds" class="formnarrow">
' . $render_minbedrooms . '
</select> to
<select name="maxbeds" class="formnarrow">
' . $render_maxbedrooms . '
</select></p>
<p>Street or Area<br/>
<input type="text" name="kw" class="formwide" value="' . $strKeywords . '" style="width:177px;" /></p>
<p>Sort By<br/>
<select name="o" class="formwide">
' . $render_sortbys . '
</select></p>
<p>Show<br/>
' . $render_shows . '</p>
<div class="button"><input type="submit" class="button" value="Search" style="width: 100px; font-weight: bold;" /></div>
<div class="button"><input type="button" class="button" style="width: 100px" value="Reset" onclick="WSReset();" /></div>
</form>';

	return '
<div id="searchResults">
' . $top . '
<ul>
' . $results . '
</ul>
' . $bottom . '
</div>

<div class="rightColumn gray">
<h5>Property Search Results</h5>
<div class="box pagination">' . $pages . '</div>

<h5>Property Search Sales</h5>
' . $form . '
</div>


';

}

function propertySearchLettings()
{

	global $db, $CONFIG;

	// from search box
	$strPropType = $_GET['t'];
	$strPageLink = 't=' . $_GET['t'] . '&amp;';
	$sql         = '';
	if ($strPropType == "House") {
		$sql .= " (deal.dea_ptype = 1) AND ";
		$strPropType = "House";
	} elseif ($strPropType == "Apartment") {
		$sql .= " (deal.dea_ptype = 2) AND ";
		$strPropType = "Apartment";
	} elseif ($strPropType == "Room") {
		$sql .= " (deal.dea_psubtype = 26) AND ";
		$strPropType = "Room";
	} elseif ($strPropType == "Commercial") {
		$sql .= " (deal.dea_ptype = 3) AND ";
		$strPropType = "Commercial";
	} else {
		$strPropType = "Any";
	}

	$minp = intval($_GET['minp']);
	$strPageLink .= 'minp=' . $minp . '&amp;';
	if ($minp) {
		$strMinPrice = $minp;
		$sql .= " deal.dea_marketprice >= " . $minp . " AND ";
	}

	$maxp = intval($_GET['maxp']);
	$strPageLink .= 'maxp=' . $maxp . '&amp;';
	if ($maxp) {
		$strMaxPrice = $maxp;
		$sql .= " deal.dea_marketprice <= " . $maxp . " AND ";
	}

	$minbeds = intval($_GET['minbeds']);
	$strPageLink .= 'minbeds=' . $minbeds . '&amp;';
	if ($minbeds) {
		$strMinBedrooms = $minbeds;
		$sql .= " deal.dea_bedroom >= " . $minbeds . " AND ";
	}
	$maxbeds = $_GET['maxbeds'];
	if ($_GET['maxbeds'] == '') {
		$maxbeds = 99;
	}
	$strPageLink .= 'maxbeds=' . $maxbeds . '&amp;';
	if ($maxbeds || $maxbeds == 0) {
		$strMaxBedrooms = $maxbeds;
		$sql .= " deal.dea_bedroom <= " . $maxbeds . " AND ";
	} else {
		$strMaxBedrooms = 99;
	}

	$strKeywords = $_GET['kw'];
	if ($strKeywords) {
		$sqlKeyword = " ( ";
		$strKeyword = explode(",", $strKeywords);
		$kwCount    = count($strKeyword);

		for ($i = 0; $i <= $kwCount; $i++) {

			if ($strKeyword[$i]) { // if keyword exists

				// fix for SE1 returning SE11, SE12 etc
				if (trim(strtolower($strKeyword[$i])) == "se1") {
					$sqlKeyword .= " property.pro_postcode LIKE 'SE1 %' OR ";
				} else {
					$sqlKeyword .= "
									property.pro_id LIKE '%" . trim($strKeyword[$i]) . "%' OR
									property.pro_addr1 LIKE '%" . trim($strKeyword[$i]) . "%' OR
									property.pro_addr3 LIKE '%" . trim($strKeyword[$i]) . "%' OR
									deal.dea_strapline LIKE '%" . trim($strKeyword[$i]) . "%' OR
									property.pro_postcode LIKE '%" . $strKeyword[$i] . "%' OR
									area.are_title LIKE '%" . trim($strKeyword[$i]) . "%' OR
									deal.dea_keywords LIKE '%" . trim($strKeyword[$i]) . "%' OR ";
				}

			}
		}
		$sqlKeyword = substr($sqlKeyword, 0, -3);
		$sqlKeyword .= ") ";
		$strPageLink .= 'kw=' . $strKeywords . '&amp;';
		$sql .= $sqlKeyword . " AND ";
	}

	$area_id = intval($_GET['area_id']);
	if ($area_id) {
		$strAreaID = $area_id;
		$sql .= " deal.dea_area = " . $area_id . " AND ";
	}

	$strFeature = $_GET['feature'];
	$strPageLink .= 'feature=' . $_GET['feature'] . '&amp;';
	if ($strFeature) {
		$sql .= " (feature.fea_title = 'Student Friendly') AND ";
	}

	// Show by Status
	$strStatus = $_GET['s'];
	$strPageLink .= 's=' . $_GET['s'] . '&amp;';
	if ($strStatus == "Available") {
		$sql .= " (deal.dea_status = 'Available') AND ";
		$strStatus = "Available";
	} else {
		$sql .= " (deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other' OR deal.dea_status = 'Exchanged') AND ";
		$strStatus = "All";
	}

	if ($_GET['branch'] == '1' || $_GET['branch'] == '2' || $_GET['branch'] == '5') {
		$sql .= " deal.dea_branch = " . intval($_GET['branch']) . " AND ";
		$strPageLink .= 'branch=' . $_GET['branch'] . '&amp;';
	}

	if ($_GET['special'] == "map") {
		$sql .= " property.pro_east =" . intval($_GET['osx']) . " AND property.pro_north =" . intval($_GET['osy']) . " AND ";
	}

	if ($_GET['prices'] == 'pcm') {
		$strPageLink .= 'prices=' . $_GET['prices'] . '&amp;';
		$strPrices = $_GET['prices'];
	} else {
		$strPrices = 'pw';
	}

	// Order records
	$strOrderBy = $_GET['o'];
	if ($strOrderBy == "Date") {
		$order      = " dea_launchdate DESC";
		$strOrderBy = "Date";
	} elseif ($strOrderBy == "Street") {
		$order      = " property.pro_addr3 ASC, dea_launchdate DESC";
		$strOrderBy = "Street";
	} else {
		$order      = " deal.dea_marketprice DESC, dea_launchdate DESC";
		$strOrderBy = "Price";
	}
	$strPageLink .= 'o=' . $strOrderBy . '&amp;';

	$sql = remove_lastchar(trim($sql), "AND");
	$sql = remove_lastchar(trim($sql), "OR");

	$sql = "SELECT
CONCAT(pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
pro_addr3,pro_postcode,are_title,
dea_id,dea_type,dea_marketprice,dea_strapline,dea_status,dea_launchdate,
med_file,med_title
FROM deal


LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal' AND med_order = 1 AND med_type= 'Photograph'

LEFT JOIN link_instruction_to_feature ON deal.dea_id = link_instruction_to_feature.dealId
LEFT JOIN feature ON link_instruction_to_feature.featureId = feature.fea_id
WHERE
$sql
AND deal.dea_type = 'Lettings' AND underTheRadar <> 1
GROUP BY deal.dea_id
ORDER BY $order";
	//echo $sql;
	$q = $db->query($sql);
	//if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {

			// added 31/07/08 to fix shorter postcodes not displaying correctly
			$pcSplit             = explode(" ", $row['pro_postcode']);
			$row['pro_postcode'] = $pcSplit[0];

			if ($row['dea_status'] == 'Under Offer' || $row['dea_status'] == 'Under Offer with Other') {
				$sot = 'LET S.T.C.';
			} elseif ($row['dea_status'] == 'Exchanged') {
				$sot = 'LET';
			} else {
				$sot = 'TO LET';
			}

			$propTitle = trim($row['pro_addr3'] . ', ' . $row['are_title'] . ' ' . $row['pro_postcode']);
			// flag new properties
			$newdate = strtotime(date('Y-m-j H:i:s')) - (18 * 24 * 60 * 60);
			if ($row['dea_launchdate']) {
				if (strtotime($row['dea_launchdate']) > $newdate) {
					$class = ' class="new"';
					$propTitle .= ' (New)';
				}
			}

			$row['dea_strapline'] = str_replace(
				array(
					 'OSP',
					 'CHAIN FREE'
				),
				array(
					 '<abbr title="Off-street Parking">OSP</abbr>',
					 '<abbr title="Offered with no forward chain">CHAIN FREE</abbr>'
				),
				$row['dea_strapline']
			);

			if ($strPrices == "pcm") {
				$price = format_price(($row['dea_marketprice'] * 52) / 12) . 'pcm';
			} else {
				$price = format_price($row['dea_marketprice']) . 'p/w';
			}

			$data[] = '
<li>
<div class="thumbnail">
<a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_thumb1.jpg', $row['med_file']) . '" alt="' . $row['pro_addr3'] . '" /></a>
</div>
<div class="details">
<h2' . $class . '><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html" title="' . $row['pro_addr3'] . '"><span class="price">' . $price . '</span> ' . $propTitle . ' </a></h2>
<p class="sot">' . $sot . '</p>
<p class="strapline">' . $row['dea_strapline'] . '</p>
<p class="link"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html">Full details</a></p>
</div>
</li>

';
			unset($class);
		}
	}

	require_once 'Pager/Pager.php';
	$params = array(
		'mode'     => 'Jumping',
		'perPage'  => 15,
		'delta'    => 100,
		'itemData' => $data,
		'path'     => $CONFIG['SITE_URL'] . 'lettings.html',
		'append'   => true,
		'fileName' => '?pageID=%d'
	);
	$pager  = & Pager::factory($params);
	$data   = $pager->getPageData();
	$links  = $pager->getLinks();

	if ($pager->numItems()) {

		if (!$links['back']) {
			$back = "";
		} else {
			$back = '<a href="' . $CONFIG['SITE_URL'] . 'lettings.html?pageID=' . $pager->getPreviousPageID() . '&amp;' . $strPageLink . '">&laquo; Prev Page</a>';
		}
		if (!$links['next']) {
			$next = "";
		} else {
			$next = '<a href="' . $CONFIG['SITE_URL'] . 'lettings.html?pageID=' . $pager->getNextPageID() . '&amp;' . $strPageLink . '">Next Page &raquo;</a>';
		}

		$pages = '
<p>Found ' . $pager->numItems() . ' Properties</p>
';
		if ($pager->numPages() > 1) {
			$pages .= '<p>Page: ' . str_replace(array('&nbsp;', '/index.php'), array(
																					' ', ''
																			   ), $links['pages']) . '</p>';
		}

		$top = '
<div class="top">
<p class="right">' . $back . ' &nbsp; ' . $next . '</p>
<p>Page: ' . $pager->getCurrentPageID() . ' of ' . $pager->numPages() . '</p>
</div>
';

		$results = '';
		foreach ($data as $output) {
			$results .= $output;
		}

		$bottom = '
<div class="bottom">
<p class="right">' . $back . ' &nbsp; ' . $next . '</p>
<p>Page: ' . $pager->getCurrentPageID() . ' of ' . $pager->numPages() . '</p>
</div>
';

	} else { // no results

		// no results

		// pages table cells
		$pages = '<p>Found 0 Properties</p>';

		// no results message
		$results = '
<li>
<h2>No Records Found</h2>
<p>Your search returned no records.</p>
<p>Please refine your search and try again.</p>
<p>Click <a href="' . $CONFIG['SITE_URL'] . 'lettings.html">here</a> to view all property
for sale, or click <a href="javascript:history.go(-1);">here</a>
to go back.</p>
</li>';
	}

	$pageLink               = $CONFIG['SITE_URL'] . 'lettings.html?pageID=' . $_GET['pageID'] . '&amp;' . $strPageLink;
	$_SESSION['searchPage'] = $pageLink;

	// build the form fields.
	$branches = array(
		1 => 'Camberwell',
		2 => 'Sydenham'
	);
	foreach ($branches as $key => $val) {
		$render_branches .= '<option value="' . $key . '"';
		if ($key == $_GET['branch']) {
			$render_branches .= ' selected="selected"';
		}
		$render_branches .= '>' . $val . '</option>' . "\n";
	}

	$propertytypes = array(
		'Any'       => 'Any',
		'House'     => 'House',
		'Apartment' => 'Apartment',
		'Room'      => 'House/Flat Share'
	);
	foreach ($propertytypes as $key => $val) {
		$render_propertytypes .= '<option value="' . $key . '"';
		if ($key == $strPropType) {
			$render_propertytypes .= ' selected="selected"';
		}
		$render_propertytypes .= '>' . $val . '</option>' . "\n";
	}

	if ($strPrices == 'xpcm') {

		$minprices = array(
			400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2100, 2200,
			2300, 2400, 2500, 2750, 3000, 3250, 3500
		);
		foreach ($minprices as $key) {
			$render_minprices .= '<option value="' . $key . '"';
			if ($key == $strMinPrice) {
				$render_minprices .= ' selected="selected"';
			}
			$render_minprices .= '>' . format_price($key) . '</option>' . "\n";
		}

		$maxprices = array(
			400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2100, 2200,
			2300, 2400, 2500, 2750, 3000, 3250, 3500
		);
		foreach ($maxprices as $key) {
			$render_maxprices .= '<option value="' . $key . '"';
			if ($key == $strMaxPrice) {
				$render_maxprices .= ' selected="selected"';
			}
			$render_maxprices .= '>' . format_price($key) . '</option>' . "\n";
		}

	} else {

		$minprices = array(100, 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1250, 1500, 2000);
		foreach ($minprices as $key) {
			$render_minprices .= '<option value="' . $key . '"';
			if ($key == $strMinPrice) {
				$render_minprices .= ' selected="selected"';
			}
			$render_minprices .= '>' . format_price($key) . '</option>' . "\n";
		}

		$maxprices = array(100, 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1250, 1500, 2000);
		foreach ($maxprices as $key) {
			$render_maxprices .= '<option value="' . $key . '"';
			if ($key == $strMaxPrice) {
				$render_maxprices .= ' selected="selected"';
			}
			$render_maxprices .= '>' . format_price($key) . '</option>' . "\n";
		}

	}

	$minbedrooms = array(
		0 => 'Studio',
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5
	);
	foreach ($minbedrooms as $key => $val) {
		$render_minbedrooms .= '<option value="' . $key . '"';
		if ($key == $strMinBedrooms) {
			$render_minbedrooms .= ' selected="selected"';
		}
		$render_minbedrooms .= '>' . $val . '</option>' . "\n";
	}
	$maxbedrooms        = array(
		99 => 'Max',
		0  => 'Studio',
		1  => 1,
		2  => 2,
		3  => 3,
		4  => 4,
		5  => 5
	);
	$render_maxbedrooms = '';
	foreach ($maxbedrooms as $key => $val) {
		$render_maxbedrooms .= '<option value="' . $key . '"';
		if ($key == $strMaxBedrooms) {
			$render_maxbedrooms .= ' selected="selected"';
		}
		$render_maxbedrooms .= '>' . $val . '</option>' . "\n";
	}

	/*
$sortbys = array('Price'=>'Price','Date'=>'Date','Street'=>'Street');
foreach ($sortbys as $key=>$val) {
	$render_sortbys .= '<label for="'.$key.'"><input type="radio" name="o" value="'.$key.'" id="'.$key.'"';
	if ($key == $strOrderBy) {
		$render_sortbys .= ' checked="checked"';
		}
	$render_sortbys .= ' />'.$val.'</label> '."\n";
	}
*/

	$sortbys = array(
		'Price'  => 'Price',
		'Date'   => 'Date',
		'Street' => 'Street'
	);
	foreach ($sortbys as $key => $val) {
		$render_sortbys .= '<option value="' . $key . '"';
		if ($key == $strOrderBy) {
			$render_sortbys .= ' selected="selected"';
		}
		$render_sortbys .= '>' . $val . '</option>' . "\n";
	}

	$shows = array(
		'All'       => 'All',
		'Available' => 'Available'
	);
	foreach ($shows as $key => $val) {
		$render_shows .= '<label for="' . $key . '"><input type="radio" name="s" value="' . $key . '" id="' . $key . '"';
		if ($key == $strStatus) {
			$render_shows .= ' checked="checked"';
		}
		$render_shows .= ' />' . $val . '</label> ' . "\n";
	}

	$priceFormats = array(
		'pw'  => 'Per Week',
		'pcm' => 'Per Month'
	);
	foreach ($priceFormats as $key => $val) {
		$render_prices .= '<label for="' . $key . '"><input type="radio" name="prices" value="' . $key . '" id="' . $key . '"';
		if ($key == $strPrices) {
			$render_prices .= ' checked="checked"';
		}
		$render_prices .= ' />' . $val . '</label> ' . "\n";
	}

	if ($_GET['feature']) {
		$checkStudent = ' checked="checked"';
	}

	$form = '<form method="get" name="WS_Form" action="lettings.html">
<p>Type of Property<br/>
<select name="t" class="formwide">
' . $render_propertytypes . '
</select> </p>
<p>Minimum Price<br/>
<select class="formwide" onchange="WSMax(this.form,0);" name="minp" id="minp">
<option value="0">No Minimum</option>
' . $render_minprices . '
</select></p>
<p>Maximum Price<br/>
<select class="formwide" onchange="WSMin(this.form,0);" name="maxp" id="maxp">
<option value="999999999">No Maximum</option>
' . $render_maxprices . '
</select></p>

<p>Display Prices<br/>
' . $render_prices . '
</p>

<p>Bedrooms<br/>
<select name="minbeds" class="formnarrow">
' . $render_minbedrooms . '
</select> to
<select name="maxbeds" class="formnarrow">
' . $render_maxbedrooms . '
</select></p>
<p>Street or Area<br/>
<input type="text" name="kw" class="formwide" value="' . $strKeywords . '" style="width:177px;" /></p>
<p>Sort By<br/>
<select name="o" class="formwide">
' . $render_sortbys . '
</select></p>
<p>Show<br/>
' . $render_shows . '</p>
<p><label><input type="checkbox" name="feature" value="Student Friendly"' . $checkStudent . ' />Student Friendly </label></p>
<div class="button"><input type="submit" class="button" value="Search" style="width: 100px; font-weight: bold;" /></div>
<div class="button"><input type="button" class="button" style="width: 100px" value="Reset" onclick="WSReset();" /></div>
</form>';

	return '
<div id="searchResults">
' . $top . '
<ul>
' . $results . '
</ul>
' . $bottom . '
</div>

<div class="rightColumn gray">
<h5>Property Search Results</h5>
<div class="box pagination">' . $pages . '</div>

<h5>Property Search Lettings</h5>
' . $form . '
</div>


';

}

function propertySearchLettings2()
{

	global $db, $CONFIG;

	// from search box
	$strPropType = $_GET['t'];
	$strPageLink = 't=' . $_GET['t'] . '&amp;';
	if ($strPropType == "House") {
		$sql .= " (deal.dea_ptype = 1) AND ";
		$strPropType = "House";
	} elseif ($strPropType == "Apartment") {
		$sql .= " (deal.dea_ptype = 2) AND ";
		$strPropType = "Apartment";
	} elseif ($strPropType == "Room") {
		$sql .= " (deal.dea_psubtype = 26) AND ";
		$strPropType = "Room";
	} elseif ($strPropType == "Commercial") {
		$sql .= " (deal.dea_ptype = 3) AND ";
		$strPropType = "Commercial";
	} else {
		$strPropType = "Any";
	}

	if ($_GET['prices'] == 'pcm') {
		$strPageLink .= 'prices=' . $_GET['prices'] . '&amp;';
		$strPrices = $_GET['prices'];
	} else {
		$strPrices = 'pw';
	}

	$minp = intval($_GET['minp']);
	$maxp = intval($_GET['maxp']);

	if ($strPrices == 'pcm') {
		$minp_sql = pcm2pw($minp);
		$maxp_sql = pcm2pw($maxp);
	} else {
		$minp_sql = $minp;
		$maxp_sql = $maxp;

	}

	$strPageLink .= 'minp=' . $minp . '&amp;';
	if ($minp) {
		$strMinPrice = $minp;
		$sql .= " deal.dea_marketprice >= " . $minp_sql . " AND ";
	}

	$strPageLink .= 'maxp=' . $maxp . '&amp;';
	if ($maxp) {
		$strMaxPrice = $maxp;
		$sql .= " deal.dea_marketprice <= " . $maxp_sql . " AND ";
	}

	$minbeds = intval($_GET['minbeds']);
	$strPageLink .= 'minbeds=' . $minbeds . '&amp;';
	if ($minbeds) {
		$strMinBedrooms = $minbeds;
		$sql .= " deal.dea_bedroom >= " . $minbeds . " AND ";
	}
	$maxbeds = $_GET['maxbeds'];
	if ($_GET['maxbeds'] == '') {
		$maxbeds = 99;
	}
	$strPageLink .= 'maxbeds=' . $maxbeds . '&amp;';
	if ($maxbeds || $maxbeds == 0) {
		$strMaxBedrooms = $maxbeds;
		$sql .= " deal.dea_bedroom <= " . $maxbeds . " AND ";
	} else {
		$strMaxBedrooms = 99;
	}

	$strKeywords = $_GET['kw'];
	if ($strKeywords) {
		$sqlKeyword = " ( ";
		$strKeyword = explode(",", $strKeywords);
		$kwCount    = count($strKeyword);

		for ($i = 0; $i <= $kwCount; $i++) {

			if ($strKeyword[$i]) { // if keyword exists

				// fix for SE1 returning SE11, SE12 etc
				if (trim(strtolower($strKeyword[$i])) == "se1") {
					$sqlKeyword .= " property.pro_postcode LIKE 'SE1 %' OR ";
				} else {
					$sqlKeyword .= "
			property.pro_id LIKE '%" . trim($strKeyword[$i]) . "%' OR
			property.pro_addr1 LIKE '%" . trim($strKeyword[$i]) . "%' OR
			property.pro_addr3 LIKE '%" . trim($strKeyword[$i]) . "%' OR
			deal.dea_strapline LIKE '%" . trim($strKeyword[$i]) . "%' OR
			property.pro_postcode LIKE '%" . $strKeyword[$i] . "%' OR
			area.are_title LIKE '%" . trim($strKeyword[$i]) . "%' OR
			deal.dea_keywords LIKE '%" . trim($strKeyword[$i]) . "%' OR ";
				}

			}
		}
		$sqlKeyword = substr($sqlKeyword, 0, -3);
		$sqlKeyword .= ") ";
		$strPageLink .= 'kw=' . $strKeywords . '&amp;';
		$sql .= $sqlKeyword . " AND ";
	}

	$area_id = intval($_GET['area_id']);
	if ($area_id) {
		$strAreaID = $area_id;
		$sql .= " deal.dea_area = " . $area_id . " AND ";
	}

	$strFeature = $_GET['feature'];
	$strPageLink .= 'feature=' . $_GET['feature'] . '&amp;';
	if ($strFeature) {
		$sql .= " (feature.fea_title = 'Student Friendly') AND ";
	}

	// Show by Status
	$strStatus = $_GET['s'];
	$strPageLink .= 's=' . $_GET['s'] . '&amp;';
	if ($strStatus == "Available") {
		$sql .= " (deal.dea_status = 'Available') AND ";
		$strStatus = "Available";
	} else {
		$sql .= " (deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other' OR deal.dea_status = 'Exchanged') AND ";
		$strStatus = "All";
	}

	if ($_GET['branch'] == '1' || $_GET['branch'] == '2' || $_GET['branch'] == '5') {
		$sql .= " deal.dea_branch = " . intval($_GET['branch']) . " AND ";
		$strPageLink .= 'branch=' . $_GET['branch'] . '&amp;';
	}

	if ($_GET['special'] == "map") {
		$sql .= " property.pro_east =" . intval($_GET['osx']) . " AND property.pro_north =" . intval($_GET['osy']) . " AND ";
	}

	// Order records
	$strOrderBy = $_GET['o'];
	if ($strOrderBy == "Date") {
		$order      = " dea_launchdate DESC";
		$strOrderBy = "Date";
	} elseif ($strOrderBy == "Street") {
		$order      = " property.pro_addr3 ASC, dea_launchdate DESC";
		$strOrderBy = "Street";
	} elseif ($strOrderBy == 'PriceASC') {
		$order      = " deal.dea_marketprice ASC, dea_launchdate DESC";
		$strOrderBy = "PriceASC";
	} else {
		$order      = " deal.dea_marketprice DESC, dea_launchdate DESC";
		$strOrderBy = "Price";
	}
	$strPageLink .= 'o=' . $strOrderBy . '&amp;';

	$sql = remove_lastchar(trim($sql), "AND");
	$sql = remove_lastchar(trim($sql), "OR");

	$sql = "SELECT
CONCAT(pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
pro_addr3,pro_postcode,are_title,
dea_id,dea_type,dea_marketprice,dea_strapline,dea_status,dea_launchdate,
med_file,med_title
FROM deal


LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal' AND med_order = 1 AND med_type= 'Photograph'

LEFT JOIN link_instruction_to_feature ON deal.dea_id = link_instruction_to_feature.dealId
LEFT JOIN feature ON link_instruction_to_feature.featureId = feature.fea_id
WHERE
$sql
AND deal.dea_type = 'Lettings' AND underTheRadar <> 1
GROUP BY deal.dea_id
ORDER BY $order";
	//echo $sql;
	$q = $db->query($sql);
	//if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {

			// added 31/07/08 to fix shorter postcodes not displaying correctly
			$pcSplit             = explode(" ", $row['pro_postcode']);
			$row['pro_postcode'] = $pcSplit[0];

			if ($row['dea_status'] == 'Under Offer' || $row['dea_status'] == 'Under Offer with Other') {
				$sot = 'LET S.T.C.';
			} elseif ($row['dea_status'] == 'Exchanged') {
				$sot = 'LET';
			} else {
				$sot = 'TO LET';
			}

			$propTitle = trim($row['pro_addr3'] . ', ' . $row['are_title'] . ' ' . $row['pro_postcode']);
			// flag new properties
			$newdate = strtotime(date('Y-m-j H:i:s')) - (18 * 24 * 60 * 60);
			if ($row['dea_launchdate']) {
				if (strtotime($row['dea_launchdate']) > $newdate) {
					$class = ' class="new"';
					$propTitle .= ' (New)';
				}
			}

			$row['dea_strapline'] = str_replace(
				array(
					 'OSP',
					 'CHAIN FREE'
				),
				array(
					 '<abbr title="Off-street Parking">OSP</abbr>',
					 '<abbr title="Offered with no forward chain">CHAIN FREE</abbr>'
				),
				$row['dea_strapline']
			);

			if ($strPrices == "pcm") {
				$price = format_price(($row['dea_marketprice'] * 52) / 12) . 'pcm';
			} else {
				$price = format_price($row['dea_marketprice']) . 'p/w';
			}

			$data[] = '
<li>
<div class="thumbnail">
<a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_thumb1.jpg', $row['med_file']) . '" alt="' . $row['pro_addr3'] . '" /></a>
</div>
<div class="details">
<h2' . $class . '><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html" title="' . $row['pro_addr3'] . '"><span class="price">' . $price . '</span> ' . $propTitle . ' </a></h2>
<p class="sot">' . $sot . '</p>
<p class="strapline">' . $row['dea_strapline'] . '</p>
<p class="link"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html">Full details</a></p>
</div>
</li>

';
			unset($class);
		}
	}

	require_once 'Pager/Pager.php';
	$params = array(
		'mode'     => 'Jumping',
		'perPage'  => 15,
		'delta'    => 100,
		'itemData' => $data,
		'path'     => $CONFIG['SITE_URL'] . 'test.html',
		'append'   => true,
		'fileName' => '?pageID=%d'
	);
	$pager  = & Pager::factory($params);
	$data   = $pager->getPageData();
	$links  = $pager->getLinks();

	if ($pager->numItems()) {

		if (!$links['back']) {
			$back = "";
		} else {
			$back = '<a href="' . $CONFIG['SITE_URL'] . 'test.html?pageID=' . $pager->getPreviousPageID() . '&amp;' . $strPageLink . '">&laquo; Prev Page</a>';
		}
		if (!$links['next']) {
			$next = "";
		} else {
			$next = '<a href="' . $CONFIG['SITE_URL'] . 'test.html?pageID=' . $pager->getNextPageID() . '&amp;' . $strPageLink . '">Next Page &raquo;</a>';
		}

		$pages = '
<p>Found ' . $pager->numItems() . ' Properties</p>
';
		if ($pager->numPages() > 1) {
			$pages .= '<p>Page: ' . str_replace(array('&nbsp;', '/index.php'), array(
																					' ', ''
																			   ), $links['pages']) . '</p>';
		}

		$top = '
<div class="top">
<p class="right">' . $back . ' &nbsp; ' . $next . '</p>
<p>Page: ' . $pager->getCurrentPageID() . ' of ' . $pager->numPages() . '</p>

</div>
';

		foreach ($data as $output) {
			$results .= $output;
		}

		$bottom = '
<div class="bottom">
<p class="right">' . $back . ' &nbsp; ' . $next . '</p>
<p>Page: ' . $pager->getCurrentPageID() . ' of ' . $pager->numPages() . '</p>
</div>
';

	} else { // no results

		// no results

		// pages table cells
		$pages = '<p>Found 0 Properties</p>';

		// no results message
		$results = '
<li>
<h2>No Records Found</h2>
<p>Your search returned no records.</p>
<p>Please refine your search and try again.</p>
<p>Click <a href="' . $CONFIG['SITE_URL'] . 'lettings.html">here</a> to view all property
for sale, or click <a href="javascript:history.go(-1);">here</a>
to go back.</p>
</li>';
	}

	$pageLink               = $CONFIG['SITE_URL'] . 'lettings.html?pageID=' . $_GET['pageID'] . '&amp;' . $strPageLink;
	$_SESSION['searchPage'] = $pageLink;

	// build the form fields.
	$branches = array(
		1 => 'Camberwell',
		2 => 'Sydenham'
	);
	foreach ($branches as $key => $val) {
		$render_branches .= '<option value="' . $key . '"';
		if ($key == $_GET['branch']) {
			$render_branches .= ' selected="selected"';
		}
		$render_branches .= '>' . $val . '</option>' . "\n";
	}

	$propertytypes = array(
		'Any'       => 'Any',
		'House'     => 'House',
		'Apartment' => 'Apartment',
		'Room'      => 'House/Flat Share'
	);
	foreach ($propertytypes as $key => $val) {
		$render_propertytypes .= '<option value="' . $key . '"';
		if ($key == $strPropType) {
			$render_propertytypes .= ' selected="selected"';
		}
		$render_propertytypes .= '>' . $val . '</option>' . "\n";
	}

	$minprices_pcm = array(
		400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2100, 2200,
		2300, 2400, 2500, 2750, 3000, 3250, 3500
	);
	foreach ($minprices_pcm as $key) {
		$render_minprices_pcm .= '<option value="' . $key . '"';
		if ($key == $strMinPrice) {
			$render_minprices_pcm .= ' selected="selected"';
		}
		$render_minprices_pcm .= '>' . format_price($key) . '</option>';
		$render_minprices_pcm_js .= 'AddToOptionList(document.WS_Form.minp, "' . $key . '", "\u00A3' . number_format($key, 0, '', ',') . '");';
	}

	$maxprices_pcm = array(
		400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2100, 2200,
		2300, 2400, 2500, 2750, 3000, 3250, 3500
	);
	foreach ($maxprices_pcm as $key) {
		$render_maxprices_pcm .= '<option value="' . $key . '"';
		if ($key == $strMaxPrice) {
			$render_maxprices_pcm .= ' selected="selected"';
		}
		$render_maxprices_pcm .= '>' . format_price($key) . '</option>';
		$render_maxprices_pcm_js .= 'AddToOptionList(document.WS_Form.maxp, "' . $key . '", "\u00A3' . number_format($key, 0, '', ',') . '");';
	}

	$minprices_pw = array(100, 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1250, 1500, 2000);
	foreach ($minprices_pw as $key) {
		$render_minprices_pw .= '<option value="' . $key . '"';
		if ($key == $strMinPrice) {
			$render_minprices_pw .= ' selected="selected"';
		}
		$render_minprices_pw .= '>' . format_price($key) . '</option>';
		$render_minprices_pw_js .= 'AddToOptionList(document.WS_Form.minp, "' . $key . '", "\u00A3' . number_format($key, 0, '', ',') . '");';
	}
	$maxprices_pw = array(100, 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1250, 1500, 2000);
	foreach ($maxprices_pw as $key) {
		$render_maxprices_pw .= '<option value="' . $key . '"';
		if ($key == $strMaxPrice) {
			$render_maxprices_pw .= ' selected="selected"';
		}
		$render_maxprices_pw .= '>' . format_price($key) . '</option>';
		$render_maxprices_pw_js .= 'AddToOptionList(document.WS_Form.maxp, "' . $key . '", "\u00A3' . number_format($key, 0, '', ',') . '");';
	}

	if ($_GET['prices'] == 'pcm') {
		$render_maxprices = $render_maxprices_pcm;
		$render_minprices = $render_minprices_pcm;
	} else {
		$render_maxprices = $render_maxprices_pw;
		$render_minprices = $render_minprices_pw;
	}

	$minbedrooms = array(
		0 => 'Studio',
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5
	);
	foreach ($minbedrooms as $key => $val) {
		$render_minbedrooms .= '<option value="' . $key . '"';
		if ($key == $strMinBedrooms) {
			$render_minbedrooms .= ' selected="selected"';
		}
		$render_minbedrooms .= '>' . $val . '</option>' . "\n";
	}
	$maxbedrooms = array(
		99 => 'Max',
		0  => 'Studio',
		1  => 1,
		2  => 2,
		3  => 3,
		4  => 4,
		5  => 5
	);
	foreach ($maxbedrooms as $key => $val) {
		$render_maxbedrooms .= '<option value="' . $key . '"';
		if ($key == $strMaxBedrooms) {
			$render_maxbedrooms .= ' selected="selected"';
		}
		$render_maxbedrooms .= '>' . $val . '</option>' . "\n";
	}

	/*
$sortbys = array('Price'=>'Price','Date'=>'Date','Street'=>'Street');
foreach ($sortbys as $key=>$val) {
	$render_sortbys .= '<label for="'.$key.'"><input type="radio" name="o" value="'.$key.'" id="'.$key.'"';
	if ($key == $strOrderBy) {
		$render_sortbys .= ' checked="checked"';
		}
	$render_sortbys .= ' />'.$val.'</label> '."\n";
	}
*/

	$sortbys = array(
		'Price'  => 'Price',
		'Date'   => 'Date',
		'Street' => 'Street'
	);
	$sortbys = array(
		'Price'    => 'Price (highest first)',
		'PriceASC' => 'Price (lowest first)',
		'Date'     => 'Date',
		'Street'   => 'Street'
	);
	foreach ($sortbys as $key => $val) {
		$render_sortbys .= '<option value="' . $key . '"';
		if ($key == $strOrderBy) {
			$render_sortbys .= ' selected="selected"';
		}
		$render_sortbys .= '>' . $val . '</option>' . "\n";
	}

	$shows = array(
		'All'       => 'All',
		'Available' => 'Available'
	);
	foreach ($shows as $key => $val) {
		$render_shows .= '<label for="' . $key . '"><input type="radio" name="s" value="' . $key . '" id="' . $key . '"';
		if ($key == $strStatus) {
			$render_shows .= ' checked="checked"';
		}
		$render_shows .= ' />' . $val . '</label> ' . "\n";
	}

	$priceFormats = array(
		'pw'  => 'Per Week',
		'pcm' => 'Per Month'
	);
	foreach ($priceFormats as $key => $val) {
		$render_prices .= '<label for="' . $key . '"><input type="radio" name="prices" value="' . $key . '" id="' . $key . '"';
		if ($key == $strPrices) {
			$render_prices .= ' checked="checked"';
		}
		$render_prices .= ' />' . $val . '</label> ' . "\n";
	}

	if ($_GET['feature']) {
		$checkStudent = ' checked="checked"';
	}

	$form = '
<script type="text/javascript">

function ClearOptions(OptionList) {
   for (x = OptionList.length; x >= 0; x--) {
      OptionList[x] = null;
   }
}

function AddToOptionList(OptionList, OptionValue, OptionText) {
   OptionList[OptionList.length] = new Option(OptionText, OptionValue);
}




function PopulatePrices() {

   var minp = document.WS_Form.minp;
   var maxp = document.WS_Form.maxp;

   // Clear out the list
   ClearOptions(document.WS_Form.minp);
   ClearOptions(document.WS_Form.maxp);

   if ($("input:radio[name=prices]:checked").val() == "pw") {
   	  AddToOptionList(document.WS_Form.minp, "0", "No Minimum");
      ' . $render_minprices_pw_js . '

   	  AddToOptionList(document.WS_Form.maxp, "999999999", "No Maximum");
	  ' . $render_maxprices_pw_js . '

   }

   if ($("input:radio[name=prices]:checked").val() == "pcm") {
   	  AddToOptionList(document.WS_Form.minp, "0", "No Minimum");
      ' . $render_minprices_pcm_js . '

	  AddToOptionList(document.WS_Form.maxp, "999999999", "No Maximum");
	  ' . $render_maxprices_pcm_js . '

   }


}





$(document).ready(function(){


$("input:radio[name=prices]").change(function(){
	PopulatePrices();
	});

});
</script>
<form method="get" name="WS_Form" >
<p>Type of Property<br/>
<select name="t" class="formwide">
' . $render_propertytypes . '
</select> </p>
<p>Minimum Price<br/>
<select class="formwide" onchange="WSMax(this.form,0);" name="minp" id="minp">
<option value="0">No Minimum</option>
' . $render_minprices . '
</select></p>
<p>Maximum Price<br/>
<select class="formwide" onchange="WSMin(this.form,0);" name="maxp" id="maxp">
<option value="999999999">No Maximum</option>
' . $render_maxprices . '
</select></p>

<p>Display Prices<br/>
' . $render_prices . '
</p>

<p>Bedrooms<br/>
<select name="minbeds" class="formnarrow">
' . $render_minbedrooms . '
</select> to
<select name="maxbeds" class="formnarrow">
' . $render_maxbedrooms . '
</select></p>
<p>Street or Area<br/>
<input type="text" name="kw" class="formwide" value="' . $strKeywords . '" style="width:177px;" /></p>
<p>Sort By<br/>
<select name="o" class="formwide">
' . $render_sortbys . '
</select></p>
<p>Show<br/>
' . $render_shows . '</p>
<p><label><input type="checkbox" name="feature" value="Student Friendly"' . $checkStudent . ' />Student Friendly </label></p>
<div class="button"><input type="submit" value="Search" style="width: 100px; font-weight: bold;" /></div>
<div class="button"><input type="button" style="width: 100px" value="Reset" onclick="WSReset();" /></div>
</form>';

	return '
<div id="searchResults">
' . $top . '
<ul>
' . $results . '
</ul>
' . $bottom . '
</div>

<div id="rightColumn">
<h5>Property Search Results</h5>
<div class="box pagination">' . $pages . '</div>

<h5>Property Search Lettings</h5>
' . $form . '
</div>


';

}

function propertyNotFound()
{

	header("HTTP/1.0 404 Not Found");
	echo '<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3c.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>Property Not Found</title>
</head><body>
<h1>Property not found</h1>
<p>The property you are trying to view could not be found. It may have been removed from our web site.</p>
<p>Please <a href="javascript:history.go(-1);">go back</a> and try again, or visit our <a href="/">home page<a/></p>
</body></html>';
	exit;
}

function searchDivert()
{

	if ($_POST['Sales']) {
		header("Location:sales.html?kw=" . $_POST['kw']);
		exit;
	} elseif ($_POST['Lettings']) {
		header("Location:lettings.html?kw=" . $_POST['kw']);
		exit;
	} else {
		header("Location:index.html");
		exit;
	}
}

function latestProperty($dept = 'sales')
{

	global $db, $CONFIG;

	// unset last search
	unset($_SESSION['searchPage']);

	if ($dept == 'sales') {
		$sql = "SELECT
		CONCAT(pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
		dea_id,dea_type,dea_marketprice,dea_bedroom,
		med_file,med_title,pty_title
		FROM deal
		LEFT JOIN property ON deal.dea_prop = property.pro_id
		LEFT JOIN ptype ON deal.dea_ptype = ptype.pty_id
		LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal' AND med_order = 1 AND med_type= 'Photograph'
		WHERE (deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Exchanged') AND
		dea_type = 'Sales' AND underTheRadar <> 1
		GROUP BY deal.dea_id
		ORDER BY dea_launchdate DESC
		LIMIT 5";

		$q = $db->query($sql);
		while ($row = $q->fetchRow()) {

			if (!$first) {
				$first    = '<a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_small.jpg', $row['med_file']) . '" alt="' . $row['pro_addr'] . '" /></a>';
				$selected = ' class="selected"';
			} else {
				unset($selected);
			}
			$list .= '<li' . $selected . ' id="deal_' . $row['dea_id'] . '"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html">' . $row['pro_addr'] . ' ' . format_price($row['dea_marketprice']) . '</a></li>' . "\n";
			$preload .= '"' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_small.jpg', $row['med_file']) . '",';

		}

		$output = '
<script type="text/javascript">
jQuery.preLoadImages(' . remove_lastchar($preload, ",") . ');
</script>
<div id="latestImageSales">' . $first . '</div>
<h4>New in Sales</h4>
<ol>
' . $list . '</ol>';

	} elseif ($dept == 'lettings') {
		$sql = "SELECT
		CONCAT(pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
		dea_id,dea_type,dea_marketprice,dea_bedroom,
		med_file,med_title,pty_title
		FROM deal
		LEFT JOIN property ON deal.dea_prop = property.pro_id
		LEFT JOIN ptype ON deal.dea_ptype = ptype.pty_id
		LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal' AND med_order = 1 AND med_type= 'Photograph'
		WHERE (deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Exchanged') AND
		dea_type = 'Lettings' AND underTheRadar <> 1
		GROUP BY deal.dea_id
		ORDER BY dea_launchdate DESC
		LIMIT 5";
		$q   = $db->query($sql);
		while ($row = $q->fetchRow()) {

			if (!$first) {
				$first    = '<a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_small.jpg', $row['med_file']) . '" alt="' . $row['pro_addr'] . '" /></a>';
				$selected = ' class="selected"';
			} else {
				unset($selected);
			}
			$list .= '<li' . $selected . ' id="deal_' . $row['dea_id'] . '"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html">' . $row['pro_addr'] . ' ' . format_price($row['dea_marketprice']) . 'p/w</a></li>' . "\n";
			$preload .= '"' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_small.jpg', $row['med_file']) . '",';

		}

		$output = '
<script type="text/javascript">
jQuery.preLoadImages(' . remove_lastchar($preload, ",") . ');
</script>
<div id="latestImageLettings">' . $first . '</div>
<h4>New in Lettings</h4>
<ol>
' . $list . '</ol>';

	}

	return $output;
}

function mostViewedProperty($dept = 'sales')
{

	global $db, $CONFIG;

	if ($dept == 'lettings') {
		$type = 'Lettings';
	} else {
		$type = 'Sales';
	}

	// most viewed property from preceeding sunday for 7 previous days
	$endDate   = strtotime('today + 1 day');
	$startDate = $endDate - (7 * 24 * 60 * 60);

	$sql = "SELECT COUNT(*) as hits, deal.* ,CONCAT(pro_addr3,', ',area.are_title,' ',LEFT(pro_postcode, 4)) AS pro_addr, media.*
	FROM propertyviews
	LEFT JOIN deal ON propertyviews.dea_id = deal.dea_id
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN area ON property.pro_area = area.are_id
	LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal' AND med_order = 1 AND med_type= 'Photograph'
	WHERE
	datetime > '" . date('Y-m-d H:i:s', $startDate) . "' AND datetime < '" . date('Y-m-d H:i:s', $endDate) . "' AND
	dea_type = '$type' AND
	(deal.dea_status = 'Available') AND underTheRadar <> 1
	GROUP BY propertyviews.dea_id
	ORDER BY hits DESC
	LIMIT 1";

	//datetime > '".date('Y-m-d H:i:s',$startDate)."' AND datetime < '".date('Y-m-d H:i:s',$endDate)."' AND
	$q = $db->query($sql);
	while ($row = $q->fetchRow()) {

		if ($type == 'Lettings') {

			$output = '<div id="indexPromoPopularLettings" class="indexPromoBox">

<div class="latestImage" style="display: block;"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_small.jpg', $row['med_file']) . '" alt="' . $row['pro_addr'] . '" /></a></div>
<h4>Most Viewed This Week</h4>
<h5>' . $row['pro_addr'] . '</h5>
<p>' . $row['dea_strapline'] . '</p>
<p>' . format_price($row['dea_marketprice']) . 'p/w</p>
<h6 class="mostViewedFullList ' . $dept . '"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html">Full Details</a></h6>
</div>';
		} else {

			$output = '<div id="indexPromoPopularSales" class="indexPromoBox">

<div class="latestImage" style="display: block;"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_small.jpg', $row['med_file']) . '" alt="' . $row['pro_addr'] . '" /></a></div>
<h4>Most Viewed This Week</h4>
<h5>' . $row['pro_addr'] . '</h5>
<p>' . $row['dea_strapline'] . '</p>
<p>' . format_price($row['dea_marketprice']) . '</p>
<h6 class="mostViewedFullList ' . $dept . '"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html">Full Details</a></h6>
</div>';
		}
	}

	return $output;

}

function top20($dept = 'sales')
{

	global $db, $CONFIG;

	// unset last search
	unset($_SESSION['searchPage']);

	if ($dept == 'sales') {
		$type = 'Sales';
	} elseif ($dept == 'lettings') {
		$type = 'Lettings';
	} else {
		return;
	}
	// most viewed property from preceeding sunday for 7 previous days
	$endDate   = strtotime('last Sunday');
	$startDate = $endDate - (7 * 24 * 60 * 60);

	$sql = "SELECT COUNT(*) as hits, deal.* ,CONCAT(pro_addr3,', ',area.are_title,' ',LEFT(pro_postcode, 4)) AS pro_addr, media.*
	FROM propertyviews
	LEFT JOIN deal ON propertyviews.dea_id = deal.dea_id
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN area ON property.pro_area = area.are_id
	LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal' AND med_order = 1 AND med_type= 'Photograph'
	WHERE
	datetime > '" . date('Y-m-d H:i:s', $startDate) . "' AND datetime < '" . date('Y-m-d H:i:s', $endDate) . "' AND
	dea_type = '$type' AND
	(deal.dea_status = 'Available')
	GROUP BY propertyviews.dea_id
	ORDER BY hits DESC
	LIMIT 20";

	$q = $db->query($sql);
	while ($row = $q->fetchRow()) {

		if (!$first) {
			$first    = '<a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"><img src="' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_full.jpg', $row['med_file']) . '" alt="' . $row['pro_addr'] . '" /></a>';
			$selected = ' class="selected"';
		} else {
			unset($selected);
		}

		if ($row['dea_type'] == 'Lettings') {
			$suffix = 'p/w';
		} else {
			unset($suffix);
		}

		if ($row['dea_status'] == 'Under Offer') {
			$class = ' class="uo"';
		} else {
			unset($class);
		}

		$list .= '<li' . $selected . ' id="deal_' . $row['dea_id'] . '"><a href="' . $CONFIG['SITE_URL'] . 'details/' . $row['dea_id'] . '.html"' . $class . '>' . $row['pro_addr'] . ' ' . format_price($row['dea_marketprice']) . $suffix . '</a></li>' . "\n";
		$preload .= '"' . IMAGE_URL_PROPERTY . $row['dea_id'] . '/' . str_replace('.jpg', '_full.jpg', $row['med_file']) . '",';

	}

	$output = '
<script type="text/javascript">
jQuery.preLoadImages(' . remove_lastchar($preload, ",") . ');
</script>
<div id="top20Image">' . $first . '</div>

<h3>Most Viewed : Top 20 ' . $type . '</h3>
<ol>
' . $list . '</ol>
<p><a href="' . $CONFIG['SITE_URL'] . $dept . '.html?o=Date">Full ' . $type . ' List</a></p>';

	return $output;
}

function contactForm()
{

	global $db, $CONFIG;

	if (!$_POST) {

		$render = '
<form action="" method="post">
<div class="row">
<label>To:</label>
<select name="to">
  <option value="post">General Enquiry</option>
  <option value="cam.sale">Camberwell Sales</option>
  <option value="cam.let">Camberwell Lettings</option>
  <option value="mgmt">Property Management</option>
  <option value="syd.sale">Sydenham Sales</option>
  <option value="syd.let">Sydenham Lettings</option>
  <option value="cam.sale">Whitstable Sales</option>
</select>
</div>
<div class="row">
<label>Name:</label>
<input type="text" name="name" class="text" />
</div>
<div class="row">
<label>Email:</label>
<input type="text" name="email" class="text" />
</div>
<div class="row">
<label>Telephone number:</label>
<input type="text" name="tel" class="text" />
</div>
<div class="row">
<label>Your message:</label>
<textarea name="message"></textarea>
</div>
<div class="row">
<input type="submit" value="Send" class="submit" />
</div>
</form>
';

	} else {

		if (!clean_input($_POST['name'])) {
			$errors[] = 'Name';
		} else {
			$name = clean_input($_POST['name']);
		}
		if (!check_email($_POST['email'])) {
			$errors[] = 'Email (must be valid)';
		} else {
			$email = clean_input($_POST['email']);
		}
		if (!clean_input($_POST['tel'])) {
			$errors[] = 'Telephone number';
		} else {
			$tel = clean_input($_POST['tel']);
		}
		if (!clean_input($_POST['message'])) {
			$errors[] = 'Your Message';
		} else {
			$message = clean_input($_POST['message']);
		}

		if ($errors) {
			$render = '<h3>Error</h3><p>The following fields are mandatory:</p>' . "\n<ul>";
			foreach ($errors as $error) {
				$render .= "<li>$error</li>\n";
			}
			$render .= "</ul>\n<p>Please <a href=\"javascript:goback();\">go back</a> and try again</p>";

		} else {

			if (!$strStaff) {
				$recipient = "cam.sale@woosterstock.co.uk";
			} else {
				$recipient = strtolower($_POST['to']) . "@woosterstock.co.uk";
			}

			$EmailSubject = "Message posted from Contact Page";

			// Message to Client
			$html_body = '<html>
<head></head>
<body>
<span style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000">
<p>Hi ' . $name . ',</p>
<p>Many thanks for your message. We will be in touch shortly.</p>
</span>
' . email_footer("html", $email, $name);

			$text_body = '
Hi ' . $strName . ',

Many thanks for your message. We will be in touch shortly.

' . email_footer("text", $email, $name);

			$text = $text_body;
			$html = $html_body;
			$crlf = "\r\n";
			$hdrs = array(
				'From'    => $recipient,
				'Subject' => $EmailSubject,
			);
			$mime = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
			$mail =& Mail::factory('mail');
			$mail->send($email, $hdrs, $body);

			// Message to Office
			$EmailBody = 'Name:			' . $name . '
Tel:			' . $tel . '
Email:			' . $email . '
Message:		' . $message . '

Sent:			' . date('r') . '
';

			$text = $EmailBody;
			$crlf = "\r\n";
			$hdrs = array(
				'From'    => $email,
				'Subject' => $EmailSubject,
			);
			$mime = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
			$mail =& Mail::factory('mail');
			$mail->send($recipient, $hdrs, $body);

			$render = '<p>Thank you for your message</p>';
		}
	}
	return $render;
}

function valuationForm()
{

	global $db, $CONFIG;

	if (!$_POST) {

		$render = '
<form action="" method="post">
<div class="row">
<label>Department:</label>
<select name="dept">
<option value="sales">Sales</option>
<option value="lettings">Lettings</option>
</select>
</div>
<div class="row">
<label>Name:</label>
<input type="text" name="name" class="text" />
</div>
<div class="row">
<label>Email:</label>
<input type="text" name="email" class="text" />
</div>
<div class="row">
<label>Telephone:</label>
<input type="text" name="tel" class="text" />
</div>
<div class="row">
<label>Full address:</label>
<textarea name="address"></textarea>
</div>
<div class="row">
<label>Type of property:</label>
<select name="type">
<option value="house">House</option>
<option value="apartment">Apartment</option>
<option value="other">Other</option>
</select>
</div>
<div class="row">
<label>Prefered Date/Time:</label>
<textarea name="datetime"></textarea>
</div>

<div class="row">
<input type="submit" value="Send" class="submit" />
</div>
</form>
';

	} else {

		if (!clean_input($_POST['name'])) {
			$errors[] = 'Name';
		} else {
			$name = clean_input($_POST['name']);
		}
		if (!check_email($_POST['email'])) {
			$errors[] = 'Email (must be valid)';
		} else {
			$email = clean_input($_POST['email']);
		}
		if (!clean_input($_POST['tel'])) {
			$errors[] = 'Telephone';
		} else {
			$tel = clean_input($_POST['tel']);
		}
		if (!clean_input($_POST['address'])) {
			$errors[] = 'Address';
		} else {
			$address = clean_input($_POST['address']);
		}
		if (!clean_input($_POST['datetime'])) {
			$errors[] = 'Prefered Date/Time';
		} else {
			$datetime = clean_input($_POST['datetime']);
		}

		if ($errors) {
			$render = '<h3>Error</h3><p>The following fields are mandatory:</p>' . "\n<ul>";
			foreach ($errors as $error) {
				$render .= "<li>$error</li>\n";
			}
			$render .= "</ul>\n<p>Please <a href=\"javascript:goback();\">go back</a> and try again</p>";

		} else {

			if ($_POST['dept'] == "lettings") {
				$recipient = "cam.let@woosterstock.co.uk";
			} else {
				$recipient = "cam.sale@woosterstock.co.uk";
			}

			$EmailSubject = "Valuation Request";

			// Message to Client
			$html_body = '<html>
<head></head>
<body>
<span style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000">
<p>Hi ' . $name . ',</p>
<p>Many thanks for your valuation request. We will be in touch shortly to book an appointment.</p>
</span>
' . email_footer("html", $email, $name);

			$text_body = '
Hi ' . $strName . ',

Many thanks for your valuation request. We will be in touch shortly to book an appointment.

' . email_footer("text", $email, $name);

			$text = $text_body;
			$html = $html_body;
			$crlf = "\r\n";
			$hdrs = array(
				'From'    => $recipient,
				'Subject' => $EmailSubject,
			);
			$mime = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
			$mail =& Mail::factory('mail');
			$mail->send($email, $hdrs, $body);

			// Message to Office
			$EmailBody = 'Name:			' . $name . '
Address:		' . $address . '
Tel:			' . $tel . '
Email:			' . $email . '
Property Type:	' . $type . '
Date/Time:		' . $datetime . '

Sent:			' . date('r') . '
';

			$text = $EmailBody;
			$crlf = "\r\n";
			$hdrs = array(
				'From'    => $email,
				'Subject' => $EmailSubject,
			);
			$mime = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
			$mail =& Mail::factory('mail');
			$mail->send($recipient, $hdrs, $body);

			$render = '<p>Thank you for your message</p>';
		}
	}
	return $render;
}

function email_footer($_format, $_email, $_name = "NULL")
{

	if ($_name == "NULL") {
		$_recipient = $_email;
	} else {
		$_recipient = $_name . ' (' . $_email . ')';
	}
	$email_footer_html = '
<table width="600" border="0">
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="3"><span style="font-family:Arial, Helvetica, sans-serif; font-size:15px; font-weight: bold; color:#FF9900">Wooster &amp; Stock </span></td>
</tr>
<tr>
  <td><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Sales<br />
	Lettings<br />
	Management<br />
  <font color="#FF9900"><a href="http://www.woosterstock.co.uk" style="color:#FF9900; text-decoration:none;">woosterstock.co.uk</a></font></span></td>
  <td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Nunhead<br />
    <a href="mailto: cam@woosterstock.co.uk" style="color:#FF9900; text-decoration:none;">cam@woosterstock.co.uk</a><br />
  Sales: 020 7708 6700<br />
  Lettings: 020 77086710</span></td>
<td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Brixton<br />
	105 Acre Lane<br />
	London SW2 5TU<br />
    <a href="mailto: brx@woosterstock.co.uk" style="color:#FF9900; text-decoration:none;">brx@woosterstock.co.uk</a><br />
  Sales: 020 7925 0590<br />
  Lettings: 020 7925 0599</span></td>
  <td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Sydenham<br />
	109 Kirkdale<br />
	London SE26 4QY<br />
    <a href="mailto: syd@woosterstock.co.uk" style="color:#FF9900; text-decoration:none;">syd@woosterstock.co.uk</a><br />
  Sales: 020 8613 0060<br />
  Lettings: 020 8613 0070</span></td>
</tr>
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="3"><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#666666">This email and any files transmitted with it are confidential and intended
solely for ' . $_recipient . '. If you are not the named addressee you should not disseminate, distribute, copy or alter this email. Any views or opinions presented in this email are
solely those of the author and might not represent those of Wooster &amp; Stock. Warning: Although Wooster &amp; Stock has taken reasonable precautions to ensure no viruses are present
in this email, the company cannot accept responsibility for any loss or damage arising from the use of this email or attachments.</span></td>
</tr>
</table>
</body>
</html>
';
	$email_footer_text = '
Wooster & Stock
Sales
Lettings
Management
woosterstock.co.uk

Nunhead
cam@woosterstock.co.uk
Sales: 020 7708 6700
Lettings: 020 7708 6710

Brixton
105 Acre Lane
London SW2 5TU
brx@woosterstock.co.uk
Sales: 020 7925 0590
Lettings: 020 7925 0599

Sydenham
109 Kirkdale
London SE26 4QY
syd@woosterstock.co.uk
Sales: 020 8613 0060
Lettings: 020 8613 0070

This email and any files transmitted with it are confidential and intended solely for ' . $_recipient . '.
If you are not the named addressee you should not disseminate, distribute, copy or alter this email. Any views or
opinions presented in this email are solely those of the author and might not represent those of Wooster & Stock.
Warning: Although Wooster & Stock has taken reasonable precautions to ensure no viruses are present in this email,
the company cannot accept responsibility for any loss or damage arising from the use of this email or attachments.
';

	if ($_format == "html") {
		return $email_footer_html;
	} elseif ($_format == "text") {
		return $email_footer_text;
	}
	unset($_format, $_email, $_name, $_address);
}
