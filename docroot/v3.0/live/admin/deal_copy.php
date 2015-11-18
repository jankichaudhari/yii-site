<?php
require_once("inx/global.inc.php");


// page for copying a deal

if ($_GET["dea_id"]) {
	$dea_id = $_GET["dea_id"];
	} elseif ($_POST["dea_id"]) {
	$dea_id = $_POST["dea_id"];
	} else {
	$errors[] = "No property ID specified";
	echo error_message($errors);
	exit;
	}

/*

copy deal table, get new dea_id
copy media table where med_dea = old dea, and insert with med_deal = new dea_id
link_client_to_instruction with new dea_id
features

NOT property table
NOT property table

*/
$sql = "SELECT * FROM deal WHERE dea_id = $dea_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	foreach($row as $key=>$val) {
		$$key = $val;
		if ($key != 'dea_id') {
			$db_data[$key] = $val;
			}
		}
	}

$formData = array(
	'copy_to'=>array(
		'type'=>'radio',
		'label'=>'Copy to',
		'required'=>2,
		'value'=>$dea_type,
		'options'=>array('Sales'=>'Sales','Lettings'=>'Lettings')
		),
	'copy_number'=>array(
		'type'=>'select',
		'label'=>'Number of copies',
		'required'=>1,
		'options'=>array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10)
		),
	);



if (!$_POST["action"]) {

$form = new Form();

$form->addForm("","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","copy");
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));



$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Copy</div>');
$form->addHtml('<p class="appInfo">The status of these copies will be set to Production</p>');
$form->addData($formData,$_POST);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");


$page = new HTML_Page2($page_defaults);
$page->setTitle("Copy");
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



$copy_to = $_POST["copy_to"];
$copy_number = $_POST["copy_number"];


// reset price related data if changing dea_type
if ($copy_to <> $dea_type) {
	unset($db_data["dea_valueprice"]);
	unset($db_data["dea_marketprice"]);
	unset($db_data["dea_commission"]);
	unset($db_data["dea_commissiontype"]);
	}

$db_data["dea_type"] = $copy_to;
$db_data["dea_status"] = 'Production';

for ($_i = 1; $_i <= $copy_number; $_i++) {

$new_dea_id = db_query($db_data,"INSERT","deal","dea_id");

$added[] = $new_dea_id;

$db_data_sot["sot_deal"] = $new_dea_id;
$db_data_sot["sot_status"] = 'Production';
$db_data_sot["sot_date"] = $date_mysql;
$db_data_sot["sot_user"] = $_SESSION["auth"]["use_id"];;
db_query($db_data_sot,"INSERT","sot","sot_id");


$sql = "SELECT * FROM link_client_to_instruction WHERE dealId = $dea_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	$db_data_link_client_to_instruction["dealId"] = $new_dea_id;
	$db_data_link_client_to_instruction["clientId"] = $row["clientId"];
	$db_data_link_client_to_instruction["capacity"] = $row["capacity"];
	db_query($db_data_link_client_to_instruction,"INSERT","link_client_to_instruction","id");
	unset($db_data_link_client_to_instruction);
	}


$sql = "SELECT * FROM link_instruction_to_feature WHERE dealId = $dea_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	$db_data_link_instruction_to_feature["dealId"] = $new_dea_id;
	$db_data_link_instruction_to_feature["featureId"] = $row["featureId"];
	db_query($db_data_link_instruction_to_feature,"INSERT","link_instruction_to_feature","f2d_id");
	unset($db_data_link_instruction_to_feature);
	}

// media, must create folder AND copy all files to new folder
$image_path_property = IMAGE_PATH_PROPERTY.$dea_id.'/';
$new_image_path_property = IMAGE_PATH_PROPERTY.$new_dea_id.'/';
if (!is_dir($new_image_path_property)) {
	if (!mkdir($new_image_path_property,0777)) {
		echo "error creating folder";
		exit;
		}
	}

$sql = "SELECT * FROM media WHERE med_table = 'deal' AND med_row = $dea_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {

	$db_data_media["med_table"] = 'deal';
	$db_data_media["med_row"] = $new_dea_id;
	$db_data_media["med_type"] = $row["med_type"];
	$db_data_media["med_order"] = $row["med_order"];
	$db_data_media["med_title"] = $row["med_title"];
	$db_data_media["med_file"] = $row["med_file"];
	$db_data_media["med_realname"] = $row["med_realname"];
	$db_data_media["med_filetype"] = $row["med_filetype"];
	$db_data_media["med_filesize"] = $row["med_filesize"];
	$db_data_media["med_blurb"] = $row["med_blurb"];
	$db_data_media["med_dims"] = $row["med_dims"];
	$db_data_media["med_features"] = $row["med_features"];
	$db_data_media["med_created"] = $row["med_created"];

	// images are all suffixed with one of the values fropm thumbnail_sizes.
	// images are gif or jpg only
	// just copy whole folder
	CopyFiles($image_path_property, $new_image_path_property);


	db_query($db_data_media,"INSERT","media","med_id");
	unset($db_data_media);


	}

	// finally add note to status notes part
	$db_data_notes["not_type"] = 'sot';
	$db_data_notes["not_row"] = $new_dea_id;
	$db_data_notes["not_blurb"] = 'This record was copied from id: '.$dea_id;
	$db_data_notes["not_user"] = $_SESSION["auth"]["use_id"];
	$db_data_notes["not_date"] = $date_mysql;
	db_query($db_data_notes,"INSERT","note","not_id");
	unset($db_data_notes);

	}

if ($copy_number > 1) {
	$render = "<p>You successfuly copied this property $copy_number times</p>";
	foreach($added AS $val) {
		$render .= '<p><a href="deal_summary?dea_id='.$val.'">ID: '.$val.'</a></p>';
		}
	echo $render;


	} else {
	header("Location:deal_summary.php?dea_id=$new_dea_id");
	}
}

?>