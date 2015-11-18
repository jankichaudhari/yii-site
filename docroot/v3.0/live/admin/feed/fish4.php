<?php
define("FEED_NAME", "fish4");
require_once(dirname(__FILE__) . "/../../../../../config/config_feed.inc.php");
$uploaderid   = '26994';
$advertiserid = '800460';

$sevendays = (60 * 60 * 24 * 7);
// path to save text file
$strFolderName = date('Ymd');
$strPath       = dirname(__FILE__) . '/fish4/' . $strFolderName;
// name of feed file
$strTextFile = 'homes_' . $uploaderid . '_' . date('Ymdhmi') . ".xml";
// name of MMO file, 1 minute newer than above - subsequent feeds must be delayed by more than 1 mnutes to ensure no overwrites
$strMMOFile = 'images_' . $uploaderid . '_' . date('Ymdhmi', (date('U') + 60)) . ".xml";
$strZipFile = 'images_' . $uploaderid . '_' . date('Ymdhmi', (date('U') + 60)) . ".zip";

createForlder($strPath);

// header
$render = '<?xml version="1.0" encoding="ISO-8859-1"?>
<FISH4.IF.HOMES.FORRENT xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.fish4.co.uk/schema/FISH4IF-homesforrent-20.xsd">
<HEAD>
	<UPLOADER_ID>' . $uploaderid . '</UPLOADER_ID>
	<FILE_REFERENCE>' . $strTextFile . '</FILE_REFERENCE>
	<EMAIL_FEEDBACK>mail@markdw.com</EMAIL_FEEDBACK>
	<FEEDBACK_LEVEL>1</FEEDBACK_LEVEL>
</HEAD>
';

$renderMMO = '<?xml version="1.0" encoding="ISO-8859-1"?>
<FISH4.IF.MMO xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.fish4.co.uk/schema/FISH4IF-mmo-20.xsd">
<HEAD>
	<UPLOADER_ID>' . $uploaderid . '</UPLOADER_ID>
	<ORIG_FILENAME>' . $strZipFile . '</ORIG_FILENAME>
	<FILE_REFERENCE>' . $strMMOFile . '</FILE_REFERENCE>
	<EMAIL_FEEDBACK>mail@markdw.com</EMAIL_FEEDBACK>
	<FEEDBACK_LEVEL>1</FEEDBACK_LEVEL>
</HEAD>
';

$sql = "SELECT
	deal.*,
	area.are_title,
	pro_addr1,pro_addr3,pro_addr5,pro_postcode,
	pro_east,pro_north,pro_latitude,pro_longitude,
	branch.bra_id,branch.bra_title,branch.bra_tel,branch.bra_fax,branch.bra_email,
	T.pty_title AS ptype,
	ST.pty_title AS psubtype,
	GROUP_CONCAT(DISTINCT CONCAT(feature.fea_title) ORDER BY feature.fea_id ASC SEPARATOR '~') AS features,
	GROUP_CONCAT(DISTINCT CONCAT(photos.med_file,'|',photos.med_title) ORDER BY photos.med_order ASC SEPARATOR '~') AS photos,
	GROUP_CONCAT(DISTINCT CONCAT(floorplans.med_file,'|',floorplans.med_title) ORDER BY floorplans.med_order ASC SEPARATOR '~') AS floorplans,
	GROUP_CONCAT(DISTINCT CONCAT(epc.med_file,'|',epc.med_title) ORDER BY epc.med_order ASC SEPARATOR '~') AS epc
