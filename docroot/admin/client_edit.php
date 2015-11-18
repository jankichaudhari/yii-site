<?php
session_start();
$pageTitle = "Client";
require("global.php");
require("secure.php");
require("queryLog.php");

if ($_GET["ClientID"]) {
	$_GET["cli_id"] = $_GET["ClientID"];
	}
if (!$_GET["cli_id"]){
	header("Location:client.php");
	exit;
	}
/*

NEW client edit as of Jan 2007

**Attempt to make this page insert/update the new v3 database table as well**
**Use db_query to log all changes**

//////////////////////////////////////////////////
// New features:
//////////////////////////////////////////////////

Flagging...
Ability to flag a client with various flags...
callback - tried to contact client but failed, so flagged to be contacted at a later stage
more??

Hotlist...
Quick easy way to grab a list of potentially hot BUYERS:
- Hot
- Warm
- Cool
- Cold

Tabbed layout, tabs are to be:
Address and Contact info (address, email, tels, prefered contact method)
Property Requirements (sales and/or lettings)
Status
	Current status (property on market, chain free, cash buyer, on market with us/other)
	Valuation (require valuation, valuation booked/complete)
	Mortgage (maip, require mortgage advice etc)
Communicate (send email, make phone call, log outcome)
Notes (public and private notes, auto date and name, note subject, search notes)
Log (log of all changes)


*/

