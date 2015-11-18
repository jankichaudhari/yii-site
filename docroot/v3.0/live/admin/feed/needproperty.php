<?php
/**
 * @var $feed Feed
 */
define("FEED_NAME", "needproperty");
require_once(dirname(__FILE__) . "/../../../../../config/config_feed.inc.php");
function backupsus()
{

	global $backupdate, $backupdir, $backupto, $fileprefix, $tararg, $bz2arg, $files;
	$backupsuscmd = "cd $backupdir;
	rm -f {$backupdate}.tar;
	rm -f {$backupdate}.tar.gz;
	tar $tararg {$backupdate}.tar $files;
	gzip $bz2arg {$backupdate}.tar;
	mv {$backupdate}.tar.gz $backupto";
	passthru("$backupsuscmd");
}

// path to save text file
$strFolderName = date('Ymd');
$strPath       = dirname(__FILE__) . '/needproperty/' . $strFolderName;
// name of textfile (date.blm)
$strTextFile = date('Ymd') . ".blm";

// name of zip (gzip) file
$backupdate = date("Ymd");
// backup to gzip fuinction
$backupdir  = $strPath;
$files      = "*";
$backupto   = $strPath;
$fileprefix = "";
$tararg     = "-cf";
createForlder($strPath);
// only get properties that have been updated in the past 10 days
$today           = strtotime($date_mysql);
$ten_days        = (5 * 24 * 60 * 60);
$date_comparison = date('Y-m-d H:i:s', ($today - $ten_days));

$sql = "SELECT

	deal.*,
	area.are_title,
	pro_addr1,pro_addr3,pro_postcode,
	pro_east,pro_north,pro_latitude,pro_longitude,
	branch.bra_id,branch.bra_title,branch.bra_tel,branch.bra_fax,
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
AND (deal.noPortalFeed <> 1 AND deal.underTheRadar <> 1)
GROUP BY dea_id
";
//echo $sql;
$q = $db->query($sql);
if (DB::isError($q)) {
	die("error: " . $q->getMessage());
}
$numRows = $q->numRows();

// write headers
$render = "#HEADER#
Version : 3
EOF : '^'
EOR : '~'
Property Count : " . $numRows . "
Generated Date : " . $date_mysql . "
#DEFINITION#
AGENT_REF^ADDRESS_1^ADDRESS_2^ADDRESS_3^ADDRESS_4^TOWN^POSTCODE1^POSTCODE2^FEATURE1^FEATURE2^FEATURE3^";
$render .= "FEATURE4^FEATURE5^FEATURE6^FEATURE7^FEATURE8^FEATURE9^FEATURE10^SUMMARY^DESCRIPTION^BRANCH_ID^";
$render .= "STATUS_ID^BEDROOMS^PRICE^PRICE_QUALIFIER^PROP_SUB_ID^CREATE_DATE^UPDATE_DATE^DISPLAY_ADDRESS^";
$render .= "PUBLISHED_FLAG^LET_DATE_AVAILABLE^LET_BOND^LET_TYPE_ID^LET_FURN_ID^LET_RENT_FREQUENCY^TRANS_TYPE_ID^";
$render .= "MEDIA_IMAGE_00^MEDIA_IMAGE_TEXT_00^MEDIA_IMAGE_01^MEDIA_IMAGE_TEXT_01^MEDIA_IMAGE_02^MEDIA_IMAGE_TEXT_02^";
$render .= "MEDIA_IMAGE_03^MEDIA_IMAGE_TEXT_03^MEDIA_IMAGE_04^MEDIA_IMAGE_TEXT_04^MEDIA_IMAGE_05^MEDIA_IMAGE_TEXT_05^";
$render .= "MEDIA_IMAGE_06^MEDIA_IMAGE_TEXT_06^MEDIA_IMAGE_07^MEDIA_IMAGE_TEXT_07^MEDIA_IMAGE_08^MEDIA_IMAGE_TEXT_08^";
$render .= "MEDIA_IMAGE_09^MEDIA_IMAGE_TEXT_09^MEDIA_IMAGE_10^MEDIA_IMAGE_TEXT_10^";

