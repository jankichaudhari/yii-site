<?php
session_start();
$pageTitle = "Client";
require("global.php");
require("secure.php");

// no longer used for editing
if ($_POST["ClientID"]) {
	header("Location:client_edit.php?cli_id=".$_POST["ClientID"]);
	exit;
	}
if ($_GET["ClientID"]) {
	header("Location:client_edit.php?cli_id=".$_GET["ClientID"]);
	exit;
	}

if ($_GET["action"] == "Clear") {

$_SESSION["Client_ID"] = "";
$_SESSION["Password"] = "";
$_SESSION["Email"] = "";
$_SESSION["Email2"] = "";
$_SESSION["Salutation"] = "";
$_SESSION["Name"] = "";
$_SESSION["Address1"] = "";
$_SESSION["Address2"] = "";
$_SESSION["Address3"] = "";
$_SESSION["City"] = "";
$_SESSION["Country"] = "";
$_SESSION["Postcode"] = "";
$_SESSION["Tel"] = "";
$_SESSION["Fax"] = "";
$_SESSION["Mobile"] = "";
$_SESSION["PropertyType"] = "";
$_SESSION["MinPrice"] = "";
$_SESSION["MaxPrice"] = "";
$_SESSION["Receptions"] = "";
$_SESSION["Bedrooms"] = "";
$_SESSION["Bathrooms"] = "";
$_SESSION["Areas"] = "";
$_SESSION["Areas2"] = "";
$_SESSION["Notes"] = "";
$_SESSION["DG"] = "";
$_SESSION["GCH"] = "";
$_SESSION["Modern"] = "";
$_SESSION["Period"] = "";
$_SESSION["Tenure"] = "";
$_SESSION["Garden"] = "";
$_SESSION["Parking"] = "";
$_SESSION["BuyToLet"] = "";
$_SESSION["HeardBy"] = "";
$_SESSION["Selling"] = "";
$_SESSION["Valuation"] = "";
$_SESSION["DateCreated"] = "";
$_SESSION["DateAccessed"] = "";
$_SESSION["DateModified"] = "";
$_SESSION["IP"] = "";
$_SESSION["Agent"] = "";
$_SESSION["Status"] = "";
$_SESSION["Hits"] = "";
$_SESSION["PropertyTypeLet"] = "";
$_SESSION["BedroomsLet"] = "";
$_SESSION["MinPriceLet"] = "";
$_SESSION["MaxPriceLet"] = "";
$_SESSION["FurnishedLet"] = "";
$_SESSION["TermLet"] = "";
$_SESSION["StatusLet"] = "";
$_SESSION["Lettings"] = "";
$_SESSION["Sales"]  = "";
$_SESSION["neg"]  = "";
$_SESSION["Branch"]  = "";

if ($_GET["backto"] == "menu") {
	$goto = "index.php";
	} else {
	$goto = "client.php?Sales=".$_GET["Sales"]."&Lettings=".$_GET["Lettings"]."";
	}

header("Location:$goto");
exit;



}
elseif ($_POST["action"] == "Update") {




	if (!$_POST["ClientID"]) {
		$errors[] = "ClientID is missing";
		}
	else {
		$intClientID = $_POST["ClientID"];
		}

	if (!$_POST["Email"]) {
		$errors[] = "Email is a required field";
		}
	else {
		//if (validate_email($_POST["Email"])) {
			$Email = trim($_POST["Email"]);
			//} else {
			//$errors[] = "Email is formatted incorrectly, or does not exist";
			//}
		}

	if (!$_POST["Name"]) {
		$errors[] = "Name is a required field";
		}
	else {
		$Name = $_POST["Name"];
		}

	$Client_ID = $_POST["Client_ID"];
	$Password = $_POST["Password"];
	$Email2 = $_POST["Email2"];
	$Salutation = $_POST["Salutation"];
	$Address1 = $_POST["Address1"];
	$Address2 = $_POST["Address2"];
	$Address3 = $_POST["Address3"];
	$City = $_POST["City"];
	$Country = $_POST["Country"];
	$Postcode = strtoupper($_POST["Postcode"]);
	$Tel = $_POST["Tel"];
	$Fax = $_POST["Fax"];
	$Mobile = $_POST["Mobile"];
	$PropertyType = $_POST["PropertyType"];
	$MinPrice = $_POST["MinPrice"];
	$MaxPrice = $_POST["MaxPrice"];
	$Receptions = $_POST["Receptions"];
	$Bedrooms = $_POST["Bedrooms"];
	$Bathrooms = $_POST["Bathrooms"];

	if ($_POST["Areas"]) {
		foreach ($_POST["Areas"] as $area) {
			$AreaSQL .= $area."^";
			}
		}
	$AreaSQL = removeCharacter($AreaSQL,"^");
	$Areas2 = $_POST["Areas2"];
	$Notes = $_POST["Notes"];
	$DG = $_POST["DG"];
	$GCH = $_POST["GCH"];
	$Modern = $_POST["Modern"];
	$Period = $_POST["Period"];
	$Tenure = $_POST["Tenure"];
	$Garden = $_POST["Garden"];
	$Parking = $_POST["Parking"];
	$BuyToLet = $_POST["BuyToLet"];
	$HeardBy = $_POST["HeardBy"];
	$Selling = $_POST["Selling"];
	$Valuation = $_POST["Valuation"];
	$DateModified = $_POST["DateModified"];
	$Status = $_POST["Status"];
	$Hits = $_POST["Hits"];
	$PropertyTypeLet = $_POST["PropertyTypeLet"];
	$BedroomsLet = $_POST["BedroomsLet"];
	$MinPriceLet = $_POST["MinPriceLet"];
	$MaxPriceLet = $_POST["MaxPriceLet"];
	$FurnishedLet = $_POST["FurnishedLet"];
	$TermLet = $_POST["TermLet"];
	$StatusLet = $_POST["StatusLet"];
	$Lettings = $_POST["Lettings"];
	$Sales  = $_POST["Sales"];
	$neg  = $_POST["neg"];

	if ($_POST["Branch"]) {
		$Branch = $_POST["Branch"];
			foreach ($Branch as $b) {
			$selected_branches .= $b.",";
			}
		}

	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}

	$sql_body = "Password = '$Password',
	Email = '$Email',
	Email2 = '$Email2',
	Name = '$Name',
	Salutation = '$Salutation',
	Address1 = '$Address1',
	Address2 = '$Address2',
	Address3 = '$Address3',
	City = '$City',
	Country = '$Country',
	Postcode = '$Postcode',
	Tel = '$Tel',
	Fax = '$Fax',
	Mobile = '$Mobile',
	PropertyType = '$PropertyType',
	MinPrice = '$MinPrice',
	MaxPrice = '$MaxPrice',
	Bedrooms = '$Bedrooms',
	Areas = '$AreaSQL',
	Areas2 = '$Areas2',
	Notes = '$Notes',
	DG = '$DG',
	GCH = '$GCH',
	Modern = '$Modern',
	Period = '$Period',
	Tenure = '$Tenure',
	Garden = '$Garden',
	Parking = '$Parking',
	BuyToLet = '$BuyToLet',
	HeardBy = '$HeardBy',
	Selling = '$Selling',
	Valuation = '$Valuation',
	DateModified = '$dateToday',
	Status = '$Status',
	PropertyTypeLet = '$PropertyTypeLet',
	BedroomsLet = '$BedroomsLet',
	MinPriceLet = '$MinPriceLet',
	MaxPriceLet = '$MaxPriceLet',
	FurnishedLet = '$FurnishedLet',
	TermLet = '$TermLet',
	StatusLet = '$StatusLet',
	Branch = '$selected_branches',
	neg = '$neg'
	";
	//change_log($_SESSION["s_userid"],"clients","Client_ID",$intClientID,$sql_body,$PHPSESSID);
	$sql = "
	UPDATE clients SET ".$sql_body."
	WHERE Client_ID = $intClientID";

	$q = $db->query($sql);
	if (DB::isError($q)) {  die("update error: ".$q->getMessage()); }

	$pageTitle = "Update Client Complete";
	echo html_header($pageTitle);

	echo '
