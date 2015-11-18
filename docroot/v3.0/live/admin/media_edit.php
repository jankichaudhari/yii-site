<?php
require_once("inx/global.inc.php");

/*
Edit a media item (photo, floorplan, other)
Show all sizes, woth form to adjust text values (title for images, floor for floorplans)
Recreate thumbnails
*/

if ($_GET["med_id"]) {
	$med_id = $_GET["med_id"];
} elseif ($_POST["med_id"]) {
	$med_id = $_POST["med_id"];
} else {
	echo "no med_id";
	exit;
}

$sql = "SELECT
med_id,med_table,med_row,med_type,med_order,med_title,med_file,med_realname,med_filetype,med_filesize,med_blurb,med_dims,med_features,med_created,
dea_id
FROM media
LEFT JOIN deal ON media.med_row = deal.dea_id
WHERE med_id = $med_id
AND med_table = 'deal'";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
while ($row = $q->fetchRow()) {
	foreach ($row as $key => $val) {
		$$key = $val;
	}
}

$image_path_property = IMAGE_PATH_PROPERTY . $dea_id . "/";
$image_url_property  = IMAGE_URL_PROPERTY . $dea_id . "/";

if ($med_type == "Photograph") {

	// which tab to show on return to production page
	$viewForm = 3;

	$formData = array(
		'med_title' => array(
			'type'       => 'select',
			'label'      => 'Title',
			'value'      => $med_title,
			'required'   => 2,
			'attributes' => array('style' => 'width:320px'),
			'options'    => $photograph_titles
		),
		'med_blurb' => array(
			'type'       => 'textarea',
			'label'      => 'Description',
			'value'      => $med_blurb,
			'attributes' => array('style' => 'width:320px')
		)
	);

} elseif ($med_type == "Floorplan") {

	// which tab to show on return to production page
	$viewForm = 4;

	$formData = array(
		'med_title'       => array(
			'type'       => 'select',
			'label'      => 'Title',
			'value'      => $med_title,
			'required'   => 2,
			'attributes' => array('style' => 'width:320px'),
			'options'    => $floorplan_titles
		),
		'med_dims'        => array(
			'type'       => 'text',
			'label'      => 'Area',
			'value'      => $med_dims,
			'required'   => 1,
			'attributes' => array('style' => 'width:120px'),
			'group'      => 'Area'
		),
		'med_measurement' => array(
			'type'          => 'radio',
			'label'         => 'Area',
			'default'       => 'mtr&sup2;',
			'required'      => 1,
			'options'       => array('mtr&sup2;' => 'metres', 'ft&sup2;' => 'feet'),
			'group'         => 'Area',
			'last_in_group' => 1
		)
	);
}

if (!$_POST["action"]) {

	$form = new Form();

	$form->addForm("", "POST", $PHP_SELF, "multipart/form-data");
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "stage", "", "1");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "dea_id", "", $dea_id);
	$form->addField("hidden", "med_id", "", $med_id);
	$form->addField("hidden", "searchLink", "", urlencode($searchLink));

	$form->addHtml("<fieldset>\n");
//$form->addLegend('Edit '.$med_title);
	$form->addHtml('<div class="block-header">Edit ' . $med_title . '</div>');

	$form->addData($formData, $_POST);
	$form->addHtml($form->addDiv($form->makeField("submit", "", "", "Save Changes", array('class' => 'submit'))));

	if ($med_type == "Photograph") {
		foreach ($thumbnail_sizes as $dims => $ext) {
			$dim    = explode('x', $dims);
			$width  = $dim[0];
			$height = $dim[1];
			if (file_exists($image_path_property . str_replace('.jpg', '_' . $ext . '.jpg', $med_file))) {
				$form->addHtml('<div align="center"><img src="' . $image_url_property . str_replace('.jpg', '_' . $ext . '.jpg', $med_file) . '" border="1" /></div>');
			}
		}
	} else {
		$form->addHtml('<div align="center"><img src="' . $image_url_property . str_replace('.jpg', '_' . $ext . '.jpg', $med_file) . '" border="1" /></div>');
	}
	$form->addHtml("</fieldset>\n");

	$navbar_array = array(
		'back'   => array('title' => 'Back', 'label' => 'Back', 'link' => $searchLink),
		'search' => array('title' => 'Property Search', 'label' => 'Property Search', 'link' => 'property_search.php')
	);
	$navbar       = navbar2($navbar_array);

	$page = new HTML_Page2($page_defaults);
	$page->setTitle("Edit Media");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	$page->addBodyContent('</div>');
	$page->display();

	exit;

} else {

	if ($_POST["med_measurement"] == "feet") {
		$_POST["med_dims"] = ft2mtr($_POST["med_dims"]);
	}
	unset($_POST["med_measurement"]);

	$result  = new Validate();
	$results = $result->process($formData, $_POST);
	$db_data = $results['Results'];

	if ($results['Errors']) {
		if (is_array($results['Results'])) {
			$return .= http_build_query($results['Results']);
		}
		echo error_message($results['Errors'], urlencode($return));
		exit;
	}
	$med_id = db_query($db_data, "UPDATE", "media", "med_id", $med_id);
#header("Location:".urldecode($_POST["searchLink"]));
	if ($_POST["searchLink"]) {
		$tmpurl = $_POST["searchLink"];
	} else {
		$tmpurl = $_GET["searchLink"];
	}
	header("Location:" . replaceQueryString(urldecode($tmpurl), 'viewForm') . '&viewForm=' . $viewForm);

	exit;
}
?>