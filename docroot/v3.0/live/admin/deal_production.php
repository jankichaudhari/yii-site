<?php
require_once("inx/global.inc.php");

/*
Deal Production

Particulars (beds, recs, garden, parking, etc)
Descriptions (strap line, full description, summary - shows helper items like nearest stations, similar deals)
Features (checkboxes: period, new build, river views etc)
Photos (unlimited photos, list view with delete and re-order option)
 - photos are to be names with follwoing convention: dea_id + street_name + pc1 + image_title + unique string for google images rankings

Floorplans (unlimited floorplans, list view with delete and re-order option)
Portals (send or do not send to each portal that accepts feeds)


NOTE: property particulars are stored in the property table as it is assumed they will never change, or if they do
it will be a permanent change that will effect all deals. Possible problem arises if the property is changed (e.g.
and extra bedroom is added) it will effect all deals on that property (historically), so previous deals will be
rendered inacurate. One possible solution is to move the particulars into the deal table, but in most cases it is more
useful to have them in the property table as they will rarely change. As a workaround, we could make the particulars
readonly if there is another deal associated with it. Or maybe store an array of particualrs in the deal table to
backward compatibility? Undecided (17/10/06)

UPDATE (30/10/06): store property particulars in deal table, but refer to them when creating another deal on that same
property (during valuation_add etc) and allow them to be copied from most recent deal into new deal. This means we do
not store particulars in the property table at all.
changes required to deal and property tables, add valuation procedure, and deal summary pages


*/

if ($_GET["do"] == "delete_image") {
	if (!$_GET["med_id"]) {
		echo "no media file selected";
		exit;
	}
	if (!$_GET["med_type"]) {
		echo "no media type selected";
		exit;
	}

	if ($_GET["med_id"]) {
		$med_id = $_GET["med_id"];
	} else {
		$med_id = $_POST["med_id"];
	}

	$sql = "SELECT med_file FROM media WHERE med_id = " . $med_id;
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	while ($row = $q->fetchRow()) {
		$med_file = $row["med_file"];
	}

	foreach ($thumbnail_sizes as $dims=> $ext) {
		@unlink(IMAGE_PATH_PROPERTY . $_GET["dea_id"] . '/' . str_replace('.jpg', '_' . $ext . '.jpg', $med_file));
	}

	$sql = "DELETE FROM media WHERE med_id = $med_id";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}

	// now reorder other images
	// re-number the order of all remaining deals in this appointment
	$sql = "SELECT med_id FROM media WHERE
	med_table = 'deal' AND
	med_row = " . $_GET["dea_id"] . " AND
	med_type = '" . $_GET["med_type"] . "'
	ORDER BY med_order ASC";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$count = 1;
	while ($row = $q->fetchRow()) {
		$sql2 = "UPDATE media SET med_order = $count WHERE med_id = " . $row["med_id"];
		$q2   = $db->query($sql2);
		$count++;
	}

	if ($_GET["med_type"] == "Photograph") {
		$viewForm = 3;
	} elseif ($_GET["med_type"] == "Floorplan") {
		$viewForm = 4;
	} elseif ($_GET["med_type"] == "EPC") {
		$viewForm = 7;
	}
	header("Location:" . replaceQueryString(urldecode($_GET["return"]), 'viewForm') . '&viewForm=' . $viewForm);
	exit;
} // reorder images
elseif ($_GET["do"] == "reorder") {

	$this_med_id = $_GET["med_id"];
	$cur         = $_GET["cur"]; // current position
	$new         = $_GET["new"]; // new position (position to move the deal to, we need to update this position with the postiion it replaces)

	// get id of deal in position we want to move our deal to
	$sql = "SELECT med_id,med_order,med_type FROM media WHERE med_table = 'deal' AND med_row = " . $_GET["dea_id"] . " AND	med_type = '" . $_GET["med_type"] . "' AND med_order = $new";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	while ($row = $q->fetchRow()) {
		$other_med_id    = $row["med_id"];
		$other_med_order = $row["med_order"];
	}

	// update this row with new position
	$db_data["med_order"] = $new;
	db_query($db_data, "UPDATE", "media", "med_id", $this_med_id);
	unset($db_data);

	// update other row with new position
	$db_data["med_order"] = $cur;
	db_query($db_data, "UPDATE", "media", "med_id", $other_med_id);
	unset($db_data);

	if ($_GET["med_type"] == "Photograph") {
		$viewForm = 3;
	} elseif ($_GET["med_type"] == "Floorplan") {
		$viewForm = 4;
	} elseif ($_GET["med_type"] == "EPC") {
		$viewForm = 7;
	}
	header("Location:" . replaceQueryString(urldecode($_GET["return"]), 'viewForm') . '&viewForm=' . $viewForm);
} // reorder images automatically in accordance with the ordered list of available titles
elseif ($_GET["do"] == "reorder_automatic") {

	if ($_GET["med_type"] == "Photograph") {
		$comparison = $photograph_titles;
		$viewForm   = 3;
	} elseif ($_GET["med_type"] == "Floorplan") {
		$comparison = $floorplan_titles;
		$viewForm   = 4;
	}

	// comparison arrays are currently associative with the same keys and vals, so we need to get them numbered
	foreach ($comparison AS $key=> $val) {
		$comparison_numbered[] = $val;
	}

	//print_r($comparison_numbered);
	$new_order = array();

	$sql = "SELECT med_id,med_title FROM media WHERE
	med_table = 'deal' AND
	med_row = " . $_GET["dea_id"] . " AND
	med_type = '" . $_GET["med_type"] . "'
	ORDER BY med_order ASC";

	$q = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$count      = 1;
	$duplicates = 1;
	while ($row = $q->fetchRow()) {

		if (in_array($row["med_title"], $comparison_numbered)) {
			$comparison_key = array_search($row["med_title"], $comparison_numbered);
			if (array_key_exists($comparison_key, $new_order)) {
				$duplicates++;
				// detect duplicate keys (two rooms with same title)
				$new_order[($comparison_key + $duplicates)] = array(
					'id'   => $row["med_id"],
					'title'=> $row["med_title"]
				);
			} else {
				$new_order[$comparison_key] = array(
					'id'   => $row["med_id"],
					'title'=> $row["med_title"]
				);
			}
		}

	}
	ksort($new_order);
	//print_r($new_order);
	//echo "<hr>";

	$count = 1;
	foreach ($new_order as $key=> $val) {
		$sql = "UPDATE media SET med_order = $count WHERE med_id = " . $val["id"];
		$q   = $db->query($sql);
		$count++;
	}

	//exit;
	header("Location:" . replaceQueryString(urldecode($_GET["return"]), 'viewForm') . '&viewForm=' . $viewForm);
} // adding a feature. must check if feature is already added and update as necesary
elseif ($_GET["do"] == "feature_add") {
	$dea_id  = $_GET["dea_id"];
	$featureId = $_GET["featureId"];
	$sql     = "DELETE FROM link_instruction_to_feature WHERE dealId = $dea_id AND featureId = $featureId";
	$q       = $db->query($sql);

	$db_data["dealId"] = $dea_id;
	$db_data["featureId"] = $featureId;
	db_query($db_data, "INSERT", "link_instruction_to_feature", "f2d_id");
	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
} // adding a feature. must check if feature is already added and update as necesary
elseif ($_GET["do"] == "feature_remove") {
	$dea_id  = $_GET["dea_id"];
	$featureId = $_GET["featureId"];
	$sql     = "DELETE FROM link_instruction_to_feature WHERE dealId = $dea_id AND featureId = $featureId";
	$q       = $db->query($sql);

	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
}