<table width="600" align="center">
  <tr>
	<td><span class="pageTitle">'.$pageTitle.'</span></td>
	<td align="right"><a href="index.php">Main Menu</a></td>
  </tr>
  <tr>
    <td colspan="2">
	  <p>&nbsp;</p>
	  <p><a href="client.php?ClientID='.$intClientID.'&searchLink='.$_POST["searchLink"].'">Edit the Client</a></p>
	  <p><a href="'.urldecode($_POST["searchLink"]).'">Back to last search</a></p>
	</td>
  </tr>
</table>
';

	}
elseif ($_POST["action"] == "Insert") {

	/*
	foreach($_POST as $key=>$val)
		{
		$_SESSION["key"] = trim($val);
		}
	echo $_SESSION["Email"];
	echo $_SESSION["Name"];
	echo $_SESSION["Address1"];
	*/


	if (!$_POST["Email"]) {
		$errors[] = "Email is a required field";
		}
	else {
		if (validate_email($_POST["Email"])) {

			$_SESSION["Email"] = trim($_POST["Email"]);
			// compare to existing client emails
			$q = $db->query("SELECT Client_ID, Email FROM clients WHERE Email = '".$Email."'");
			while ($row = $q->fetchRow()) {
				$found_email = $row["Email"];
				}
			if ($found_email) {
				$errors[] = "Email is already present in the database";
				}

			} else {
			$errors[] = "Email is formatted incorrectly, or does not exist";
			$_SESSION["Email"] = trim($_POST["Email"]);
			}
		}
	if (!$_POST["Name"]) {
		$errors[] = "Name is a required field";
		}
	else {
		$_SESSION["Name"] = $_POST["Name"];
		}


	$_SESSION["Client_ID"] = $_POST["Client_ID"];
	$_SESSION["Password"] = $_POST["Password"];
	$_SESSION["Email2"] = $_POST["Email2"];
	$_SESSION["Salutation"] = $_POST["Salutation"];
	$_SESSION["Address1"] = $_POST["Address1"];
	$_SESSION["Address2"] = $_POST["Address2"];
	$_SESSION["Address3"] = $_POST["Address3"];
	$_SESSION["City"] = $_POST["City"];
	$_SESSION["Country"] = $_POST["Country"];
	$_SESSION["Postcode"] = $_POST["Postcode"];
	$_SESSION["Tel"] = $_POST["Tel"];
	$_SESSION["Fax"] = $_POST["Fax"];
	$_SESSION["Mobile"] = $_POST["Mobile"];
	$_SESSION["PropertyType"] = $_POST["PropertyType"];
	$_SESSION["MinPrice"] = $_POST["MinPrice"];
	$_SESSION["MaxPrice"] = $_POST["MaxPrice"];
	$_SESSION["Receptions"] = $_POST["Receptions"];
	$_SESSION["Bedrooms"] = $_POST["Bedrooms"];
	$_SESSION["Bathrooms"] = $_POST["Bathrooms"];

	if ($_POST["Areas"]) {
		foreach ($_POST["Areas"] as $area) {
			$AreaSQL .= $area."^";
			}
		}
	$AreaSQL = removeCharacter($AreaSQL,"^");

	$_SESSION["Areas2"] = $_POST["Areas2"];
	$_SESSION["Notes"] = $_POST["Notes"];
	$_SESSION["DG"] = $_POST["DG"];
	$_SESSION["GCH"] = $_POST["GCH"];
	$_SESSION["Modern"] = $_POST["Modern"];
	$_SESSION["Period"] = $_POST["Period"];
	$_SESSION["Tenure"] = $_POST["Tenure"];
	$_SESSION["Garden"] = $_POST["Garden"];
	$_SESSION["Parking"] = $_POST["Parking"];
	$_SESSION["BuyToLet"] = $_POST["BuyToLet"];
	$_SESSION["HeardBy"] = $_POST["HeardBy"];
	$_SESSION["Selling"] = $_POST["Selling"];
	$_SESSION["Valuation"] = $_POST["Valuation"];
	$_SESSION["DateModified"] = $_POST["DateModified"];
	$_SESSION["Status"] = $_POST["Status"];
	$_SESSION["Hits"] = $_POST["Hits"];
	$_SESSION["PropertyTypeLet"] = $_POST["PropertyTypeLet"];
	$_SESSION["BedroomsLet"] = $_POST["BedroomsLet"];
	$_SESSION["MinPriceLet"] = $_POST["MinPriceLet"];
	$_SESSION["MaxPriceLet"] = $_POST["MaxPriceLet"];
	$_SESSION["FurnishedLet"] = $_POST["FurnishedLet"];
	$_SESSION["TermLet"] = $_POST["TermLet"];
	$_SESSION["StatusLet"] = $_POST["StatusLet"];
	$_SESSION["Lettings"] = $_POST["Lettings"];
	$_SESSION["Sales"]  = $_POST["Sales"];
	$_SESSION["neg"]  = $_POST["neg"];

	if ($_POST["Branch"]) {
		$_SESSION["Branch"] = $_POST["Branch"];
			foreach ($Branch as $b) {
			$selected_branches .= $b.",";
			}
		}
	$branch_array = explode(",",$selected_branches);

	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}

	$sql = "
	INSERT INTO clients
	(Password,Email,Email2,Name,Salutation,Address1,Address2,Address3,City,Country,
	Postcode,Tel,Fax,Mobile,PropertyType,MinPrice,MaxPrice,Receptions,Bedrooms,
	Bathrooms,Areas,Areas2,Notes,DG,GCH,Modern,Period,Tenure,Garden,Parking,
	BuyToLet,HeardBy,Selling,Valuation,DateCreated,DateModified,Status,PropertyTypeLet,
	BedroomsLet,MinPriceLet,MaxPriceLet,FurnishedLet,TermLet,StatusLet,Lettings,Sales,Branch,neg)
	VALUES
	('$Password','$Email','$Email2','$Name','$Salutation','$Address1','$Address2','$Address3','$City','$Country',
	'$Postcode','$Tel','$Fax','$Mobile','$PropertyType','$MinPrice','$MaxPrice','$Receptions','$Bedrooms',
	'$Bathrooms','$AreaSQL','$Areas2','$Notes','$DG','$GCH','$Modern','$Period','$Tenure','$Garden','$Parking',
	'$BuyToLet','$HeardBy','$Selling','$Valuation','$dateToday','$dateToday','$Status','$PropertyTypeLet',
	'$BedroomsLet','$MinPriceLet','$MaxPriceLet','$FurnishedLet','$TermLet','$StatusLet','$Lettings','$Sales','$selected_branches','$neg')
	";

	$q = $db->query($sql);
	//echo $sql;
	if (DB::isError($q)) {  die("insert error: ".$q->getMessage()); }

	$query = 'SELECT LAST_INSERT_ID()';
	$result = mysql_query($query);
	$rec = mysql_fetch_array($result);
	$insert_id = $rec[0];

	$pageTitle = "Add Client Complete";
	echo html_header($pageTitle);
	echo '