// adding 2 epc fields (23/09/08)
$render .= "MEDIA_IMAGE_60^MEDIA_IMAGE_TEXT_60^MEDIA_IMAGE_61^MEDIA_IMAGE_TEXT_61^";

$render .= "MEDIA_FLOOR_PLAN_00^MEDIA_FLOOR_PLAN_TEXT_00^MEDIA_FLOOR_PLAN_01^MEDIA_FLOOR_PLAN_TEXT_01^MEDIA_FLOOR_PLAN_02^MEDIA_FLOOR_PLAN_TEXT_02^";
$render .= "MEDIA_FLOOR_PLAN_03^MEDIA_FLOOR_PLAN_TEXT_03^MEDIA_FLOOR_PLAN_04^MEDIA_FLOOR_PLAN_TEXT_04^";
$render .= "MEDIA_DOCUMENT_01^";
$render .= "~\n";
$render .= "#DATA#\n";

//loop through recordset

//while
if (isset($feed) && $feed instanceof Feed) {

}
while ($row = $q->fetchRow()) {
	$intRMBranchID = $feed->getBranchPortalCode($row['dea_branch']);

	$render .= $intRMBranchID . "_" . $row['dea_id'] . "^"; //AGENT_REF
	$render .= $row['pro_addr1'] . "^"; //ADDRESS_1
	$render .= $row['pro_addr3'] . "^"; //ADDRESS_2
	$render .= $row['are_title'] . "^"; //ADDRESS_3
	$render .= "^"; //ADDRESS_4
	$render .= $row['pro_addr5'] . "^"; //TOWN
	$pc = explode(" ", $row['pro_postcode']);
	$render .= $pc[0] . "^"; //POSTCODE1
	$render .= $pc[1] . "^"; //POSTCODE2

// features, grab all related featured from the table and show the 10 most important
// non-lettings property ignore fea_type = 'Lettings' rows
	if ($row['dea_type'] != 'Lettings') {
		$where = " AND fea_type != 'Lettings'";
	} else {
		unset($where);
	}

	$furnishedId = 3;

	$sqlInner = "SELECT fea_id,fea_title FROM link_instruction_to_feature
LEFT JOIN feature ON link_instruction_to_feature.featureId = feature.fea_id
WHERE link_instruction_to_feature.dealId = " . $row['dea_id'] . " $where
ORDER BY fea_weight DESC
LIMIT 10";
	$qInner   = $db->query($sqlInner);
	while ($rowInner = $qInner->fetchRow()) {
		$features[] = $rowInner['fea_title'];

		if ($rowInner['fea_id'] == 38) {
			$furnishedId = '0';
		} elseif ($rowInner['fea_id'] == 39) {
			$furnishedId = '1';
		} elseif ($rowInner['fea_id'] == 40) {
			$furnishedId = '2';
		} elseif ($rowInner['fea_id'] == 52) {
			$furnishedId = '4';
		}
	}

	$render .= $features[0] . "^"; //FEATURE1 property type
	$render .= $features[1] . "^"; //FEATURE2 bedroms
	$render .= $features[2] . "^"; //FEATURE3 ch
	$render .= $features[3] . "^"; //FEATURE4 dg
	$render .= $features[4] . "^"; //FEATURE5
	$render .= $features[5] . "^"; //FEATURE6
	$render .= $features[6] . "^"; //FEATURE7
	$render .= $features[7] . "^"; //FEATURE8
	$render .= $features[8] . "^"; //FEATURE9
	$render .= $features[9] . "^"; //FEATURE10
	unset($features);

	$length = (300 - strlen($row['dea_strapline'])); // 300 is allowed, but i add (cont) so we use 294

	$desc    = strip_tags($row['dea_description'], "<p><b><i>");
	$trimmed = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $desc);
	$trimmed = html_entity_decode($trimmed);