// form has been submitted....
if ($_POST["action"] == "update") {

	if (!$_POST["cli_id"]){
		echo "No cli_id";
		exit;
		}

	// add new note
	if ($_POST["SubmitNote"]) {


		if ($_POST["not_id"]) {

			$fieldnames[] = "not_subject";
			$newvalues[] = trim($_POST["not_subject"]);
			$fieldnames[] = "not_note";
			$newvalues[] = trim($_POST["not_note"]);
			$fieldnames[] = "not_flag";
			$newvalues[] = trim($_POST["not_flag"]);

			queryLog($fieldnames,$newvalues,'note','not_id',$_POST["not_id"],'Update');

			/*
			$sql = "UPDATE note SET
			not_subject = '".trim($_POST["not_subject"])."',
			not_note = '".trim($_POST["not_note"])."\n(edit ".$_SESSION["s_name"]." ".$dateToday.")',
			not_flag = '".$_POST["not_flag"]."'
			WHERE not_id = ".$_POST["not_id"];

			$q = $db->query($sql);
			if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
			*/
			header("Location:?cli_id=".$_POST["cli_id"]."&view=notes&searchLink=".$_POST["searchLink"]."&changes=Update+Successful");
			exit;


		} else {

			$sql = "INSERT INTO note
			(not_date, not_user, not_table, not_row, not_subject, not_note, not_flag)
			VALUES
			('$dateToday', '".$_SESSION["s_userid"]."', 'clients', '".$_POST["cli_id"]."', '".$_POST["not_subject"]."', '".trim($_POST["not_note"])."', '".$_POST["not_flag"]."')";

			$q = $db->query($sql);
			if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
			header("Location:?cli_id=".$_POST["cli_id"]."&view=notes&searchLink=".$_POST["searchLink"]."&changes=Update+Successful");
			exit;
			}

		} // end notes


	// build arrays for queryLog, this way it will record blank values
	if ($_POST["view"] == "contact") {
		$fields = array('Email','Name','Address1','Address2','Address3','City',
		'Country','Postcode','Tel','Fax','Mobile','Title','contact_method');
		}
	elseif ($_POST["view"] == "requirements") {
		$fields = array('PropertyType','MinPrice','MaxPrice','Receptions','Bedrooms','Bathrooms',
		'Areas','Notes','Status','PropertyTypeLet','BedroomsLet','MinPriceLet','MaxPriceLet',
		'StatusLet','Lettings','Sales','Branch'); //'FurnishedLet','TermLet',
		}
	elseif ($_POST["view"] == "status") {
		$fields = array('Selling','Val','neg','Mortgage','ValNotes','MortgageNotes','Password','HeardBy');
		}
	elseif ($_POST["view"] == "email") {
		}
	elseif ($_POST["view"] == "notes") {
		}
	elseif ($_POST["view"] == "log") {
		}



	foreach ($fields AS $field) {
		//trim($_POST[$field]) &&
		if ($field !== "Areas" && $field !== "Branch") {


			$fieldnames[] = $field;
			$newvalues[] = trim($_POST[$field]);

			}
		}


	if ($_POST["Areas"]) {
		foreach ($_POST["Areas"] as $area) {
			$AreaSQL .= $area."^";
			}
		$AreaSQL = removeCharacter($AreaSQL,"^");
		$fieldnames[] = "Areas";
		$newvalues[] = $AreaSQL;
		}
	if ($_POST["Branch"]) {
		foreach ($_POST["Branch"] as $b) {
			$selected_branches .= $b.",";
			}
		$BranchSQL = removeCharacter($selected_branches,",");
		$fieldnames[] = "Branch";
		$newvalues[] = $BranchSQL;
		}

	//$fieldnames[] = "DateModified";
	//$newvalues[] = $dateToday;

	#print_r($fieldnames);
	#echo "<br>";
	#print_r($newvalues);

	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}

	queryLog($fieldnames,$newvalues,'clients','Client_ID',$_POST["cli_id"],'Update');

	#print_r($fieldnames);
	#print_r($newvalues);
	header("Location:?cli_id=".$_POST["cli_id"]."&view=".$_POST["view"]."&searchLink=".$_POST["searchLink"]."&changes=Update+Successful");


} else { // form is not submitted



if (!$_GET["view"]) {
	$_GET["view"] = "contact";
	}


$cli_id = $_GET["cli_id"]; // 25782;

$sql = "SELECT
clients.*,
foundby.*,
staff.*,
CONCAT(staff.Staff_Fname,' ',Staff_Sname) AS Staff_Name

FROM clients

LEFT JOIN foundby ON clients.HeardBy = foundby.FoundBy_ID
LEFT JOIN staff ON clients.neg = staff.Staff_ID

WHERE clients.Client_ID = $cli_id";

$q = $db->query($sql);
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {

	// get all field values into variables
	foreach($row as $key=>$val) {
		$_SESSION[$key] = $val;
		$$key = $val;
		}

	}
#print_r($_SESSION);

$Areas = str_replace(", ","^",$Areas);
$Areas = explode("^",$Areas);
$sqlArea = "SELECT * FROM area ORDER BY area_title";
$qArea = $db->query($sqlArea);
if (DB::isError($qArea)) {  die("db error: ".$qArea->getMessage()); }
$RenderArea = '<table width="100%"><tr>';
while ($rowArea = $qArea->fetchRow()) {

	$RenderArea .= '<td class="greyFormSmall">
	<label for="'.$rowArea["area_title"].'"><input type="checkbox" name="Areas[]" value="'.$rowArea["area_title"].'" id="'.$rowArea["area_title"].'"';
	if (in_array(trim($rowArea["area_title"]),$Areas)) {
		$RenderArea .= ' checked';
		}
	$RenderArea .= '>'.$rowArea["area_title"].'</label></td>';

	$i++;
	if ($i % 5 == 0)
		{
		$RenderArea .= '
		</tr>
		<tr>';
		}
	}
$RenderArea .= '</tr></table>';


if (!$FoundBy_Title || $HeardBy == 38) {
	$sqlFound = "SELECT * FROM foundby ORDER BY FoundBy_Type";
	$qFound = $db->query($sqlFound);
	if (DB::isError($qFound)) {  die("insert error: ".$qFound->getMessage()); }

	while ($rowFound = $qFound->fetchRow()) {
		$RenderFound .= '<option value="'.$rowFound["FoundBy_ID"].'"';
		if ($_SESSION["HeardBy"] == $rowFound["FoundBy_ID"]) {
			$RenderFound .= ' selected';
			}
		$RenderFound .= '>'.$rowFound["FoundBy_Title"].'</option>';
		}
	$FoundBy = '<select name="HeardBy">'.$RenderFound.'</select>';
	} else {
	$FoundBy = $FoundBy_Title.'<input type="hidden" name="FoundBy" value="'.$HeardBy.'">';
	}



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

echo html_header($pageTitle);
?>


<form method="post" enctype="multipart/form-data" name="form">
  <input type="hidden" name="cli_id" value="<?php echo $cli_id; ?>">
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="view" value="<?php echo $_GET["view"]; ?>">
  <input type="hidden" name="searchLink" value="<?php echo urlencode($_GET["searchLink"]); ?>">
	<table width="600" align="center">
	  <tr>
		<td><span class="pageTitle"><?php echo $pageTitle; ?>: <?php echo $Name; ?></span></td>
		<td align="right"><?php if ($_GET["searchLink"]) { echo '<a href="'.urldecode($_GET["searchLink"]).'">Back to Search</a> &nbsp; '; } ?><a href="index.php">Main Menu</a></td>
	  </tr>
	</table>
	<table width="600" align="center" cellpadding="4" cellspacing="3">
	  <tr>
	    <td class="anchorNav">
		<a href="?cli_id=<?php echo $cli_id; ?>&view=contact&searchLink=<?php echo urlencode($searchLink); ?>">Contact Info</a> ::
		<a href="?cli_id=<?php echo $cli_id; ?>&view=requirements&searchLink=<?php echo urlencode($searchLink); ?>">Requirements</a> ::
		<a href="?cli_id=<?php echo $cli_id; ?>&view=status&searchLink=<?php echo urlencode($searchLink); ?>">Status</a> ::
		<a href="?cli_id=<?php echo $cli_id; ?>&view=email&searchLink=<?php echo urlencode($searchLink); ?>">Email</a> ::
		<a href="?cli_id=<?php echo $cli_id; ?>&view=notes&searchLink=<?php echo urlencode($searchLink); ?>">Notes</a> ::
		<a href="?cli_id=<?php echo $cli_id; ?>&view=log&searchLink=<?php echo urlencode($searchLink); ?>">Log</a></td>
	  </tr>
	<?php
	if ($_GET["changes"]) {
	?>
	  <tr>
	    <td colspan="2" class="changes"><?php echo $_GET["changes"];?></td>
      </tr>
	<?php
	  } else { // show tip

	  $tips = array(
	  'You must always Save Changes before moving to another page',
	  'Use proper case when entering client names and addresses',
	  'Ask the client how they would prefer to be contacted, and tick the relevant box on the <a href="?cli_id='.$cli_id.'&view=contact&searchLink='.urlencode($searchLink).'">Contact Info page</a>',
	  'Always enter a minimum and maximum price',
	  'At least one branch must be selected for email updates to work',
	  'To unsubscribe a client, simply say No to email updates',
	  'Always ask the client if we can help with their mortgage requirements',
	  'If the client wants a valuation, please fill in fields on the <a href="?cli_id='.$cli_id.'&view=status&searchLink='.urlencode($searchLink).'">Status page</a>',
	  'Clients can log into the site using their email address and password'
	  );
	  $max = count($tips);
	  $rand = rand(0,$max-1);

	  ?>
	  <tr>
	    <td colspan="2" align="left"><img src="images/error.gif" align="absmiddle"> &nbsp;<?php echo $tips[$rand];?></td>
      </tr>
	  <?php
	  }
	?>
	</table>

<!-- end of header -->


<?php
// contact page
if ($_GET["view"] == "contact") {
?>
<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
  <tr>
    <th colspan="2" class="greyForm">Contact Info </th>
  </tr>
  <tr>
    <td width="20%" align="right" class="greyForm">Name</td>
    <td class="greyForm"><select name="Title" id="Title">
	<option value=""></option>
      <?php echo db_enum("clients","Title",$_SESSION["Title"]) ?>
    </select>
    <input type="text" name="Name" style="width: 250px" value="<?php echo ucwords($Name); ?>"></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Email</td>
    <td class="greyForm"><input name="Email" type="text" id="Email" style="width: 200px" value="<?php echo strtolower($Email); ?>">
      <input name="contact_method" type="radio" value="Email"<?php
	  if ($contact_method == "Email") {
	  	echo " checked> <strong>Primary</strong>";
		} elseif (!$contact_method) {
		echo ">(not specified)<b";
		}  ?>>
	</td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Telephone</td>
    <td class="greyForm"><input name="Tel" type="text" id="Tel" style="width: 200px" value="<?php echo $Tel; ?>">
    <input name="contact_method" type="radio" value="Tel"<?php
	  if ($contact_method == "Tel") {
	  	echo " checked> <strong>Primary</strong>";
		} elseif (!$contact_method) {
		echo ">(not specified)<b";
		}  ?>>    </td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Mobile</td>
    <td class="greyForm"><input name="Mobile" type="text" id="Mobile" style="width: 200px" value="<?php echo $Mobile; ?>">
    <input name="contact_method" type="radio" value="Mobile"<?php
	  if ($contact_method == "Mobile") {
	  	echo " checked> <strong>Primary</strong>";
		} elseif (!$contact_method) {
		echo ">(not specified)<b";
		}  ?>></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Fax</td>
    <td class="greyForm"><input name="Fax" type="text" id="Fax" style="width: 200px" value="<?php echo $Fax; ?>"></td>
  </tr>
  <tr>
    <td colspan="2" class="greyForm">Address</td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Address</td>
    <td class="greyForm"><input name="Address1" type="text" id="Address1" style="width: 350px" value="<?php echo $Address1; ?>"></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Town / City</td>
    <td class="greyForm"><input name="Address2" type="text" id="Address2" style="width: 350px" value="<?php echo $Address2; ?>"></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">County</td>
    <td class="greyForm"><input name="Address3" type="text" id="Address3" style="width: 350px" value="<?php echo $Address3; ?>"></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Postcode</td>
    <td class="greyForm"><input name="Postcode" type="text" id="Postcode" style="width: 100px" maxlength="10" value="<?php echo $Postcode; ?>"></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Country</td>
    <td class="greyForm"><select name="Country" style="width: 350px">
      <?php echo $RenderCountry; ?>
    </select></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Save Changes"></td>
  </tr>
</table>
<?php
// end contact page
}
?>










<?php
// requirements page
if ($_GET["view"] == "requirements") {
?>
<table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%" valign="top">
	<table align="center" width="100%" border="0" cellspacing="3" cellpadding="4"  bordercolor="#990000">
  <tr>
    <th colspan="2" class="salesForm">Sales</th>
  </tr>
  <tr>
    <td class="redForm" height="30" align="right">Email Updates</td>
    <td class="redForm"><label for="StatusYes">
      <input type="radio" name="Status" id="StatusYes" value="L" <?php if ($_SESSION["Status"] == "L") { echo "checked"; } ?>>
    Yes</label>
&nbsp;&nbsp;
    <label for="StatusNo">
    <input type="radio" name="Status" id="StatusNo" value="S" <?php if ($_SESSION["Status"] == "S") { echo "checked"; } ?>>
    No</label></td>
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
  <tr align="center">
    <td colspan="2" class="greyForm"><a href="prop_search.php?action=Search&PriceFrom=<?php echo $MinPrice; ?>&PriceTo=<?php echo $MaxPrice; ?>&BedFrom=<?php echo $Bedrooms;?>&PropType=<?php echo $PropertyType;?>&Status[]=1&Status[]=2&Order=state_of_trade_id,Price"><strong>Show suitable property</strong></a> <br><span class="tinytext">(please save any changes before clicking)</span></td>
    </tr>
</table>
    </td>
    <td width="50%" valign="top">
<table align="center" width="100%" border="0" cellspacing="3" cellpadding="4">
  <tr>
    <th colspan="2" class="lettingsForm">Lettings</th>
  </tr>
  <tr>
    <td class="redForm" height="30" align="right">Email Updates</td>
    <td class="redForm"><label for="StatusYesLet">
      <input type="radio" name="StatusLet" id="StatusYesLet" value="L" <?php if ($_SESSION["StatusLet"] == "L") { echo "checked"; } ?>>
    Yes</label>
&nbsp;&nbsp;
    <label for="StatusNoLet">
    <input type="radio" name="StatusLet" id="StatusNoLet" value="S" <?php if ($_SESSION["StatusLet"] == "S") { echo "checked"; } ?>>
    No</label></td>
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
  <!--
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
				   	if ($_SESSION["FurnishedLet"] == $rowFurn["Furnished_ID"]) {
				   		echo ' selected';
						}
				   	echo '>'.$rowFurn["Furnished_Title"].'</option>
				   	';
				   	}
				   	?>
    </select></td>
  </tr>
  -->
  <tr align="center">
    <td colspan="2" class="greyForm"><a href="prop_search_lettings.php?action=Search&PriceFrom=<?php echo $MinPriceLet; ?>&PriceTo=<?php echo $MaxPriceLet; ?>&BedFrom=<?php echo $BedroomsLet;?>&PropType=<?php echo $PropertyTypeLet;?>&Status[]=1&Status[]=2&Order=state_of_trade_id,Price"><strong>Show suitable property</strong>></a> <br>
      <span class="tinytext">(please save any changes before clicking)</span></td>
    </tr>
</table></td>
  </tr>
</table>
<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Save Changes"></td>
  </tr>
</table>
<br>
<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
  <tr>
    <td class="greyForm" colspan="2">Branch &amp; Areas</td>
  </tr>
  <tr>
    <td align="center" class="redForm">Branch: <?php echo $RenderBranch; ?></td>
  </tr>
  <tr>
    <td><?php echo $RenderArea; ?></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Save Changes"></td>
  </tr>
</table>
<?php
// end requirements page
}
?>