if ($_GET["stage"]) {
	$stage = $_GET["stage"];
} elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
} else {
	$stage = 1;
}

if ($_GET["dea_id"]) {
	$stage = $_GET["dea_id"];
} elseif ($_POST["dea_id"]) {
	$stage = $_POST["dea_id"];
} else {
	echo "error - no dea_id";
	exit;
}
if ($_GET["dea_id"]) {
	$dea_id = $_GET["dea_id"];
} else {
	$dea_id = $_POST["dea_id"];
}

if ($_POST["viewForm"]) {
	$viewForm = $_POST["viewForm"];
} elseif ($_GET["viewForm"]) {
	$viewForm = $_GET["viewForm"];
} else {
	$viewForm = "1";
}

// start a new page
$page = new HTML_Page2($page_defaults);

// improvement 06/10/06 - link table for multiple vendors on a single deal
// additions for production are: linked(garden,parking), link to media table for p&fp
$sql = "SELECT
	deal.*,
	video.videoId as video_id,
	property.pro_id,property.pro_addr3,property.pro_postcode,property.pro_area,
	CONCAT(property.pro_addr1,' ',property.pro_addr2,' ',property.pro_addr3,' ',property.pro_addr4,' ',property.pro_postcode) AS pro_addr,
	cli_id,CONCAT(cli_fname,' ',cli_sname) AS cli_name,
	CONCAT(use_fname,' ',use_sname) AS use_name,
	link_client_to_instruction.*,
	branch.bra_id,branch.bra_title,
	T.pty_title AS ptype,
	ST.pty_title AS psubtype,
	GROUP_CONCAT(feature.fea_id SEPARATOR '|') AS features,feature.*,
	area.are_title

FROM
	deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id

LEFT JOIN property ON deal.dea_prop = property.pro_id

LEFT JOIN branch ON deal.dea_branch = branch.bra_id
LEFT JOIN user ON deal.dea_neg = user.use_id

LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id

LEFT JOIN link_instruction_to_feature ON deal.dea_id = link_instruction_to_feature.dealId
LEFT JOIN feature ON link_instruction_to_feature.featureId = feature.fea_id

LEFT JOIN area ON property.pro_area = area.are_id
LEFT JOIN instructionVideo video ON deal.dea_id = video.instructionId

WHERE
deal.dea_id = $dea_id
GROUP BY deal.dea_id
";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage() . $sql);
}
$numRows = $q->numRows();
if ($numRows == 0) {
	echo "select error";
	exit;
} else {
	while ($row = $q->fetchRow()) {

		$vendors[$row["cli_id"]] = $row["cli_name"];
		foreach ($row as $key=> $val) {
			$$key = $val;
			// this render is used for debug only
			// $render .= $key."->".$val."<br>";
		}
	}
}

// get matching areas
$pc1           = explode(" ", $pro_postcode);
$pc1           = $pc1[0];
$matched_areas = array();
$sql           = "SELECT are_id, are_title, are_postcode FROM area WHERE are_postcode = '$pc1'";
$q             = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$matched_areas[$row["are_title"]] = $row["are_id"];
	if ($numRows == 1) {
		$default_area = $row["are_title"];
	}
}
if ($are_title) {
	$default_area = $are_title;
}

if ($matched_areas) {
	$formDataArea = array(
		'pro_id'     => array(
			'type' => 'hidden',
			'value'=> $pro_id
		),
		'pro_area'   => array(
			'type'   => 'radio',
			'label'  => 'Area',
			'value'  => $are_title,
			'options'=> $matched_areas
		),
		'pro_areanew'=> array(
			'type'      => 'button',
			'label'     => 'New Area',
			'value'     => 'New Area',
			'attributes'=> array(
				'class'  => 'button',
				'onClick'=> 'javascript:addArea(\'' . $pc1 . '\',\'' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '\')'
			)
		)
	);
} else {
	$formDataArea = array(
		'pro_id'     => array(
			'type' => 'hidden',
			'value'=> $pro_id
		),
		'pro_areanew'=> array(
			'type'      => 'button',
			'label'     => 'New Area',
			'value'     => 'New Area',
			'attributes'=> array(
				'class'  => 'button',
				'onClick'=> 'javascript:addArea(\'' . $pc1 . '\',\'' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '\')'
			)
		)
	);
}

// features
$sql = "SELECT * FROM feature ORDER BY fea_title";
$q   = $db->query($sql);