//	$trimmed = str_replace("&amp;#039;", "'", $trimmed);
//	$trimmed = str_replace("&amp;amp;#039;", "'", $trimmed);
//	$trimmed = str_replace("&amp;eacute;", "�", $trimmed);
	$trimmedFull = $trimmed;
	$trimmed     = substr($trimmed, 0, $length);

	$render .= strip_tags($row['dea_strapline']) . ": " . $trimmed . "^"; //SUMMARY

	$longDescription = $row['dea_description'];
	if ($row['total_area']) {
		$longDescription .= "<p>Approximate Gross Internal Area: " . $row['total_area'] . " square metres</p>";
	}
	$longDescription = strip_tags($longDescription, '<p><b></i>');
	$longDescription .= "<p>For further information or to arrange a viewing, please contact our <b>" . $row['bra_title'] . " Branch</b> on <b>" . $row['bra_tel'] . ".</b></p>";
	$longDescription .= "<p>Visit <b>www.woosterstock.co.uk</b> for full details, colour photos, maps and floor plans.</p>";
	$longDescription .= "<p>We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property sales details.</p>";

	$render .= preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $longDescription) . "^"; //DESCRIPTION

	$render .= $intRMBranchID . "^"; //BRANCH_ID

	if ($row['dea_status'] == 'Available') { //STATUS_ID - 0 for sale, 3 under offer, 3 = let stc

		$render .= "0" . "^";

	} elseif ($row['dea_status'] == 'Under Offer' || $row['dea_status'] == 'Under Offer with Other') {

		if ($row['dea_type'] == 'Sales') {
			$render .= "3" . "^";
		} else {
			$render .= "5" . "^";
		}

	}

	$render .= $row['dea_bedroom'] . "^"; //BEDROOMS
	$render .= $row['dea_marketprice'] . "^"; //PRICE
	$render .= "0" . "^"; //PRICE_QUALIFIER

	if ($row['dea_psubtype'] == 4) { //PROP_SUB_ID
		$render .= "4" . "^";
	} elseif ($row['dea_psubtype'] == 5) {
		$render .= "3" . "^";
	} elseif ($row['dea_psubtype'] == 6) {
		$render .= "1" . "^";
	} elseif ($row['dea_psubtype'] == 7) {
		$render .= "2" . "^";
	} elseif ($row['dea_psubtype'] == 8) {
		$render .= "5" . "^";
	} elseif ($row['dea_psubtype'] == 9) {
		$render .= "12" . "^";
	} elseif ($row['dea_psubtype'] == 10) {
		$render .= "26" . "^";
	} elseif ($row['dea_psubtype'] == 11) {
		$render .= "29" . "^";
	} elseif ($row['dea_psubtype'] == 12) {
		$render .= "11" . "^";
	} elseif ($row['dea_psubtype'] == 13) {
		$render .= "9" . "^";
	} elseif ($row['dea_psubtype'] == 14) {
		$render .= "19" . "^";
	} elseif ($row['dea_psubtype'] == 15) {
		$render .= "19" . "^";
	} elseif ($row['dea_psubtype'] == 16) {
		$render .= "51" . "^";
	} elseif ($row['dea_psubtype'] == 17) {
		$render .= "20" . "^";
	} elseif ($row['dea_psubtype'] == 19) {
		$render .= "28" . "^";
	} elseif ($row['dea_psubtype'] == 20) {
		$render .= "28" . "^";
	} elseif ($row['dea_psubtype'] == 21) {
		$render .= "28" . "^";
	} elseif ($row['dea_psubtype'] == 22) {
		$render .= "23" . "^";
	} elseif ($row['dea_psubtype'] == 23) {
		$render .= "28" . "^";
	} elseif ($row['dea_psubtype'] == 25) {
		$render .= "21" . "^";
	} elseif ($row['dea_psubtype'] == 26) {
		$render .= "48" . "^";
	} elseif ($row['dea_psubtype'] == 27) {
		$render .= "19" . "^";
	} elseif ($row['dea_psubtype'] == 28) {
		$render .= "19" . "^";
	} elseif ($row['dea_psubtype'] == 29) {
		$render .= "19" . "^";
	} // defaults for other and new subtypes (use main type)
	else {
		if ($row['dea_ptype'] == 1) {
			$render .= "26" . "^";
		} elseif ($row['dea_ptype'] == 2) {
			$render .= "28" . "^";
		} elseif ($row['dea_ptype'] == 3) {
			$render .= "19" . "^";
		}
	}

	if ($row['dea_launchdate'] == '0000-00-00 00:00:00' || !$row['dea_launchdate']) {
		$render .= date('Y-m-d H:i:s') . "^";
	} else {
		$render .= $row['dea_launchdate'] . "^"; //CREATE_DATE
	}

	$render .= $date_mysql . "^"; //UPDATE_DATE

	$render .= $row['pro_addr3'] . ", " . $row['are_title'] . ", " . $pc[0] . "^"; //DISPLAY_ADDRESS
	$render .= "1" . "^"; //PUBLISHED_FLAG

	if ($row['dea_type'] == 'Lettings') { //LET_DATE_AVAILABLE
		if ($row['dea_available'] == '0000-00-00 00:00:00' || !$row['dea_available']) {
			// if no available date, use launch date
			if ($row['dea_launchdate'] == '0000-00-00 00:00:00' || !$row['dea_launchdate']) {
				// if no launch date, use now
				$render .= date('Y-m-d H:i:s') . "^";
			} else {
				$render .= $row['dea_launchdate'] . "^";
			}

		} else {
			$render .= $row['dea_available'] . "^"; //CREATE_DATE
		}
	} else {
		$render .= "^";
	}

	$render .= "^"; //LET_BOND

	if ($row['PriceType'] == 1) { //LET_TYPE_ID
		$render .= "1" . "^";
	} elseif ($row['PriceType'] == 2) {
		$render .= "2" . "^";
	} else {
		$render .= "0" . "^";
	}

	$render .= intval($furnishedId) . "^"; //LET_FURN_ID
	unset($furnishedId);

	$render .= "0" . "^"; //LET_RENT_FREQUENCY
	if ($row['dea_type'] == 'Sales') { //TRANS_TYPE_ID
		$render .= "1" . "^";
	} else {
		$render .= "2" . "^";
	}

