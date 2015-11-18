<?php
define("FEED_NAME", "trovit");
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

// header
$render = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
$render .= '<trovit>' . "\n";

while ($row = $q->fetchRow()) {

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

	$render .= '<ad>' . "\n";

	$render .= '<id><![CDATA[' . $row["dea_id"] . ']]></id>' . "\n";
	$render .= '<url><![CDATA[http://' . WS_HOSTNAME . '/Detail.php?id=' . $row["dea_id"] . ']]></url>' . "\n";
	$render .= '<title><![CDATA[' . $row["pro_address"] . ']]></title>' . "\n";

	if ($row["dea_type"] == 'Sales') {
		$render .= '<type><![CDATA[For sale]]></type>' . "\n";
	} else {
		$render .= '<type><![CDATA[For rent]]></type>' . "\n";
	}

	$render .= '<agency><![CDATA[Wooster &amp; Stock]]></agency>' . "\n";
	$render .= '<content><![CDATA[<h1>' . $row["dea_strapline"] . '</h1>' . $description . ']]></content>' . "\n";
	$render .= '<price><![CDATA[' . $row["dea_marketprice"] . ']]></price>' . "\n";

	if ($row["dea_ptype"] == 1) {
		$render .= '<property_type><![CDATA[House]]></property_type>' . "\n";
	}
	elseif ($row["dea_ptype"] == 2) {
		$render .= '<property_type><![CDATA[Flat]]></property_type>' . "\n";
	}
	elseif ($row["dea_ptype"] == 3) {
		$render .= '<property_type><![CDATA[Commercial]]></property_type>' . "\n";
	}

	$render .= '<rooms><![CDATA[' . $row["dea_bedroom"] . ']]></rooms>' . "\n";
	$render .= '<bathrooms><![CDATA[' . $row["dea_bathroom"] . ']]></bathrooms>' . "\n";
	$render .= '<address><![CDATA[' . $row["pro_address"] . ']]></address>' . "\n";
	$render .= '<city><![CDATA[' . $row["pro_addr5"] . ']]></city>' . "\n";
	$render .= '<postcode><![CDATA[' . $row["pro_postcode"] . ']]></postcode>' . "\n";
	$render .= '<region><![CDATA[' . $row["are_title"] . ']]></region>' . "\n";
	$render .= '<latitude><![CDATA[' . $row["pro_latitude"] . ']]></latitude>' . "\n";
	$render .= '<longitude><![CDATA[' . $row["pro_longitude"] . ']]></longitude>' . "\n";

	// images
	$render .= '<pictures>' . "\n";
	if ($row["photos"]) {
		$photo_array = explode("~", $row["photos"]);
	}
	foreach ($photo_array as $val) {

		$data  = explode("|", $val);
		$file  = $data[0];
		$title = $data[1];

		if (file_exists(WS_PATH_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_large.jpg", $file))) {
			$photourl .= WS_URL_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_large.jpg", $file);
		}
		else {
			$photourl .= WS_URL_IMAGES . '/' . $row["dea_id"] . '/' . str_replace(".jpg", "_small.jpg", $file);
		}

		$render .= '<picture>' . "\n";
		$render .= '<picture_url><![CDATA[' . $photourl . ']]></picture_url>' . "\n";
		$render .= '<picture_title><![CDATA[' . $title . ']]></picture_title>' . "\n";
		$render .= '</picture>' . "\n";

		unset($photourl, $data, $file, $title);
	}
	$render .= '</pictures>' . "\n";

	$render .= '<date><![CDATA[' . date('d/m/Y', strtotime($row["dea_launchdate"])) . ']]></date>' . "\n";

	$render .= '</ad>' . "\n";

	unset($description);

}

$render .= '</trovit>';

$strPath = WS_PATH_DOC_ROOT . '/feed/trovit';
createForlder($strPath);
// name of textfile (date.blm)
$strTextFile = "trovit.xml";
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