<?php
// status page
if ($_GET["view"] == "status") {
?>
<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="2" class="greyForm">Status</th>
    </tr>
    <tr>
      <td align="right" class="greyForm">Assigned Negotiator </td>
      <td class="greyForm"><select name="neg" style="width: 250px">
          <?php
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
		echo $strRenderNeg;
	  ?>
      </select></td>
    </tr>
  <tr>
    <td align="right" class="greyForm">Current Sale Status</td>
    <td class="greyForm"><select name="Selling" style="width: 250px">
	<option value=""></option>
        <?php echo db_enum("clients","Selling",$_SESSION["Selling"]); ?>
    </select></td>
  </tr>
  <tr>
    <td align="right" class="greyForm"> Valuation Required </td>
    <td class="greyForm"><select name="Val" style="width: 250px">
	<option value=""></option>
        <?php echo db_enum("clients","Val",$_SESSION["Val"]); ?>
    </select></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Valuation Notes </td>
    <td class="greyForm"><textarea name="ValNotes" rows="4" style="width: 250px"><?php echo $_SESSION["ValNotes"]; ?></textarea></td>
  </tr>
  <tr>
    <td align="right" class="greyForm"> Mortgage Status </td>
    <td class="greyForm"><select name="Mortgage" style="width: 250px">
	<option value=""></option>
      <?php echo db_enum("clients","Mortgage",$_SESSION["Mortgage"]); ?>
    </select></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Mortgage Notes </td>
    <td class="greyForm"><textarea name="MortgageNotes" rows="4" style="width: 250px"><?php echo $_SESSION["MortgageNotes"]; ?></textarea></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Account Password </td>
    <td class="greyForm"><input name="Password" type="text" id="Password" style="width: 250px" value="<?php echo $_SESSION["Password"]; ?>"></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Save Changes"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Registered since</td>
    <td class="greyForm"><?php echo $DateCreated; ?></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Registered by </td>
    <td class="greyForm"><?php echo $RegisteredBy; ?></td>
  </tr>
  <tr>
    <td align="right" class="greyForm">Referer</td>
    <td class="greyForm"><?php echo $FoundBy; ?></td>
  </tr>
  <!--
  <tr>
    <td align="right" class="greyForm">Last Edited </td>
    <td class="greyForm"><?php echo $DateModified; ?></td>
  </tr>
  -->
</table>
<?php
// end status page
}
?>