<table width="600" align="center">
  <tr>
	<td><span class="pageTitle">'.$pageTitle.'</span></td>
	<td align="right"><a href="index.php">Main Menu</a></td>
  </tr>
  <tr>
    <td colspan="2">
	  <p>&nbsp;</p>
	  <p><a href="client.php?ClientID='.$insert_id.'">Edit the client</p></p>
	  <p><a href="client.php?Sales='.$_GET["Sales"].'&Lettings='.$_GET["Lettings"].'">Add another client</a></p>
	</td>
  </tr>
</table>
';
$_SESSION["Client_ID"] = "";
$_SESSION["Password"] = "";
$_SESSION["Email"] = "";
$_SESSION["Email2"] = "";
$_SESSION["Salutation"] = "";
$_SESSION["Name"] = "";
$_SESSION["Address1"] = "";
$_SESSION["Address2"] = "";
$_SESSION["Address3"] = "";
$_SESSION["City"] = "";
$_SESSION["Country"] = "";
$_SESSION["Postcode"] = "";
$_SESSION["Tel"] = "";
$_SESSION["Fax"] = "";
$_SESSION["Mobile"] = "";
$_SESSION["PropertyType"] = "";
$_SESSION["MinPrice"] = "";
$_SESSION["MaxPrice"] = "";
$_SESSION["Receptions"] = "";
$_SESSION["Bedrooms"] = "";
$_SESSION["Bathrooms"] = "";
$_SESSION["Areas"] = "";
$_SESSION["Areas2"] = "";
$_SESSION["Notes"] = "";
$_SESSION["DG"] = "";
$_SESSION["GCH"] = "";
$_SESSION["Modern"] = "";
$_SESSION["Period"] = "";
$_SESSION["Tenure"] = "";
$_SESSION["Garden"] = "";
$_SESSION["Parking"] = "";
$_SESSION["BuyToLet"] = "";
$_SESSION["HeardBy"] = "";
$_SESSION["Selling"] = "";
$_SESSION["Valuation"] = "";
$_SESSION["DateCreated"] = "";
$_SESSION["DateAccessed"] = "";
$_SESSION["DateModified"] = "";
$_SESSION["IP"] = "";
$_SESSION["Agent"] = "";
$_SESSION["Status"] = "";
$_SESSION["Hits"] = "";
$_SESSION["PropertyTypeLet"] = "";
$_SESSION["BedroomsLet"] = "";
$_SESSION["MinPriceLet"] = "";
$_SESSION["MaxPriceLet"] = "";
$_SESSION["FurnishedLet"] = "";
$_SESSION["TermLet"] = "";
$_SESSION["StatusLet"] = "";
$_SESSION["Lettings"] = "";
$_SESSION["Sales"]  = "";
$_SESSION["neg"]  = "";
$_SESSION["Branch"]  = "";