// media
// 10 images
// 2 epc
// 5 floorplans
	$image_path = WS_PATH_IMAGES . '/' . $row['dea_id'] . '/';

	if ($row['photos']) {
		$photo_array = explode("~", $row['photos']);
	}

	$max_images = 10;

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

			$rm_image_name = $intRMBranchID . '_' . $row['dea_id'] . '_IMG_' . padzero($i) . '.jpg';

			// if the file already exists, delet before re-writing
			if (file_exists($strPath . "/" . $rm_image_name)) {
				unlink($strPath . "/" . $rm_image_name);
			}
			copy($source_image, $strPath . "/" . $rm_image_name);

			$render .= $rm_image_name . "^"; //MEDIA_IMAGE_00
			$filesToDelete[] = $strPath . "/" . $rm_image_name;

			$render .= $photo[1] . "^"; //MEDIA_IMAGE_TEXT_00

		} else { // no image

			$render .= "^"; //MEDIA_IMAGE_00
			$render .= "^"; //MEDIA_IMAGE_TEXT_00
		}

	}

	unset($photo, $photo_array, $i);

// epc document
	if ($row['epc']) {
		$epc_array = explode("~", $row['epc']);
	}

	$max_epc       = 2;
	$source_image  = "";
	$rm_image_name = "";
	for ($i = 0; $i < $max_epc; $i++) {

		if ($epc_array[$i]) {
			$epc = explode("|", $epc_array[$i]);

			if (file_exists($image_path . str_replace(".gif", "_large.gif", $epc[0]))) {
				$source_image = $image_path . str_replace(".gif", "_large.gif", $epc[0]);
			} elseif (file_exists($image_path . str_replace(".gif", "_small.gif", $epc[0]))) {
				$source_image = $image_path . str_replace(".gif", "_small.gif", $epc[0]);
			}

			$rm_image_name = $intRMBranchID . '_' . $row['dea_id'] . '_IMG_' . padzero($i + 60) . '.gif';

			// if the file already exists, delet before re-writing
			if (file_exists($strPath . "/" . $rm_image_name)) {
				unlink($strPath . "/" . $rm_image_name);
			}
			copy($source_image, $strPath . "/" . $rm_image_name);

			$render .= $rm_image_name . "^"; //MEDIA_IMAGE_00
			$filesToDelete[] = $strPath . "/" . $rm_image_name;

			$render .= $epc[1] . "^"; //MEDIA_IMAGE_TEXT_00

		} else { // no image

			$render .= "^"; //MEDIA_IMAGE_00
			$render .= "^"; //MEDIA_IMAGE_TEXT_00
		}

	}

	unset($epc, $epc_array, $i);