<?php
// email page
if ($_GET["view"] == "email") {
?>
<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="2" class="greyForm">Send Email (NOT WORKING) </th>
    </tr>
	<tr>
	  <td width="20%" align="right" class="greyForm">From</td>
	  <td class="greyForm"><select name="email_from" style="width: 350px">
	    <option value="<?php echo $_SESSION["s_user"].'@woosterstock.co.uk'; ?>"><?php echo $_SESSION["s_user"].'@woosterstock.co.uk'; ?></option>
	    <option value="post@woosterstock.co.uk">post@woosterstock.co.uk</option>
      </select></td>
  </tr>
	<tr>
	  <td align="right" class="greyForm">To</td>
	  <td class="greyForm"><input name="email_to" type="text" style="width: 350px" value="<?php echo $Email; ?>"></td>
  </tr>
	<tr align="center">
	  <td colspan="2">Choose from a standard email template...</td>
  </tr>
	<tr>
	  <td align="right" class="greyForm">Templates</td>
	  <td class="greyForm"><select name="email_from" style="width: 350px">
        <option value=""></option>
        <option value="1">Are you still looking for property?</option>
      </select></td>
  </tr>
	<tr align="center">
	  <td colspan="2" class="greyForm"><input type="button" name="email_send" value="Send"></td>
    </tr>
	<tr align="center">
	  <td colspan="2">...or, create a custom email </td>
  </tr>
	<tr>
	  <td align="right" class="greyForm">Subject</td>
      <td class="greyForm"><input type="text" name="email_subject" style="width: 350px"></td>
	</tr>
	<tr>
	  <td align="right" class="greyForm">Message</td>
	  <td class="greyForm"><textarea name="email_message" rows="6" id="newnote" style="width: 350px;"></textarea></td>
    </tr>
	<tr align="center">
	  <td colspan="2" class="greyForm"><input type="button" name="email_send" value="Send"></td>
    </tr>
</table>

<?php
// end email page
}
?>










