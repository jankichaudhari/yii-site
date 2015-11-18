<!--#!/usr/local/bin/php -q-->
<?php

// create primelocation (fastcrop) datafeed, prepare images and send to ftp server

// run forked, or cron

/*
stil to do
features
dates
only send media that has changed
qualifier
furnished
PriceType = but no equvalent in new system
*/

// only required includes, global stops this working
require_once("/home/woosterstock/htdocs/v3.0/live/admin/inx/db.inc.php");
require_once("/home/woosterstock/htdocs/v3.0/live/admin/inx/format.inc.php");
require_once("/home/woosterstock/htdocs/v3.0/live/admin/inx/error.inc.php");
require_once("/home/woosterstock/htdocs/v3.0/live/admin/inx/general.inc.php");



function backupsus() {
	global $backupdate,$backupdir,$backupto,$fileprefix,$tararg,$bz2arg,$files;
	$backupsuscmd = "cd $backupdir;
	tar $tararg {$backupdate}.tar $files;
	gzip $bz2arg {$backupdate}.tar;
	mv {$backupdate}.tar.gz $backupto";
	passthru ("$backupsuscmd");
	}

// copy all files in a folder to ftp site
function ftp_copy($src_dir, $dst_dir) {
	global $conn_id;
	$d = dir($src_dir);
	   while($file = $d->read()) {
		   if ($file != "." && $file != "..") {
			   if (is_dir($src_dir."/".$file)) {
				   if (!@ftp_chdir($conn_id, $dst_dir."/".$file)) {
				   ftp_mkdir($conn_id, $dst_dir."/".$file);
				   }
			   ftp_copy($src_dir."/".$file, $dst_dir."/".$file);
			   }
			   else {
			   $upload = ftp_put($conn_id, $dst_dir."/".$file, $src_dir."/".$file, FTP_BINARY);
			   }
		   }
	   }
	$d->close();
	}





// path to save text file
$strFolderName = date('Ymdi');
$strPath = '/home/woosterstock/htdocs/v3.0/live/admin/feed/primelocation/'.$strFolderName;

// name of textfile (date.blm)
$strTextFile = "WOSTGR.txt";



// name of zip (gzip) file
$backupdate = date("Ymd");
// backup to gzip fuinction
$backupdir = $strPath;
$files = "*";
$backupto = $strPath;
$fileprefix = "";
$tararg = "-cf";


// create folder
if (!is_dir($strPath)) {
	if (!mkdir($strPath, 0777)) {
		echo 'error creating folder: '.$strPath;
		exit;
		}
	} else {
	// folder exists, feed already sent today
	// this is implemented becuase the feed is going to be kicked off by gemma logging in, so once a day it enough
	//exit;
	}

// only get properties that have been updated in the past 10 days
$today = strtotime($date_mysql);
$ten_days = (5*24*60*60);
$date_comparison =  date('Y-m-d H:i:s',($today-$ten_days));

// mode - full refresh or incremental
$Mode = "FULL";

$sql = "SELECT

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

LEFT JOIN media AS photos ON deal.dea_id = photos.med_row AND photos.med_table = 'deal' AND photos.med_type = 'Photograph'
LEFT JOIN media AS floorplans ON deal.dea_id = floorplans.med_row AND floorplans.med_table = 'deal' AND floorplans.med_type = 'Floorplan'

LEFT JOIN link_instruction_to_feature ON dealId = deal.dea_id
LEFT JOIN feature ON featureId = feature.fea_id

