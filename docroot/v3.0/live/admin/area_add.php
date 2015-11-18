<?php
require_once("inx/global.inc.php");

$are_postcode = $_GET["are_postcode"];
$return = $_GET["return"];


$formData = array(
	'are_title'=>array(
		'type'=>'text',
		'label'=>'Area Title',
		'required'=>2,
		'function'=>'format_street',
		'attributes'=>array('class'=>'wide')
		),
	'are_postcode'=>array(
		'type'=>'text',
		'label'=>'Postcode',
		'value'=>$are_postcode,
		'required'=>2,
		'function'=>'format_postcode',
		'attributes'=>array('class'=>'narrow','maxlength'=>'4')
		),
	'are_branch'=>array(
		'type'=>'select_branch',
		'label'=>'Branch',
		'required'=>2,
		'attributes'=>array('class'=>'wide')
		)
	);

// form is not submitted, show the form
if (!$_GET["action"]) {

// start new form object
$form = new Form();

$form->addForm("form","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","return","",urlencode($return));
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Add Area</div>');
$form->addData($formData,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$return),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

// start a new page
$page = new HTML_Page2($page_defaults);

$page->setTitle("Add Area");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

} else {


// validate
$result = new Validate();
$results = $result->process($formData,$_GET);
$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}
if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}

// check area table for possible matches
$sql = "SELECT * FROM area WHERE are_title = '".$db_data["are_title"]."'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		$db_data["are_title"] = $row["are_title"];
		}
	} else {
	# insert and redirect
	db_query($db_data,"INSERT","area","are_id");
	}



header("Location:".urldecode($_GET["return"])."&pro_area=".$db_data["are_title"]);


}
?>