<?php
// notes page
// notes should get entered into a separate table, with subject, content, user and date
if ($_GET["view"] == "notes") {

if ($_GET["not_id"]) {
	$sql_not = "SELECT * FROM note WHERE not_id = ".$_GET["not_id"];
	$q_not = $db->query($sql_not);
	if (DB::isError($q_not)) {  die("error: ".$q_not->getMessage()); }
	while ($row = $q_not->fetchRow()) {
		$not_subject = $row["not_subject"];
		$not_note = $row["not_note"];
		$not_flag = $row["not_flag"];
		}

	$render_editnote = '
	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="2" class="greyForm">Edit Note</th>
    </tr>
	<tr>
	  <td width="20%" align="right" class="greyForm">Type</td>
      <td class="greyForm"><select name="not_subject">
       '.db_enum("note","not_subject",$not_subject).'
	   </select></td>
	</tr>
	<tr>
	  <td align="right" class="greyForm">Note</td>
	  <td class="greyForm"><textarea name="not_note" rows="4" id="newnote" style="width: 350px;">'.$not_note.'</textarea></td>
    </tr>
	<tr>
	  <td align="right" class="greyForm">Flag</td>
	  <td class="greyForm"><select name="not_flag">
       '.db_enum("note","not_flag",$not_flag).'
      </select></td>
	  </tr>
	<tr>
	  <td class="greyForm"><input type="hidden" name="not_id" value="'.$_GET["not_id"].'"></td>
	  <td class="greyForm"><input type="submit" name="SubmitNote" value="Save Changes"></td>
    </tr>
  </table>
  ';

	echo $render_editnote;

	// edit note page contains log of all changes

	$sqlLog = "SELECT *, date_format(changelog.cha_datetime, '%d/%m/%y %h:%i:%s') as cha_date
	FROM changelog, admin
	WHERE changelog.cha_user = admin.adm_id AND changelog.cha_table = 'note' AND changelog.cha_row = ".$_GET["not_id"]."
	ORDER BY changelog.cha_datetime DESC";
		$qLog = $db->query($sqlLog);
		if (DB::isError($qLog)) {  die("error: ".$qLog->getMessage()); }
		$strRenderLog = '
       	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="5" class="greyForm">Log for this Note</th>
    </tr>
    <tr>
	<td>
	<table width="100%" cellspacing="2" cellpadding="1">
		<tr>
		<td><strong>Date</strong></td>
		<td><strong>Field</strong></td>
		<td><strong>Old Value</strong></td>
		<td><strong>New Value</strong></td>
		<td><strong>User</strong></td>
		</tr>
		';
		while ($rowLog = $qLog->fetchRow()) {

			$cha_date = $rowLog["cha_date"];

			$strRenderLog .= '<tr>
			<td>'.$cha_date.'</td>
			<td>'.str_replace("not_","",$rowLog["cha_field"]).'</td>
			<td><span title="'.$rowLog["cha_old"].'">'.strip_tags(substr($rowLog["cha_old"],0,25)).'</span></td>
			<td><span title="'.$rowLog["cha_new"].'">'.strip_tags(substr($rowLog["cha_new"],0,25)).'</span></td>
			<td>'.$rowLog["adm_name"].'</td>
			</tr>
			';
			}
		$strRenderLog .= '
		</table>
		</td>
		</tr>
		</table>';
		echo $strRenderLog;


	// end of edit note page


	} else {

if (!$_GET["order"]) {
	$_GET["order"] = "not_date DESC";
	}
if ($_GET["user"]) {
	$extra_sql = " AND not_user = ".$_GET["user"];
	}
$sqlNotes = "SELECT
note.*,
admin.*,
date_format(note.not_date, '%a %D %b %y %H:%i') as date
FROM note
LEFT JOIN admin ON note.not_user = admin.adm_id
WHERE not_table = 'clients' AND not_row = '$cli_id'
$extra_sql
ORDER BY ".$_GET["order"];
$qNotes = $db->query($sqlNotes);
if (DB::isError($qNotes)) {  die("error: ".$qNotes->getMessage()); }

while ($row = $qNotes->fetchRow()) {
	$notesTable .= '
	<tr>
	<td><strong>'.$row["not_subject"].'</strong> by <strong>'.$row["adm_name"].'</strong> on '.$row["date"].'</td>
	<td>Flag: <span class="flag'.$row["not_flag"].'"><img src="images/flag'.$row["not_flag"].'.gif" align="absmiddle">'.$row["not_flag"].'</span></td>
	<td>[ <a href="?cli_id='.$cli_id.'&view=notes&amp;not_id='.$row["not_id"].'">Edit</a> ]</td>
	</tr>
	<tr>
	<td colspan="3">'.nl2br($row["not_note"]).'</td>
	</tr>
	<tr>
	<td colspan="3"><hr size="1"></td>
	</tr>
	';
	}

$link = 'cli_id='.$cli_id.'&amp;view='.$_GET["view"];
?>


  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th class="greyForm">Notes &nbsp; [ <a href="#newnote">New</a> ]</th>
    </tr>
    <tr>
      <td class="greyForm">Sort by: <a href="?<?php echo $link.'&order=not_date DESC'; ?>">Date (newest first)</a> / <a href="?<?php echo $link.'&order=not_flag'; ?>">Flag</a> / <a href="?<?php echo $link.'&order=not_subject'; ?>">Type</a> / <a href="?<?php echo $link.'&order=adm_name ASC'; ?>">User (a-z)</a> / <a href="?<?php echo $link.'&user='.$_SESSION["s_userid"]; ?>">My Notes</a> </td>
    </tr>
    <tr>
      <td>
	  <table width="100%" cellspacing="0" cellpadding="1">
	  <?php echo $notesTable; ?>
	  </table>
	  </td>
    </tr>
  </table>
  <br>
  <a name="newnote"></a>
    <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="2" class="greyForm">New Note</th>
    </tr>
	<tr>
	  <td width="20%" align="right" class="greyForm">Type</td>
      <td class="greyForm"><select name="not_subject">
       <?php echo db_enum("note","not_subject"); ?>
      </select></td>
	</tr>
	<tr>
	  <td align="right" class="greyForm">Note</td>
	  <td class="greyForm"><textarea name="not_note" rows="4" id="newnote" style="width: 350px;"></textarea></td>
    </tr>
	<tr>
	  <td align="right" class="greyForm">Flag</td>
	  <td class="greyForm"><select name="not_flag">
       <?php echo db_enum("note","not_flag"); ?>
      </select></td>
	  </tr>
	<tr>
	  <td class="greyForm">&nbsp;</td>
	  <td class="greyForm"><input type="submit" name="SubmitNote" value="Add"></td>
    </tr>
  </table>
  <?php if ($Areas2) { ?>
 <br>
 <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
	<tr>
      <th class="greyForm">Old Notes pre-2007 (readonly) </th>
    </tr>
    <tr>
      <td><textarea rows="4" readonly="readonly" style="width: 100%"><?php echo $Areas2; ?></textarea></td>
    </tr>
</table>
	<?php } ?>
	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
	<tr><td>
	<p>Using notes:</p>
	<p>Notes are ordered by date, with the newest at the top. Each note has a &quot;Type&quot; which is for grouping the notes and making them easier to find. Also stored is the author and date/time it was added. The flag is for flagging notes that need to have more action taken on them, for example if you tried to contact a client but could not get through, you would enter a note to that effect and flag is as Follow-up. Once you have successfully contacted the client, you can change the flag to Complete. (there will be a method of search flagged notes on all clients.) </p>
	<p>To help you find notes more easily, there are a number of options. Most of these will sort the notes in a particular way, either by Date, Type, Author and so on. The last option is &quot;My notes&quot; and this will only show notes authored by you.</p>
	<p>All notes can be edited. All changes are stored in a log, which appear below the note you are editing. </p>
	</td></tr>
	</table>
	<?php
for ($i = 0; $i <= 40; $i++) {
	echo "<br>";
	}

}
// end notes page
}
?>