// format and send email to appropriate branch or branches with full client details
$subject = $insert_id." - New Client Registration";
$body = "
*************************************************************
* THIS CLIENT WAS REGISTERED VIA ADMIN BY A MEMBER OF STAFF *
*************************************************************

(log into admin for full details: http://www.woosterstock.co.uk/admin/client.php?ClientID=".$insert_id.")

Client details:
-----------------------------------------------------------------------
Name:\t\t$Name
Email:\t$Email
Tel:\t\t$Tel
Date:\t\t$dateFriendly
ID:\t\t".$insert_id."

Branches selected:
-----------------------------------------------------------------------
Camberwell:\t";
if (in_array("1",$branch_array)) {
$body .= "YES";
$to = "post@woosterstock.co.uk, ";
} else {
$body .= "NO";
}

$body .= "
Shad Thames:\t";
if (in_array("3",$branch_array)) {
$body .= "YES";
$to .= "shadthames@woosterstock.co.uk, ";
} else {
$body .= "NO";
}

$body .= "
Sydenham:\t\t";
if (in_array("2",$branch_array)) {
$body .= "YES";
$to .= "sydenham@woosterstock.co.uk, ";
} else {
$body .= "NO";
}


if ($Status == "L") {
$body .= "

Sales Property requirements:
-----------------------------------------------------------------------
PropType:\t$PropertyType
MinPrice:\t$MinPrice
MaxPrice:\t$MaxPrice
Bedrooms:\t$Bedrooms
Postition:\t$Selling
";

	}

if ($StatusLet == "L") {

// temporary send all lettings related enqurires to shad
if ($Status !== "L") {
	$to = "shadthames@woosterstock.co.uk";
	} else {
	$to .= "shadthames@woosterstock.co.uk";
	}

$body .= "

Lettings Property requirements:
-----------------------------------------------------------------------
PropType:\t$PropertyTypeLet
MinPrice:\t$MinPriceLet
MaxPrice:\t$MaxPriceLet
Bedrooms:\t$BedroomsLet
Term:\t\t$TermLet
Furnished:\t$FurnishedLet
";
	}

$headers 	= "Content-Type:text/plain;CHARSET=iso-8859-8-i\r\n";
$headers	.="From:$Email\r\n";
mail($to, $subject, $body, $headers);



}
else { // if form is not submitted


	if (!$_GET["ClientID"]) { // if id not entered, show insert form
		$action = "Insert";
		$intState = 11; // set status to pending
		$pageTitle = "Insert New Client";
		if ($_GET["Sales"] == "L") { $Status = "L"; } else { $Status = "S"; }
		if ($_GET["Lettings"] == "L") { $StatusLet = "L"; } else { $StatusLet = "S"; }

		} else {
		$intClientID = $_GET["ClientID"];
		$action = "Update";
		$pageTitle = "Edit Client Details";

		$sql = "SELECT * FROM clients WHERE clients.Client_ID = $intClientID";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }

		while ($row = $q->fetchRow()) {

		$_SESSION["Client_ID"] = $row["Client_ID"];
		$_SESSION["Password"] = $row["Password"];
		$_SESSION["Email"] = $row["Email"];
		$_SESSION["Email2"] = $row["Email2"];
		$_SESSION["Salutation"] = $row["Salutation"];
		$_SESSION["Name"] = $row["Name"];
		$_SESSION["Address1"] = $row["Address1"];
		$_SESSION["Address2"] = $row["Address2"];
		$_SESSION["Address3"] = $row["Address3"];
		$_SESSION["City"] = $row["City"];
		$_SESSION["Country"] = $row["Country"];
		$_SESSION["Postcode"] = $row["Postcode"];
		$_SESSION["Tel"] = $row["Tel"];
		$_SESSION["Fax"] = $row["Fax"];
		$_SESSION["Mobile"] = $row["Mobile"];
		$_SESSION["PropertyType"] = $row["PropertyType"];
		$_SESSION["MinPrice"] = $row["MinPrice"];
		$_SESSION["MaxPrice"] = $row["MaxPrice"];
		$_SESSION["Receptions"] = $row["Receptions"];
		$_SESSION["Bedrooms"] = $row["Bedrooms"];
		$_SESSION["Bathrooms"] = $row["Bathrooms"];
		$_SESSION["Areas"] = $row["Areas"];
		$_SESSION["Areas2"] = $row["Areas2"];
		$_SESSION["Notes"] = $row["Notes"];
		$_SESSION["DG"] = $row["DG"];
		$_SESSION["GCH"] = $row["GCH"];
		$_SESSION["Modern"] = $row["Modern"];
		$_SESSION["Period"] = $row["Period"];
		$_SESSION["Tenure"] = $row["Tenure"];
		$_SESSION["Garden"] = $row["Garden"];
		$_SESSION["Parking"] = $row["Parking"];
		$_SESSION["BuyToLet"] = $row["BuyToLet"];
		$_SESSION["HeardBy"] = $row["HeardBy"];
		$_SESSION["Selling"] = $row["Selling"];
		$_SESSION["Valuation"] = $row["Valuation"];
		$_SESSION["DateCreated"] = $row["DateCreated"];
		$_SESSION["DateAccessed"] = $row["DateAccessed"];
		$_SESSION["DateModified"] = $row["DateModified"];
		$_SESSION["IP"] = $row["IP"];
		$_SESSION["Agent"] = $row["Agent"];
		$_SESSION["Status"] = $row["Status"];
		$_SESSION["Hits"] = $row["Hits"];
		$_SESSION["PropertyTypeLet"] = $row["PropertyTypeLet"];
		$_SESSION["BedroomsLet"] = $row["BedroomsLet"];
		$_SESSION["MinPriceLet"] = $row["MinPriceLet"];
		$_SESSION["MaxPriceLet"] = $row["MaxPriceLet"];
		$_SESSION["FurnishedLet"] = $row["FurnishedLet"];
		$_SESSION["TermLet"] = $row["TermLet"];
		$_SESSION["StatusLet"] = $row["StatusLet"];
		$_SESSION["Lettings"] = $row["Lettings"];
		$_SESSION["Sales"]  = $row["Sales"];
		$_SESSION["neg"]  = $row["neg"];
		$_SESSION["Branch"]  = $row["Branch"];
			}
		}

	if (!$_SESSION["Country"]) { $_SESSION["Country"] = 217; }
	$sqlCountry = "SELECT * FROM country ORDER BY Country_Title";
	$qCountry = $db->query($sqlCountry);
	if (DB::isError($qCountry)) {  die("insert error: ".$qCountry->getMessage()); }

	while ($rowCountry = $qCountry->fetchRow()) {
		$RenderCountry .= '<option value="'.$rowCountry["Country_ID"].'"';
		if ($_SESSION["Country"] == $rowCountry["Country_ID"]) {
			$RenderCountry .= ' selected';
			}
		$RenderCountry .= '>'.$rowCountry["Country_Title"].'</option>';
		}

	$sqlFound = "SELECT * FROM foundby  WHERE FoundBy_ID != 43 ORDER BY FoundBy_Type";
	$qFound = $db->query($sqlFound);
	if (DB::isError($qFound)) {  die("insert error: ".$qFound->getMessage()); }

	while ($rowFound = $qFound->fetchRow()) {
		$RenderFound .= '<option value="'.$rowFound["FoundBy_ID"].'"';
		if ($_SESSION["HeardBy"] == $rowFound["FoundBy_ID"]) {
			$RenderFound .= ' selected';
			}
		$RenderFound .= '>'.$rowFound["FoundBy_Title"].'</option>';
		}

	$Areas = str_replace(", ","^",$_SESSION["Areas"]);
	$Areas = explode("^",$Areas);
	$sqlArea = "SELECT * FROM area ORDER BY area_title";
	$qArea = $db->query($sqlArea);
	if (DB::isError($qArea)) {  die("insert error: ".$qArea->getMessage()); }
	$RenderArea = '<table width="100%"><tr>';
	while ($rowArea = $qArea->fetchRow()) {

		$RenderArea .= '<td><input type="checkbox" name="Areas[]" value="'.$rowArea["area_title"].'"';
		if (in_array(trim($rowArea["area_title"]),$Areas)) {
			$RenderArea .= ' checked';
			}
		$RenderArea .= '>'.$rowArea["area_title"].'</td>';

		$i++;
		if ($i % 5 == 0)
			{
			$RenderArea .= '
			</tr>
			<tr>';
			}
		}
		$RenderArea .= '</tr></table>';

	//$Branch
	$branch_array = explode(",",$_SESSION["Branch"]);
	$sqlBranch = "SELECT * FROM branch";
	$qBranch = $db->query($sqlBranch);
	if (DB::isError($qBranch)) {  die("insert error: ".$qBranch->getMessage()); }

	while ($rowBranch = $qBranch->fetchRow()) {
		$RenderBranch .= '<input type="checkbox" name="Branch[]" value="'.$rowBranch["Branch_ID"].'"' ;
		if (in_array($rowBranch["Branch_ID"],$branch_array)) {
			$RenderBranch .= ' checked';
			}
	if ($rowBranch["Branch_ID"] == 3) {
		$RenderBranch .= ' disabled';
		}
		$RenderBranch .= '> '.$rowBranch["Branch_Title"].' &nbsp;';
		}
	/*
	$sqlStatus = "SELECT state_ID, state_Title FROM state_of_trade"; // WHERE state_ID <> 6
	$qStatus = $db->query($sqlStatus);
	if (DB::isError($qStatus)) {  die("insert error: ".$qStatus->getMessage()); }

	while ($rowStatus = $qStatus->fetchRow()) {
		$RenderStatus .= '<option value="'.$rowStatus["state_ID"].'"';
		if ($intStatus == $rowStatus["state_ID"]) {
			$RenderStatus .= ' selected';
			}
		$RenderStatus .= '>'.$rowStatus["state_Title"].'</option>';
		}
		*/
	$sqlNeg = "SELECT * FROM staff WHERE (Staff_Type = 'SalesNegotiator' OR Staff_Type = 'LettingsNegotiator') AND Staff_Status = 'Current' ORDER BY Staff_Fname";
	$qNeg = $db->query($sqlNeg);
	if (DB::isError($qNeg)) {  die("insert error: ".$qNeg->getMessage()); }
	if (!$intNeg) {
		$strRenderNeg .= '<option value=""> -- select -- </option>';
		}
	while ($rowNeg = $qNeg->fetchRow()) {
		$strRenderNeg .= '<option value="'.$rowNeg["Staff_ID"].'"';
		if ($_SESSION["neg"] == $rowNeg["Staff_ID"]) {
			$strRenderNeg .= ' selected';
			}
		$strRenderNeg .= '>'.$rowNeg["Staff_Fname"].' '.$rowNeg["Staff_Sname"].'</option>';
		}