FROM deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id
LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id
LEFT JOIN media AS photos ON deal.dea_id = photos.med_row AND photos.med_table = 'deal' AND photos.med_type = 'Photograph'
LEFT JOIN media AS floorplans ON deal.dea_id = floorplans.med_row AND floorplans.med_table = 'deal' AND floorplans.med_type = 'Floorplan'
LEFT JOIN media AS epc ON deal.dea_id = epc.med_row AND epc.med_table = 'deal' AND epc.med_type = 'EPC'
LEFT JOIN link_instruction_to_feature ON dealId = deal.dea_id
LEFT JOIN feature ON featureId = feature.fea_id
WHERE
(deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other')
AND dea_type = 'Lettings'
AND (deal.noPortalFeed <> 1 AND deal.underTheRadar <> 1)
GROUP BY dea_id
";
//echo $sql;
$q = $db->query($sql);
if (DB::isError($q)) {
	die("error: " . $q->getMessage());
}
$numRows = $q->numRows();

while ($row = $q->fetchRow()) {

// property type
	if ($row["dea_psubtype"] == 4) {
		$ptype = 'detached';
	} elseif ($row["dea_psubtype"] == 9) {
		$ptype = 'bungalow';
	} elseif ($row["dea_psubtype"] == 12) {
		$ptype = 'maisonette';
	} elseif ($row["dea_ptype"] == 1) {
		$ptype = 'semidetached';
	} elseif ($row["dea_ptype"] == 2) {
		$ptype = 'flat';
	} elseif ($row["dea_ptype"] == 3) {
		$ptype = 'other';
	} else { // default
		$ptype = 'flat';
	}

	if ($row["dea_bedroom"] == 0) {
		$bedroom = 'studio';
	} elseif ($row["dea_bedroom"] > 10) {
		$bedroom = 10;
	} else {
		$bedroom = $row["dea_bedroom"];
	}

	$furnished = 'unfurnished';

//features and furnished
	$sqlInner = "SELECT fea_id,fea_title FROM link_instruction_to_feature
LEFT JOIN feature ON link_instruction_to_feature.featureId = feature.fea_id
WHERE link_instruction_to_feature.dealId = " . $row["dea_id"];
	$qInner   = $db->query($sqlInner);
	while ($rowInner = $qInner->fetchRow()) {

		// garden
		if ($rowInner["fea_id"] == 7) {
			$garden = 'balcony';
		} elseif ($rowInner["fea_id"] == 8) {
			$garden = 'roofgarden';
		} elseif ($rowInner["fea_id"] == 4 || $rowInner["fea_id"] == 5 || $rowInner["fea_id"] == 6) {
			$garden = 'garden';
		}

		// parking
		if ($rowInner["fea_id"] == 11 || $rowInner["fea_id"] == 47) {
			$parking = 'offroad';
		} elseif ($rowInner["fea_id"] == 9 || $rowInner["fea_id"] == 10) {
			$parking = 'garage';
		}

		// furnished
		if ($rowInner["fea_id"] == 38) {
			$furnished = 'furnished';
		} elseif ($rowInner["fea_id"] == 39) {
			$furnished = 'partfurnished';
		} elseif ($rowInner["fea_id"] == 40) {
			$furnished = 'unfurnished';
		} elseif ($rowInner["fea_id"] == 52) {
			$furnished = 'furnishedorunfurnished';
		}
	}

// description
	$description = str_replace(
		array("&eacute;", "&rsquo;", "&pound;", "&lsquo;", "&ndash;"),
		array("&#233;", "&#8217;", "&#163;", "&#8216;", "&#8211;"),
		$row["dea_description"]
	);
	$description = remove_lastchar($description, "<p>");
// remove all tags except <p>
	$description = strip_tags($description, '<p>');
// remove any attributes from tags
	$description = preg_replace('/<\s*(\w+)[^>]+>/i', '<$1>', $description);

// receptions, max 3
	if ($row["dea_reception"] >= 3) {
		$row["dea_reception"] = 3;
	}
// receptions, min 1
	if ($row["dea_reception"] == 0 || !$row["dea_reception"]) {
		$row["dea_reception"] = 1;
	}

// overwriting sydenham branch details
	$row["bra_tel"]   = '08456 800 460';
	$row["bra_email"] = 'cam.let@woosterstock.co.uk';
	$row["bra_fax"]   = '08456 800 461';

	$render .= '
<OBJECT>
	<OBJECT_HEAD>
		<PUBLICATION_ID></PUBLICATION_ID>
		<ADVERTISER_ID>' . $advertiserid . '</ADVERTISER_ID>
		<ORDERNO>' . $advertiserid . '-' . $row["dea_id"] . '</ORDERNO>
		<ADVERTISER_REFERENCE>' . $row["dea_id"] . '</ADVERTISER_REFERENCE>
		<PROVIDER_REFERENCE>' . $row["dea_id"] . '</PROVIDER_REFERENCE>
		<FROMDATE>' . date('d/m/y') . '</FROMDATE>
		<TODATE>' . date('d/m/y', date('U') + $sevendays) . '</TODATE>
		<PRIVATE_TRADE INDICATOR="t"/>
	</OBJECT_HEAD>
	<HOMES_RENT>
		<LOCATION_HOMES>
			<STREET_NAME>' . $row["pro_addr3"] . '</STREET_NAME>
			<TOWN>' . $row["are_title"] . '</TOWN>
			<COUNTY>' . $row["pro_addr5"] . '</COUNTY>
			<POSTCODE>' . $row["pro_postcode"] . '</POSTCODE>
			<SHOW_ADDRESS_LEVEL>nopostcode</SHOW_ADDRESS_LEVEL>
		</LOCATION_HOMES>
		<RENT_PROPERTY_TYPE>' . $ptype . '</RENT_PROPERTY_TYPE>
		<NO_OF_BEDROOMS>' . $bedroom . '</NO_OF_BEDROOMS>
		<NO_OF_BATHROOMS>' . $row["dea_bathroom"] . '</NO_OF_BATHROOMS>
		<NO_OF_RECEPTION_ROOMS>' . $row["dea_reception"] . '</NO_OF_RECEPTION_ROOMS>
		<PRICE>' . $row["dea_marketprice"] . '</PRICE>
		<HOME_PAYMENT_FREQUENCY>week</HOME_PAYMENT_FREQUENCY>
		<CURRENCY>GBP</CURRENCY>
		<POA_INDICATOR>no</POA_INDICATOR>
		<GARDEN>' . $garden . '</GARDEN>
		<PARKING>' . $parking . '</PARKING>
		<FURNISHED>' . $furnished . '</FURNISHED>
		<SEARCH_RESULT_DESCRIPTION><![CDATA[' . $row["dea_strapline"] . ']]></SEARCH_RESULT_DESCRIPTION>
		<GENERAL_DESCRIPTION><![CDATA[' . $description . ']]></GENERAL_DESCRIPTION>
		<ADVERT_URL>http://www.woosterstock.co.uk/DetailLet.php?id=' . $row["dea_id"] . '</ADVERT_URL>
		<ADVERTISER_WEB_SITE>http://www.woosterstock.co.uk</ADVERTISER_WEB_SITE>
		<ESTATE_CONTACT>
			<CONTACT_NAME>Wooster &amp; Stock</CONTACT_NAME>
			<CONTACT_TELEPHONE>' . $row["bra_tel"] . '</CONTACT_TELEPHONE>
			<CONTACT_EMAIL_ADDRESS>' . $row["bra_email"] . '</CONTACT_EMAIL_ADDRESS>
			<CONTACT_FAX>' . $row["bra_fax"] . '</CONTACT_FAX>
		</ESTATE_CONTACT>
	</HOMES_RENT>
</OBJECT>
';

	unset($ptype, $bedroom, $garden, $parking, $furnished, $description);

// MMO render

	$renderMMO .= '<OBJECT>
<OBJECT_HEAD>
<ADVERTISER_ID>' . $advertiserid . '</ADVERTISER_ID>
<ORDERNO>' . $advertiserid . '-' . $row["dea_id"] . '</ORDERNO>
</OBJECT_HEAD>
';

	$image_path = WS_PATH_IMAGES . '/' . $row["dea_id"] . '/';

	if ($row["photos"]) {
		$photo_array = explode("~", $row["photos"]);
	} else {
		$photo_array = array();
	}
	foreach ($photo_array as $val) {
		$photo = explode("|", $val);
		if (file_exists($image_path . str_replace(".jpg", "_large.jpg", $photo[0]))) {
			$source_image = $image_path . str_replace(".jpg", "_large.jpg", $photo[0]);
		}
		elseif (file_exists($image_path . str_replace(".jpg", "_small.jpg", $photo[0]))) {
			$source_image = $image_path . str_replace(".jpg", "_small.jpg", $photo[0]);
		}
		$image_filename = $uploaderid . '_' . $advertiserid . '_' . $row["dea_id"] . '_IMG_' . padzero($i) . '.jpg';
		// if the file already exists, delet before re-writing
		if (file_exists($strPath . "/" . $image_filename)) {
			unlink($strPath . "/" . $image_filename);
		}
		copy($source_image, $strPath . "/" . $image_filename);
		$filesToDelete[] = $strPath . "/" . $image_filename;
		$renderMMO .= '<MO REF="' . $image_filename . '" PRIORITY="' . ($i + 1) . '" MMO_TYPE="photo" REMOVE="no"/>' . "\n";
		$i++;
	}
	unset($i);

// floorplans
	if ($row["floorplans"]) {
		$floorplan_array = explode("~", $row["floorplans"]);
	} else {
		$floorplan_array = array();
	}
	foreach ($floorplan_array as $val) {
		$floorplan = explode("|", $val);
		if (file_exists($image_path . $floorplan[0])) {
			$source_image = $image_path . $floorplan[0];
		}
		$image_filename = $uploaderid . '_' . $advertiserid . '_' . $row["dea_id"] . '_FLP_' . padzero($i) . '.jpg';
		// if the file already exists, delet before re-writing
		if (file_exists($strPath . "/" . $image_filename)) {
			unlink($strPath . "/" . $image_filename);
		}
		copy($source_image, $strPath . "/" . $image_filename);
		$filesToDelete[] = $strPath . "/" . $image_filename;
		$renderMMO .= '<MO REF="' . $image_filename . '" PRIORITY="' . ($i + 1) . '" MMO_TYPE="floorplan" REMOVE="no"/>' . "\n";
		$i++;
	}
	unset($i);
// epc
	if ($row["epc"]) {
		$epc_array = explode("~", $row["epc"]);
	} else {
		$epc_array = array();
	}
	foreach ($epc_array as $val) {
		$epc = explode("|", $val);
		if (file_exists($image_path . str_replace(".jpg", "_large.jpg", $epc[0]))) {
			$source_image = $image_path . str_replace(".jpg", "_large.jpg", $epc[0]);
		}
		elseif (file_exists($image_path . str_replace(".jpg", "_small.jpg", $epc[0]))) {
			$source_image = $image_path . str_replace(".jpg", "_small.jpg", $epc[0]);
		}
		$image_filename = $uploaderid . '_' . $advertiserid . '_' . $row["dea_id"] . '_IMG_' . padzero($i) . '.jpg';
		// if the file already exists, delet before re-writing
		if (file_exists($strPath . "/" . $image_filename)) {
			unlink($strPath . "/" . $image_filename);
		}
		copy($source_image, $strPath . "/" . $image_filename);
		$filesToDelete[] = $strPath . "/" . $image_filename;
		$renderMMO .= '<MO REF="' . $image_filename . '" PRIORITY="' . ($i + 1) . '" MMO_TYPE="hipsimage" REMOVE="no"/>' . "\n";
		$i++;
	}
	unset($i);

	$renderMMO .= '</OBJECT>' . "\n";

	unset($i, $photo_array, $photo, $floorplan_array, $floorplan);

}

$render .= '</FISH4.IF.HOMES.FORRENT>';
$renderMMO .= '</FISH4.IF.MMO>';

$local_file = $strPath . "/" . $strTextFile;
// if the file already exists, delete before re-writing
if (file_exists($local_file)) {
	unlink($local_file);
}
// write $render to file
if (!file_put_contents($local_file, $render)) {
	echo "could not write to file - $local_file";
	exit;
}

$local_file = $strPath . "/" . $strMMOFile;
// if the file already exists, delete before re-writing
if (file_exists($local_file)) {
	unlink($local_file);
}
// write $render to file
if (!file_put_contents($local_file, $renderMMO)) {
	echo "could not write to file - $local_file";
	exit;
}

// add xml to files array
$filesToDelete[] = $local_file;

// write all images and xml file to zip
include ('Archive/Zip.php'); // imports

$obj = new Archive_Zip($strPath . "/" . $strZipFile); // name of zip file

$files = $filesToDelete;
if ($obj->create($files, array('remove_all_path'=> 1))) {

} else {
	$errors[] = 'Error in zip file creation';
}

// delete all files no longer required
foreach ($filesToDelete as $filename) {
	@unlink($filename);
}

/*
FTP Username = pppmgold
FTP Password = kw4djr0x
FTP Hostname = uploads.fish4.co.uk
*/

//$ftp_server   = "uploads.fish4.co.uk";
//$ftp_username = "pppmgold";
//$ftp_password = "kw4djr0x";
$destDir = "/";
$ftp     = new FTP($CONFIG[FEED_NAME]['ftp_server'], $CONFIG[FEED_NAME]['ftp_username'], $CONFIG[FEED_NAME]['ftp_password']);
try {
	$ftp->upload($strPath . "/" . $strTextFile, $destDir);
	$ftp->upload($strPath . "/" . $strZipFile, $destDir);
} catch (Exception $e) {

}

file_put_contents(WS_PATH_LOGS . "/ftpUploadErrorLog.log", $ftp->getErrorLog(), FILE_APPEND);
file_put_contents(WS_PATH_LOGS . "/ftpUploadMessageLog.log", $ftp->getMessageLog(), FILE_APPEND);
