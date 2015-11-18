<?php
define("FEED_NAME", "primelocation");
require_once(dirname(__FILE__) . "/../../../../../config/config_feed.inc.php");
function backupsus()
{

	global $backupdate, $backupdir, $backupto, $fileprefix, $tararg, $bz2arg, $files;
	$backupsuscmd = "cd $backupdir;
	tar $tararg {$backupdate}.tar $files;
	gzip $bz2arg {$backupdate}.tar;
	mv {$backupdate}.tar.gz $backupto";
	passthru("$backupsuscmd");
}

// copy all files in a folder to ftp site
function ftp_copy($src_dir, $dst_dir)
{

	global $conn_id;
	$d = dir($src_dir);
	while ($file = $d->read()) {
		if ($file != "." && $file != "..") {
			if (is_dir($src_dir . "/" . $file)) {
				if (!@ftp_chdir($conn_id, $dst_dir . "/" . $file)) {
					ftp_mkdir($conn_id, $dst_dir . "/" . $file);
				}
				ftp_copy($src_dir . "/" . $file, $dst_dir . "/" . $file);
			} else {
				$upload = ftp_put($conn_id, $dst_dir . "/" . $file, $src_dir . "/" . $file, FTP_BINARY);
			}
		}
	}
	$d->close();
}

// path to save text file
$strFolderName = date('Ymd');
$strPath       = dirname(__FILE__) . '/primelocation/' . $strFolderName;
$strTextFile   = "WOSTGR.txt";
$backupdate    = date("Ymd");
$backupdir     = $strPath;
$files         = "*";
$backupto      = $strPath;
$fileprefix    = "";
$tararg        = "-cf";
createForlder($strPath);
$Mode = "FULL";
$sql  = "SELECT

	deal.*,
	area.are_title,
	pro_addr1,pro_addr3,pro_postcode,LEFT(pro_postcode,4) AS pro_shortpostcode,
	pro_east,pro_north,pro_latitude,pro_longitude,
	branch.bra_id,branch.bra_title,
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

LEFT JOIN media AS photos ON deal.dea_id = photos.med _row AND photos.med_table = 'deal' AND photos.med_type = 'Photograph'
LEFT JOIN media AS floorplans ON deal.dea_id = floorplans.med_row AND floorplans.med_table = 'deal' AND floorplans.med_type = 'Floorplan'

LEFT JOIN link_instruction_to_feature ON dealId = deal.dea_id
LEFT JOIN feature ON featureId = feature.fea_id

