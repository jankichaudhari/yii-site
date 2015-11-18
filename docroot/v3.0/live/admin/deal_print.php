<?php
require_once("inx/global.inc.php");
// printer friendly deal details
if (!$_GET["dea_id"]) {
	die("no dea_id");
}

if ($_GET["dea_id"]) {
	$stage = $_GET["dea_id"];
} else {
	$stage = $_POST["dea_id"];
}

// start a new page
$page = new HTML_Page2($page_defaults);

$sql = "SELECT
	deal.*,DATE_FORMAT(deal.dea_available,'%D %M %Y') as date_available_formatted,
	media.*,
	area.are_title,
	pro_addr3,LEFT(pro_postcode, 4) AS pro_postcode,
	pro_east,pro_north,pro_latitude,pro_longitude,
	CONCAT(use_fname,' ',use_sname) AS use_name,
	branch.bra_id,branch.bra_title,
	T.pty_title AS ptype,
	ST.pty_title AS psubtype,
	GROUP_CONCAT(DISTINCT CONCAT(feature.fea_title) ORDER BY feature.fea_id ASC SEPARATOR '~') AS features
FROM
	deal

LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id
LEFT JOIN user ON deal.dea_neg = user.use_id

LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id

LEFT JOIN media ON deal.dea_id = media.med_row AND media.med_table = 'deal'

LEFT JOIN link_instruction_to_feature ON dealId = deal.dea_id
LEFT JOIN feature ON featureId = feature.fea_id

WHERE
deal.dea_id = $stage
GROUP BY med_id
ORDER BY media.med_order
";
//echo $sql;
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
if ($numRows == 0) {
	echo "select error<br>$sql";
	exit;
} else {
	while ($row = $q->fetchRow()) {

		/**
		 * @var    $dea_id
		 * @var    $pro_addr3
		 * @var    $dea_strapline
		 * @var    $dea_description
		 * @var    $dea_tenure
		 */
		foreach ($row as $key=> $val) {
			$$key = $val;
		}

		$title       = $pro_addr3;
		$strap       = $dea_strapline;
		$description = $dea_description;

		if ($dea_type == 'Sales') {
			$price = format_price($row["dea_marketprice"]) . ' ' . $dea_tenure;
		} elseif ($dea_type == 'Lettings') {
			$price = format_price($row["dea_marketprice"], 'GBP', '1') . ' per week / ' . format_price(pw2pcm($row["dea_marketprice"]), 'GBP', '1') . ' per month';
		}

		if ($row["med_type"] == 'Photograph') {
			$photos[$row["med_id"]] = array(
				'file' => $row["med_file"],
				'title'=> $row["med_title"]
			);
		} elseif ($row["med_type"] == 'Floorplan') {
			$floorplans[$row["med_id"]] = array(
				'file' => $row["med_file"],
				'title'=> $row["med_title"]
			);
			$total_area                 = ($total_area + $row["med_dims"]);

		} elseif ($row["med_type"] == 'EPC') {
			$epc[$row["med_id"]] = array(
				'file' => $row["med_file"],
				'title'=> $row["med_title"]
			);
		}

	}
}

if ($features) {
	$feature_array = explode("~", $features);
	foreach ($feature_array AS $val) {
		$feature_render .= '<li>' . $val . '</li>';
	}
}

// hides unproofed details from all except production and editor
if (!in_array('Production', $_SESSION["auth"]["roles"])) {
	if ($dea_status == 'Valuation' || $dea_status == 'Instructed' || $dea_status == 'Production' || $dea_status == 'Proofing') {
		$strap = 'Property details currently unavailable';
		$price = 'TBC';
		unset($description, $total_area, $photos, $floorplans);
	}
}

$render = '<div id="header">' . $logo . '
<h1>' . $title . '</h1>
<h3>' . $price . '</h3>
<h3>' . $are_title . ' ' . $pro_postcode . '</h3></div>
<h2>' . $strap . '</h2>

';

if ($photos) {
	$main_image = str_replace('.jpg', '_large.jpg', array_shift($photos));
	$render .= '<img src="' . IMAGE_URL_PROPERTY . $dea_id . '/' . $main_image["file"] . '" alt="' . $title . '" id="mainimage" /><br />' . "\n";
}
$render .= '<div class="description">
' . wordwrap($description, 150, "\n");

$render .= '
</div>';
if ($total_area) {
	$render .= '
<p>Approximate Gross Internal Area: ' . $total_area . ' square metres</p>
';
}
if ($dea_available && $dea_status == 'Available') {

	if (strtotime($dea_available) > 943920000) { // that is strtotime(0000-00-00 00:00:00)

		if (strtotime($dea_available) > strtotime($date_mysql)) {
			$feature_render .= '<li>Available: ' . $date_available_formatted . '</li>';
		} else {
			$feature_render .= '<li>Available Now</li>';
		}
	}
}
if ($feature_render) {
	$render .= '<div id="features"><ul>' . $feature_render . '</ul></div>';
}

