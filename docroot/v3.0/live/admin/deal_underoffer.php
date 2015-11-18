<?php
require_once("inx/global.inc.php");
// setting status to under offer following acceptance of offer.
// page 1 asks the question
// page 2 sets the status

$off_id     = (isset($_REQUEST['off_id']) ? $_REQUEST['off_id'] : "");
$dea_id     = (isset($_REQUEST['dea_id']) ? $_REQUEST['dea_id'] : "");
$dea_status = (isset($_REQUEST['dea_status']) ? $_REQUEST['dea_status'] : "");
$formName   = (isset($_REQUEST['formName']) ? $_REQUEST['formName'] : "");
if (!$_GET["action"]) {

	$formData1 = array(
		'dea_status'=> array(
			'type'    => 'radio',
			'label'   => 'Set deal to under offer?',
			'value'   => 'Yes',
			'required'=> 2,
			'options' => array('Yes'=> 'Yes',
							   'No' => 'No')
		)
	);

	$form = new Form();

	$form->addForm("", "GET", $PHP_SELF);
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "action", "", "update");

	$form->addField("hidden", "off_id", "", $off_id);

	$form->addField("hidden", "dea_id", "", $dea_id);
	$form->addField("hidden", "searchLink", "", urlencode($_GET["searchLink"]));

	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Edit Status</div>');
	$form->addData($formData1, $_GET);

	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class'=> 'submit'))));
	$form->addHtml("</fieldset>\n");
	$form->addHtml("</div>\n");

	$navbar_array = array(
		'back'  => array('title'=> 'Back',
						 'label'=> 'Back',
						 'link' => $_GET["searchLink"]),
		'search'=> array('title'=> 'Property Search',
						 'label'=> 'Property Search',
						 'link' => 'property_search.php')
	);
	$navbar       = navbar2($navbar_array);
	$page         = new HTML_Page2($page_defaults);
	$page->setTitle("Edit Status");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
#$page->addBodyContent($render);
	$page->addBodyContent('</div>');
	$page->display();

}
else {

	if ($_GET["dea_status"] == 'Yes') {

		$db_data["dea_status"] = 'Under Offer';
		$dea_id                = db_query($db_data, "UPDATE", "deal", "dea_id", $dea_id);

		// insert new row into sot table if the status has been changed

		if ($db_data["dea_status"] && $db_data["dea_status"] !== $dea_status) {
			$db_data2["sot_deal"]   = $dea_id;
			$db_data2["sot_status"] = $db_data["dea_status"];
			$db_data2["sot_date"]   = $date_mysql;
			$db_data2["sot_notes"]  = $_GET["notes"];
			$db_data2["sot_user"]   = $_SESSION["auth"]["use_id"];
			$sot_id                 = db_query($db_data2, "INSERT", "sot", "sot_id");
		}

	}

	header("Location:" . urldecode($_GET["searchLink"]));
}
?>