$feature_columns         = 3;
$table_width             = 650;
$cell_width              = round($table_width / $feature_columns);
$render_feature_External = '<table cellspacing="0" cellpadding="1"><tr><th colspan="' . $feature_columns . '">External</th></tr><tr>';
$render_feature_Internal = '<table cellspacing="0" cellpadding="1"><tr><th colspan="' . $feature_columns . '">Internal</th></tr><tr>';
$render_feature_Locality = '<table cellspacing="0" cellpadding="1"><tr><th colspan="' . $feature_columns . '">Locality</th></tr><tr>';
$render_feature_Lettings = '<table cellspacing="0" cellpadding="1"><tr><th colspan="' . $feature_columns . '">Lettings</th></tr><tr>';
$countExternal           = 1;
$countInternal           = 1;
$countLocality           = 1;
$countLettings           = 1;
$current_features        = explode("|", $features);

while ($row = $q->fetchRow()) {

	$all_features[$row["fea_id"]] = $row["fea_title"];

	// display features in groups
	if ($row["fea_type"] == 'External') {

		$render_feature_External .= '<td width="' . $cell_width . '"><label for="feature' . $row["fea_id"] . '"><input type="checkbox" name="feature_id[]" id="feature' . $row["fea_id"] . '" value="' . $row["fea_id"] . '"';

		if (in_array($row["fea_id"], $current_features)) {
			$render_feature_External .= ' checked';
		}
		$render_feature_External .= ' />' . $row["fea_title"] . '</label></td>' . "\n";
		if ($countExternal % $feature_columns == 0) {
			$render_feature_External .= '</tr><tr>';
		}
		$countExternal++;

	} elseif ($row["fea_type"] == 'Internal') {

		$render_feature_Internal .= '<td width="' . $cell_width . '"><label for="feature' . $row["fea_id"] . '"><input type="checkbox" name="feature_id[]" id="feature' . $row["fea_id"] . '" value="' . $row["fea_id"] . '"';

		if (in_array($row["fea_id"], $current_features)) {
			$render_feature_Internal .= ' checked';
		}
		$render_feature_Internal .= ' />' . $row["fea_title"] . '</label></td>' . "\n";
		if ($countInternal % $feature_columns == 0) {
			$render_feature_Internal .= '</tr><tr>';
		}
		$countInternal++;

	} elseif ($row["fea_type"] == 'Locality') {

		$render_feature_Locality .= '<td width="' . $cell_width . '"><label for="feature' . $row["fea_id"] . '"><input type="checkbox" name="feature_id[]" id="feature' . $row["fea_id"] . '" value="' . $row["fea_id"] . '"';

		if (in_array($row["fea_id"], $current_features)) {
			$render_feature_Locality .= ' checked';
		}
		$render_feature_Locality .= ' />' . $row["fea_title"] . '</label></td>' . "\n";
		if ($countLocality % $feature_columns == 0) {
			$render_feature_Locality .= '</tr><tr>';
		}
		$countLocality++;

	} elseif ($row["fea_type"] == 'Lettings') {

		$render_feature_Lettings .= '<td width="' . $cell_width . '"><label for="feature' . $row["fea_id"] . '"><input type="checkbox" name="feature_id[]" id="feature' . $row["fea_id"] . '" value="' . $row["fea_id"] . '"';

		if (in_array($row["fea_id"], $current_features)) {
			$render_feature_Lettings .= ' checked';
		}
		$render_feature_Lettings .= ' />' . $row["fea_title"] . '</label></td>' . "\n";
		if ($countLettings % $feature_columns == 0) {
			$render_feature_Lettings .= '</tr><tr>';
		}
		$countLettings++;

	}

}
$render_feature_External .= '</tr></table>';
$render_feature_Internal .= '</tr></table>';
$render_feature_Locality .= '</tr></table>';
$render_feature_Lettings .= '</tr></table>';

$image_path_property = IMAGE_PATH_PROPERTY . $dea_id . "/";
$image_url_property  = IMAGE_URL_PROPERTY . $dea_id . "/";
// make image folder specific to this deal
if (!is_dir($image_path_property)) {
	if (!mkdir($image_path_property, 0755)) {
		echo "error creating folder";
		exit;
	}
}
// make custom filename
$custom_pro_addr3 = str_replace(" ", "_", $pro_addr3);
$custom_pro_addr3 = characters_only($custom_pro_addr3);
#$custom_pro_addr3 = str_replace("'","",$custom_pro_addr3);
$custom_pro_postcode = explode(" ", $pro_postcode);
$custom_pro_postcode = $custom_pro_postcode[0];
$custom_filename     = $custom_pro_addr3 . "_" . $custom_pro_postcode . "_";
#dea_id + street_name + pc1 + image_title + unique string

foreach ($_GET as $key=> $val) {
	$$key = $val;
}

$photos = '
			<label for="managePhotosButton" class="formLabel"></label>
			<input type="button" value="Manage Photos" id="managePhotosButton">
			<br>
			<label for="video id" class="formLabel">Video id</label>
			<input type="text" placeholder="video id" name="video_id" id="video id" value="' . $video_id . '"><br>
			<input type="submit" name="form1" value="Save changes" class="submit">';

$last_order_photo = $photoCount;
unset($counter);

$sql = "SELECT
*,
DATE_FORMAT(med_created, '%d/%m/%y') AS med_created FROM media WHERE
med_table = 'deal' AND
med_row = '$dea_id' AND
med_type = 'Floorplan'
ORDER BY med_order ASC
";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}

