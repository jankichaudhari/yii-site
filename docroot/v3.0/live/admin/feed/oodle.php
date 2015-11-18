<?php
require_once(dirname(__FILE__) . "/../../../../../config/config_feed.inc.php");
$sql = "SELECT
	deal.*,
	area.are_title,
	pro_addr1,pro_addr3,pro_addr5,LEFT(pro_postcode,4) as pro_postcode,
	CONCAT(pro_addr3,' ',area.are_title,' ',LEFT(pro_postcode,4)) as pro_address,
	pro_east,pro_north,pro_latitude,pro_longitude,
	branch.bra_id,branch.bra_title,branch.bra_tel,branch.bra_fax,branch.bra_email,
	T.pty_title AS ptype,
	ST.pty_title AS psubtype,
	GROUP_CONCAT(DISTINCT CONCAT(feature.fea_title) ORDER BY feature.fea_id ASC SEPARATOR '~') AS features,
	GROUP_CONCAT(DISTINCT CONCAT(photos.med_file) ORDER BY photos.med_order ASC SEPARATOR '~') AS photos,
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

$column_headers = array(
	'category',
	'id',
	'title',
	'url',
	'address',
	'city',
	'country',
	'latitude',
	'longitude',
	'neighborhood',
	'zip_code',
	'agent',
	'agent_email',
	'agent_phone',
	'agent_url',
	'amenities',
	'bathrooms',
	'bedrooms',
	'create_time',
	'currency',
	'description',
	'furnished',
	'image_url',
	'ip_address',
	'price',
	'registration',
);

// delimeter
$d = ",";

foreach ($column_headers as $val) {
	$render .= trim($val) . "$d";
}
$render = remove_lastchar($render, ",") . "\n";

while ($row = $q->fetchRow()) {

	// sub sql to get features
	$sqlInner = "SELECT fea_id,fea_title FROM link_instruction_to_feature
	LEFT JOIN feature ON link_instruction_to_feature.featureId = feature.fea_id
	WHERE link_instruction_to_feature.dealId = " . $row["dea_id"] . " $where
	ORDER BY fea_weight DESC
	LIMIT 10";
	$qInner   = $db->query($sqlInner);
	while ($rowInner = $qInner->fetchRow()) {
		$features .= $rowInner["fea_title"] . "&#44; ";

		if ($rowInner["fea_id"] == 38) {
			$furnishedId = 'Furnished';
		} elseif ($rowInner["fea_id"] == 39) {
			$furnishedId = 'Partly furnished';
		} elseif ($rowInner["fea_id"] == 40) {
			$furnishedId = 'Unfurnished';
		} elseif ($rowInner["fea_id"] == 52) {
			$furnishedId = 'Furnished';
		}
	}

	// category
	if ($row["dea_ptype"] == 1) {
		$render .= "Homes";
	}
	elseif ($row["dea_ptype"] == 2) {
		$render .= "Flats";
	}
	elseif ($row["dea_ptype"] == 3) {
		$render .= "Commercial & Office Space";
	}

	if ($row["dea_type"] == 'Sales') {
		$render .= " for Sale$d";
	} else {
		$render .= " To Let$d";
	}

	// id
	$render .= $row["dea_id"] . "$d";
	// title
	$render .= str_replace(',', '&#44;', $row["dea_strapline"]) . "$d";
	// url
	$render .= "http://" . WS_HOSTNAME . "/Detail.php?id=" . $row["dea_id"] . "$d";
	// address
	$render .= trim($row["pro_address"]) . "$d";
	//city
	$render .= $row["pro_addr5"] . "$d";
	//country
	$render .= "GB$d";
	//latitude
	$render .= $row["pro_latitude"] . "$d";
	//longitude
	$render .= $row["pro_longitude"] . "$d";
	//neighborhood
	$render .= $row["are_title"] . "$d";
	//zip_code
	$render .= $row["pro_postcode"] . "$d";

	//agent
	$render .= "Wooster &amp; Stock$d";
	//agent_email
	$render .= $row["bra_email"] . "$d";
	//agent_phone
	$render .= $row["bra_tel"] . "$d";
	//agent_url
	$render .= "http://" . WS_HOSTNAME . "/$d";

	//amenities - features to follow
	$render .= remove_lastchar($features, "&#44;") . "$d";

	//bathrooms
	$render .= $row["dea_bathroom"] . "$d";
	//bedrooms
	$render .= $row["dea_bedroom"] . "$d";
	//create_time
	$render .= date('Y-m-d h:i:s') . "$d";
	//currency
	$render .= "GBP$d";
	//description
	$render .= str_replace(',', '&#44;', $row["dea_description"]) . "$d";

	//furnished
	$render .= $furnishedId . "$d";

	//image_url
	if ($row["photos"]) {
		$photo_array = explode("~", $row["photos"]);
	}
	foreach ($photo_array as $val) {
		//$render .= 'http://www.woosterstock.co.uk/v3/images/p/'.$row["dea_id"].'/'.str_replace(".jpg","_large.jpg",$val)."|";

		if (file_exists(WS_PATH_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_large.jpg", $val))) {
			$render .= WS_URL_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_large.jpg", $val) . "|";
		}
		else {
			$render .= WS_URL_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_small.jpg", $val) . "|";
		}
	}
	$render = remove_lastchar($render, "|") . $d;

	//ip_address
	$render .= $_SERVER['SERVER_ADDR'] . "$d";

	//price
	$render .= $row["dea_marketprice"] . "$d";

	//registration
	$render .= "no";

	$render .= "\n";

	unset($features, $furnishedId);
}

$strPath = dirname(__FILE__)  . '/oodle';
// name of textfile (date.blm)
$strTextFile = "oodle.csv";
$local_file  = $strPath . "/" . $strTextFile;

// if the file already exists, delet before re-writing
if (file_exists($local_file)) {
	unlink($local_file);
}

// write $render to file

if (!file_put_contents($local_file, $render)) {
	echo "could not write to file";
	exit;
}