echo html_header($pageTitle);
?>
<form method="post" enctype="multipart/form-data" name="form" onSubmit="return ValidateClientForm();">
  <input type="hidden" name="ClientID" value="<?php echo $intClientID; ?>">
  <input type="hidden" name="action" value="<?php echo $action; ?>">
  <input type="hidden" name="searchLink" value="<?php echo urlencode($searchLink); ?>">
	<table width="600" align="center">
	  <tr>

      <td><span class="pageTitle"><?php echo $pageTitle; ?></span> <?php if ($DateCreated) { ?>&nbsp;(registered:
        <?php echo $DateCreated; ?>) <?php } ?></td>
		<td align="right"><a href="?action=Clear&Sales=<?php echo $_GET["Sales"];?>&Lettings=<?php echo $_GET["Lettings"];?>">Clear</a> : <a href="?action=Clear&backto=menu">Main Menu</a></td>
	  </tr>
	</table>
    <table border="0" width="600" align="center" cellpadding="0" cellspacing="0">
	  <tr>
        <td width="50%" valign="top"><table width="100%" border="0" cellspacing="3" cellpadding="4">
            <tr align="center">
              <td class="head" colspan="2">Client Details</td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Email</td>
              <td class="greyForm"><input type="text" name="Email" value="<?php echo $_SESSION["Email"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Password</td>
              <td class="greyForm"><input type="text" name="Password" value="<?php echo $_SESSION["Password"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Name</td>
              <td class="greyForm"><input type="text" name="Name" value="<?php echo $_SESSION["Name"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Address</td>
              <td class="greyForm"><input type="text" name="Address1" value="<?php echo $_SESSION["Address1"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Town / City</td>
              <td class="greyForm"><input type="text" name="Address2" value="<?php echo $_SESSION["Address2"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">County</td>
              <td class="greyForm"><input type="text" name="Address3" value="<?php echo $_SESSION["Address3"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Postcode</td>
              <td class="greyForm"><input type="text" name="Postcode" value="<?php echo $_SESSION["Postcode"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Country</td>
              <td class="greyForm"><select name="Country" style="width: 180px">
                  <?php echo $RenderCountry; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Tel</td>
              <td class="greyForm"><input type="text" name="Tel" value="<?php echo $_SESSION["Tel"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Fax</td>
              <td class="greyForm"><input type="text" name="Fax" value="<?php echo $_SESSION["Fax"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Mobile</td>
              <td class="greyForm"><input type="text" name="Mobile" value="<?php echo $_SESSION["Mobile"]; ?>" style="width: 180px">
              </td>
            </tr>
            <tr>
              <td colspan="2" height="5"></td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Referer</td>
              <td class="greyForm"><select name="HeardBy" style="width: 180px">
			  <option value=""></option>
			  <?php echo $RenderFound;  ?>
			  </select></td>
            </tr>
            <tr>
              <td align="right" class="greyForm">Sale Status</td>
              <td class="greyForm"><select name="Selling" style="width: 180px">
                  <?php echo db_enum("clients","Selling",$_SESSION["Selling"]); ?>
              </select></td>
            </tr>
            <tr>
              <td align="right" class="greyForm"> Valuation?</td>
              <td class="greyForm">Yes
                  <input type="radio" name="Valuation" value="Yes"<?php if ($_SESSION["Valuation"] == "Yes") {  echo " checked"; }  ?>>
