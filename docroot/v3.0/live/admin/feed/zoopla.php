`<?php
/**
 * @var $feed Feed
 */
define("FEED_NAME", "zoopla");
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
$strPath = dirname(__FILE__) . '/zoopla/' . $strFolderName;
// name of textfile (date.blm)
$strTextFile = date('Ymd') . ".blm";

// name of zip (gzip) file
$backupdate = date("Ymd");
// backup to gzip fuinction
$backupdir = $strPath;
$files = "*";
$backupto = $strPath;
$fileprefix = "";
$tararg = "-cf";

createForlder($strPath);

// only get properties that have been updated in the past 10 days
$today = strtotime($date_mysql);
$ten_days = (5 * 24 * 60 * 60);
$date_comparison = date('Y-m-d H:i:s', ($today - $ten_days));

$imagesql = "SELECT max(photos) AS photos FROM (SELECT

	count(DISTINCT(photos.med_file)) AS photos

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
GROUP BY dea_id) AS t1";
//SET the value of group_concat_max_len
setVariable(99999);
$qimage = $db->query($imagesql);
if (DB::isError($qimage)) {
	die("error: " . $qimage->getMessage());
}
while ($rowimage = $qimage->fetchRow()) {
	$max_images = $rowimage["photos"];
}
if ($max_images > 59) {
	$max_images = 59;
}
$floorplansql = "SELECT max(floorplans) AS floorplans FROM (SELECT

	count(DISTINCT(floorplans.med_file)) AS floorplans

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
GROUP BY dea_id) AS t1";
$qfloorplan = $db->query($floorplansql);
if (DB::isError($qfloorplan)) {
	die("error: " . $qfloorplan->getMessage());
}
while ($rowfloorplan = $qfloorplan->fetchRow()) {
	$max_floorplans = $rowfloorplan["floorplans"];
}

$epcsql = "SELECT max(epc) AS epc FROM (SELECT

	count(DISTINCT(epc.med_file)) AS epc

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
GROUP BY dea_id) AS t1";
$qepc = $db->query($epcsql);
if (DB::isError($qepc)) {
	die("error: " . $qepc->getMessage());
}
while ($rowepc = $qepc->fetchRow()) {
	$max_epc = $rowepc["epc"];
}

$sql = include __DIR__ . '/main_query.php';
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
for ($i_images = 0; $i_images < $max_images; $i_images++) {
	$render .= "MEDIA_IMAGE_" . padzero($i_images) . "^MEDIA_IMAGE_TEXT_" . padzero($i_images) . "^";
}

// adding 2 epc fields (23/09/08)
$iepcvalue = 60;
for ($i_epc = 0; $i_epc < $max_epc; $i_epc++) {
	$render .= "MEDIA_IMAGE_" . padzero($iepcvalue) . "^MEDIA_IMAGE_TEXT_" . padzero($iepcvalue) . "^";
	$iepcvalue++;
}

for ($i_floorplans = 0; $i_floorplans < $max_floorplans; $i_floorplans++) {
	$render .= "MEDIA_FLOOR_PLAN_" . padzero($i_floorplans) . "^MEDIA_FLOOR_PLAN_TEXT_" . padzero($i_floorplans) . "^";
}

$render .= "MEDIA_DOCUMENT_01^";
$render .= "~\n";
$render .= "#DATA#\n";

//loop through recordset

//while
while ($row = $q->fetchRow()) {
	$intRMBranchID = $feed->getBranchPortalCode($row['dea_branch']);

	$render .= $intRMBranchID . "_" . $row['dea_id'] . "^"; //AGENT_REF
	$render .= ($row['feed_line1'] ? : $row['line1']) . "^"; //ADDRESS_1
	$render .= ($row['feed_line2'] ? : $row['line3']) . "^"; //ADDRESS_2 PLEASE NOTE address line 3 is used as a fallback
	$render .= ($row['feed_line3'] ? : $row['line2']) . "^"; //ADDRESS_3
	$render .= ($row['feed_line4'] ? : $row['line4']) . "^"; //ADDRESS_4
	$render .= ($row['feed_city'] ? : $row['line5']) . "^"; //ADDRESS_TOWN
	$pc = explode(" ", $row['postcode']);
	$render .= $pc[0] . "^"; //POSTCODE1
	$render .= $pc[1] . "^"; //POSTCODE2

	$render .= "^"; //FEATURE1 property type
	$render .= "^"; //FEATURE2 bedroms
	$render .= "^"; //FEATURE3 ch
	$render .= "^"; //FEATURE4 dg
	$render .= "^"; //FEATURE5
	$render .= "^"; //FEATURE6
	$render .= "^"; //FEATURE7
	$render .= "^"; //FEATURE8
	$render .= "^"; //FEATURE9
	$render .= "^"; //FEATURE10

	$length = strlen($row["dea_strapline"]);
	$length = (300 - $length); // 300 is allowed, but i add (cont) so we use 294

	$desc    = strip_tags($row["dea_description"], '<p><b><i>');
	$trimmed = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $desc);
	$trimmed = html_entity_decode($trimmed);
	$trimmed = substr($trimmed, 0, $length);

	$render .= strip_tags($row["dea_strapline"]) . ": " . $trimmed . "^"; //SUMMARY

	$longDescription = strip_tags($row["dea_description"], '<p><b><i>');
	if ($row["total_area"]) {
		$longDescription .= "<p>Approximate Gross Internal Area: " . $row["total_area"] . " square metres</p>";
	}
	$longDescription .= "<p>For further information or to arrange a viewing, please contact our <b>" . $row["bra_title"] . " Branch</b> on <b>" . $row["bra_tel"] . ".</b></p>";
	$longDescription .= "<p>Visit <b>www.woosterstock.co.uk</b> for full details, colour photos, maps and floor plans.</p>";
	$longDescription .= "<p>We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property sales details.</p>";

	$render .= preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $longDescription) . "^"; //DESCRIPTION

	$render .= $intRMBranchID . "^"; //BRANCH_ID

	if ($row["dea_status"] == 'Available') { //STATUS_ID - 0 for sale, 3 under offer, 3 = let stc

		$render .= "0" . "^";

	} elseif ($row["dea_status"] == 'Under Offer' || $row["dea_status"] == 'Under Offer with Other' || $row["dea_status"] == 'Exchanged') {

		if ($row["dea_type"] == 'Sales') {
			$render .= "3" . "^";
		} else {
			$render .= "5" . "^";
		}

	}

	$render .= $row["dea_bedroom"] . "^"; //BEDROOMS
	$render .= $row["dea_marketprice"] . "^"; //PRICE
	if ($row['dea_qualifier'] == 'POA') {
		$render .= "1" . "^"; //PRICE_QUALIFIER
	} else {
		$render .= "0" . "^"; //PRICE_QUALIFIER
	}

	if ($row["dea_psubtype"] == 4) { //PROP_SUB_ID
		$render .= "4" . "^";
	} elseif ($row["dea_psubtype"] == 5) {
		$render .= "3" . "^";
	} elseif ($row["dea_psubtype"] == 6) {
		$render .= "1" . "^";
	} elseif ($row["dea_psubtype"] == 7) {
		$render .= "2" . "^";
	} elseif ($row["dea_psubtype"] == 8) {
		$render .= "5" . "^";
	} elseif ($row["dea_psubtype"] == 9) {
		$render .= "12" . "^";
	} elseif ($row["dea_psubtype"] == 10) {
		$render .= "26" . "^";
	} elseif ($row["dea_psubtype"] == 11) {
		$render .= "29" . "^";
	} elseif ($row["dea_psubtype"] == 12) {
		$render .= "11" . "^";
	} elseif ($row["dea_psubtype"] == 13) {
		$render .= "9" . "^";
	} elseif ($row["dea_psubtype"] == 14) {
		$render .= "19" . "^";
	} elseif ($row["dea_psubtype"] == 15) {
		$render .= "19" . "^";
	} elseif ($row["dea_psubtype"] == 16) {
		$render .= "51" . "^";
	} elseif ($row["dea_psubtype"] == 17) {
		$render .= "20" . "^";
	} elseif ($row["dea_psubtype"] == 19) {
		$render .= "28" . "^";
	} elseif ($row["dea_psubtype"] == 20) {
		$render .= "28" . "^";
	} elseif ($row["dea_psubtype"] == 21) {
		$render .= "28" . "^";
	} elseif ($row["dea_psubtype"] == 22) {
		$render .= "23" . "^";
	} elseif ($row["dea_psubtype"] == 23) {
		$render .= "28" . "^";
	} elseif ($row["dea_psubtype"] == 25) {
		$render .= "21" . "^";
	} elseif ($row["dea_psubtype"] == 26) {
		$render .= "48" . "^";
	} elseif ($row["dea_psubtype"] == 27) {
		$render .= "19" . "^";
	} elseif ($row["dea_psubtype"] == 28) {
		$render .= "19" . "^";
	} elseif ($row["dea_psubtype"] == 29) {
		$render .= "19" . "^";
	} // defaults for other and new subtypes (use main type)
	else {
		if ($row["dea_ptype"] == 1) {
			$render .= "26" . "^";
		} elseif ($row["dea_ptype"] == 2) {
			$render .= "28" . "^";
		} elseif ($row["dea_ptype"] == 3) {
			$render .= "19" . "^";
		}
	}

	$render .= $row["dea_launchdate"] . "^"; //CREATE_DATE
	$render .= $date_mysql . "^"; //UPDATE_DATE

	$render .= $row["pro_addr3"] . ", " . $row["are_title"] . ", " . $pc[0] . "^"; //DISPLAY_ADDRESS
	$render .= "1" . "^"; //PUBLISHED_FLAG

	if ($row["dea_type"] == 'Lettings') { //LET_DATE_AVAILABLE
		$render .= $row["dea_available"] . "^";
	} else {
		$render .= "^";
	}

	$render .= "^"; //LET_BOND

	if ($row["PriceType"] == 1) { //LET_TYPE_ID
		$render .= "1" . "^";
	} elseif ($row["PriceType"] == 2) {
		$render .= "2" . "^";
	} else {
		$render .= "0" . "^";
	}

	if ($row["furnished"] == 1) { //LET_FURN_ID
		$render .= "2" . "^";
	} elseif ($row["furnished"] == 2) {
		$render .= "1" . "^";
	} elseif ($row["furnished"] == 3) {
		$render .= "0" . "^";
	} else {
		$render .= "3" . "^";
	}

	$render .= "0" . "^"; //LET_RENT_FREQUENCY

	if ($row["dea_type"] == 'Sales') { //TRANS_TYPE_ID
		$render .= "1" . "^";
	} else {
		$render .= "2" . "^";
	}

// media
// 10 images
// 2 epc
// 5 floorplans
	$image_path = WS_PATH_IMAGES . '/' . $row["dea_id"] . '/';

	if ($row["photos"]) {
		$photo_array = explode("~", $row["photos"]);
	}
	/*	$max_images = sizeof($photo_array);*/
	/*  $max_images = 10; */

	for ($i = 0; $i < $max_images; $i++) {

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
	if ($row["epc"]) {
		$epc_array = explode("~", $row["epc"]);
	}
	/* $max_epc = sizeof($epc_array); */
	/* $max_epc = 2;	 */

	for ($i = 0; $i < $max_epc; $i++) {

		if ($epc_array[$i]) {
			$epc = explode("|", $epc_array[$i]);

			if (file_exists($image_path . str_replace(".jpg", "_large.jpg", $epc[0]))) {
				$source_image = $image_path . str_replace(".jpg", "_large.jpg", $epc[0]);
			} elseif (file_exists($image_path . str_replace(".jpg", "_small.jpg", $epc[0]))) {
				$source_image = $image_path . str_replace(".jpg", "_small.jpg", $epc[0]);
			}

			$rm_image_name = $intRMBranchID . '_' . $row["dea_id"] . '_IMG_' . padzero($i + 60) . '.jpg';

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
	if ($row["floorplans"]) {
		$floorplan_array = explode("~", $row["floorplans"]);
	}

	/* $max_floorplans = sizeof($floorplan_array);	*/
	/* $max_floorplans = 5; */

	for ($i = 0; $i < $max_floorplans; $i++) {

		if ($floorplan_array[$i]) {
			$floorplan = explode("|", $floorplan_array[$i]);

			if (file_exists($image_path . $floorplan[0])) {
				$source_image = $image_path . $floorplan[0];
			}

			$rm_image_name = $intRMBranchID . '_' . $row["dea_id"] . '_FLP_' . padzero($i) . '.gif';

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

	/*
	$render = remove_lastchar($render,"^");
	$render = remove_lastchar($render,"^"); */
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

//echo $local_file . "\n";
//exit;
// write $render to file

if (!file_put_contents($local_file, $render)) {
	echo "could not write to file";
	exit;
}
$filesToDelete[] = $strPath . "/" . $backupdate . ".tar";

// copy all images to gzip file
backupsus();

// delete all files in array (that is all images and the blm text file)
foreach ($filesToDelete as $filename) {
	@unlink($filename);
}

$ftp = new FTP($CONFIG[FEED_NAME]['ftp_server'], $CONFIG[FEED_NAME]['ftp_username'], $CONFIG[FEED_NAME]['ftp_password']);
try {
	$ftp->upload($strPath, $CONFIG[FEED_NAME]['ftp_destination']);
} catch (Exception $e) {
}
file_put_contents(WS_PATH_LOGS . "/ftpUploadErrorLog.log", $ftp->getErrorLog(), FILE_APPEND);
file_put_contents(WS_PATH_LOGS . "/ftpUploadMessageLog.log", $ftp->getMessageLog(), FILE_APPEND);

function setVariable($len)
{

	$query  = "set group_concat_max_len=$len";
	$result = mysql_query($query);
	if (!$result) {
		die("error! $query<br>" . mysql_error());
	}
}
