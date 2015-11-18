<?php
session_start();
$pageTitle = "Property";

require("global.php");
require("secure.php");
require("HTTP/Upload.php");

if ($_GET["view"] == "print") {
	// print view
	if (!$_GET["propID"]) {
		echo "Missing Property ID";
		exit;
	} else {
		$intPropID = $_GET["propID"];
	}

	$strPageBreak = '
	<DIV style=""page-break-after:always""></DIV>
	';

	$sql = "SELECT * FROM property, area, state_of_trade WHERE property.prop_ID = $intPropID  AND property.area_ID = area.area_ID AND property.state_of_trade_id = state_of_trade.state_ID LIMIT 1";
	$q   = $db->query($sql);
	//echo $sql;
	if (DB::isError($q)) {
		die("property select error: " . $q->getMessage());
	}

	while ($row = $q->fetchRow()) {

		$strAddress         = $row["Address1"];
		$strArea            = $row["area_title"];
		$pc                 = explode(" ", $row["Postcode"]);
		$strPostcode        = $pc[0];
		$strDescription     = $row["description"];
		$strLongDescription = $row["longDescription"];
		$SaleLet            = $row["SaleLet"];
		$lease_free         = $row["lease_free"];
		$intBranch          = $row["Branch"];

		if ($SaleLet == 1) {
			// AND property.lease_free = leasefree.id_LeaseFree
			$sqlTenure = "SELECT * FROM leasefree WHERE id_LeaseFree = " . $lease_free . " LIMIT 1";
			$qTenure   = $db->query($sqlTenure);
			while ($rowTenure = $qTenure->fetchRow()) {
				$strTenure = $rowTenure["leaseFree_Name"];
			}

			$strPrice = price_format($row["Price"]);
		} elseif ($SaleLet == 2) {
			$strPrice = price_format($row["Price"]) . " per week / " . price_format(pw2pcm($row["Price"])) . " per month";
		}

		$osx = $row["osx"];
		$osy = $row["osy"];

		$strState = "<B><font color=red>" . $row["state_title"] . "</font></B>";

		if ($SaleLet == 1 && ($row["lease_free"] == 2 || $row["lease_free"] == 3)) {
			$strLongDescription .= '
<P><I>The following details have been provided by the vendor and have not been verified.</I></P>
<P><B>Tenure:</B> <I>' . $strTenure;
			if ($row["lease_length"]) {
				$strLongDescription .= ' with some ' . $row["lease_length"] . ' years remaining on the lease';
			}
			$strLongDescription .= '</I></P>
<P><B>Service Charge:</B> <I>' . $row["service_charge"] . '</I></P>
<P><B>Ground Rent:</B> <I>' . $row["ground_rent"] . '</I></P>
';
			if ($row["other_details"]) {
				$strLongDescription .= '<P><B>Other Details:</B> <I>' . $row["other_details"] . '</I></P>';
			}

		}

		if ($row["total_area"]) {
			$strLongDescription .= '
<P><B>Approximate Gross Internal Area:</B> <I>' . $row["total_area"] . ' square meters</I></P>';
		}
		$strImgAlt       = $strAddress;
		$strImgPathOpen  = '<img src="http://www.woosterstock.co.uk/customerPages/images/';
		$strImgPathClose = '" border="1" height="200" width="200" hspace="1" vspace="1" alt="' . $strImgAlt . '">';
		$strFpPathOpen   = '<img src="http://www.woosterstock.co.uk/customerPages/images/';
		$strFpPathClose  = '" border="1" hspace="2" vspace="2" alt="Floorplan">';

		$strImg0 = $row["image0"];
		if ($row["image1"]) {
			$strImg1 = $strImgPathOpen . $row["image1"] . $strImgPathClose;
		}
		if ($row["image2"]) {
			$strImg2 = $strImgPathOpen . $row["image2"] . $strImgPathClose;
		}
		if ($row["image3"]) {
			$strImg3 = '<br>' . $strImgPathOpen . $row["image3"] . $strImgPathClose;
		}
		if ($row["image4"]) {
			$strImg4 = $strImgPathOpen . $row["image4"] . $strImgPathClose;
		}
		if ($row["image5"]) {
			$strImg5 = '<br>' . $strImgPathOpen . $row["image5"] . $strImgPathClose;
		}
		if ($row["image6"]) {
			$strImg6 = $strImgPathOpen . $row["image6"] . $strImgPathClose;
		}
		if ($row["image7"]) {
			$strImg7 = '<br>' . $strImgPathOpen . $row["image7"] . $strImgPathClose;
		}
		if ($row["image8"]) {
			$strImg8 = $strImgPathOpen . $row["image8"] . $strImgPathClose;
		}
		if ($row["image9"]) {
			$strImg9 = '<br>' . $strImgPathOpen . $row["image9"] . $strImgPathClose;
		}
		if ($row["image10"]) {
			$strImg10 = $strImgPathOpen . $row["image10"] . $strImgPathClose;
		}

		if ($row["image11"]) {
			$strFp1 = $strPageBreak . '<p><font face=arial size=5><b>Floor Plans</b></font></p>' . $strFpPathOpen . $row["image11"] . $strFpPathClose;
		}
		if ($row["image12"]) {
			$strFp2 = $strPageBreak . $strFpPathOpen . $row["image12"] . $strFpPathClose;
		}
		if ($row["image13"]) {
			$strFp3 = $strPageBreak . $strFpPathOpen . $row["image13"] . $strFpPathClose;
		}
		if ($row["image14"]) {
			$strFp4 = $strPageBreak . $strFpPathOpen . $row["image14"] . $strFpPathClose;
		}
		if ($row["image15"]) {
			$strFp5 = $strPageBreak . $strFpPathOpen . $row["image15"] . $strFpPathClose;
		}

		$locationimage = "/images/mapping/crosshair2.gif";
		$pixelwidth    = 44;
		$pixelheight   = 44;

		$basex = GetTile($osx);
		$basey = GetTile($osy);

		// get surrounding tiles
		$tile1 = ($basex - 500) . "x" . ($basey + 500) . ".gif";
		$tile2 = ($basex) . "x" . ($basey + 500) . ".gif";
		$tile3 = ($basex + 500) . "x" . ($basey + 500) . ".gif";
		$tile4 = ($basex - 500) . "x" . ($basey) . ".gif";
		$tile5 = ($basex) . "x" . ($basey) . ".gif";
		$tile6 = ($basex + 500) . "x" . ($basey) . ".gif";
		$tile7 = ($basex - 500) . "x" . ($basey - 500) . ".gif";
		$tile8 = ($basex) . "x" . ($basey - 500) . ".gif";
		$tile9 = ($basex + 500) . "x" . ($basey - 500) . ".gif";

		$osx = (($osx - $basex) + 500);
		$osx = round($osx / 2.675);
		$osx = ($osx - ($pixelwidth / 2)); // minus n to compensate dot size, this is 50% dimensions of dot

		$osy = ($osy - $basey);
		$osy = (1000 - $osy);
		$osy = round($osy / 2.65);
		$osy = ($osy - ($pixelheight / 2));

		$layer = '<div id="layer" style="position:absolute; left:' . $osx . '; top:' . $osy . '; width:' . $pixelwidth . 'px; height:' . $pixelheight . 'px; z-index:2"><img src="' . $locationimage . '" width="' . $pixelwidth . '" height="' . $pixelheight . '"></div>';

	}
	echo html_header("Wooster & Stock - " . $strAddress . ", " . $strPostcode . "");
	?>

	<div align="center">
	<table width="600" align="center" bgcolor="#FFFFFF" border="0">
		<tr>
			<td>&nbsp;</td>
			<td>
				<h1><font face="Arial, Helvetica, sans-serif"><?php echo $strAddress; ?></font></h1>
			</td>
			<td align=right><a href="javascript:history.go(-1);"><img src="/images/logo<?php echo $intBranch; ?>.gif"
																	  width="175" height="88" border="0"
																	  alt="Wooster & Stock"></a></td>
		</tr>
		<tr>
			<td align="right"><font face="Arial, Helvetica, sans-serif" size="2"><b>Price:</b></font></td>
			<td colspan="2"><font face="Arial, Helvetica, sans-serif" size="2"><?php echo $strPrice; ?></font></td>
		</tr>
		<?php if ($SaleLet == 1) { ?>
			<tr>
				<td width="75">&nbsp;</td>
				<td colspan="2"><font face="Arial, Helvetica, sans-serif" size="2"><?php echo $strTenure; ?></font></td>
			</tr>
		<?php } ?>
		<tr>
			<td height="49" valign="top" width="75" align="right"><font face="Arial, Helvetica, sans-serif" size="2"><b>Address:</b></font>
			</td>
			<td height="49" colspan="2"><font face="Arial, Helvetica, sans-serif" size="2"><?php echo $strAddress; ?>
					<br>
					<?php echo $strArea; ?><br>
					<?php echo $strPostcode; ?></font></td>
		</tr>
		<tr>
			<td height="2" valign="top" width="75"><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;</font></td>
			<td height="2" colspan="2"><font face="Arial, Helvetica, sans-serif"
											 size="2"><?php echo $strDescription; ?></font></td>
		</tr>
	</table>
	<p align="center"><img src="http://www.woosterstock.co.uk/customerPages/images/<?php echo $strImg0; ?>" width="400"
						   height="400" alt="<?php echo $strImgAlt; ?>" border="1"></p>
	<table width="600" border="0">
		<tr>
			<td width="75" align="right" valign="top"><font face="Arial, Helvetica, sans-serif"
															size="2"><b>Description:</b></font></td>
			<td valign="top"><font face="Arial, Helvetica, sans-serif"
								   size="2"><?php echo $strLongDescription; ?></font></td>
		</tr>
	</table>
	<br>
	<?php echo $strPageBreak; ?>
	<!-- Images -->
	<?php echo $strImg1 . $strImg2 . $strImg3 . $strImg4 . $strImg5 . $strImg6 . $strImg7 . $strImg8 . $strImg9 . $strImg10; ?>
	<!-- End of Images-->


	<!-- Floorplans -->
	<?php echo "<p>" . $strFp1 . $strFp2 . $strFp3 . $strFp4 . $strFp5; ?>
	<!-- End of Floorplans -->
	<p class="PageBreak"></p>
	<div align=center>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div id="masterlayer" style="position:relative; width: 561px; height: 561px;">
						<?php echo $layer; ?>
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile1; ?>"
										 name="tile1" width="187" height="187" border="0" id="tile1"></td>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile2; ?>"
										 name="tile2" width="187" height="187" border="0" id="tile2"></td>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile3; ?>"
										 name="tile3" width="187" height="187" border="0" id="tile3"></td>
							</tr>
							<tr>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile4; ?>"
										 name="tile4" width="187" height="187" border="0" id="tile4"></td>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile5; ?>"
										 name="tile5" width="187" height="187" border="0" id="tile5"></td>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile6; ?>"
										 name="tile6" width="187" height="187" border="0" id="tile6"></td>
							</tr>
							<tr>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile7; ?>"
										 name="tile7" width="187" height="187" border="0" id="tile7"></td>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile8; ?>"
										 name="tile8" width="187" height="187" border="0" id="tile8"></td>
								<td><img src="http://www.woosterstock.co.uk/images/mapping/tiles/<?php echo $tile9; ?>"
										 name="tile9" width="187" height="187" border="0" id="tile9"></td>
							</tr>
							<tr>
								<td colspan="3"><img src="http://www.woosterstock.co.uk/images/mapping/copyright.gif"
													 alt="Copyright Collins Bartholomew Ltd 2003" width="561" height="9"
													 border="0"></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<br clear=all>
	<table width="600" border="0">
		<tr>
			<td width="99%" colspan="4" align="center">&nbsp;</td>
		</tr>
		<tr align="left">
			<td><span class="footerTitle">Wooster &amp; Stock</span> <br></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr align="left" valign="bottom">
			<td><p class="footerUrl">
					www.<font color="#FF9900">woosterstock</font>.co.uk&nbsp;&nbsp;</p>
			</td>
			<td><span class="footerBranch">Head Office</span><br>
        <span class="footerAddr">Quay House, Kings Grove<br>
London SE15 2NB<br>
020 7732 4757</span></td>
			<td><span class="footerBranch">Shad Thames</span><br>
        <span class="footerAddr">Spicy Quay, Shad Thames<br>
London SE1 2YG<br>
020 7378 7235 </span></td>
			<td><span class="footerBranch">Sydenham</span><br>
        <span class="footerAddr">109 Kirkdale, Sydenham<br>
London SE26 4QY<br>
020 8613 0060</span></td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr align="center">
			<td colspan="4"><span class="footerFine">We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property details. Document printed <?php echo $dateLong; ?></span>
			</td>
		</tr>
	</table>

<?php
} else { // view

	if ($_GET["action"] == "activate") {
		// check required field are populated, then set to Available
		if (!$_GET["propID"]) {
			echo "Missing Property ID";
			exit;
		} else {
			$intPropID = $_GET["propID"];
		}
		$sql = "SELECT * FROM property WHERE property.prop_ID = $intPropID LIMIT 1";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("property select error: " . $q->getMessage());
		}

		while ($row = $q->fetchRow()) {

			$postcodeLen = strlen($row["Postcode"]);
			$osxLen      = strlen($row["osx"]);
			$osyLen      = strlen($row["osy"]);

			if (!is_numeric($row["Price"])) {
				$errors[] = "Price must be a number";
			}
			if (!$row["house_number"]) {
				$errors[] = "House Number is required";
			}
			if (!$row["Address1"]) {
				$errors[] = "Street is required";
			}
			if ($postcodeLen < 6) {
				$errors[] = "Full Postcode is required";
			}
			if ($osxLen <> 6) {
				$errors[] = "OSX must be 6 characters";
			}
			if ($osyLen <> 6) {
				$errors[] = "OSY must be 6 characters";
			}
			if (!$row["description"]) {
				$errors[] = "Strap Line is required";
			}
			if (!$row["longDescription"]) {
				$errors[] = "Description is required";
			}
			if (!$row["image0"]) {
				$errors[] = "Main Image is required";
			}
			// check presence and dimensions of main (400x400), ftx (146x146), ftxx (56x56)
			// check all floorplans for maximum width (750)
			$notes = $row["measure"];
		}

		if ($errors) {
			echo html_header("Error");
			echo error_message($errors);
			exit;
		} else {
			$notes .= "\n$dateFriendly Activated";
			$sql = "
			UPDATE property SET
			state_of_trade_id = 1,
			measure = '$notes',
			DateEdited = '$dateToday'
			WHERE prop_ID = $intPropID";
			//echo $sql;
			$q = $db->query($sql);
			if (DB::isError($q)) {
				die("activate error: " . $q->getMessage());
			}

			$pageTitle = "Property Activated";
			echo html_header($pageTitle);

			echo '
<table width="600" align="center">
  <tr>
	<td><span class="pageTitle">' . $pageTitle . '</span></td>
	<td align="right"><a href="index.php">Main Menu</a></td>
  </tr>
  <tr>
    <td colspan="2">
	  <p>&nbsp;</p>
	  <p><a href="property.php?propID=' . $intPropID . '">Edit the property</a></p>
	  <p><a href="property.php?propID=' . $intPropID . '&view=print">Print the property</a></p>
	  <p><a href="mailer.php?propID=' . $intPropID . '">Send mailshot</a></p>
	</td>
  </tr>
</table>';
			exit;

		}

	}

	if ($_POST["action"] == "Update") {

		$intPropID = $_POST["propID"];

		if (!$_POST["Branch"]) {
			$errors[] = "Branch is a required field";
		} else {
			$Branch = trim($_POST["Branch"]);
		}

		if (!$_POST["Neg"]) {
			$errors[] = "Negotiator is a required field";
		} else {
			$Neg = trim($_POST["Neg"]);
		}

		if (!$_POST["Address1"]) {
			$errors[] = "Street Name is a required field";
		} else {
			$Address1 = format_street($_POST["Address1"]);
		}

		if (!$_POST["house_number"]) {
			$errors[] = "House Number is a required field";
		} else {
			$house_number = trim($_POST["house_number"]);
		}

		if (!$_POST["Postcode"]) {
			$errors[] = "Postcode is a required field";
		} else {
			$Postcode = strtoupper($_POST["Postcode"]);
		}

		if (!$_POST["osx"]) {
			$osx = 0;
		} else {
			$osx = trim($_POST["osx"]);
		}

		if (!$_POST["osy"]) {
			$osy = 0;
		} else {
			$osy = trim($_POST["osy"]);
		}

		$Price             = trim($_POST["Price"]);
		$PriceType         = trim($_POST["PriceType"]);
		$Price2            = trim($_POST["Price2"]);
		$PriceType2        = trim($_POST["PriceType2"]);
		$lease_free        = $_POST["lease_free"];
		$state_of_trade_id = $_POST["state_of_trade_id"];

		if ($intStatus == 6) { // if attamepting to set as exchanging contracts, show error
			$errors[] = "Exchanging Contracts is no longer used, please choose a different status";
		}

		$type_id = $_POST["type_id"];
		$area_id = $_POST["area_id"];

		if (!$_POST["description"]) {
			$errors[] = "Strap Line is a required field";
		} else {
			$description = trim(format_strap($_POST["description"]));
		}

		if (!$_POST["longDescription"]) {
			$errors[] = "Description is a required field";
		} else {
			$longDescription = trim($_POST["longDescription"]);
		}

		$gch          = $_POST["gch"];
		$doubleGlazed = $_POST["doubleGlazed"];
		$receptions   = $_POST["receptions"];
		$bedrooms     = $_POST["bedrooms"];
		$bathrooms    = $_POST["bathrooms"];
		$measure      = $_POST["measure"]; // this is the notes field
		$total_area   = $_POST["total_area"];
		$image0       = $_POST["image0"];
		$image1       = $_POST["image1"];
		$image2       = $_POST["image2"];
		$image3       = $_POST["image3"];
		$image4       = $_POST["image4"];
		$image5       = $_POST["image5"];
		$image6       = $_POST["image6"];
		$image7       = $_POST["image7"];
		$image8       = $_POST["image8"];
		$image9       = $_POST["image9"];
		$image10      = $_POST["image10"];
		$image11      = $_POST["image11"];
		$image12      = $_POST["image12"];
		$image13      = $_POST["image13"];
		$image14      = $_POST["image14"];
		$image15      = $_POST["image15"];
		$banner       = $_POST["banner"];
		$BannerLink   = $_POST["BannerLink"];
		$ThumbNail    = $_POST["ThumbNail"];
		$Countdown    = $_POST["Countdown"];
		$Dates        = $_POST["Dates"];
		$Hits         = $_POST["Hits"];
		$managed      = $_POST["managed"];
		$furnished    = $_POST["furnished"];

		$lease_length   = $_POST["lease_length"];
		$ground_rent    = htmlspecialchars($_POST["ground_rent"]);
		$ground_rent    = str_replace("�", "&pound;", $ground_rent);
		$service_charge = htmlspecialchars($_POST["service_charge"]);
		$service_charge = str_replace("�", "&pound;", $service_charge);
		$other_details  = htmlspecialchars($_POST["other_details"]);

		if ($errors) {
			echo html_header("Error");
			echo error_message($errors);
			exit;
		}
		$sql = "
	UPDATE property SET
	Price = '$Price',
	PriceType = '$PriceType',
	Price2 = '$Price2',
	PriceType2 = '$PriceType2',
	house_number = '$house_number',
	Address1 = '$Address1',
	Postcode = '$Postcode',
	osx = '$osx',
	osy = '$osy',
	lease_free = '$lease_free',
	state_of_trade_id = '$state_of_trade_id',
	type_id = '$type_id',
	area_id = '$area_id',
	longDescription = '$longDescription',
	description = '$description',
	gch = '$gch',
	doubleGlazed = '$doubleGlazed',
	receptions = '$receptions',
	bedrooms = '$bedrooms',
	bathrooms = '$bathrooms',
	measure = '$measure',
	total_area = '$total_area',
	image0 = '$image0',
	image1 = '$image1',
	image2 = '$image2',
	image3 = '$image3',
	image4 = '$image4',
	image5 = '$image5',
	image6 = '$image6',
	image7 = '$image7',
	image8 = '$image8',
	image9 = '$image9',
	image10 = '$image10',
	image11 = '$image11',
	image12 = '$image12',
	image13 = '$image13',
	image14 = '$image14',
	image15 = '$image15',
	banner = '$banner',
	BannerLink = '$BannerLink',
	ThumbNail = '$ThumbNail',
	Countdown = '$Countdown',
	DateEdited = '$dateToday',
	Hits = '$Hits',
	Neg = '$Neg',
	Branch = '$Branch',
	managed = '$managed',
	furnished = '$furnished',
	lease_length = '$lease_length',
	service_charge = '$service_charge',
	ground_rent = '$ground_rent',
	other_details = '$other_details'
	WHERE prop_ID = $intPropID";
		//echo $sql;
		$q = $db->query($sql);
		if (DB::isError($q)) {
			die("property update error: " . $q->getMessage());
		}

		$pageTitle = "Update Property Complete";
		echo html_header($pageTitle);

		echo '
<table width="600" align="center">
  <tr>
	<td><span class="pageTitle">Edit Property Complete</span></td>
	<td align="right"><a href="index.php">Main Menu</a></td>
  </tr>
  <tr>
    <td colspan="2">
	  <p>&nbsp;</p>
	  <p><a href="property.php?propID=' . $intPropID . '">Edit the property</a></p>
	  <p><a href="property.php?propID=' . $intPropID . '&view=print">Print the property</a></p>
	  <p><a href="mailer.php?propID=' . $intPropID . '">Send mailshot</a></p>';
		if ($_GET["searchLink"]) {
			echo '<p><a href="' . urldecode($_GET["searchLink"]) . '">Back to last search</a></p>';
		}
		echo '
	</td>
  </tr>
</table>
';

	} elseif ($_POST["action"] == "Insert") {

		if (!$_POST["Branch"]) {
			$errors[] = "Branch is a required field";
		} else {
			$Branch = trim($_POST["Branch"]);
		}

		if (!$_POST["Neg"]) {
			$errors[] = "Negotiator is a required field";
		} else {
			$Neg = trim($_POST["Neg"]);
		}

		if (!$_POST["Address1"]) {
			$errors[] = "Street Name is a required field";
		} else {
			$Address1 = trim(format_street($_POST["Address1"]));
		}

		if (!$_POST["house_number"]) {
			$errors[] = "House Number is a required field";
		} else {
			$house_number = trim($_POST["house_number"]);
		}

		$Price      = trim($_POST["Price"]);
		$PriceType  = trim($_POST["PriceType"]);
		$Price2     = trim($_POST["Price2"]);
		$PriceType2 = trim($_POST["PriceType2"]);

		$lease_free        = $_POST["lease_free"];
		$state_of_trade_id = $_POST["state_of_trade_id"];
		$type_id           = $_POST["type_id"];
		$area_id           = $_POST["area_id"];

		if (!$_POST["description"]) { // create temporary strap line
			if ($_POST["bedrooms"]) {
				$description = $_POST["bedrooms"] . ' Bed ';
			}
			$sqlD = "SELECT * FROM propType WHERE type_ID = $type_id";
			$qD   = $db->query($sqlD);
			while ($rowD = $qD->fetchRow()) {
				$description .= $rowD["type_Title"];
			}
		} else {
			$description = trim(format_strap($_POST["description"]));
		}

		if (!$_POST["longDescription"]) {
			$longDescription = '<p>Full details to follow</p>';
		} else {
			$longDescription = trim($_POST["longDescription"]);
		}

		$gch            = $_POST["gch"];
		$doubleGlazed   = $_POST["doubleGlazed"];
		$receptions     = $_POST["receptions"];
		$bedrooms       = $_POST["bedrooms"];
		$bathrooms      = $_POST["bathrooms"];
		$measure        = $_POST["measure"];
		$total_area     = $_POST["total_area"];
		$image0         = $_POST["image0"];
		$image1         = $_POST["image1"];
		$image2         = $_POST["image2"];
		$image3         = $_POST["image3"];
		$image4         = $_POST["image4"];
		$image5         = $_POST["image5"];
		$image6         = $_POST["image6"];
		$image7         = $_POST["image7"];
		$image8         = $_POST["image8"];
		$image9         = $_POST["image9"];
		$image10        = $_POST["image10"];
		$image11        = $_POST["image11"];
		$image12        = $_POST["image12"];
		$image13        = $_POST["image13"];
		$image14        = $_POST["image14"];
		$image15        = $_POST["image15"];
		$banner         = $_POST["banner"];
		$BannerLink     = $_POST["BannerLink"];
		$ThumbNail      = $_POST["ThumbNail"];
		$Countdown      = $_POST["Countdown"];
		$Dates          = $_POST["Dates"];
		$managed        = $_POST["managed"];
		$furnished      = $_POST["furnished"];
		$lease_length   = $_POST["lease_length"];
		$ground_rent    = htmlspecialchars($_POST["ground_rent"]);
		$ground_rent    = str_replace("�", "&pound;", $ground_rent);
		$service_charge = htmlspecialchars($_POST["service_charge"]);
		$service_charge = str_replace("�", "&pound;", $service_charge);
		$other_details  = htmlspecialchars($_POST["other_details"]);

		if (!$_POST["SaleLet"]) {
			$errors[] = "Sales or Lettings missing";
		} else {
			$SaleLet = $_POST["SaleLet"];
		}

		if ($errors) {
			echo html_header("Error");
			echo error_message($errors);
			exit;
		}

		$sql = "
	INSERT INTO property
	(Price,Pricetype,Price2,PriceType2,house_number,Address1,Postcode,osx,osy,lease_free,state_of_trade_id,type_id,
	area_id,longDescription,description,gch,doubleGlazed,receptions,bedrooms,bathrooms,
	measure,total_area,image0,image1,image2,image3,image4,image5,image6,image7,image8,
	image9,image10,image11,image12,image13,image14,image15,banner,BannerLink,ThumbNail,
	Countdown,Dates,Hits,Neg,Branch,SaleLet,managed,furnished,lease_length,service_charge,ground_rent,other_details)
	VALUES
	('$Price','$PriceType','$Price2','$PriceType2','$house_number','$Address1','$Postcode','$osx','$osy','$lease_free','$state_of_trade_id','$type_id',
	'$area_id','$longDescription','$description','$gch','$doubleGlazed','$receptions','$bedrooms','$bathrooms',
	'$measure','$total_area','$image0','$image1','$image2','$image3','$image4','$image5','$image6','$image7','$image8',
	'$image9','$image10','$image11','$image12','$image13','$image14','$image15','$banner','$BannerLink','$ThumbNail',
	'$Countdown','$dateToday','$Hits','$Neg','$Branch','$SaleLet','$managed','$furnished','$lease_length','$service_charge','$ground_rent','$other_details')";
		$q   = $db->query($sql);
		//echo $sql;
		if (DB::isError($q)) {
			die("insert error: " . $q->getMessage());
		}

		$query     = 'SELECT LAST_INSERT_ID()';
		$result    = mysql_query($query);
		$rec       = mysql_fetch_array($result);
		$insert_id = $rec[0];

		$pageTitle = "Add Property Complete";
		echo html_header($pageTitle);
		echo '
<table width="600" align="center">
  <tr>
	<td><span class="pageTitle">' . $pageTitle . '</span></td>
	<td align="right"><a href="index.php">Main Menu</a></td>
  </tr>
  <tr>
    <td colspan="2">
	  <p>&nbsp;</p>
	  <p><a href="property.php?propID=' . $insert_id . '">Edit the property</p>
	  <p><a href="property.php?propID=' . $intPropID . '&view=print">Print the property</a></p>
	  <p><a href="property.php?SaleLet=' . $SaleLet . '">Add another property</a></p>';
		if ($_GET["searchLink"]) {
			echo '<p><a href="' . urldecode($_GET["searchLink"]) . '">Back to last search</a></p>';
		}
		echo '
	</td>
  </tr>
</table>
';

	} else { // if form is not submitted

		if (!$_GET["propID"]) { // if id not entered, show insert form
			if (!$_GET["SaleLet"]) {
				echo html_header("Sales or Lettings?") . '
			<p><a href="?SaleLet=1">Sales</a></p>
			<p><a href="?SaleLet=2">Lettings</a></p>
			';
				exit;
			}
			$action    = "Insert";
			$intStatus = 11; // set status to pending
			$pageTitle = "Insert New Property";
		} else {
			$intPropID = $_GET["propID"];
			$action    = "Update";
			$pageTitle = "Edit Property Details";

			$sql = "SELECT * FROM property WHERE property.prop_ID = $intPropID LIMIT 1";
			$q   = $db->query($sql);
			if (DB::isError($q)) {
				die("property select error: " . $q->getMessage());
			}

			while ($row = $q->fetchRow()) {
				$intPrice      = $row["Price"];
				$intPriceType  = $row["PriceType"];
				$intPrice2     = $row["Price2"];
				$intPriceType2 = $row["PriceType2"];
				$strNumber     = $row["house_number"];
				$strStreet     = $row["Address1"];
				$strPostcode   = $row["Postcode"];
				$intOSX        = $row["osx"];
				$intOSY        = $row["osy"];
				$intTenure     = $row["lease_free"];
				$intStatus     = $row["state_of_trade_id"];
				$intType       = $row["type_id"];

				if ($intType == "1" || $intType == "2" || $intType == "5" || $intType == "7") {
					$strLinkType = "House";
				} elseif ($intType == "3" || $intType == "4" || $intType == "6") {
					$strLinkType = "Apartment";
				} elseif ($intType == "8") {
					$strLinkType = "Commercial";
				} elseif ($intType == "9") {
					$strLinkType = "Live/Work";
				}

				$intArea            = $row["area_id"];
				$strLongDescription = $row["longDescription"];
				$strDescription     = $row["description"];
				$strGCH             = $row["gch"];
				$strDG              = $row["doubleGlazed"];
				$intReceptions      = $row["receptions"];
				$intBedrooms        = $row["bedrooms"];
				$intBathrooms       = $row["bathrooms"];
				$strMeasure         = $row["measure"];
				$strTotalArea       = $row["total_area"];
				$strImage0          = $row["image0"];
				$strImage1          = $row["image1"];
				$strImage2          = $row["image2"];
				$strImage3          = $row["image3"];
				$strImage4          = $row["image4"];
				$strImage5          = $row["image5"];
				$strImage6          = $row["image6"];
				$strImage7          = $row["image7"];
				$strImage8          = $row["image8"];
				$strImage9          = $row["image9"];
				$strImage10         = $row["image10"];
				$strImage11         = $row["image11"];
				$strImage12         = $row["image12"];
				$strImage13         = $row["image13"];
				$strImage14         = $row["image14"];
				$strImage15         = $row["image15"];
				$strBanner          = $row["banner"];
				$strBannerLink      = $row["BannerLink"];
				$strThumbNail       = $row["ThumbNail"];
				$strCountdown       = $row["Countdown"];
				$strDates           = $row["Dates"];
				$strHits            = $row["Hits"];
				$intNeg             = $row["Neg"];
				$intBranch          = $row["Branch"];
				$intSaleLet         = $row["SaleLet"];
				$strFurnished       = $row["furnished"];
				$strManaged         = $row["managed"];
				$SaleLet            = $row["SaleLet"];

				$strLeaseLength = $row["lease_length"];
				$strGroundRent  = $row["ground_rent"];
				if (!$strGroundRent) {
					$strGroundRent = "TBC";
				}
				$strServiceCharge = $row["service_charge"];
				if (!$strServiceCharge) {
					$strServiceCharge = "TBC";
				}
				$strOtherDetails = $row["other_details"];
			}

		}

		$sqlNeg = "SELECT * FROM Staff WHERE (Staff_Type = 'SalesNegotiator' OR Staff_Type = 'LettingsNegotiator') AND Staff_Status = 'Current' ORDER BY Staff_Fname";
		$qNeg   = $db->query($sqlNeg);
		if (DB::isError($qNeg)) {
			die("insert error: " . $qNeg->getMessage());
		}
		if (!$intNeg) {
			$strRenderNeg .= '<option value=""> -- select -- </option>';
		}
		while ($rowNeg = $qNeg->fetchRow()) {
			$strRenderNeg .= '<option value="' . $rowNeg["Staff_ID"] . '"';
			if ($intNeg == $rowNeg["Staff_ID"]) {
				$strRenderNeg .= ' selected';
			}
			$strRenderNeg .= '>' . $rowNeg["Staff_Fname"] . ' ' . $rowNeg["Staff_Sname"] . '</option>';
		}

		$sqlBranch = "SELECT * FROM Branch ORDER BY Branch_Title";
		$qBranch   = $db->query($sqlBranch);
		if (DB::isError($qBranch)) {
			die("insert error: " . $qBranch->getMessage());
		}
		if (!$intBranch) {
			$strRenderBranch .= '<option value=""> -- select -- </option>';
		}
		while ($rowBranch = $qBranch->fetchRow()) {
			$strRenderBranch .= '<option value="' . $rowBranch["Branch_ID"] . '"';
			if ($intBranch == $rowBranch["Branch_ID"]) {
				$strRenderBranch .= ' selected';
			}
			$strRenderBranch .= '>' . $rowBranch["Branch_Title"] . '</option>';
		}

		$sqlPropType = "SELECT type_ID, type_Title FROM propType";
		$qPropType   = $db->query($sqlPropType);
		if (DB::isError($qPropType)) {
			die("insert error: " . $qPropType->getMessage());
		}
		if (!$intType) {
			$strRenderPropType .= '<option value=""> -- select -- </option>';
		}
		while ($rowPropType = $qPropType->fetchRow()) {
			$strRenderPropType .= '<option value="' . $rowPropType["type_ID"] . '"';
			if ($intType == $rowPropType["type_ID"]) {
				$strRenderPropType .= ' selected';
			}
			$strRenderPropType .= '>' . $rowPropType["type_Title"] . '</option>';
		}

		$sqlArea = "SELECT * FROM Area ORDER BY area_title";
		$qArea   = $db->query($sqlArea);
		if (DB::isError($qArea)) {
			die("insert error: " . $qArea->getMessage());
		}
		if (!$intArea) {
			$strRenderArea .= '<option value=""> -- select -- </option>';
		}
		while ($rowArea = $qArea->fetchRow()) {
			$strAreaArrayX[]  = $rowArea["area_osx"];
			$strAreaArrayY[]  = $rowArea["area_osy"];
			$strAreaArrayPC[] = $rowArea["area_pc"];

			$strRenderArea .= '<option value="' . $rowArea["area_ID"] . '"';
			if ($intArea == $rowArea["area_ID"]) {
				$strRenderArea .= ' selected';
			}
			$strRenderArea .= '>' . $rowArea["area_title"] . '</option>';
		}

		if ($SaleLet == 1) {
			$sqlStatus = "SELECT state_ID, state_Title FROM state_of_trade";
		} else {
			$sqlStatus = "SELECT state_ID, state_Title FROM state_of_trade_let";
		}
		$qStatus = $db->query($sqlStatus);
		if (DB::isError($qStatus)) {
			die("insert error: " . $qStatus->getMessage());
		}

		while ($rowStatus = $qStatus->fetchRow()) {
			$strRenderStatus .= '<option value="' . $rowStatus["state_ID"] . '"';
			if ($intStatus == $rowStatus["state_ID"]) {
				$strRenderStatus .= ' selected';
			}
			$strRenderStatus .= '>' . $rowStatus["state_Title"] . '</option>';
		}

		$sqlTenure = "SELECT id_LeaseFree, leaseFree_Name FROM leaseFree";
		$qTenure   = $db->query($sqlTenure);
		if (DB::isError($qTenure)) {
			die("insert error: " . $qTenure->getMessage());
		}
		if (!$intTenure) {
			//$strRenderTenure .= '<option value=""> -- select -- </option>';
		}
		while ($rowTenure = $qTenure->fetchRow()) {
			$strRenderTenure .= '<option value="' . $rowTenure["id_LeaseFree"] . '"';
			if ($intTenure == $rowTenure["id_LeaseFree"]) {
				$strRenderTenure .= ' selected';
				$strTenureTitle = $rowTenure["leaseFree_Name"];
			}
			$strRenderTenure .= '>' . $rowTenure["leaseFree_Name"] . '</option>';
		}

		$sqlFurnished = "SELECT * FROM furnished";
		$qFurnished   = $db->query($sqlFurnished);
		if (DB::isError($qFurnished)) {
			die("insert error: " . $qFurnished->getMessage());
		}

		while ($rowFurnished = $qFurnished->fetchRow()) {
			$strRenderFurnished .= '<option value="' . $rowFurnished["Furnished_ID"] . '"';
			if ($strFurnished == $rowFurnished["Furnished_ID"]) {
				$strRenderFurnished .= ' selected';
			}
			$strRenderFurnished .= '>' . $rowFurnished["Furnished_Title"] . '</option>';
		}

		echo html_header($pageTitle);
		?>
		<script language="JavaScript" type="text/JavaScript">
			function populateCoords() {
				var osx = new Array()
				<?php
				$count = count($strAreaArrayX);
				for($counter=0; $counter < $count; $counter++)
				{
				  echo 'osx['.$counter.'] = "'.$strAreaArrayX[$counter].'"
				  ';
				}
				?>

				var osy = new Array()
				<?php
				$count = count($strAreaArrayY);
				for($counter=0; $counter < $count; $counter++)
				{
				  echo 'osy['.$counter.'] = "'.$strAreaArrayY[$counter].'"
				  ';
				}
				?>

				var pc = new Array()
				<?php
				$count = count($strAreaArrayPC);
				for($counter=0; $counter < $count; $counter++)
				{
				  echo 'pc['.$counter.'] = "'.$strAreaArrayPC[$counter].'"
				  ';
				}
				?>
				var optionNumber = document.form.area_id.selectedIndex

				if (document.form.osx.value == "") {
					document.form.osx.value = osx[(optionNumber - 1)]
				}
				if (document.form.osy.value == "") {
					document.form.osy.value = osy[(optionNumber - 1)]
				}
				if (document.form.Postcode.value == "") {
					document.form.Postcode.value = pc[(optionNumber - 1)]
				}
			}
		</script>

		<form method="post" enctype="multipart/form-data" name="form">
		<input type="hidden" name="propID" value="<?php echo $intPropID; ?>">
		<input type="hidden" name="action" value="<?php echo $action; ?>">
		<input type="hidden" name="searchLink" value="<?php echo urlencode($_GET["searchLink"]); ?>">
		<table width="600" align="center">
			<tr>
				<td><span class="pageTitle"><?php echo $pageTitle; ?></span></td>
				<td align="right"><a href="?propID=<?php echo $intPropID; ?>&view=print">Print</a></td>
				<td align="right"><a href="index.php">Main Menu</a></td>
			</tr>
		</table>
		<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
		<tr>
			<td align="right" class="greyForm">Branch</td>
			<td align="left" class="greyForm"><select name="Branch" id="Branch" style="width: 190px;">
					<?php echo $strRenderBranch; ?> </select></td>
			<td align="right" class="greyForm">Negotiator</td>
			<td colspan="3" align="left" class="greyForm"><select name="Neg" style="width: 190px;">
					<?php echo $strRenderNeg; ?> </select></td>
		</tr>
		<tr>
			<th colspan="6" class="greyForm">Property Details</th>
		</tr>



		<?php if ($SaleLet == 1) { ?>
			<tr>
				<td width="100" align="right" class="greyForm">Price</td>
				<td width="200" class="greyForm"><input name="Price" type="text" style="width: 80px;"
														onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;"
														value="<?php echo $intPrice; ?>" size="8" maxlength="8">
				</td>
				<td width="100" align="right" nowrap class="greyForm">Property Type</td>
				<td width="200" colspan="3" class="greyForm"><select name="type_id" style="width: 190px;">
						<?php echo $strRenderPropType; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Number</td>
				<td class="greyForm"><input type="text" name="house_number" value="<?php echo $strNumber; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">Market State</td>
				<td colspan="3" class="greyForm"><?php if ($intStatus <> 11) { ?>
						<select name="state_of_trade_id" style="width: 190px;"
								onChange="alert('You are changing the status of this property. Please enter the REASON, DATE and YOUR INITIALS in the notes field!')">
							<?php echo $strRenderStatus; ?>
						</select>
					<?PHP } else { ?>
						<input type="hidden" name="state_of_trade_id" value="11">
						Pending &nbsp; <a
								href="?action=activate&propID=<?php echo $intPropID; ?>"><strong>ACTIVATE</strong></a>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Street</td>
				<td class="greyForm"><input type="text" name="Address1" value="<?php echo $strStreet; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">C/Heating</td>
				<td width="200" class="greyForm"><input type="checkbox" name="gch" value="1"<?php if ($strGCH) {
						echo " checked";
					} ?>>
				</td>
				<td align="right" class="greyForm">D/Glazing</td>
				<td width="50" class="greyForm"><input type="checkbox" name="doubleGlazed" value="1"<?php if ($strDG) {
						echo " checked";
					} ?>></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Area</td>
				<td class="greyForm"><select name="area_id" style="width: 190px;" onChange="populateCoords()">
						<?php echo $strRenderArea; ?>
					</select>
				</td>
				<td align="right" class="greyForm">Tenure</td>
				<td colspan="3" class="greyForm"><select name="lease_free" style="width: 190px;">
						<?php echo $strRenderTenure; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Postcode</td>
				<td class="greyForm"><input name="Postcode" type="text" value="<?php echo $strPostcode; ?>" size="8"
											maxlength="8" style="width: 80px;">
				</td>
				<td align="right" class="greyForm"><?php if ($intOSX && $intOSY) { ?>
					<a href="javascript:gomap(<?php echo $intOSX . "," . $intOSY; ?>)">
						<?php } ?>
						Coordinates
						<?php if ($intOSX && $intOSY) { ?>
					</a>
				<?php } ?></td>
				<td colspan="3" class="greyForm"><input name="osx" type="text" value="<?php if ($intOSX) {
						echo $intOSX;
					} ?>" size="6" maxlength="6"
														onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;">
					x
					<input name="osy" type="text" value="<?php if ($intOSY) {
						echo $intOSY;
					} ?>" size="6" maxlength="6"
						   onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;">
				</td>
			</tr>

		<?php } elseif ($SaleLet == 2) { ?>



			<tr>
				<td width="100" align="right" class="greyForm">Price p/w</td>
				<td width="200" class="greyForm"><input name="Price" type="text" style="width: 80px;"
														onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;"
														value="<?php echo $intPrice; ?>" size="8" maxlength="8">
					<select name="PriceType">
						<option value="1"<?php if ($intPriceType == 1) {
							echo " selected";
						} ?>>Long Term
						</option>
						<option value="2"<?php if ($intPriceType == 2) {
							echo " selected";
						} ?>>Short Term
						</option>
					</select>
				</td>
				<td width="100" align="right" nowrap class="greyForm">Property Type</td>
				<td width="200" colspan="3" class="greyForm"><select name="type_id" style="width: 190px;">
						<?php echo $strRenderPropType; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Price p/w</td>
				<td class="greyForm"><input name="Price2" type="text" style="width: 80px;"
											onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;"
											value="<?php echo $intPrice2; ?>" size="8" maxlength="8">
					<select name="PriceType2">
						<option value="0"<?php if ($intPriceType2 == 0) {
							echo " selected";
						} ?>></option>
						<option value="1"<?php if ($intPriceType2 == 1) {
							echo " selected";
						} ?>>Long Term
						</option>
						<option value="2"<?php if ($intPriceType2 == 2) {
							echo " selected";
						} ?>>Short Term
						</option>
					</select></td>
				<td align="right" class="greyForm">Market State</td>
				<td colspan="3" class="greyForm"><?php if ($intStatus <> 11) { ?>
						<select name="state_of_trade_id" style="width: 190px;"
								onChange="alert('You are changing the status of this property. Please enter the REASON, DATE and YOUR INITIALS in the notes field!')">
							<?php echo $strRenderStatus; ?>
						</select>
					<?php } else { ?>
						<input type="hidden" name="state_of_trade_id" value="11">
						Pending &nbsp; <a
								href="?action=activate&propID=<?php echo $intPropID; ?>"><strong>ACTIVATE</strong></a>
					<?php } ?></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Number</td>
				<td class="greyForm"><input type="text" name="house_number" value="<?php echo $strNumber; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">Managed</td>
				<td colspan="3" class="greyForm"><select name="managed" style="width: 190px;">
						<option value="No"<?php if ($strManaged == "No") {
							echo " selected";
						} ?>>No
						</option>
						<option value="Yes"<?php if ($strManaged == "Yes") {
							echo " selected";
						} ?>>Yes
						</option>
					</select></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Street</td>
				<td class="greyForm"><input type="text" name="Address1" value="<?php echo $strStreet; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">C/Heating</td>
				<td width="200" class="greyForm"><input type="checkbox" name="gch" value="1"<?php if ($strGCH) {
						echo " checked";
					} ?>>
				</td>
				<td align="right" class="greyForm">D/Glazing</td>
				<td width="50" class="greyForm"><input type="checkbox" name="doubleGlazed" value="1"<?php if ($strDG) {
						echo " checked";
					} ?>></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Area</td>
				<td class="greyForm"><select name="area_id" style="width: 190px;" onChange="populateCoords()">
						<?php echo $strRenderArea; ?>
					</select>
				</td>
				<td align="right" class="greyForm">Furnished</td>
				<td colspan="3" class="greyForm">
					<select name="furnished" style="width: 190px;">
						<?php echo $strRenderFurnished; ?>
					</select></td>

			</tr>
			<tr>
				<td class="greyForm" align="right">Postcode</td>
				<td class="greyForm"><input name="Postcode" type="text" value="<?php echo $strPostcode; ?>" size="8"
											maxlength="8" style="width: 80px;">
				</td>
				<td align="right" class="greyForm"><?php if ($intOSX && $intOSY) { ?>
					<a href="javascript:gomap(<?php echo $intOSX . "," . $intOSY; ?>)">
						<?php } ?>
						Coordinates
						<?php if ($intOSX && $intOSY) { ?>
					</a>
				<?php } ?></td>
				<td colspan="3" class="greyForm"><input name="osx" type="text" value="<?php echo $intOSX; ?>" size="6"
														maxlength="6"
														onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;">
					x
					<input name="osy" type="text" value="<?php echo $intOSY; ?>" size="6" maxlength="6"
						   onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;">
				</td>
			</tr>


		<?php } ?>









		<tr align="center">
			<td colspan="6" class="greyForm">Receptions:
				<select name="receptions">
					<?php
					for ($i = 1; $i <= 9; $i++) {
						echo '<option value="' . $i . '"';
						if ($i == $intReceptions) {
							echo ' selected';
						}
						echo '>' . $i . '</option>';
					}
					?>
				</select> &nbsp;&nbsp;Bedrooms:
				<select name="bedrooms">
					<?php
					for ($i = 0; $i <= 9; $i++) {
						echo '<option value="' . $i . '"';
						if ($i == $intBedrooms) {
							echo ' selected';
						}
						echo '>' . $i . '</option>';
					}
					?>
				</select> &nbsp;&nbsp;Bathrooms:
				<select name="bathrooms">
					<?php
					for ($i = 1; $i <= 9; $i++) {
						echo '<option value="' . $i . '"';
						if ($i == $intBathrooms) {
							echo ' selected';
						}
						echo '>' . $i . '</option>';
					}
					?>
				</select></td>
		</tr>
		<tr align="center">
			<td colspan="8">
				<hr noshade size="1">
				<input name="Submit" type="submit" value="   <?php echo $action; ?> Property   ">
				<hr noshade size="1">
			</td>
		</tr>
		</table>
		<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
			<tr>
				<th colspan="2" class="greyForm">Strap Line</th>
			</tr>
			<tr>
				<td colspan="2" class="greyForm"><input name="description" type="text" style="width:100%;"
														value="<?php echo $strDescription; ?>" maxlength="110"></td>
			</tr>
			<tr>
				<th colspan="2" class="greyForm">Description</th>
			</tr>
			<tr>
				<td colspan="2" class="greyForm"><textarea name="longDescription"
														   style="width:100%; height:400px"><?php echo $strLongDescription; ?></textarea>
					<script language="javascript1.2">
						editor_generate('longDescription');
					</script>
				</td>
			</tr>
			<?php if ($SaleLet == 1 && ($intTenure == 2 || $intTenure == 3)) { ?>
				<tr>
					<td colspan="2"><EM>The following details have been provided by the vendor and have not been
							verified.</EM></td>
				</tr>
				<tr>
					<td><strong>Tenure:</strong></td>
					<td><em><?php echo $strTenureTitle; ?> with some
							<input name="lease_length" type="text" id="lease_length" style="font-style:italic" size="4"
								   maxlength="4" value="<?php echo $strLeaseLength; ?>">
							years remaining on the lease</em></td>
				</tr>
				<tr>
					<td><strong>Service Charge:</strong></td>
					<td><input name="service_charge" type="text" id="service_charge"
							   style="width:450px; font-style:italic" value="<?php echo $strServiceCharge; ?>"
							   maxlength="220"></td>
				</tr>
				<tr>
					<td><strong>Grount Rent:</strong></td>
					<td><input name="ground_rent" type="text" id="ground_rent" style="width:450px; font-style:italic"
							   value="<?php echo $strGroundRent; ?>" maxlength="220"></td>
				</tr>
				<tr>
					<td><strong>Other Details: </strong></td>
					<td><input name="other_details" type="text" id="other_details"
							   style="width:450px; font-style:italic" value="<?php echo $strOtherDetails; ?>"
							   maxlength="220"></td>
				</tr>
			<?php } ?>
			<tr>
				<td colspan="2"><strong>Approximate Gross Internal Area:</strong>
					<input name="total_area" type="text" value="<?php echo $strTotalArea; ?>" size="4" maxlength="6">
					<I>square meters</I>
				</td>
			</tr>
			<tr align="center">
				<td colspan="7">
					<hr noshade size="1">
					<input name="Submit" type="submit" value="   <?php echo $action; ?> Property   ">
					<hr noshade size="1">
				</td>
			</tr>
		</table>

		<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
			<tr>
				<th colspan="2" class="greyForm">Images</th>
				<th colspan="2" align="center" class="greyForm">Floorplans</th>
			</tr>
			<tr>
				<td width="100" align="right" class="greyForm">Thumb Nail</td>
				<td width="200" class="greyForm"><input type="text" name="ThumbNail" style="width: 190px;"
														value="<?php echo $strThumbNail; ?>">
				</td>
				<td width="100" align="right" class="greyForm">Floorplan1</td>
				<td width="200" class="greyForm"><input type="text" name="image11" value="<?php echo $strImage11; ?>"
														style="width: 190px;"></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Main Image</td>
				<td class="greyForm"><input type="text" name="image0" value="<?php echo $strImage0; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">Floorplan2</td>
				<td class="greyForm"><input type="text" name="image12" value="<?php echo $strImage12; ?>"
											style="width: 190px;"></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image1</td>
				<td class="greyForm"><input type="text" name="image1" value="<?php echo $strImage1; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">Floorplan3</td>
				<td class="greyForm"><input type="text" name="image13" value="<?php echo $strImage13; ?>"
											style="width: 190px;"></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image2</td>
				<td class="greyForm"><input type="text" name="image2" value="<?php echo $strImage2; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">Floorplan4</td>
				<td class="greyForm"><input type="text" name="image14" value="<?php echo $strImage14; ?>"
											style="width: 190px;"></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image3</td>
				<td class="greyForm"><input type="text" name="image3" value="<?php echo $strImage3; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">Floorplan5</td>
				<td class="greyForm"><input type="text" name="image15" value="<?php echo $strImage15; ?>"
											style="width: 190px;"></td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image4</td>
				<td class="greyForm"><input type="text" name="image4" value="<?php echo $strImage4; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">&nbsp;</td>
				<td class="greyForm">&nbsp;</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image5</td>
				<td class="greyForm"><input type="text" name="image5" value="<?php echo $strImage5; ?>"
											style="width: 190px;">
				</td>
				<td align="right" class="greyForm">&nbsp;</td>
				<td class="greyForm">&nbsp;</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image6</td>
				<td class="greyForm"><input type="text" name="image6" value="<?php echo $strImage6; ?>"
											style="width: 190px;">
				</td>
				<td class="greyForm">&nbsp;</td>
				<td class="greyForm">&nbsp;</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image7</td>
				<td class="greyForm"><input type="text" name="image7" value="<?php echo $strImage7; ?>"
											style="width: 190px;">
				</td>
				<td class="greyForm">&nbsp;</td>
				<td class="greyForm">&nbsp;</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image8</td>
				<td class="greyForm"><input type="text" name="image8" value="<?php echo $strImage8; ?>"
											style="width: 190px;">
				</td>
				<td class="greyForm">&nbsp;</td>
				<td class="greyForm">&nbsp;</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image9</td>
				<td class="greyForm"><input type="text" name="image9" value="<?php echo $strImage9; ?>"
											style="width: 190px;">
				</td>
				<td class="greyForm">&nbsp;</td>
				<td class="greyForm">&nbsp;</td>
			</tr>
			<tr>
				<td class="greyForm" align="right">Image10</td>
				<td class="greyForm"><input type="text" name="image10" value="<?php echo $strImage10; ?>"
											style="width: 190px;">
				</td>
				<td class="greyForm">&nbsp;</td>
				<td class="greyForm">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
			<tr>
				<th class="greyForm">Notes</th>
			</tr>
			<tr>
				<td class="greyForm"><textarea name="measure" cols="30" rows="16"
											   style="width: 590;"><?php echo $strMeasure; ?></textarea>
				</td>
			</tr>
			<tr align="center">
				<td class="greyForm"><b><?php echo $strHits; ?></b> hits since added on <b><?php echo $strDates; ?></b>
				</td>
			</tr>
			<tr align="center">
				<td>
					<hr noshade size="1">
					<input name="Submit" type="submit" value="   <?php echo $action; ?> Property   ">
					<input type="hidden" name="SaleLet" value="<?php echo $SaleLet; ?>">
					<input type="hidden" name="banner" value="<?php echo $strBanner; ?>">
					<input type="hidden" name="BannerLink" value="<?php echo $strBannerLink; ?>">
					<hr noshade size="1">
				</td>
			</tr>
		</table>
		</form>
		</body>
		</html>
	<?php } // end action if ?>
<?php } // end view if ?>