$floorplanCount = $q->numRows();
if ($floorplanCount <> 0) {
	$counter = 1;
	while ($row = $q->fetchRow()) {

		$floorplans .= '
		<td width="100" align="right"><a href="media_edit.php?dea_id=' . $_GET["dea_id"] . '&med_id=' . $row["med_id"] . '&med_type=' . $row["med_type"] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '"><img src="' . $image_url_property . str_replace('.gif', '_s.gif', $row["med_file"]) . '" alt="Floorplan ' . $row["med_order"] . '"></a></td>
		<td>
		' . $row["med_title"] . '<br>
		' . $row["med_dims"] . 'm&sup2;<br>
		' . $row["med_created"] . '<br><br>
		';
		// disable first arrow
		if ($row["med_order"] == 1) {
			$floorplans .= '<img src="/images/sys/admin/icons/arrow_up_sm_grey.gif" border="0" alt="Move Up" height="16" width="16">';
		} else {
			$floorplans .= '<a href="?do=reorder&dea_id=' . $dea_id . '&med_id=' . $row["med_id"] . '&med_type=' . $row["med_type"] . '&cur=' . $row["med_order"] . '&new=' . ($row["med_order"] - 1) . '&return=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/arrow_up_sm.gif" border="0" alt="Move Up" height="16" width="16"></a>';
		}
		// disable last arrow
		if ($counter == $floorplanCount) {
			$floorplans .= '<img src="/images/sys/admin/icons/arrow_down_sm_grey.gif" border="0" alt="Move Down" height="16" width="16">';
		} else {
			$floorplans .= '<a href="?do=reorder&dea_id=' . $dea_id . '&med_id=' . $row["med_id"] . '&med_type=' . $row["med_type"] . '&cur=' . $row["med_order"] . '&new=' . ($row["med_order"] + 1) . '&return=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/arrow_down_sm.gif" border="0" alt="Move Down" height="16" width="16"></a>';
		}
		$floorplans .= '
		<a href="?do=delete_image&dea_id=' . $_GET["dea_id"] . '&med_id=' . $row["med_id"] . '&med_type=' . $row["med_type"] . '&return=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '" onMouseover="showhint(\'Delete this Floorplan\', this, event, \'\')"><img src="/images/sys/admin/icons/cross-icon.png" border="0" alt="Delete" height="16" width="16"></a><div id="tooltip"></div>
		</td>';

		if ($counter % 3 == 0) {
			$floorplans .= '</tr>
		<tr width="33%">';
		}
		$counter++;
	}
	$floorplans = '<table width="90%" align="center"><tr width="33%">' . $floorplans . '</tr></table>';
	if ($floorplanCount > 1) {
		$floorplan_order_auto = '<p style="margin:0px;padding:0px;margin-left:40px;"><a href="?do=reorder_automatic&dea_id=' . $dea_id . '&med_type=Floorplan&return=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '">Click here to automatically put the floorplans in recommended order</a></p>';
	}
}

unset($counter);

$sql = "SELECT
*,
DATE_FORMAT(med_created, '%d/%m/%y') AS med_created FROM media WHERE
med_table = 'deal' AND
med_row = '$dea_id' AND
med_type = 'EPC'
ORDER BY med_order ASC
";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$last_order_pec = $photoCount;
$epcCount       = $q->numRows();
if ($epcCount <> 0) {
	$counter = 1;
	while ($row = $q->fetchRow()) {

		$epc .= '
		<td width="100" align="right"><img src="' . $image_url_property . str_replace('.gif', '_thumb1.gif', $row["med_file"]) . '" alt="EPC ' . $row["med_order"] . '"></td>
		<td>
		' . $row["med_title"] . '<br>
		' . $row["med_created"] . '<br><br>
		';
		// disable first arrow
		if ($row["med_order"] == 1) {
			$epc .= '<img src="/images/sys/admin/icons/arrow_up_sm_grey.gif" border="0" alt="Move Up" height="16" width="16">';
		} else {
			$epc .= '<a href="?do=reorder&dea_id=' . $dea_id . '&med_id=' . $row["med_id"] . '&med_type=' . $row["med_type"] . '&cur=' . $row["med_order"] . '&new=' . ($row["med_order"] - 1) . '&return=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/arrow_up_sm.gif" border="0" alt="Move Up" height="16" width="16"></a>';
		}
		// disable last arrow
		if ($counter == $epcCount) {
			$epc .= '<img src="/images/sys/admin/icons/arrow_down_sm_grey.gif" border="0" alt="Move Down" height="16" width="16">';
		} else {
			$epc .= '<a href="?do=reorder&dea_id=' . $dea_id . '&med_id=' . $row["med_id"] . '&med_type=' . $row["med_type"] . '&cur=' . $row["med_order"] . '&new=' . ($row["med_order"] + 1) . '&return=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/arrow_down_sm.gif" border="0" alt="Move Down" height="16" width="16"></a>';
		}
		$epc .= '
		<a href="?do=delete_image&dea_id=' . $_GET["dea_id"] . '&med_id=' . $row["med_id"] . '&med_type=' . $row["med_type"] . '&return=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '" onMouseover="showhint(\'Delete this EPC\', this, event, \'\')"><img src="/images/sys/admin/icons/cross-icon.png" border="0" alt="Delete" height="16" width="16"></a><div id="tooltip"></div>
		</td>';

		if ($counter % 3 == 0) {
			$epc .= '</tr>
		<tr width="33%">';
		}
		$counter++;
	}
	$epc = '<table width="90%" align="center"><tr width="33%">' . $epc . '</tr></table>';

}

// summary screen
if (!$dea_marketprice) {
	$price = format_price($dea_valueprice) . ' (Valuation)';
} else {
	if ($dea_qualifier !== 'None') {
		$qual = ' (' . $dea_qualifier . ')';
	}
	$price = format_price($dea_marketprice) . ' ' . $dea_tenure . $qual;
}

$summary_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" width="160">Address</td>
	<td>' . $pro_addr . '</td>
  </tr>
  <tr>
    <td class="label">Price</td>
	<td>' . $price . '</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td>
	<input type="button" class="button" value="Print Details" onClick="javascript:openPrintWindow(\'' . $dea_id . '\');">
	</td>
  </tr>
</table>
';

// mailshot history
// get past mailshots for this deal - to prevent duplicates, resitrict to one per day
$sql = "SELECT mailshot.*,CONCAT(use_fname,' ',use_sname) AS use_name,
DATE_FORMAT(mai_date, '%d/%m/%y') AS date,COUNT(hit.hit_id) AS hits
FROM mailshot
LEFT JOIN hit ON mai_id = hit.hit_mailshot
LEFT JOIN user ON mai_user = user.use_id
WHERE mai_deal = $dea_id
GROUP BY mai_date
ORDER BY mai_date DESC";
//echo $sql; exit;
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {

	$render_mailshot .= '
	<tr>
	  <td>' . $row["date"] . '</td>
	  <td>' . $mailshot_types[$row["mai_type"]] . '</td>
	  <td>' . $row["mai_status"] . '</td>
	  <td>' . $row["mai_count"] . '</td>
	  <td>' . $row["hits"] . '</td>
	  <td>' . $row["use_name"] . '</td>
	</tr>
	';

}
$mailshot_history = '
<table id="detailTable" cellpadding="2" cellspacing="2" border="0" width="95%" align="center">
  <tr>
    <th>Date</th>
	<th>Type</th>
	<th>Status</th>
	<th>Sent</th>
	<th>Hits</th>
	<th>User</th>
  </tr>