&nbsp;&nbsp;No
            <input type="radio" name="Valuation" value="No"<?php if ($_SESSION["Valuation"] != "Yes") { echo " checked"; }  ?>></td>
            </tr>
        </table></td>
        <td width="50%" valign="top"><table width="100%" border="0" cellspacing="3" cellpadding="4">
            <tr align="center">
              <td class="head" colspan="2">SALES Requirements</td>
            </tr>
            <tr>
              <td align="right" class="greyForm" width="41%">Property Type</td>
              <td class="greyForm" width="59%"><select name="PropertyType" style="width:120px">
                  <?php echo db_enum("clients","PropertyType",$_SESSION["PropertyType"]); ?>
                </select>
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm" width="41%">Bedrooms</td>
              <td class="greyForm" width="59%"><select name="Bedrooms" style="width:120px">
            <?php
			for ($i = 0; $i <= 9; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $_SESSION["Bedrooms"]) {
				echo ' selected';
				}
			echo '>'.$i.'</option>';
			}
			?>
                </select>

              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm" width="41%">Minimum Price</td>
              <td class="greyForm" width="59%"><select name="MinPrice" size="1" style="width:120px">
                  <option value="0">No Minimum</option>
            <?php
			for ($i = 80000; $i <= 500000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MinPrice"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+5000;
				}
			for ($i = 510000; $i <= 990000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MinPrice"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+10000;
				}
			for ($i = 1000000; $i <= 3000000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MinPrice"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+1000000;
				}
				?>
                </select>
              </td>
            </tr>
            <tr>
              <td align="right" class="greyForm" width="41%">Maximum Price</td>
              <td class="greyForm" width="59%"><select name="MaxPrice" size="1" style="width:120px">

                  <option value="999999999">No Maximum</option>
                              <?php
			for ($i = 80000; $i <= 500000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MaxPrice"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+5000;
				}
			for ($i = 510000; $i <= 990000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MaxPrice"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+10000;
				}
			for ($i = 1000000; $i <= 3000000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MaxPrice"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+1000000;
				}
				?>
                </select>
              </td>
            </tr>
            <tr>
              <td height="30" align="right" class="redForm">Email Updates</td>
              <td class="redForm"><label for="StatusYes">
                <input type="radio" name="Status" id="StatusYes" value="L" <?php if ($_SESSION["Status"] == "L") { echo "checked"; } ?>>
            Yes</label>