if ($photos) {
	$counter = 1;
	foreach ($photos as $photo_title=> $photo_image) {
		$render .= '<img src="' . IMAGE_URL_PROPERTY . $dea_id . '/' . str_replace('.jpg', '_small.jpg', $photo_image["file"]) . '" alt="' . $photo_image["title"] . '" />' . "\n";
		if ($counter % 2 == 0) {
			$render .= '<br clear=all />';
		}
		$counter++;
	}
}
if ($floorplans) {

	$counter = 1;
	foreach ($floorplans as $floorplan_title=> $floorplan_image) {

		$render .= '<br clear=all class="break" />';

		// limit height to 950px
		$path = IMAGE_PATH_PROPERTY . $dea_id . '/' . $floorplan_image["file"];
		$dims = getimagesize($path);

//		if ($dims[1] > 950) {
//			$height = 950;
//		} else {
//			$height = $dims[1];
//		}

		$width =$dims[0];
		$height =$dims[1];
		$size = '';
		if($dim[0] > $dim[1]){	//horz
			if ($dims[0] > 700) {
				$width = 700;
				$size = ' width="' . $width . '" ';
			}
		} else if($dim[1] > $dim[0]){//VERT
			if ($dims[1] > 950) {
				$height = 950;
				$size = 'height="' . $height . '" ';
			}
		} else {
			if ($dims[0] > 700) {
				$width = 700;
				$size = ' width="' . $width . '" ';
			}
			if ($dims[1] > 950) {
				$height = 950;
				$size = 'height="' . $height . '" ';
			}
		}

		$render .= '<img src="' . IMAGE_URL_PROPERTY . $dea_id . '/' . $floorplan_image["file"] . '" ' . $size . '  alt="' . $floorplan_image["title"] . '" style="page-break-before:always;page-break-after:always" />' . "\n";
		$counter++;
		unset($dims, $height);
	}
}

if ($epc) {

	// make sure there is a break before epc
	//$render .= '<br clear=all class="break" />';

	foreach ($epc as $epc_title=> $epc_image) {
		$render .= '<br clear=all class="break" />';
		$render .= '<img src="' . IMAGE_URL_PROPERTY . $dea_id . '/' . str_replace('.gif', '_large.gif', $epc_image["file"]) . '" alt="' . $epc_image["title"] . '" />' . "\n";
		if ($counter % 2 == 0) {

		}
		$counter++;
		unset($dims, $height);
	}
}

$render .= '<br clear=all class="break" />';

$page->setTitle($title);
$page->addStyleSheet('css/print.css');
$page->setBodyAttributes(array('onLoad'=> 'self.focus();'));

$email_subject = $title . ' ' . str_replace('&', '%26', $are_title) . ' ' . $pro_postcode . ' - Wooster %26 Stock';
if ($dea_type == 'Sales') {
	$email_body = 'Dear CLIENT,%0D%0A%0D%0A' . $strap . '%0D%0A' . $title . ' ' . $postcode . '%0D%0A' .
			number_format($dea_marketprice) . ' (GBP)%0D%0Ahttp://www.woosterstock.co.uk/Detail.php?id=' . $dea_id .
			'%0D%0A%0D%0ARegards,%0D%0A%0D%0A' . $_SESSION["auth"]["use_fname"] . ' ' . $_SESSION["auth"]["use_sname"];

} elseif ($dea_type == 'Lettings') {
	$email_body = 'Dear CLIENT,%0D%0A%0D%0A' . $strap . '%0D%0A' . $title . ' ' . $postcode . '%0D%0A' .
			number_format($dea_marketprice) . ' per week (GBP)%0D%0Ahttp://www.woosterstock.co.uk/DetailLet.php?id=' . $dea_id .
			'%0D%0A%0D%0ARegards,%0D%0A%0D%0A' . $_SESSION["auth"]["use_fname"] . ' ' . $_SESSION["auth"]["use_sname"];

//	$email_body = "Dear CLIENT,\n\n" . $strap . "\n" . $title . ' ' . $postcode . "\n" .
//			number_format($dea_marketprice) . " per week (GBP)\nhttp://www.woosterstock.co.uk/DetailLet.php?id=" . $dea_id .
//			"\n\nRegards,\n\n" . $_SESSION["auth"]["use_fname"] . ' ' . $_SESSION["auth"]["use_sname"];
}
$email_body = str_replace('&amp;', '%26', $email_body);
if ($dea_status == 'Available' || $dea_status == 'Under Offer' || $dea_status == 'Under Offer with Other' || $dea_status == 'Exchanged') {
	$mailto = '<a href="mailto:?subject=' . $email_subject . '&body=' . $email_body . '"><img src="/images/sys/admin/icons/mail-icon.png">Email</a>';
}
$page->addBodyContent('
<div id="hidePrint">
	<a href="#" onclick="window.print();"><img src="/images/sys/admin/icons/print-icon.png">Print</a>
	' . $mailto . '
	<a href="#" onclick="window.close();"><img src="/images/sys/admin/icons/cross-icon.png">Close</a>
</div>');

$page->addBodyContent('<div id="property">');
$page->addBodyContent($render);

if ($pro_latitude && $pro_longitude) {
	$str = '
	<p style="page-break-before: always;"></p>
	<div style="width:600px; margin: 0 auto;">
	<img src="https://maps.googleapis.com/maps/api/staticmap?center=' . $pro_latitude . ',' . $pro_longitude . '&zoom=15&markers=color:blue|' . $pro_latitude . ',' . $pro_longitude . '&size=600x600&sensor=false" alt="maps" style="width:600px; height:600px">
	</div>

	';
	$page->addBodyContent($str);
}

$page->addBodyContent('</div>');
$page->display();
?>