' . $render_mailshot . '
</table>
';

// form1 - property particulars
$ptype     = ptype2($dea_ptype, $dea_psubtype);
$formData1 = array(
	'dea_ptype'    => array(
		'type'    => 'select_multi',
		'label'   => 'Property Type',
		'required'=> 2,
		'options' => array(
			'dd1'=> $ptype['dd1'],
			'dd2'=> $ptype['dd2']
		)
	),
	'dea_bedroom'  => array(
		'type'      => 'select_number',
		'label'     => 'Bedrooms',
		'value'     => $dea_bedroom,
		'attributes'=> array('class'=> 'narrow')
	),
	'dea_reception'=> array(
		'type'      => 'select_number',
		'label'     => 'Receptions',
		'value'     => $dea_reception,
		'attributes'=> array('class'=> 'narrow')
	),
	'dea_bathroom' => array(
		'type'      => 'select_number',
		'label'     => 'Bathrooms',
		'value'     => $dea_bathroom,
		'attributes'=> array('class'=> 'narrow')
	),
	'dea_floor'    => array(
		'type'      => 'select',
		'label'     => 'Floor',
		'value'     => $dea_floor,
		'options'   => db_enum("deal", "dea_floor", "array"),
		'attributes'=> array('class'=> 'medium'),
		'tooltip'   => '<i>Floor</i> only applies to apartments. For houses, leave as NA'
	),
	'dea_floors'   => array(
		'type'      => 'select_number',
		'label'     => 'Floors',
		'options'   => array('min'=> '1'),
		'value'     => $dea_floors,
		'attributes'=> array('class'=> 'narrow')
	)
);

// additional superadmin fields
if (in_array('SuperAdmin', $_SESSION["auth"]["roles"])) {
	$formData1['dea_launchdate'] = array(
		'type'   => 'text',
		'label'  => 'Launch Date',
		'value'  => $dea_launchdate,
		'tooltip'=> 'This controls the order in which properties are displayed on the site.'
	);
	$formData1['dea_keywords']   = array(
		'type'      => 'text',
		'label'     => 'Keywords',
		'value'     => $dea_keywords,
		'required'  => 1,
		'attributes'=> array('style'=> 'width:450px')
	);
}

// form 2 - descriptions
$formData2 = array(
	'dea_strapline'  => array(
		'type'      => 'text',
		'label'     => 'Strap Line',
		'value'     => $dea_strapline,
		'required'  => 2,
		'attributes'=> array('style'=> 'width:450px'),
		'function'  => 'format_strap'
	),
	'dea_description'=> array(
		'type'      => 'htmlarea2',
		'label'     => 'Full Description',
		'value'     => $dea_description,
		'required'  => 1,
		'attributes'=> array(
			'width' => '450px',
			'height'=> '350px'
		),
		'function'  => 'format_description'
	)
);

#$htmledit = new FCKeditor('dea_description',$dea_description);

// form 3 - add new image(s)
$formData3 = array(
	'med_file' => array(
		'type'      => 'file',
		'label'     => 'Image',
		'required'  => 3,
		'attributes'=> array('style'=> 'width:320px'),
		'tooltip'   => 'Only JPG files are allowed'
	),
	'med_title'=> array(
		'type'      => 'select',
		'label'     => 'Title',
		'required'  => 2,
		'attributes'=> array('style'=> 'width:320px'),
		'options'   => $photograph_titles
	),
	'med_blurb'=> array(
		'type'      => 'textarea',
		'label'     => 'Description',
		'attributes'=> array('style'=> 'width:320px')
	)
);

// form 4 - floorplan(s)
$formData4 = array(
	'med_file_fp'       => array(
		'type'      => 'file',
		'label'     => 'Floorplan',
		'required'  => 3,
		'attributes'=> array('style'=> 'width:320px'),
		'tooltip'   => 'Only GIF files are allowed'
	),
	'med_title_fp'      => array(
		'type'      => 'select',
		'label'     => 'Title',
		'required'  => 2,
		'attributes'=> array('style'=> 'width:320px'),
		'options'   => $floorplan_titles
	),
	'med_dims_fp'       => array(
		'type'      => 'text',
		'label'     => 'Area',
		'required'  => 1,
		'attributes'=> array('style'=> 'width:120px'),
		'group'     => 'Area'
	),
	'med_measurement_fp'=> array(
		'type'         => 'radio',
		'label'        => 'Area',
		'default'      => 'mtr&sup2;',
		'required'     => 1,
		'options'      => array(
			'mtr&sup2;'=> 'metres',
			'ft&sup2;' => 'feet'
		),
		'group'        => 'Area',
		'last_in_group'=> 1
	)
);

// form 7 - epc
$formData7 = array(
	'med_file_other' => array(
		'type'      => 'file',
		'label'     => 'Document',
		'required'  => 3,
		'attributes'=> array('style'=> 'width:320px'),
		'tooltip'   => 'Only JPG files are allowed'
	),
	'med_title_other'=> array(
		'type'    => 'hidden',
		'label'   => 'Type',
		'required'=> 2,
		'value'   => 'EPC'
	)
);