&nbsp;&nbsp;
            <label for="StatusNo">
            <input type="radio" name="Status" id="StatusNo" value="S" <?php if ($_SESSION["Status"] == "S") { echo "checked"; } ?>>
            No</label></td>
            </tr>
          </table>
            <br>
            <table width="100%" border="0" cellspacing="3" cellpadding="4">
              <tr align="center">
                <td class="head" colspan="2">LETTINGS Requirements</td>
              </tr>
              <tr>
                <td align="right" class="greyForm" width="41%">Property Type</td>
                <td class="greyForm" width="59%"><select name="PropertyTypeLet" style="width:120px">
                  <?php echo db_enum("clients","PropertyType",$_SESSION["PropertyTypeLet"]); ?>
				  </select>
                </td>
              </tr>
              <tr>
                <td align="right" class="greyForm" width="41%">Bedrooms</td>
                <td class="greyForm" width="59%"><select name="BedroomsLet" style="width:120px">
                  <?php
			for ($i = 0; $i <= 9; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $_SESSION["BedroomsLet"]) {
				echo ' selected';
				}
			echo '>'.$i.'</option>';
			}
			?>
                </select>
                </td>
              </tr>
              <tr>
                <td align="right" class="greyForm" width="41%">Minimum Price</td>
                <td class="greyForm" width="59%"><select name="MinPriceLet" style="width: 120px">
                  <option value="0">No Minimum</option>
                  <?php
			for ($i = 50; $i <= 1000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MinPriceLet"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+50;
				}
			for ($i = 1000; $i <= 5000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MinPriceLet"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+250;
				}
				?>
                </select>
            p/w</td>
              </tr>
              <tr>
                <td align="right" class="greyForm" width="41%">Maximum Price</td>
                <td class="greyForm" width="59%"><select name="MaxPriceLet" style="width: 120px">
                  <option value="999999999">No Maximum</option>
                  <?php
			for ($i = 50; $i <= 1000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MaxPriceLet"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+50;
				}
			for ($i = 1000; $i <= 5000;) {
				echo '<option value="'.$i.'"';
				if ($i == $_SESSION["MaxPriceLet"]) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>';
				$i = $i+250;
				}
				?>
                </select>
            p/w </td>
              </tr>
              <tr>
                <td align="right" class="greyForm">Furnished</td>
                <td class="greyForm"><select name="FurnishedLet" style="width:120px">
				<option value="Any">Any</option>
				<?php
				$sqlFurn = "SELECT * FROM furnished";
				$qFurn = $db->query($sqlFurn);
				if (DB::isError($qFurn)) {  die("error: ".$qFurn->getMessage()); }

				while ($rowFurn = $qFurn->fetchRow()) {
         			echo '<option value="'.$rowFurn["Furnished_ID"].'"';
				   	if ($_SESSION["Furnished"] == $rowFurn["Furnished_ID"]) {
				   		echo ' selected';
						}
				   	echo '>'.$rowFurn["Furnished_Title"].'</option>
				   	';
				   	}
				   	?>
                </select></td>
              </tr>
              <tr>
                <td align="right" class="greyForm"> Term</td>
                <td class="greyForm"><select name="TermLet" style="width:120px">
                  <?php echo db_enum("clients","TermLet",$_SESSION["TermLet"]); ?>
                </select></td>
              </tr>
              <tr>
                <td height="30" align="right" class="redForm">Email Updates</td>
                <td class="redForm"><label for="StatusYesLet">
                  <input type="radio" name="StatusLet" id="StatusYesLet" value="L" <?php if ($_SESSION["StatusLet"] == "L") { echo "checked"; } ?>>
            Yes</label>
