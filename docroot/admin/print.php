<?php
session_start();
$pageTitle = "Property";

require("global.php");
require("secure.php");

if (!$_GET["propID"]) {
	echo "Missing Property ID";
	exit;
} else {
	$intPropID = $_GET["propID"];
}

$strPageBreak = '
	<DIV style="page-break-after:always"></DIV>
	';

$sql = "SELECT *,DATE_FORMAT(property.date_available,'%D %M %Y') as date_available_formatted FROM property, area, state_of_trade WHERE property.prop_ID = $intPropID  AND property.area_ID = area.area_ID AND property.state_of_trade_id = state_of_trade.state_ID LIMIT 1";
$q = $db->query($sql);
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

	if ($row["suffix"] == "OIRO") {
		$suffix = " (offers in region of)";
	} elseif ($row["suffix"] == "OIEO") {
		$suffix = " (offers in excess of)";
	}

	if ($SaleLet == 1) {
		// AND property.lease_free = leasefree.id_LeaseFree
		$sqlTenure = "SELECT * FROM leasefree WHERE id_LeaseFree = " . $lease_free . " LIMIT 1";
		$qTenure   = $db->query($sqlTenure);
		while ($rowTenure = $qTenure->fetchRow()) {
			$strTenure = $rowTenure["leaseFree_Name"];
		}
		$strPrice = price_format($row["Price"]) . $suffix;
	} elseif ($SaleLet == 2) {
		$strPrice = price_format($row["Price"]) . " per week / " . price_format(pw2pcm($row["Price"])) . " per month" . $suffix;
	}

	$osx = $row["osx"];
	$osy = $row["osy"];

	$strState = "<B><font color=red>" . $row["state_title"] . "</font></B>";

	if ($SaleLet == 1 && ($row["lease_free"] == 2 || $row["lease_free"] == 3)) {
		$strLongDescription .= '
<P><I>The following details have been provided by the vendor and have not been verified.</I></P>
<P><B>Tenure:</B> <I>' . $strTenure;
//if ($row["lease_length"]) {  $strLongDescription .= ' with some '.$row["lease_length"].' years remaining on the lease'; }
		$strLongDescription .= '</I></P>
<P><B>Service Charge:</B> <I>' . $row["service_charge"] . '</I></P>
<P><B>Ground Rent:</B> <I>' . $row["ground_rent"] . '</I></P>
';
		if ($row["other_details"]) {
			$strLongDescription .= '<P><B>Other Details:</B> <I>' . $row["other_details"] . '</I></P>';
		}

	}

	if ($row["date_available"] && ($row["state_of_trade_id"] == 1 || $row["state_of_trade_id"] == 11 || $row["state_of_trade_id"] == 12)) {
		if (strtotime($row["date_available"]) > 943920000) { // that is strtotime(0000-00-00 00:00:00)
			if (strtotime($row["date_available"]) > strtotime($dateToday)) {
				$strLongDescription .= '<li>Available: ' . $row["date_available_formatted"] . '</li>';
			} else {
				$strLongDescription .= '<li>Available Now</li>';
			}
		}
	}

	if ($row["total_area"]) {
		$strLongDescription .= '
<P><B>Approximate Gross Internal Area:</B> <I>' . $row["total_area"] . ' square metres</I></P>';
	}

	$strImgAlt       = $strAddress;
	$strImgPathOpen  = '<img src="' . $image_folder;
	$strImgPathClose = '" border="1" height="200" width="200" hspace="1" vspace="1" alt="' . $strImgAlt . '">';
	$strFpPathOpen   = '<img src="' . $image_folder;
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
		$attr = getimagesize($image_folder . $row["image11"]);
		if ($attr[1] > 950) {
			$height = '" height="950 ';
		}
		$strFp1 = $strPageBreak . $strFpPathOpen . $row["image11"] . $height . $strFpPathClose;
		$height = '';
	}
	if ($row["image12"]) {
		$attr = getimagesize($image_folder . $row["image12"]);
		if ($attr[1] > 950) {
			$height = '" height="950 ';
		}
		$strFp2 = $strPageBreak . $strFpPathOpen . $row["image12"] . $height . $strFpPathClose;
		$height = '';
	}
	if ($row["image13"]) {
		$attr = getimagesize($image_folder . $row["image13"]);
		if ($attr[1] > 950) {
			$height = '" height="950 ';
		}
		$strFp3 = $strPageBreak . $strFpPathOpen . $row["image13"] . $height . $strFpPathClose;
		$height = '';
	}
	if ($row["image14"]) {
		$attr = getimagesize($image_folder . $row["image14"]);
		if ($attr[1] > 950) {
			$height = '" height="950 ';
		}
		$strFp4 = $strPageBreak . $strFpPathOpen . $row["image14"] . $height . $strFpPathClose;
		$height = '';
	}
	if ($row["image15"]) {
		$attr = getimagesize($image_folder . $row["image15"]);
		if ($attr[1] > 950) {
			$height = '" height="950 ';
		}
		$strFp5 = $strPageBreak . $strFpPathOpen . $row["image15"] . $height . $strFpPathClose;
		$height = '';
	}

	$locationimage = "/images/mapping/dot4.gif";
	$pixelwidth    = 12;
	$pixelheight   = 12;

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
			<td align=right><a href="property.php?propID=<?php echo $intPropID; ?>"><img
							src="/images/ws_logo_175x88.gif" width="175" height="88" border="0"></a></td>
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
	<p align="center"><img src="<?php echo $image_folder . $strImg0; ?>" width="400" height="400"
						   alt="<?php echo $strImgAlt; ?>" border="1"></p>
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
	<?php echo "<p>" . $strFp1 . $strFp2 . $strFp3 . $strFp4 . $strFp5 . "</p>"; ?>
	<!-- End of Floorplans -->
	<?php echo $strPageBreak; ?>

	<table border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
				<div id="masterlayer" style="position:relative; width: 561px; height: 561px;">
					<?php echo $layer; ?>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td><img src="<?php echo $map_folder . $tile1; ?>" name="tile1" width="187" height="187"
									 border="0" id="tile1"></td>
							<td><img src="<?php echo $map_folder . $tile2; ?>" name="tile2" width="187" height="187"
									 border="0" id="tile2"></td>
							<td><img src="<?php echo $map_folder . $tile3; ?>" name="tile3" width="187" height="187"
									 border="0" id="tile3"></td>
						</tr>
						<tr>
							<td><img src="<?php echo $map_folder . $tile4; ?>" name="tile4" width="187" height="187"
									 border="0" id="tile4"></td>
							<td><img src="<?php echo $map_folder . $tile5; ?>" name="tile5" width="187" height="187"
									 border="0" id="tile5"></td>
							<td><img src="<?php echo $map_folder . $tile6; ?>" name="tile6" width="187" height="187"
									 border="0" id="tile6"></td>
						</tr>
						<tr>
							<td><img src="<?php echo $map_folder . $tile7; ?>" name="tile7" width="187" height="187"
									 border="0" id="tile7"></td>
							<td><img src="<?php echo $map_folder . $tile8; ?>" name="tile8" width="187" height="187"
									 border="0" id="tile8"></td>
							<td><img src="<?php echo $map_folder . $tile9; ?>" name="tile9" width="187" height="187"
									 border="0" id="tile9"></td>
						</tr>
						<tr>
							<td colspan="3"><img src="<?php echo $map_folder; ?>copyright.gif"
												 alt="Copyright Collins Bartholomew Ltd 2003" width="561" height="9"
												 border="0"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>

	<br clear=all>
	<table width="610" border="0">
		<tr>
			<td colspan="3" align="center">&nbsp;</td>
		</tr>
		<tr align="left">
			<td colspan="3"><span class="footerTitle">Wooster &amp; Stock</span></td>
		</tr>
		<tr align="left" valign="bottom">
			<td width="180"><p class="footerUrl">
					<font color="#FF9900">woosterstock.co.uk</font>&nbsp;&nbsp;</p></td>
			<td width="180"><span class="footerBranch">Camberwell</span><br>
         <span class="footerAddr">17 Nunhead Green<br>
      London SE15 3QQ <br>
     Sales: 020 7708 6700<br> 
     Lettings: 08456 800 460</span></td>
			<td width="350"><span class="footerBranch">Sydenham</span><br>
         <span class="footerAddr">109 Kirkdale<br>
      London SE26 4QY<br>
     Sales: 020 8613 0060<br> 
     Lettings: 08456 800 464</span></td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" align="center"><span class="footerFine">We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property details. Document printed <?php echo $dateLong; ?></span>
		</tr>
	</table>
	</body>
	</html>
