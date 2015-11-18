<?php
define("FEED_NAME", "loot");
require_once(dirname(__FILE__) . "/../../../../../config/config_feed.inc.php");
require_once WS_PATH_COMPONENTS . '/Spreadsheet/Excel/Writer.php';
$strPath        = dirname(__FILE__) . '/loot';
$column_headers = array('ACCOUNT_ID', 'AD_ID', 'TITLE', 'BODY', 'PRICE', 'PRICE_TYPE', 'CATEGORY_ID', 'Property_NumOfBeds', 'OFFER_TYPE', 'POSTCODE', 'CONTACT_EMAIL',
						'CONTACT_TEL', 'CONTACT_MOBILE', 'CONTACT_FAX', 'IMAGE_URL', 'IMAGE_URL', 'IMAGE_URL', 'IMAGE_URL', 'IMAGE_URL', 'IMAGE_URL',
						'IMAGE_URL', 'IMAGE_URL', 'IMAGE_URL', 'IMAGE_URL',);

if (!file_exists($strPath)) {
	mkdir($strPath, 0777);
}
$sql = "SELECT
	deal.*,
	area.are_title,
	pro_addr1,pro_addr3,pro_addr5,pro_postcode,
	CONCAT(pro_addr3,' ',area.are_title,' ',LEFT(pro_postcode,4)) AS pro_address,
	branch.bra_id,branch.bra_title,branch.bra_tel,branch.bra_fax,branch.bra_email,
	T.pty_title AS ptype,
	ST.pty_title AS psubtype,
	GROUP_CONCAT(DISTINCT CONCAT(photos.med_file) ORDER BY photos.med_order ASC SEPARATOR '~') AS photos
FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN area ON property.pro_area = area.are_id
	LEFT JOIN branch ON deal.dea_branch = branch.bra_id
	LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
	LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id
	LEFT JOIN media AS photos ON deal.dea_id = photos.med_row AND photos.med_table = 'deal' AND photos.med_type = 'Photograph'
WHERE (deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer With Other') AND deal.dea_ptype IN (1,2)
AND (deal.noPortalFeed <> 1 AND deal.underTheRadar <> 1)
GROUP BY dea_id
ORDER BY dea_launchdate DESC LIMIT 250";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("error: " . $q->getMessage());
}

// We give the path to our file here
$workbook = new Spreadsheet_Excel_Writer($strPath . '/loot.xls');
$workbook->setVersion(8);
$worksheet = &$workbook->addWorksheet('Loot');

$rowNumber = 0;
$colNumber = 0;
foreach ($column_headers as $val) {
	$worksheet->write($rowNumber, $colNumber++, trim($val));
}

$rowNumber = 1;
$colNumber = 0;
while ($row = $q->fetchRow()) {

	$categoryType = '';
	$colNumber    = 0;
	$worksheet->write($rowNumber, $colNumber++, "feed_wooster"); //ACCOUNT_ID
	$worksheet->write($rowNumber, $colNumber++, $row["dea_id"]); //AD_ID
	$worksheet->write($rowNumber, $colNumber++, $row["pro_address"]); //TITLE
	$worksheet->write($rowNumber, $colNumber++, getDescription($row));
	$worksheet->write($rowNumber, $colNumber++, round($row["dea_marketprice"])); //PRICE
	$worksheet->write($rowNumber, $colNumber++, ($row["dea_type"] == 'Lettings' ? 'pw' : '')); //PRICE_TYPE
	$worksheet->write($rowNumber, $colNumber++, getCategoryType($row));
	$worksheet->write($rowNumber, $colNumber++, $row["dea_bedroom"]); //PROPERTY_NUM_BEDROOMS
	$worksheet->write($rowNumber, $colNumber++, 'OFF'); //OFFER_TYPE
	$worksheet->write($rowNumber, $colNumber++, $row["pro_postcode"]); //POSTCODE
	$worksheet->write($rowNumber, $colNumber++, $row["bra_email"]); //CONTACT_EMAIL
	$worksheet->write($rowNumber, $colNumber++, $row["bra_tel"]); //CONTACT_TEL
	$worksheet->write($rowNumber, $colNumber++, ""); //CONTACT_MOBILE // empty field.
	$worksheet->write($rowNumber, $colNumber++, $row["bra_fax"]); //CONTACT_FAX

	//image_url
	if ($row["photos"]) {
		$photo_array = explode("~", $row["photos"]);
	} else {
		$photo_array = array();
	}
	$imageCount = 0;
	foreach ($photo_array as $val) {
		if ($imageCount > 9) {
			break;
		}

		if (file_exists(WS_PATH_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_large.jpg", $val))) {
			$worksheet->write($rowNumber, $colNumber++, WS_URL_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_large.jpg", $val));
		}
		else {
			$worksheet->write($rowNumber, $colNumber++, WS_URL_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_small.jpg", $val));
		}
		$imageCount++;
	}
	$rowNumber++;
}

$workbook->close();

$ftp = new FTP($CONFIG[FEED_NAME]['ftp_server'], $CONFIG[FEED_NAME]['ftp_username'], $CONFIG[FEED_NAME]['ftp_password']);

try {
	$ftp->upload($strPath, $CONFIG[FEED_NAME]['ftp_destination']);

} catch (Exception $e) {
}
file_put_contents(WS_PATH_LOGS . "/ftpUploadErrorLog.log", $ftp->getErrorLog(), FILE_APPEND);
file_put_contents(WS_PATH_LOGS . "/ftpUploadMessageLog.log", $ftp->getMessageLog(), FILE_APPEND);

// =================================================================================
// <<< helpers
function getCategoryType($data)
{
	$categoryType = '';
	if ($data["dea_type"] == 'Sales') {
		if ($data["dea_ptype"] == 1) {
			$categoryType = 'Houses for Sale';
		}
		elseif ($data["dea_ptype"] == 2) {
			$categoryType = 'Flats and Apartments for Sale';
		}
	} elseif ($data["dea_type"] == 'Lettings') {
		if ($data["psubtype"] == 'Room') {
			$categoryType = 'Bedsits and Studios';
		} else {
			if ($data["dea_ptype"] == 1) {
				$categoryType = 'Houses to Rent';
			}
			elseif ($data["dea_ptype"] == 2) {
				$categoryType = 'Flats to Rent';
			}
		}
	}
	return $categoryType;
}

function getDescription($row)
{
	$description = trim(strip_tags($row["dea_description"]));
	$description = strlen($description) > 1950 ? substr($description, 0, 1945) . "... " : $description;
	$description = str_replace(array('&#039;', '&#233;', '&rsquo;'), array('\'', 'e', '\''), $description);
	$description .= ' - Presented by Wooster & Stock Estate Agents';
	return $description;
}
// helpers >>>
// =================================================================================