&nbsp;&nbsp;
            <label for="StatusNoLet">
            <input type="radio" name="StatusLet" id="StatusNoLet" value="S" <?php if ($_SESSION["StatusLet"] == "S") { echo "checked"; } ?>>
            No</label></td>
              </tr>
          </table></td>
      </tr>
       <tr align="center">
        <td height="40" colspan="2"><strong>Negotiator:
	    <select name="neg"><?php echo $strRenderNeg; ?></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Branch:</strong> <?php echo $RenderBranch; ?></td>
      </tr>
      <tr align="center">
        <td colspan="2"><hr size="1" noshade>
            <input name="Submit" type="submit" value="   <?php echo $action; ?> Client   ">
          <br>
            <hr size="1" noshade></td>
      </tr>

      <tr align="center">
        <td colspan="2"><?php echo $RenderArea; ?> </td>
      </tr>
      <tr align="left">
        <td colspan="2"><table width="100%" border="0" cellspacing="3" cellpadding="4">
            <tr>
              <td class="head">Private Notes (enter all correspondance with client here, include date and your initials)</td>
            </tr>
            <tr>
              <td class="greyForm"><textarea name="Areas2" rows="5" style="width: 100%"><?php echo $_SESSION["Areas2"]; ?></textarea></td>
            </tr>
            <tr>
              <td class="head">User Notes (read-only) </td>
            </tr>
            <tr>
              <td class="greyForm"><textarea name="Notes" rows="5" style="width: 100%" readonly="true"><?php echo $_SESSION["Notes"]; ?></textarea></td>
            </tr>
        </table></td>
      </tr>
      <tr align="center">
        <td colspan="2"><hr size="1" noshade>
            <input name="submit" type="submit" value="    Update Record    ">
            <br>
            <hr size="1" noshade></td>
      </tr>
      <tr align="center">
        <td colspan="2">&nbsp;</td>
      </tr>
    </table>
</form>
</body>
</html>
<?php } ?>