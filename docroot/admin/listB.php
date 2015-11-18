<?php
session_start();
require("global.php"); 
require("secure.php"); 


if (!in_array($s_userid,$emailers)) {
	echo "You do not have persmission to send mailshots";
	exit;
	}
	
// time limit for non-administrators only
//if ($_SESSION["s_loa"] <> "Administrator") {
	$sql = "SELECT mai_date FROM mailshot ORDER BY mai_date DESC LIMIT 1";
	$q = $db->query($sql);
	while ($row = $q->fetchRow()) {
		if ($row["mai_date"] > $dateLast20Mins) {
			echo 'Only one mailshot is allowed every 20 minutes. Last one sent '.$row["mai_date"];
			//exit;
			}
		}
//	}

if (!$_GET["action"]) {
	/*
	$prop_id = $_GET["propID"];
	
	$sql = "SELECT * FROM mailshot, admin WHERE mai_userid = adm_id AND mai_prop = $prop_id ORDER BY mai_date DESC";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("mailshot select error: ".$q->getMessage()); }

	$render = '<table width="100%" align="center" border="0" cellspacing="3" cellpadding="3">
	<tr>
	<td width="20"><strong>ID</strong></td>
	<td width="80"><strong>Date</strong></td>
	<td width="80"><strong>Type</strong></td>
	<td width="80"><strong>Sent</strong></td>
	<td width="80"><strong>Hits</strong></td>
	<td><strong>Sender</strong></td>
	</tr>';
	while ($row = $q->fetchRow()) {
		$render .= '<tr>
		<td>'.$row["mai_id"].'</td>
		<td>'.$row["mai_date"].'</td>
		<td>'.ucwords($row["mai_type"]).'</td>
		<td>'.$row["mai_count"].'</td>
		<td>'.$row["mai_hits"].'</td>
		<td>'.$row["adm_name"].'</td>
		</tr>';		
		}
		
	$render .= '</table>';	
	echo html_header("Mailing List");
	echo '
	<table width="600" align="center">
	  <tr> 
		<td><span class="pageTitle">Mailing List</span></td>
		<td align="right"><a href="property.php?propID='.$prop_id.'">Back to Property</a> &nbsp; &nbsp; <a href="index.php">Main Menu</a></td>
	  </tr>
	</table>
	<table width="600" align="center">
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td><a href="?action=compose&propID='.$prop_id.'">Compose New Mailshot</a></td>
	</tr>
	<tr>
	<td>'.
	$render.'
	</td>	
	</table>
	</body>
	</html>';
*/
	}
	
