<?php
define("FEED_NAME", "propertyfinder");
require_once(dirname(__FILE__) . "/../../../../../config/config_feed.inc.php");
// path to save text file
$strFolderName = date('Ymd');
$strPath       = dirname(__FILE__) . '/propertyfinder/' . $strFolderName;
$strTextFile   = date('Ymd') . ".txt";
$backupdate    = date("Ymd");
$backupdir     = $strPath;
$files         = "*";
$backupto      = $strPath;
$fileprefix    = "";
$tararg        = "-cf";
createForlder($strPath);
$sql = "SELECT

	deal.*,
	area.are_title,
	pro_addr1,pro_addr3,pro_postcode,
	pro_east,pro_north,pro_latitude,pro_longitude,
	branch.bra_id,branch.bra_title,branch.bra_tel,branch.bra_fax,branch.bra_email,
	T.pty_title AS ptype,
	ST.pty_title AS psubtype,
	GROUP_CONCAT(DISTINCT CONCAT(feature.fea_title) ORDER BY feature.fea_id ASC SEPARATOR '~') AS features,
	GROUP_CONCAT(DISTINCT CONCAT(photos.med_file,'|',photos.med_title) ORDER BY photos.med_order ASC SEPARATOR '~') AS photos,
	GROUP_CONCAT(DISTINCT CONCAT(floorplans.med_file,'|',floorplans.med_title) ORDER BY floorplans.med_order ASC SEPARATOR '~') AS floorplans

FROM deal

LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id

LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id

LEFT JOIN media AS photos ON deal.dea_id = photos.med_row AND photos.med_table = 'deal' AND photos.med_type = 'Photograph'
LEFT JOIN media AS floorplans ON deal.dea_id = floorplans.med_row AND floorplans.med_table = 'deal' AND floorplans.med_type = 'Floorplan'

LEFT JOIN link_instruction_to_feature ON dealId = deal.dea_id
LEFT JOIN feature ON featureId = feature.fea_id

WHERE
(deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other')
AND LENGTH(property.pro_postcode) > 4
AND dea_branch != 5
AND (deal.noPortalFeed <> 1 AND deal.underTheRadar <> 1)
GROUP BY dea_id
";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("error: " . $q->getMessage());
}
$numRows = $q->numRows();

// write headers
$render = "#VERSION 3
#SOURCE Mark Williams Wooster and Stock
#DATE " . date("Ymd") . "
#RECORDS " . $numRows . "
#IMAGES " . $imagecount . "
";

//loop through recordset

while ($row = $q->fetchRow()) {

// different id's for each branch
	if ($row["dea_branch"] == 1) { // camberwell sales
		$intRMBranchID = 204991;
	} elseif ($row["dea_branch"] == 2) { // sydenham sales
		$intRMBranchID = 601886;
	} elseif ($row["dea_branch"] == 3) { // camberwell lettings
		$intRMBranchID = 221822;
	} elseif ($row["dea_branch"] == 4) { // sydenham lettings
		$intRMBranchID = 221823;
	}

	$render .= $intRMBranchID . "_" . $row["dea_id"] . "|"; //Unique ID
	$render .= $row["pro_addr3"] . "|"; //Street Name
	$render .= $row["are_title"] . "|"; //District
	$render .= $row["pro_addr5"] . "|"; //County
	$render .= $row["pro_postcode"] . "|"; //Postcode
	$render .= $row["dea_strapline"] . "|"; //Short Description

	$longDescription = $row["dea_description"];

	$longDescription .= "<p>For further information or to arrange a viewing, please contact our <b>" . $row["bra_title"] . " Office</b> on <b>" . $row["bra_tel"] . ".</b></p>";
	$longDescription .= "<p>Visit <b>www.woosterstock.co.uk</b> for full details, colour photos, maps and floor plans.</p>";
	$longDescription .= "<p>We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property sales details.</p>";

	$render .= preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $longDescription) . "|"; //Long Description

	if ($row["dea_psubtype"] == 5) { //Property Type (commercial and live/work not included)
		$render .= "semi" . "|";
	} elseif ($row["dea_psubtype"] == 4) {
		$render .= "detached" . "|";
	} elseif ($row["dea_psubtype"] == 9) {
		$render .= "bungalow" . "|";
	} elseif ($row["dea_psubtype"] == 13) {
		$render .= "studio" . "|";
	} elseif ($row["dea_psubtype"] == 6 || $row["dea_psubtype"] == 7) {
		$render .= "terrace" . "|";
	}
	elseif ($row["dea_ptype"] == 2) {
		$render .= "flat" . "|";
	} else {
		$render .= "terrace" . "|"; // default to terrace if not identified
	}

	$render .= $row["dea_bedroom"] . "|"; //Bedrooms
	$render .= $row["dea_marketprice"] . "|"; //Price (minimum)
	$render .= "|"; //Price (maximum)
	$render .= "|"; //Price Test (qualifier)
	if ($row["dea_type"] == 'Lettings') { //Price Rental Period
		$render .= "W" . "|";
	} else {
		$render .= "|";
	}
	$render .= "|"; //Rental Property Available Date
	$render .= "|"; //Rental Let Term
	$render .= "|"; //Rental Property Pets Allowed
	$render .= "|"; //Rent includes utility bills

	if ($row["dea_type"] == 'Lettings') { //Tenure Type
		$render .= "rental" . "|";
	} else {
		if ($row["dea_tenure"] == 'Freehold') {
			$render .= "freehold" . "|";
		} else {
			$render .= "leasehold" . "|";
		}
	}

	$render .= "" . "|"; //LET_FURN_ID

