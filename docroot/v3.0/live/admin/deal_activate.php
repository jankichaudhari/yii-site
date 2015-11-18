<?php
require_once("inx/global.inc.php");
// activate a property

if ($_GET["dea_id"]) {
	$dea_id = $_GET["dea_id"];
	} else {
	$errors[] = 'No property specified';
	echo error_message($errors);
	exit;
	}

if (!$_GET["action"]) {

$formData1 = array(
	'dea_launchdate'=>array(
		'type'=>'radio',
		'label'=>'Release as new?',
		'value'=>'No',
		'required'=>2,
		'options'=>array('Yes'=>'Yes','No'=>'No'),
		'tooltip'=>'Choosing yes will make this property the newest on the site'
		)
	);

if (!in_array('Production',$_SESSION["auth"]["roles"])) {
	$formData1['dea_launchdate']['attributes'] = array('disabled'=>'disabled');
	}

if (in_array('Mailshot',$_SESSION["auth"]["roles"])) {
	$formData1['mailshot'] = array(
		'type'=>'radio',
		'label'=>'Send mailshot?',
		'value'=>'No',
		'required'=>2,
		'options'=>array('Yes'=>'Yes','No'=>'No')
		);
	}


$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","searchLink","",urlencode($_GET["searchLink"]));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Release Property</div>');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$_GET["searchLink"]),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);
$page = new HTML_Page2($page_defaults);
$page->setTitle("Release Property");
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


	$db_data["dea_status"] = 'Available';


	if ($_GET["dea_launchdate"] == 'Yes') {
		$db_data["dea_launchdate"] = $date_mysql;
		}
	$dea_id = db_query($db_data,"UPDATE","deal","dea_id",$dea_id);

	$db_data2["sot_deal"] = $dea_id;
	$db_data2["sot_status"] = $db_data["dea_status"];
	$db_data2["sot_date"] = $date_mysql;
	$db_data2["sot_notes"] = $_GET["notes"];
	$db_data2["sot_user"] = $_SESSION["auth"]["use_id"];
	$sot_id = db_query($db_data2,"INSERT","sot","sot_id");

	// make the images folder readable
	$render = 'Options +Indexes';
	$local_file = IMAGE_PATH_PROPERTY."/".$dea_id."/.htaccess";
	if (!@file_put_contents($local_file,$render)) {
		//echo "could not write to file";
		//exit;
		}
	// add the default index page which includes image_display from elsewhere
	if (!file_exists(IMAGE_PATH_PROPERTY."/".$dea_id."/index.php")) {
		copy(IMAGE_PATH_PROPERTY."/default_image_index.php",IMAGE_PATH_PROPERTY."/".$dea_id."/index.php");
		}


	if ($_GET["mailshot"] == 'Yes') {
		header("Location:mailshot.php?dea_id=$dea_id");
		exit;
		}



	header("Location:deal_summary.php?dea_id=$dea_id");
	}
?>