if (!$_POST["action"]) {

	$form = new Form();

	$form->addForm("testForm", "POST", $PHP_SELF, "multipart/form-data");
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "stage", "", "1");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "dea_id", "", $dea_id);
	$form->addField("hidden", "pro_id", "", $pro_id);
	$form->addField("hidden", "last_order_photo", "", $photoCount);
	$form->addField("hidden", "last_order_floorplan", "", $floorplanCount);
	$form->addField("hidden", "last_order_epc", "", $epcCount);
	$form->addField("hidden", "searchLink", "", urlencode($searchLink));

	$form->addHtml('<h1>' . $pro_addr . ' (' . $dea_type . ')</h1>');

	$formName = 'form1';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Particulars</div>');
	$form->addHtml('<div id="' . $formName . '">');
	if (!$pro_area) {
		$form->addData($formDataArea, $_POST);
	}
	$form->addData($formData1, $_POST);
	$form->addHtml($form->addRow('textarea', 'dea_notes_production', 'Add Production Note', '', array('class'=> 'noteInput'), '', ''));
	$form->addHtml(renderNotes('deal_production', $dea_id, array(
																'viewform'=> '1',
																'label'   => 'Production Notes'
														   )));
	$buttons = $form->makeField("submit", $formName, "", "Save Changes", array('class'=> 'submit'));
	$buttons .= $form->makeField("button", "", "", "Summary", array(
																   'class'  => 'button',
																   'onClick'=> 'document.location.href=\'deal_summary.php?dea_id=' . $dea_id . '\''
															  ));
	$buttons .= $form->makeField("button", "", "", "Copy", array(
																'class'  => 'button',
																'onClick'=> 'document.location.href=\'deal_copy.php?dea_id=' . $dea_id . '\''
														   ));
	$form->addHtml($form->addDiv($buttons));
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form2';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Descriptions</div>');
	$form->addHtml('<div id="' . $formName . '" style="">');
	$form->addData($formData2, $_POST);
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class'=> 'submit'))));
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form5';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Features</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$feature_form = $form->makeField('select', 'featureId', 'Feature', '', '', db_lookup("feature", "feature", "array", "", "fea_title"));
	$feature_form .= $form->makeField("button", $formName, "", "Add", array(
																		   'class'  => 'button',
																		   'onClick'=> 'javascript:addFeature();'
																	  ));
	$form->addHtml('<div style="margin-left:10px" class="feature">');
	if ($dea_type == 'Lettings') {
		$form->addHtml($render_feature_Lettings);
	}
	$form->addHtml($render_feature_External);
	$form->addHtml($render_feature_Internal);
	$form->addHtml($render_feature_Locality);
	$form->addHtml('</div>');
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class'=> 'submit'))));
//$form->addHtml($form->addLabel('Add Feature','Add Feature',$feature_form));
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form3';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Photos</div>');
	$form->addHtml('<div id="' . $formName . '" style="">');
	if ($photos) {
		$form->addHtml($photos);
	}
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form4';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Floorplans</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addData($formData4, $_POST);
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Upload", array('class'=> 'submit'))));
	if ($floorplans) {
		$form->addSeperator();
		$form->addHtml($floorplan_order_auto);
		$form->addHtml($floorplans);
	}
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form7';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">EPC</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addData($formData7, $_POST);
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Upload", array('class'=> 'submit'))));
	if ($epc) {
		$form->addSeperator();
		$form->addHtml($epc);
	}
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form6';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Mailshots</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addHtml($mailshot_history);
// only show the mailshot button if the property is available and role is Mailshot
	if ($dea_status == 'Available' && in_array('Mailshot', $_SESSION["auth"]["roles"])) {
		$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Create New Mailshot", array('class'=> 'submit'))));
	}
	$form->addHtml('<a href="/admin4/instruction/customMailshot/id/' . $_GET['dea_id'] . '">Custom Mailshot</a>');
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	if (!$searchLink) {
		$searchLink = "deal_summary.php?dea_id=$dea_id";
	}
	$navbar_array = array(
		'back'  => array(
			'title'=> 'Back',
			'label'=> 'Back',
			'link' => $searchLink
		),
		'search'=> array(
			'title'=> 'Property Search',
			'label'=> 'Property Search',
			'link' => 'property_search.php'
		),
		'print' => array(
			'title'=> 'Print',
			'label'=> 'Print',
			'link' => 'javascript:dealPrint(\'' . $dea_id . '\');'
		)
	);
	$navbar       = navbar2($navbar_array);

	$onLoad .= 'showForm(' . $viewForm . '); ' . $ptype['onload'];

	$additional_js = '
if (!previousID) {
	var previousID = "form' . $viewForm . '";
	}

function addFeature() {
	var featureId = document.testForm.featureId.options[document.testForm.featureId.options.selectedIndex].value;
	document.location.href = \'?do=feature_add&dea_id=' . $dea_id . '&featureId=\'+featureId+\'&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '&viewForm=5\';
	}

';
	$additional_js .= "

				$(window).ready(function() {
						$('#managePhotosButton').click(function() {
							var popup = new Popup('/admin4/instruction/production/id/" . $dea_id . "');
							popup.open();
						})
				});";

	if ($_GET["msg"]) {
		$onLoad .= 'javascript:hideMsg();';
		$msg = '
	<script type="text/javascript" language="javascript">
	<!--
	function hideMsg(){
		setTimeout("hideMsgDiv()",1500);
		}
	function hideMsgDiv() {
		new Effect.Fade("floating_message");
		}
	-->
	</script><div id="notify"><div id="floating_message">' . urldecode($_GET["msg"]) . '</div></div>';
	}

	$page->setTitle("Production > $pro_addr");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addScript('js/scriptaculous/prototype.js');
	$page->addScript('js/scriptaculous/scriptaculous.js');
	$page->addScript('/js/Popup.js');
	$page->addScript('/js/jquery-1.4.3.min.js');
	$page->addScriptDeclaration($additional_js);

	$page->addScriptDeclaration($ptype['js']);