<?php
// log page
if ($_GET["view"] == "log") {

$sqlLog = "SELECT *, date_format(changelog.cha_datetime, '%d/%m/%y %h:%i:%s') as cha_date FROM changelog, admin WHERE changelog.cha_user = admin.adm_id AND changelog.cha_table = 'clients' AND changelog.cha_row = $cli_id ORDER BY changelog.cha_datetime DESC";
		$qLog = $db->query($sqlLog);
		if (DB::isError($qLog)) {  die("error: ".$qLog->getMessage()); }
		$strRenderLog = '
       	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="5" class="greyForm">Log</th>
    </tr>
    <tr>
	<td>
	<table width="100%" cellspacing="2" cellpadding="1">
		<tr>
		<td><strong>Date</strong></td>
		<td><strong>Field</strong></td>
		<td><strong>Old Value</strong></td>
		<td><strong>New Value</strong></td>
		<td><strong>User</strong></td>
		</tr>
		';
		while ($rowLog = $qLog->fetchRow()) {

			$cha_date = $rowLog["cha_date"];

			$strRenderLog .= '<tr>
			<td>'.$cha_date.'</td>
			<td>'.$rowLog["cha_field"].'</td>
			<td><span title="'.$rowLog["cha_old"].'">'.strip_tags(substr($rowLog["cha_old"],0,25)).'</span></td>
			<td><span title="'.$rowLog["cha_new"].'">'.strip_tags(substr($rowLog["cha_new"],0,25)).'</span></td>
			<td>'.$rowLog["adm_name"].'</td>
			</tr>
			';
			}
		$strRenderLog .= '
		</table>
		</td>
		</tr>
		</table>';
		echo $strRenderLog;
?>


<?php
// end log page
}
?>


<?php
// end action if
}
?>