// floorplans
	if ($row['floorplans']) {
		$floorplan_array = explode("~", $row['floorplans']);
	}

	$max_floorplans = 5;

	for ($i = 0; $i <= $max_floorplans; $i++) {

		if ($floorplan_array[$i]) {
			$floorplan = explode("|", $floorplan_array[$i]);

			if (file_exists($image_path . $floorplan[0])) {
				$source_image = $image_path . $floorplan[0];
			}

			$rm_image_name = $intRMBranchID . '_' . $row['dea_id'] . '_FLP_' . padzero($i) . '.gif';

			// if the file already exists, delete before re-writing
			if (file_exists($strPath . "/" . $rm_image_name)) {
				unlink($strPath . "/" . $rm_image_name);
			}
			copy($source_image, $strPath . "/" . $rm_image_name);

			$render .= $rm_image_name . "^"; //MEDIA_FLOOR_PLAN_01
			$filesToDelete[] = $strPath . "/" . $rm_image_name;

			$render .= $floorplan[1] . "^"; //MEDIA_FLOOR_PLAN_TEXT_01

		} else { // no image

			$render .= "^"; //MEDIA_FLOOR_PLAN_01
			$render .= "^"; //MEDIA_FLOOR_PLAN_TEXT_01
		}

	}
	unset($floorplan, $floorplan_array, $i);

	$render = remove_lastchar($render, "^"); // @vit what does this part?
	$render = remove_lastchar($render, "^");
// brochure

	$render .= "http://" . WS_HOSTNAME . "/property/pdf/" . $row['dea_id'];

	$render .= "~\n"; //End of record + line feed

// loop
}

// end of datafeed

$render .= "#END#";

$local_file = $strPath . "/" . $strTextFile;

// if the file already exists, delet before re-writing
if (file_exists($local_file)) {
	unlink($local_file);
}

// write $render to file

if (!file_put_contents($local_file, $render)) {
	echo "could not write to file";
	exit;
}

// add the blm to delete array
#$filesToDelete[] = $strPath."/".$strTextFile;
// add the tar to the delete array
$filesToDelete[] = $strPath . "/" . $backupdate . ".tar";

// copy all images to gzip file
//backupsus();

// delete all files in array (that is all images and the blm text file)

$ftp = new FTP($CONFIG[FEED_NAME]['ftp_server'], $CONFIG[FEED_NAME]['ftp_username'], $CONFIG[FEED_NAME]['ftp_password']);
try {
	$ftp->upload($strPath, $CONFIG[FEED_NAME]['ftp_destination']);
} catch (Exception $e) {
}
foreach ($filesToDelete as $filename) {
	@unlink($filename);
}
file_put_contents(WS_PATH_LOGS . "/ftpUploadErrorLog.log", $ftp->getErrorLog(), FILE_APPEND);
file_put_contents(WS_PATH_LOGS . "/ftpUploadMessageLog.log", $ftp->getMessageLog(), FILE_APPEND);