elseif ($_GET["action"] == "compose") {
// compose, count clients, user selects type of email to send

$prop_id = $_GET["propID"];

if (!$_GET["sender"]) {
	$sender = "post@woosterstock.co.uk";
	} else {
	$sender = $_GET["sender"];
	}


//$sql = "SELECT * FROM property, area, branch, proptype, leasefree WHERE property.prop_ID = $prop_id  AND property.area_ID = area.area_ID AND property.Branch = branch.Branch_ID AND property.type_id = proptype.type_ID AND property.lease_free = leasefree.id_LeaseFree LIMIT 1";		
$sql = "SELECT * FROM property, area, branch, proptype, leasefree WHERE property.prop_ID = $prop_id  AND property.area_ID = area.area_ID AND property.Branch = branch.Branch_ID AND property.type_id = proptype.type_ID LIMIT 1";		
$q = $db->query($sql);
if (DB::isError($q)) {  die("property select error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	$pc = explode(" ",$row["Postcode"]);
	$strap = $row["description"];
	$addr = $row["Address1"].", ".$row["area_title"]." ".$pc[0];
	$area = $row["area_title"];
	$price = $row["Price"];
	$beds = $row["bedrooms"];
	$sot = $row["state_of_trade_id"];
	$salelet = $row["SaleLet"];
	$type_id = $row["type_id"];
	//$tenure = $row["leaseFree_Name"];
	$branch_id = $row["Branch_ID"];
	$branch = $row["Branch_Title"];
	$tel = $row["Branch_Tel"];
	$fax = $row["Branch_Fax"];
	}
	
if (!$sot == 1) {
	echo "You can only compose mailshots for available property";
	exit;
	}

if ($type_id == 1 || $type_id == 2 || $type_id == 5 || $type_id == 7) {
	$proptype = "House";
	} elseif ($type_id == 3 || $type_id == 4 || $type_id == 6) {
	$proptype = "Apartment";
	} elseif ($type_id == 8) {
	$proptype = "Commercial";
	} elseif ($type_id == 9) {
	$proptype = "Live/Work";
	}

if ($salelet == 1) {
	$formatted_price = price_format($price)." ".$tenure;
	} elseif ($salelet == 2) {
	$formatted_price = price_format($price)."pw / ".price_format(pw2pcm($price))."pcm";	
	}	
	
$sql = "SELECT count(*) as emailCount FROM clients WHERE ";

if ($area) { 
	//$sql .= " clients.Areas LIKE '%".$area."%' AND ";
	}
if ($proptype <> "Any") {
	$sql .= " (clients.PropertyType = '".$proptype."' OR clients.PropertyType = 'Any') AND ";
	}

if ($beds) {
	$sql .= " clients.Bedrooms <= ".$beds." AND ";
	}

if ($branch_id) {
	$sql .= " (clients.Branch LIKE '%".$branch_id."%' OR clients.Branch = 0 OR clients.Branch IS NULL) AND ";
	}
	
if ($salelet == 1) {
	if ($price) {
		$sql .= " clients.MinPrice <= ".$price." AND clients.MaxPrice >=".$price." AND ";
		}
	$sql .= " clients.Status = 'L'";
	
	} elseif ($salelet == 2) {

	if ($price) {
		$sql .= " clients.MinPriceLet <= ".$price." AND clients.MaxPriceLet >=".$price." AND ";
		}
	$sql .= " clients.StatusLet = 'L'";
	}



echo "<!--".$sql."-->";
$q = $db->query($sql);
if (DB::isError($q)) {  die("client select error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	//$render .= $row["Email"]."\n";
	//$emailCount++;
	$emailCount = $row["emailCount"];
	}



// ------------- body ---------------
$render_body = $strap.'
'.$addr.'
'.$formatted_price.'

http://www.woosterstock.co.uk/go.php?id=[TBA]

To arrange a viewing please call our '.$branch.' branch on '.$tel.'

If you do not wish to receive any further emails from us, please reply to this message with the word REMOVE in the subject line.';

echo html_header("Mailing List");
echo '

	<table width="600" align="center">
	  <tr> 
		<td><span class="pageTitle">Mailing List</span></td>
		<td align="right"><a href="property.php?propID='.$prop_id.'">Back to Property</a> &nbsp; &nbsp; <a href="index.php">Main Menu</a></td>
	  </tr>
	</table>
<table width="500" align="center">
<tr>
<td>
<form method="get">
<p><br>'.$emailCount.' matching clients found</p>
<table border="0">
<tr>
<td>
<p>Subject:</p>
<input type="radio" name="type" value="new">A new property has been added!<br>
<input type="radio" name="type" value="reduced">A property has been reduced in price<br>
<input type="radio" name="type" value="back">A property has come back on the market<br>
<p>Message:</p>
<textarea name="message" style="width:500px; height:250px;"';
//if ($_SESSION["s_loa"] <> "Administrator") { echo ' readonly="true" '; }
echo '>'.$render_body.'</textarea>
</td>
</tr>
<tr>
<td align="center"><input type="submit" value="    Preview and Send    ">
<input type="hidden" name="prop_id" value="'.$prop_id.'">
<input type="hidden" name="sender" value="'.$sender.'"></td>
<input type="hidden" name="action" value="preview">
</tr>
</table></td>
</tr>
</table>
</form>
</body>
</html>
';
}
 
elseif ($_GET["action"] == "preview") {

$prop_id = $_GET["prop_id"];
$type = $_GET["type"];
$sender = $_GET["sender"];
$subject = $_GET["type"];
$message = trim($_GET["message"]);
$userid = $_SESSION["s_userid"];

if (!$subject) { 
	echo "Gotta choose a subject"; 
	exit;
	}

//$sql = "SELECT * FROM property, area, branch, proptype, leasefree WHERE property.prop_ID = $prop_id  AND property.area_ID = area.area_ID AND property.Branch = branch.Branch_ID AND property.type_id = proptype.type_ID AND property.lease_free = leasefree.id_LeaseFree LIMIT 1";		
$sql = "SELECT * FROM property, area, branch, proptype WHERE property.prop_ID = $prop_id  AND property.area_ID = area.area_ID AND property.Branch = branch.Branch_ID AND property.type_id = proptype.type_ID LIMIT 1";		
$q = $db->query($sql);
if (DB::isError($q)) {  die("property select error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	$pc = explode(" ",$row["Postcode"]);
	$strap = $row["description"];
	$addr = $row["Address1"].", ".$row["area_title"]." ".$pc[0];
	$area = $row["area_title"];
	$price = $row["Price"];
	$beds = $row["bedrooms"];
	$sot = $row["state_of_trade_id"];
	$salelet = $row["SaleLet"];
	$type_id = $row["type_id"];
	//$tenure = $row["leaseFree_Name"];
	$branch = $row["Branch_Title"];
	$tel = $row["Branch_Tel"];
	$fax = $row["Branch_Fax"];
	}
	
if (!$sot == 1) {
	echo "You can only compose mailshots for available property";
	exit;
	}

if ($type_id == 1 || $type_id == 2 || $type_id == 5 || $type_id == 7) {
	$proptype = "House";
	} elseif ($type_id == 3 || $type_id == 4 || $type_id == 6) {
	$proptype = "Apartment";
	} elseif ($type_id == 8) {
	$proptype = "Commercial";
	} elseif ($type_id == 9) {
	$proptype = "Live/Work";
	}

if ($salelet == 1) {
	$formatted_price = price_format($price); //." ".$tenure;
	} elseif ($salelet == 2) {
	$formatted_price = price_format($price)."pw / ".price_format(pw2pcm($price))."pcm";	
	}	
	
$sql = "SELECT * FROM clients WHERE ";

if ($area) { 
	//$sql .= " clients.Areas LIKE '%".$area."%' AND ";
	}
if ($proptype <> "Any") {
	$sql .= " (clients.PropertyType = '".$proptype."' OR clients.PropertyType = 'Any') AND ";
	}

if ($beds) {
	//$sql .= " clients.Bedrooms >= ".$beds." AND ";
	}

if ($salelet == 1) {
	if ($price) {
		$sql .= " clients.MinPrice <= ".$price." AND clients.MaxPrice >=".$price." AND ";
		}
	$sql .= " clients.Status = 'L'";
	
	} elseif ($salelet == 2) {

	if ($price) {
	$sql .= " clients.MinPriceLet <= ".$price." AND clients.MaxPriceLet >=".$price." AND ";
	}
	$sql .= " clients.StatusLet = 'L'";
	}
	
echo "<!--".$sql."-->";
$q = $db->query($sql);
if (DB::isError($q)) {  die("client select error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	$render .= $row["Email"]."\n";
	$emailCount++;
	}
echo '
<!--'.$emailCount.'-->
';
$render .= "mark.d.williams@btinternet.com";

$list_file = "/home/woosterstock/cgi-bin/mailinglist/list.txt";
/*
if (is_writable($list_file)) {
	if (!$handle = fopen($list_file, 'w')) { 
		echo "Could not open list.txt ";
		exit; 
		}	
	if (fwrite($handle, $render) === FALSE) {
		echo "Error writing to list.txt ";
		exit;
		}
	//echo '<p>Mailing List created ('.$emailCount.' clients)</p>';
	fclose($handle);
	}
else {
	echo "list.txt not writable";
	exit;
	}

*/
if ($type == "new") { 
	$subject = 'A new property has been added!';
	$message = 'Further to your recent enquiry, a new property which may be of interest to you has been added to our website.
	
'.$message;
	
	} elseif ($type == "reduced") { 
	$subject = 'A property has been reduced in price';
	$message = 'Further to your recent enquiry, a property which may be of interest to you has been reduced in price.
	
'.$message;
	
	} elseif ($type == "back") { 
	$subject = 'A property has come back on the market';
	$message = 'Further to your recent enquiry, a property which may be of interest to you has come back on the market.
	
'.$message;
	}


// insert into mailshot table
$sql = "INSERT INTO mailshot 
(mai_type,mai_count,mai_prop,mai_body,mai_sender,mai_userid,mai_date)
VALUES
('$type','$emailCount','$prop_id','$message.','$sender','$userid','$dateToday')";
$q = $db->query($sql);
if (DB::isError($q)) {  die("mailshot insert error: ".$q->getMessage()); }
$id = mysql_insert_id();

//$find = "Detail.php?propID=".$prop_id;
//$replace = "go.php?id=".$id;
$message = str_replace("[TBA]",$id,$message);
echo html_header("Mailing List");
echo '
<table width="600" align="center">
	  <tr> 
		<td><span class="pageTitle">Mailing List</span></td>
		<td align="right">&nbsp;</td>
	  </tr>
	</table>
<table width="500" align="center">
<tr>
<td>
<p>&nbsp;</p>
<p>The mailing list (list.txt) has been created with '.$emailCount.' clients.</p>
<p>Please carefully check the message below before sending</p>
<p><strong>Subject</strong><br>'.$subject.'</p>
<p><strong>Message</strong><br>'.str_replace("\n","<br>",$message).'</p>

<form method="post" action="/cgi-bin/mailinglist/mail-adminB.cgi">
<table width="100%">
<tr>
<td align="center">
<input type="hidden" name="subject" value="'.$subject.'">
<input type="hidden" name="message" value="'.$message.'">
<input type="hidden" name="prop_id" value="'.$prop_id.'">
<input type="hidden" name="sender" value="'.$_SESSION["s_user"].'@woosterstock.co.uk">
<input type="submit" value="    Send Mailshot    ">
</td>
</tr>
</table>
</form>
</td>
</tr>
</table>
</body>
</html>
';

}
?>