#$page->setBodyAttributes(array('onLoad'=>$ptype['onload']));
	$page->setBodyAttributes(array('onLoad'=> $onLoad));
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	if ($msg) {
		$page->addBodyContent($msg);
	}
	$page->addBodyContent('<a href="postcode_tools_property.php?pro_id=' . $pro_id . '&dea_id=' . $dea_id . '">' . $pro_id . '</a>');
	$page->addBodyContent('</div>');
	$page->display();

	exit;

} elseif ($_POST["action"] == "update") {

	$result = new Validate();

	foreach ($_POST as $key=> $val) {
		#$_POST[$key] = trim($val);
	}

	if ($_POST["form1"]) {

		$viewForm = 1;

		// extract notes from _POST and store in notes table
		if ($_POST["dea_notes_production"]) {
			$notes        = $_POST["dea_notes_production"];
			$db_data_note = array(
				'not_blurb'=> $notes,
				'not_row'  => $dea_id,
				'not_type' => 'deal_production',
				'not_user' => $_SESSION["auth"]["use_id"],
				'not_date' => $date_mysql
			);
			db_query($db_data_note, "INSERT", "note", "not_id");
		}
		unset($_POST["dea_notes_production"]);

		// add psubtype
		$addFormData1 = array(
			'dea_psubtype'=> array(
				'label'   => 'Property Type',
				'required'=> 2,
				'value'   => $_POST["dea_psubtype"]
			)
		);
		$formData1    = join_arrays(array($formData1, $addFormData1));
		#print_r($formData3);

		$results = $result->process($formData1, $_POST);
		$db_data = $results['Results'];

		// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&viewForm=' . $viewForm . '&';

		if ($results['Errors']) {
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}
		$dea_id = db_query($db_data, "UPDATE", "deal", "dea_id", $dea_id);
		if ($_POST["pro_area"]) {
			db_query(array('pro_area'=> $_POST["pro_area"]), "UPDATE", "property", "pro_id", $_POST["pro_id"]);
		}

	} // form 2, the descriptions
	elseif ($_POST["form2"]) {
		$viewForm = 2;

		$results = $result->process($formData2, $_POST);
		$db_data = $results['Results'];

		// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&viewForm=' . $viewForm . '&';

		if ($results['Errors']) {
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}
		$dea_id = db_query($db_data, "UPDATE", "deal", "dea_id", $dea_id);

	} // form 3, photos
	elseif ($_POST["form3"]) {
		$viewForm = 3;

		// get last order number to increment upwards
		if (!$_POST["last_order_photo"]) {
			$order = 1;
		} else {
			$order = ($_POST["last_order_photo"] + 1);
		}

		$results = $result->process($formData3, $_POST);
		#$db_data = $results['Results'];

		#print_r($_POST);
		// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&viewForm=' . $viewForm . '&';

		if ($results['Errors']) {
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}

		// upload the image
		require_once "HTTP/Upload.php";

		$upload = new HTTP_Upload("en");
		$files  = $upload->getFiles();

		foreach ($files as $file) {

			if (PEAR::isError($file)) {
				echo $file->getMessage();
			}

			if ($file->isValid()) {
				if ($file->getProp("type") == 'image/pjpeg' || $file->getProp("type") == 'image/jpeg') {
					$allowed = "1";
				}
				if (!$allowed) {
					echo "Only JPG images are allowed<br>";
					echo $file->getProp("type");
					exit;
				}

				$file->setName("uniq", $custom_filename);
				$dest_name = $file->moveTo($image_path_property);

				#echo $image_path_property.$dest_name;
				if (PEAR::isError($dest_name)) {
					echo $dest_name->getMessage();

				}

				// create all thumbnails
				processPhoto($dest_name, $dea_id);

				// delete the original
				unlink($image_path_property . $dest_name);

				$db_data["med_table"]     = 'deal';
				$db_data["med_row"]       = $_POST["dea_id"];
				$db_data["med_type"]      = 'Photograph';
				$db_data["med_order"]     = $order;
				$db_data["med_title"]     = $_POST["med_title"];
				$db_data["med_blurb"]     = $_POST["med_blurb"];
				$db_data["med_file"]      = $file->getProp("name");
				$db_data["med_realname"]  = $file->getProp("real");
				$db_data["med_filetype "] = $file->getProp("type");
				$db_data["med_filesize"]  = $file->getProp("size");
				$db_data["med_created"]   = $date_mysql;
				$med_id                   = db_query($db_data, "INSERT", "media", "med_id");
				#print_r($db_data);
				$order++;
				$i++;

			} elseif ($file->isMissing()) {
				#echo "No file was provided.";
			} elseif ($file->isError()) {
				echo $file->errorMsg();

			}

		}

	} // form 4, floorplanms
	elseif ($_POST["form4"]) {
		$viewForm = 4;

		// get last order number to increment onwards
		if (!$_POST["last_order_floorplan"]) {
			$order = 1;
		} else {
			$order = ($_POST["last_order_floorplan"] + 1);
		}

		$results = $result->process($formData4, $_POST);
		#$db_data = $results['Results'];

		#print_r($_POST["med_title"]);
		// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&viewForm=' . $viewForm . '&';

		if ($results['Errors']) {
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}

		// upload the image
		require_once "HTTP/Upload.php";

		$upload = new HTTP_Upload("en");
		$files  = $upload->getFiles();

		foreach ($files as $file) {
			if (PEAR::isError($file)) {
				echo $file->getMessage();
			}

			if ($file->isValid()) {
				if ($file->getProp("type") == 'image/gif' || $file->getProp("type") == 'image/jpeg') {
					$allowed = "1";
				}
				if (!$allowed) {
					echo "Only JPG images are allowed<br>";
					echo $file->getProp("type");
					exit;
				}

				$file->setName("uniq", $custom_filename);
				$dest_name = $file->moveTo($image_path_property);
				#echo $image_path_property.$dest_name;
				if (PEAR::isError($dest_name)) {
					echo $dest_name->getMessage();
				}

				// create thumbnails
				$thumbnail_width  = 100;
				$thumbnail_height = 100;
				require_once("inx/phpThumb/phpthumb.class.php");
				$phpThumb = new phpThumb();
				// set data
				$phpThumb->setSourceFilename($image_path_property . $dest_name);
				$phpThumb->setParameter('h', $thumbnail_height);
				$phpThumb->setParameter('w', $thumbnail_width);
				$phpThumb->setParameter('config_output_format', 'gif');
				$phpThumb->setParameter('config_allow_src_above_docroot', true);
				// generate & output thumbnail
				$output_filename = $image_path_property . str_replace('.gif', '', $dest_name) . '_s.' . $phpThumb->config_output_format;
				if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
					$output_size_x = ImageSX($phpThumb->gdimg_output);
					$output_size_y = ImageSY($phpThumb->gdimg_output);
					if ($output_filename) {
						if ($phpThumb->RenderToFile($output_filename)) {
							// do something on success
							#echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
						} else {
							// do something with debug/error messages
							echo 'Failed (size=' . $thumbnail_width . '):<pre>' . implode("\n\n", $phpThumb->debugmessages) . '</pre>';
						}
					} else {
						$phpThumb->OutputThumbnail();
					}
				} else {
					// do something with debug/error messages
					echo 'Failed (size=' . $thumbnail_width . ').<br>';
					echo '<div style="background-color:#FFEEDD; font-weight: bold; padding: 10px;">' . $phpThumb->fatalerror . '</div>';
					echo '<form><textarea rows="10" cols="60" wrap="off">' . htmlentities(implode("\n* ", $phpThumb->debugmessages)) . '</textarea></form><hr>';
				}

				if ($_POST["med_measurement_fp"] == "feet") {
					$_POST["med_dims_fp"] = ft2mtr($_POST["med_dims_fp"]);
				}

				$db_data["med_table"]     = 'deal';
				$db_data["med_row"]       = $_POST["dea_id"];
				$db_data["med_type"]      = 'Floorplan';
				$db_data["med_order"]     = $order;
				$db_data["med_title"]     = $_POST["med_title_fp"];
				$db_data["med_blurb"]     = $_POST["med_blurb_fp"];
				$db_data["med_dims"]      = $_POST["med_dims_fp"];
				$db_data["med_file"]      = $file->getProp("name");
				$db_data["med_realname"]  = $file->getProp("real");
				$db_data["med_filetype "] = $file->getProp("type");
				$db_data["med_filesize"]  = $file->getProp("size");
				$db_data["med_created"]   = $date_mysql;
				$med_id                   = db_query($db_data, "INSERT", "media", "med_id");
				#print_r($db_data);
				$order++;
				$i++;

			} elseif ($file->isMissing()) {
				#echo "No file was provided.";
			} elseif ($file->isError()) {
				echo $file->errorMsg();
			}

		}

	} // form 5 features
	elseif ($_POST["form5"]) {
		$viewForm = 5;

		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&viewForm=' . $viewForm . '&';

		if (!$_POST["feature_id"]) {
			$sql = "DELETE FROM link_instruction_to_feature WHERE dealId = $dea_id";
			$q   = $db->query($sql);
		} else {

			$new_features = $_POST["feature_id"];

			// loop through all roles
			foreach ($all_features as $key=> $val) {

				if ($current_features && $new_features) {

					// if val is present in CURRENT and not preset in NEW, delete
					if (in_array($key, $current_features) && !in_array($key, $new_features)) {
						$sql = "DELETE FROM link_instruction_to_feature WHERE dealId = $dea_id AND featureId = $key";
						$q   = $db->query($sql);
					}

					// if val is present in NEW and not preset in CURRENT, insert
					if (in_array($key, $new_features) && !in_array($key, $current_features)) {
						$db_data["dealId"] = $dea_id;
						$db_data["featureId"] = $key;
						db_query($db_data, "INSERT", "link_instruction_to_feature", "f2d_id");
					}
				}
			}
		}
	} elseif ($_POST["form6"]) {
		header("Location:mailshot.php?dea_id=$dea_id");
		exit;
	} // form 7, other documents
	elseif ($_POST["form7"]) {
		$viewForm = 7;

		// get last order number to increment onwards
		if (!$_POST["last_order_epc"]) {
			$order = 1;
		} else {
			$order = ($_POST["last_order_epc"] + 1);
		}

		$results = $result->process($formData7, $_POST);
		#$db_data = $results['Results'];

		#print_r($_POST["med_title"]);
		// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&viewForm=' . $viewForm . '&';

		if ($results['Errors']) {
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}

		// upload the image
		require_once "HTTP/Upload.php";

		$upload = new HTTP_Upload("en");
		$files  = $upload->getFiles();

		foreach ($files as $file) {
			if (PEAR::isError($file)) {
				echo $file->getMessage();
			}

			if ($file->isValid()) {
				if ($file->getProp("type") == 'image/gif') {
					$allowed = "1";
				}
				if (!$allowed) {
					echo "Only GIF images are allowed<br>";
					echo $file->getProp("type");
					exit;
				}

				$file->setName("uniq", $custom_filename);
				$dest_name = $file->moveTo($image_path_property);
				#echo $image_path_property.$dest_name;
				if (PEAR::isError($dest_name)) {
					echo $dest_name->getMessage();
				}
				processEPC($dest_name, $dea_id);
				unlink($image_path_property . $dest_name);

				$db_data["med_table"]     = 'deal';
				$db_data["med_row"]       = $_POST["dea_id"];
				$db_data["med_type"]      = $_POST["med_title_other"];
				$db_data["med_order"]     = $order;
				$db_data["med_title"]     = $_POST["med_title_other"];
				$db_data["med_file"]      = $file->getProp("name");
				$db_data["med_realname"]  = $file->getProp("real");
				$db_data["med_filetype "] = $file->getProp("type");
				$db_data["med_filesize"]  = $file->getProp("size");
				$db_data["med_created"]   = $date_mysql;
				$med_id                   = db_query($db_data, "INSERT", "media", "med_id");
				#print_r($db_data);
				$order++;
				$i++;

			} elseif ($file->isMissing()) {
				#echo "No file was provided.";
			} elseif ($file->isError()) {
				echo $file->errorMsg();
			}
		}

	}
	$sql = "DELETE FROM instructionVideo WHERE instructionId = '" . $_POST['dea_id'] . "'";
	$db->query($sql);
	if (isset($_POST['video_id']) && $_POST['video_id']) {
		$data = file_get_contents('http://vimeo.com/api/v2/video/' . $_POST['video_id'] . '.json');
		$data = json_decode($data, true);

		if ($data[0]) {
			$sql = "INSERT INTO instructionVideo SET instructionId = '" . $_POST['dea_id'] . "',
				videoId ='" . $_POST['video_id'] . "',
				host = 'vimeo',
				videoData = '" . mysql_real_escape_string(json_encode(str_replace(["\n", "\r"], "", $data[0]))) . "'";
			$db->query($sql);
		}
	}
	// redirect
	header("Location:?$return&viewForm=$viewForm&msg=Update+Successful");
	exit;

}