// media
// 6 images
// 2 floorplans  (not including)
	$image_path = WS_PATH_IMAGES . '/' . $row["dea_id"] . '/';

	if ($row["photos"]) {
		$photo_array = explode("~", $row["photos"]);
	}

	$max_images = 7;

	for ($i = 0; $i <= $max_images; $i++) {

		if ($photo_array[$i]) {
			$photo = explode("|", $photo_array[$i]);

			if (file_exists($image_path . $photo[0])) {
				$source_image = $image_path . $photo[0];
			} elseif (file_exists($image_path . str_replace(".jpg", "_full.jpg", $photo[0]))) {
				$source_image = $image_path . str_replace(".jpg", "_full.jpg", $photo[0]);
			} elseif (file_exists($image_path . str_replace(".jpg", "_large.jpg", $photo[0]))) {
				$source_image = $image_path . str_replace(".jpg", "_large.jpg", $photo[0]);
			} elseif (file_exists($image_path . str_replace(".jpg", "_small.jpg", $photo[0]))) {
				$source_image = $image_path . str_replace(".jpg", "_small.jpg", $photo[0]);
			}


			$rm_image_name = $intRMBranchID . '_' . $row["dea_id"] . '_IMG_' . padzero($i) . '.jpg';

			// if the file already exists, delet before re-writing
			if (file_exists($strPath . "/" . $rm_image_name)) {
				unlink($strPath . "/" . $rm_image_name);
			}
			copy($source_image, $strPath . "/" . $rm_image_name);

			$render .= $rm_image_name . "|"; //MEDIA_IMAGE_00
			$filesToDelete[] = $strPath . "/" . $rm_image_name;

			//$render .= $photo[1]."^";			//MEDIA_IMAGE_TEXT_00

		} else { // no image

			$render .= "|"; //MEDIA_IMAGE_00
			//$render .= "^";		//MEDIA_IMAGE_TEXT_00
		}

	}

	unset($i);

	$render .= $row["bra_tel"] . "|";
	$render .= $row["bra_email"] . "|";
	$render .= $row["bra_fax"] . "|";

	$render .= $intRMBranchID . "|";
	$render .= "|"; //Age of Property

	$render .= "urban|"; //Location
	$render .= "|"; //Parking Garage

	$render .= "|"; //Parking Off Street

	$render .= "|"; //Central heating

	$render .= "|"; //Double Glazing

	$render .= $row["dea_reception"] . "|"; //Receptions
	$render .= $row["dea_bathroom"] . "|"; //Bathrooms
	$render .= "|"; //Conservatory
	$render .= "|"; //Swimming Pool
	$render .= "|"; //Fireplace
	$render .= "|"; //Waterfront
	$render .= "|"; //Paddock
	$render .= "|"; //Handicap Features
	$render .= "|"; //Balcony

	$render .= "|"; //Garden

	$render .= "http://" . WS_HOSTNAME . "/Print.php?id=" . $row["dea_id"] . "|"; // Website URL

	if ($row["dea_status"] == 'Available') { //Status
		$render .= "available" . "|";
	} elseif ($row["dea_status"] == 'Under Offer' || $row["dea_status"] == 'Under Offer with Other') {
		$render .= "under offer" . "|";
	}
	$render .= "|"; //Virtual Tour
	$render .= ""; //Virtual Tour URL (no pipe)

	$render .= "\n"; //End of record + line feed

// loop
}

// end of datafeed

// write $render to file
$local_file = $strPath . "/" . $strTextFile;
if (!file_put_contents($local_file, $render)) {
	echo "could not write to file";
	exit;
}
// add the blm to delete array
$filesToDelete[] = $strPath . "/" . $strTextFile;
// add the tar to the delete array
$filesToDelete[] = $strPath . "/" . $backupdate . ".tar";

// copy all images to gzip file
backupsus();

// delete all files in array (that is all images and the blm text file)
foreach ($filesToDelete as $filename) {
	@unlink($filename);
}

// log in to ftp site and upload contents of folder

$ftp_server   = "ftp.assertaupload.com";
$ftp_username = "hamburg142";
$ftp_password = "woost3r";

$src_dir = $strPath;
$dst_dir = "/";
$ftp = new FTP($CONFIG[FEED_NAME]['ftp_server'], $CONFIG[FEED_NAME]['ftp_username'], $CONFIG[FEED_NAME]['ftp_password']);
try {
	$ftp->upload($strPath, $dst_dir);
} catch (Exception $e) {

}
file_put_contents(WS_PATH_LOGS . "/ftpUploadErrorLog.log", $ftp->getErrorLog(), FILE_APPEND);
file_put_contents(WS_PATH_LOGS . "/ftpUploadMessageLog.log", $ftp->getMessageLog(), FILE_APPEND);
// =================================================================================
// <<<

function backupsus()
{
	global $backupdate, $backupdir, $backupto, $fileprefix, $tararg, $bz2arg, $files;
	$backupsuscmd = "cd $backupdir;
	tar $tararg {$backupdate}.tar $files;
	gzip $bz2arg {$backupdate}.tar;
	mv {$backupdate}.tar.gz $backupto";
	passthru("$backupsuscmd");
}

//  >>>
// =================================================================================