WHERE
dea_id = 4231 AND
(deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other')

GROUP BY dea_id
";
//echo $sql;
$q = $db->query($sql);
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
$numRows = $q->numRows();


// write headers
$render = "RowID|ExportDate|PropertyID|AgentBranchCode|Mode|Action|FullPostCode|CountryCode|Name|Address|RegionCode|";
$render .= "Summary|Details|PricePrefix|Price|PriceCurrency|SellingState|PropertyType|NewHome|SaleOrRent|Tenure|";
$render .= "BedRooms|BathRooms|ReceptionRooms|AdditionalKeywords|SharedComm|AdditionalContent\n";



$counter = 1; // record counter for datafeed

//loop through recordset
while ($row = $q->fetchRow()) {

print_r($row);

$render .= $counter."|";					//RowID
$render .= $date_mysql."|";					//ExportDate
$render .= $row["dea_id"]."|";				//PropertyID

if ($row["dea_branch"] == 1 || $row["dea_branch"] == 3) {					//AgentBranchCode
	$render .= "woho"."|";						// woho is head office, wosy is sydenham
	} elseif ($row["dea_branch"] == 2 || $row["dea_branch"] == 4) {
	$render .= "wosy"."|";
	}

$render .= $Mode."|";						//Mode
$render .= "|";								//Action
$render .= $row["pro_postcode"]."|";		//FullPostCode
$render .= "GB"."|";						//CountryCode
$render .= "|";								//Name (shown on site, so not used)
$render .= $row["pro_addr3"].", ".$row["are_title"]." ".$row["pro_shortpostcode"]."|";			//Address
$render .= "|";								//RegionCode
$render .= $row["dea_strapline"]."|";		//Summary

$longDescription = str_replace("|","",$row["dea_description"]);
if ($row["total_area"]) {
	$longDescription .= "<p>Approximate Gross Internal Area: ".$row["total_area"]." square metres</p>";
	}
$longDescription .= "<p>Visit <b>www.woosterstock.co.uk</b> for full details, colour photos, maps and floor plans.</p>";
$longDescription .= "<p>We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property sales details.</p>";
$render .= preg_replace("/[\r\n]+[\s\t]*[\r\n]+/","",$longDescription)."|";			//Details

if ($row["dea_type"] == 'Sales') {					//PricePrefix
	$render .=  "F"."|";
	} elseif ($row["dea_type"] == 'Lettings') {
	$render .= "W"."|";
	}

$render .= $row["dea_marketprice"]."|";				//Price
$render .= "GBP"."|";						//PriceCurrency

if ($row["dea_status"] == 'Available') { 		//SellingState
	$render .= "V"."|";
	} elseif ($row["dea_status"] == 'Under Offer' || $row["dea_status"] == 'Under Offer with Other') {
	$render .= "U"."|";
	}


if ($row["dea_ptype"] == '2') { 		//PropertyType (house of flat only)
	$render .= "F"."|";
	} else {
	$render .= "H"."|";
	}

$render .= "|";								//NewHome

if ($row["dea_type"] == 'Sales') {					//SaleOrRent
	$render .=  "S"."|";
	} elseif ($row["dea_type"] == 'Lettings') {
	$render .= "R"."|";
	}

if ($row["dea_type"] == 1) {
	if ($row["dea_tenure"] == 'Freehold') {				//Tenure
		$render .= "F"."|";
		} elseif ($row["dea_tenure"] == 'Leasehold') {
		$render .= "L"."|";
		} elseif ($row["dea_tenure"] == 'Share of Freehold') {
		$render .= "S"."|";
		}
	} else {
	$render .= "|";
	}

$render .= $row["dea_bedroom"]."|";			//BedRooms
$render .= $row["dea_bathroom"]."|";			//BathRooms
$render .= $row["dea_reception"]."|";			//ReceptionRooms
$render .= "|";								//AdditionalKeywords
$render .= "|";								//SharedComm
$render .= "|";								//AdditionalContent




// media
// 5 images
// 4 floorplans
// pics n plans must be suffixed with alphabetical character, then deal id
$image_path = '/home/woosterstock/htdocs/v3/images/p/'.$row["dea_id"].'/';
$photo_prefix_array = array('','a','b','c','d');
$floorplan_prefix_array = array('p','q','r','s');

if ($row["photos"]) {
	$photo_array = explode("~",$row["photos"]);
	}

$max_images = 4;

for($i = 0; $i <= $max_images; $i++) {

	if ($photo_array[$i]) {
		$photo = explode("|",$photo_array[$i]);

		if (file_exists($image_path.str_replace(".jpg","_large.jpg",$photo[0]))) {
			$source_image = $image_path.str_replace(".jpg","_large.jpg",$photo[0]);
			}
		elseif (file_exists($image_path.str_replace(".jpg","_small.jpg",$photo[0]))) {
			$source_image = $image_path.str_replace(".jpg","_small.jpg",$photo[0]);
			}

		$rm_image_name = $row["dea_id"].$photo_prefix_array[$i].'.jpg';
		echo "<p>$source_image == $rm_image_name</p>";
		// if the file already exists, delet before re-writing
		if (file_exists($strPath."/".$rm_image_name)) {
			unlink($strPath."/".$rm_image_name);
			}
		copy($source_image,$strPath."/".$rm_image_name);

		$filesToDelete[] = $strPath."/".$rm_image_name;

		}

	}

unset($i);

if ($row["floorplans"]) {
	$floorplan_array = explode("~",$row["floorplans"]);
	}

$max_floorplans = 3;

for($i = 0; $i <= $max_floorplans; $i++) {

	if ($floorplan_array[$i]) {
		$floorplan = explode("|",$floorplan_array[$i]);

		if (file_exists($image_path.$floorplan[0])) {
			$source_image = $image_path.$floorplan[0];
			}

		$rm_image_name = $row["dea_id"].$floorplan_prefix_array[$i].'.gif';

		// if the file already exists, delete before re-writing
		if (file_exists($strPath."/".$rm_image_name)) {
			unlink($strPath."/".$rm_image_name);
			}
		copy($source_image,$strPath."/".$rm_image_name);

		$filesToDelete[] = $strPath."/".$rm_image_name;

		}

	}


$render .= "\n";	//End of record + line feed
$counter++;
// loop

/*
// different id's for each branch (currently lettings dont have seperate branch ids
if ($row["dea_type"] == 'Sales' && $row["dea_branch"] == 1) { // cam.sale
	$intRMBranchID = 3120;
	}
elseif ($row["dea_type"] == 'Sales' && $row["dea_branch"] == 2) { // syd.sale
	$intRMBranchID = 24869;
	}
elseif ($row["dea_type"] == 'Lettings' && $row["dea_branch"] == 1) { // cam.let
	$intRMBranchID = 3120;
	}
elseif ($row["dea_type"] == 'Lettings' && $row["dea_branch"] == 2) { // syd.let
	$intRMBranchID = 24869;
	}

$render .= $intRMBranchID."_".$row["dea_id"]."^"; 			//AGENT_REF
$render .= $row["pro_addr1"]."^";							//ADDRESS_1
$render .= $row["pro_addr3"]."^"; 							//ADDRESS_2
$render .= $row["are_title"]."^";							//ADDRESS_3
$render .= "^";												//ADDRESS_4
$render .= $row["pro_addr5"]."^";							//TOWN
$pc = explode(" ",$row["pro_postcode"]);
$render .= $pc[0]."^";										//POSTCODE1
$render .= $pc[1]."^";										//POSTCODE2

$render .= "^";												//FEATURE1 property type
$render .= "^";												//FEATURE2 bedroms
$render .= "^";												//FEATURE3 ch
$render .= "^";												//FEATURE4 dg
$render .= "^";												//FEATURE5
$render .= "^";												//FEATURE6
$render .= "^";												//FEATURE7
$render .= "^";												//FEATURE8
$render .= "^";												//FEATURE9
$render .= "^";												//FEATURE10

$length = strlen($row["dea_strapline"]);
$length = (300-$length); // 300 is allowed, but i add (cont) so we use 294

$desc = strip_html($row["dea_description"]);
$trimmed = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/","",$desc);
$trimmed = str_replace("&amp;#039;","'",$trimmed);
$trimmed = str_replace("&amp;amp;#039;","'",$trimmed);
$trimmed = str_replace("&amp;eacute;","�",$trimmed);


$trimmed = substr($trimmed, 0, $length);

$render .= $row["dea_strapline"].": ".$trimmed."^";	//SUMMARY

$longDescription = $row["dea_description"];
if ($row["total_area"]) {
	$longDescription .= "<p>Approximate Gross Internal Area: ".$row["total_area"]." square metres</p>";
	}
$longDescription .= "<p>For further information or to arrange a viewing, please contact our <b>".$row["bra_title"]." Branch</b> on <b>".$row["bra_tel"].".</b></p>";
$longDescription .= "<p>Visit <b>www.woosterstock.co.uk</b> for full details, colour photos, maps and floor plans.</p>";
$longDescription .= "<p>We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property sales details.</p>";

$render .= preg_replace("/[\r\n]+[\s\t]*[\r\n]+/","",$longDescription)."^";		//DESCRIPTION

$render .= $intRMBranchID."^";						//BRANCH_ID



if ($row["dea_status"] == 'Available') { 			//STATUS_ID - 0 for sale, 1 under offer
	$render .= "0"."^";
	} elseif ($row["dea_status"] == 'Under Offer' || $row["dea_status"] == 'Under Offer with Other') {
	$render .= "1"."^";
	}

$render .= $row["dea_bedroom"]."^";					//BEDROOMS
$render .= $row["dea_marketprice"]."^";				//PRICE
$render .= "0"."^";									//PRICE_QUALIFIER



if ($row["dea_psubtype"] == 4) { 					//PROP_SUB_ID
	$render .= "4"."^";
	}
elseif ($row["dea_psubtype"] == 5) {
	$render .= "3"."^";
	}
elseif ($row["dea_psubtype"] == 6) {
	$render .= "1"."^";
	}
elseif ($row["dea_psubtype"] == 7) {
	$render .= "2"."^";
	}
elseif ($row["dea_psubtype"] == 8) {
	$render .= "5"."^";
	}
elseif ($row["dea_psubtype"] == 9) {
	$render .= "12"."^";
	}
elseif ($row["dea_psubtype"] == 10) {
	$render .= "26"."^";
	}
elseif ($row["dea_psubtype"] == 11) {
	$render .= "29"."^";
	}
elseif ($row["dea_psubtype"] == 12) {
	$render .= "11"."^";
	}
elseif ($row["dea_psubtype"] == 13) {
	$render .= "9"."^";
	}
elseif ($row["dea_psubtype"] == 14) {
	$render .= "19"."^";
	}
elseif ($row["dea_psubtype"] == 15) {
	$render .= "19"."^";
	}
elseif ($row["dea_psubtype"] == 16) {
	$render .= "51"."^";
	}
elseif ($row["dea_psubtype"] == 17) {
	$render .= "20"."^";
	}
elseif ($row["dea_psubtype"] == 19) {
	$render .= "28"."^";
	}
elseif ($row["dea_psubtype"] == 20) {
	$render .= "28"."^";
	}
elseif ($row["dea_psubtype"] == 21) {
	$render .= "28"."^";
	}
elseif ($row["dea_psubtype"] == 22) {
	$render .= "23"."^";
	}
elseif ($row["dea_psubtype"] == 23) {
	$render .= "28"."^";
	}
elseif ($row["dea_psubtype"] == 25) {
	$render .= "21"."^";
	}
elseif ($row["dea_psubtype"] == 26) {
	$render .= "48"."^";
	}
elseif ($row["dea_psubtype"] == 27) {
	$render .= "19"."^";
	}
elseif ($row["dea_psubtype"] == 28) {
	$render .= "19"."^";
	}
elseif ($row["dea_psubtype"] == 29) {
	$render .= "19"."^";
	}
// defaults for other and new subtypes (use main type)
else {
	if ($row["dea_ptype"] == 1) {
		$render .= "26"."^";
		}
	elseif ($row["dea_ptype"] == 2) {
		$render .= "28"."^";
		}
	elseif ($row["dea_ptype"] == 3) {
		$render .= "19"."^";
		}
	}



$render .= $row["dea_launchdate"]."^";			//CREATE_DATE
$render .= $date_mysql."^";			//UPDATE_DATE

$render .= $row["pro_addr3"].", ".$row["are_title"].", ".$pc[0]."^";		//DISPLAY_ADDRESS
$render .= "1"."^";															//PUBLISHED_FLAG


if ($row["dea_type"] == 'Lettings') {													//LET_DATE_AVAILABLE
	$render .=  $row["dea_available"]."^";
	} else {
	$render .= "^";
	}

$render .= "^";																//LET_BOND

if ($row["PriceType"] == 1) {												//LET_TYPE_ID
	$render .= "1"."^";
	} elseif ($row["PriceType"] == 2) {
	$render .= "2"."^";
	} else {
	$render .= "0"."^";
	}

if ($row["furnished"] == 1) { 					//LET_FURN_ID
	$render .= "2"."^";
	} elseif ($row["furnished"] == 2) {
	$render .= "1"."^";
	} elseif ($row["furnished"] == 3) {
	$render .= "0"."^";
	} else {
	$render .= "3"."^";
	}

$render .= "0"."^";						//LET_RENT_FREQUENCY

if ($row["dea_type"] == 'Sales') {		//TRANS_TYPE_ID
	$render .= "1"."^";
	} else {
	$render .= "2"."^";
	}




// media
// 10 images
// 5 floorplans
$image_path = '/home/woosterstock/htdocs/v3/images/p/'.$row["dea_id"].'/';

if ($row["photos"]) {
	$photo_array = explode("~",$row["photos"]);
	}

$max_images = 10;

for($i = 0; $i <= $max_images; $i++) {


	if ($photo_array[$i]) {
		$photo = explode("|",$photo_array[$i]);

		if (file_exists($image_path.str_replace(".jpg","_large.jpg",$photo[0]))) {
			$source_image = $image_path.str_replace(".jpg","_large.jpg",$photo[0]);
			}
		elseif (file_exists($image_path.str_replace(".jpg","_small.jpg",$photo[0]))) {
			$source_image = $image_path.str_replace(".jpg","_small.jpg",$photo[0]);
			}

		$rm_image_name = $intRMBranchID.'_'.$row["dea_id"].'_IMG_'.padzero($i).'.jpg';

		// if the file already exists, delet before re-writing
		if (file_exists($strPath."/".$rm_image_name)) {
			unlink($strPath."/".$rm_image_name);
			}
		copy($source_image,$strPath."/".$rm_image_name);

		$render .= $rm_image_name."^";		//MEDIA_IMAGE_00
		$filesToDelete[] = $strPath."/".$rm_image_name;

		$render .= $photo[1]."^";			//MEDIA_IMAGE_TEXT_00

		} else { // no image

		$render .= "^";		//MEDIA_IMAGE_00
		$render .= "^";		//MEDIA_IMAGE_TEXT_00
		}

	}

unset($i);

if ($row["floorplans"]) {
	$floorplan_array = explode("~",$row["floorplans"]);
	}

$max_floorplans = 5;

for($i = 0; $i <= $max_floorplans; $i++) {

	if ($floorplan_array[$i]) {
		$floorplan = explode("|",$floorplan_array[$i]);

		if (file_exists($image_path.$floorplan[0])) {
			$source_image = $image_path.$floorplan[0];
			}

		$rm_image_name = $intRMBranchID.'_'.$row["dea_id"].'_FLP_'.padzero($i).'.gif';

		// if the file already exists, delete before re-writing
		if (file_exists($strPath."/".$rm_image_name)) {
			unlink($strPath."/".$rm_image_name);
			}
		copy($source_image,$strPath."/".$rm_image_name);

		$render .= $rm_image_name."^";		//MEDIA_FLOOR_PLAN_01
		$filesToDelete[] = $strPath."/".$rm_image_name;

		$render .= $floorplan[1]."^";			//MEDIA_FLOOR_PLAN_TEXT_01

		} else { // no image

		$render .= "^";		//MEDIA_FLOOR_PLAN_01
		$render .= "^";		//MEDIA_FLOOR_PLAN_TEXT_01
		}

	}


$render = remove_lastchar($render,"^");
$render = remove_lastchar($render,"^");
// brochure

$render .= "http://www.woosterstock.co.uk/Print.php?id=".$row["dea_id"]."^";


$render .= "~\n";	//End of record + line feed

// loop
*/
}

// end of datafeed

$render .= "#END#";


die($render);


$local_file = $strPath."/".$strTextFile;

// if the file already exists, delet before re-writing
if (file_exists($local_file)) {
	unlink($local_file);
	}

// write $render to file

if (!file_put_contents($local_file,$render)) {
	echo "could not write to file";
	exit;
	}







/*
// add the blm to delete array
#$filesToDelete[] = $strPath."/".$strTextFile;
// add the tar to the delete array
$filesToDelete[] = $strPath."/".$backupdate.".tar";


// copy all images to gzip file
backupsus();


// delete all files in array (that is all images and the blm text file)
foreach ($filesToDelete as $filename) {
	@unlink($filename);
	}

*/


// ******************* PRIMELOCATION **********************

$ftp_server = "ftp1.primelocation.com";
$ftp_username = "wostgr";
$ftp_password = "mw0805ws";

#$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
if (ftp_connect($ftp_server)) {
	$conn_id = ftp_connect($ftp_server);
	} else {
	$errors[] = "could not connect to $ftp_server";
	}
$login_result = ftp_login($conn_id, $ftp_username, $ftp_password);

$src_dir = $strPath;
$dst_dir = ".";

ftp_copy($src_dir, $dst_dir);

ftp_close($conn_id);








if (is_array($errors)) {
	$error_msg = "Errors:\n";
	foreach ($errors AS $err) {
		$error_msg .= $err."\n";
		}
	}
// completion email
mail('markdw@hotmail.com','feed notify',"feed notify for $strPath\n\n$error_msg");

?>