WHERE
(deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other')
AND dea_id != 4413 AND dea_id != 4494
AND (deal.noPortalFeed <> 1 AND deal.underTheRadar <> 1)
GROUP BY dea_id";
$q    = $db->query($sql);
if (DB::isError($q)) {
	die("error: " . $q->getMessage());
}
$numRows = $q->numRows();

// write headers
$render = "RowID|ExportDate|PropertyID|AgentBranchCode|Mode|Action|FullPostCode|CountryCode|Name|Address|RegionCode|";
$render .= "Summary|Details|PricePrefix|Price|PriceCurrency|SellingState|PropertyType|NewHome|SaleOrRent|Tenure|";
$render .= "BedRooms|BathRooms|ReceptionRooms|AdditionalKeywords|SharedComm|AdditionalContent\n";

$counter = 1; // record counter for datafeed

//loop through recordset
while ($row = $q->fetchRow()) {

	$render .= $counter . "|"; //RowID
	$render .= $date_mysql . "|"; //ExportDate
	$render .= $row["dea_id"] . "|"; //PropertyID

	if ($row["dea_branch"] == 1 || $row["dea_branch"] == 3) { //AgentBranchCode
		$render .= "woho" . "|"; // woho is head office, wosy is sydenham
	} elseif ($row["dea_branch"] == 2 || $row["dea_branch"] == 4) {
		$render .= "wosy" . "|";
	}

	$render .= $Mode . "|"; //Mode
	$render .= "|"; //Action
	$render .= $row["pro_postcode"] . "|"; //FullPostCode
	$render .= "GB" . "|"; //CountryCode
	$render .= "|"; //Name (shown on site, so not used)
	$render .= $row["pro_addr3"] . ", " . $row["are_title"] . " " . $row["pro_shortpostcode"] . "|"; //Address
	$render .= "|"; //RegionCode
	$render .= $row["dea_strapline"] . "|"; //Summary

	$longDescription = str_replace("|", "", $row["dea_description"]);
	if ($row["total_area"]) {
		$longDescription .= "<p>Approximate Gross Internal Area: " . $row["total_area"] . " square metres</p>";
	}
	$longDescription .= "<p>Visit <b>www.woosterstock.co.uk</b> for full details, colour photos, maps and floor plans.</p>";
	$longDescription .= "<p>We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property sales details.</p>";
	$render .= preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $longDescription) . "|"; //Details

	if ($row["dea_type"] == 'Sales') { //PricePrefix
		$render .= "F" . "|";
	} elseif ($row["dea_type"] == 'Lettings') {
		$render .= "W" . "|";
	}

	$render .= $row["dea_marketprice"] . "|"; //Price
	$render .= "GBP" . "|"; //PriceCurrency

	if ($row["dea_status"] == 'Available') { //SellingState
		$render .= "V" . "|";
	} elseif ($row["dea_status"] == 'Under Offer' || $row["dea_status"] == 'Under Offer with Other') {
		$render .= "U" . "|";
	}

	if ($row["dea_ptype"] == '2') { //PropertyType (house of flat only)
		$render .= "F" . "|";
	} else {
		$render .= "H" . "|";
	}

	$render .= "|"; //NewHome

	if ($row["dea_type"] == 'Sales') { //SaleOrRent
		$render .= "S" . "|";
	} elseif ($row["dea_type"] == 'Lettings') {
		$render .= "R" . "|";
	}

	if ($row["dea_type"] == 1) {
		if ($row["dea_tenure"] == 'Freehold') { //Tenure
			$render .= "F" . "|";
		} elseif ($row["dea_tenure"] == 'Leasehold') {
			$render .= "L" . "|";
		} elseif ($row["dea_tenure"] == 'Share of Freehold') {
			$render .= "S" . "|";
		}
	} else {
		$render .= "|";
	}

	$render .= $row["dea_bedroom"] . "|"; //BedRooms
	$render .= $row["dea_bathroom"] . "|"; //BathRooms
	$render .= $row["dea_reception"] . "|"; //ReceptionRooms
	$render .= "|"; //AdditionalKeywords
	$render .= "|"; //SharedComm
	$render .= "|"; //AdditionalContent

// media
// 5 images
// 4 floorplans
// pics n plans must be suffixed with alphabetical character, then deal id
	$image_path             = WS_PATH_IMAGES . '/' . $row["dea_id"] . '/';
	$photo_prefix_array     = array('', 'a', 'b', 'c', 'd');
	$floorplan_prefix_array = array('p', 'q', 'r', 's');

	if ($row["photos"]) {
		$photo_array = explode("~", $row["photos"]);
	}

	$max_images = 4;

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

			$rm_image_name = $row["dea_id"] . $photo_prefix_array[$i] . '.jpg';

			// if the file already exists, delet before re-writing
			if (file_exists($strPath . "/" . $rm_image_name)) {
				unlink($strPath . "/" . $rm_image_name);
			}
			copy($source_image, $strPath . "/" . $rm_image_name);

			$filesToDelete[] = $strPath . "/" . $rm_image_name;

		}

	}

	unset($i);

	if ($row["floorplans"]) {
		$floorplan_array = explode("~", $row["floorplans"]);
	}

	$max_floorplans = 3;

	for ($i = 0; $i <= $max_floorplans; $i++) {

		if ($floorplan_array[$i]) {
			$floorplan = explode("|", $floorplan_array[$i]);

			if (file_exists($image_path . $floorplan[0])) {
				$source_image = $image_path . $floorplan[0];
			}

			$rm_image_name = $row["dea_id"] . $floorplan_prefix_array[$i] . '.gif';

			// if the file already exists, delete before re-writing
			if (file_exists($strPath . "/" . $rm_image_name)) {
				unlink($strPath . "/" . $rm_image_name);
			}
			copy($source_image, $strPath . "/" . $rm_image_name);

			$filesToDelete[] = $strPath . "/" . $rm_image_name;

		}

	}

	$render .= "\n"; //End of record + line feed
	$counter++;
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
$src_dir = $strPath;
$dst_dir = $CONFIG[FEED_NAME]['ftp_destination'];
$ftp     = new FTP($CONFIG[FEED_NAME]['ftp_server'], $CONFIG[FEED_NAME]['ftp_username'], $CONFIG[FEED_NAME]['ftp_password']);
try {
	$ftp->upload($strPath, $dst_dir);
} catch (Exception $e) {
}
file_put_contents(WS_PATH_LOGS . "/ftpUploadErrorLog.log", $ftp->getErrorLog(), FILE_APPEND);
file_put_contents(WS_PATH_LOGS . "/ftpUploadMessageLog.log", $ftp->getMessageLog(), FILE_APPEND);



