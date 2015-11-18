<?php
require_once("../inx/global.inc.php");
require_once("../inx/dbtree.inc.php");
include("menu.php");


/* 
this will un-associate a property record from selected 
directory entry, allowing a new one to be specified
14/09/2006
*/
if ($_GET["action"] == "reset_directory_address") {
if (!$_GET["dir_id"]) {
	echo "no directory enrty selected";
	exit;
	}

$sql = "UPDATE directory SET dir_pro = '' WHERE dir_id = ".$_GET["dir_id"];
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }

header("Location:edit.php?dir_id=".$_GET["dir_id"]);
exit;
}




if (!$_GET["stage"]) {
	$stage = 1;
	} else {
	$stage = $_GET["stage"];
	}



switch ($stage) {
###########################################################
# stage 1 - new directory entry
###########################################################
case 1:

foreach ($_GET AS $key=>$val) {
	$$key = trim($val);
	}

$sql = "SELECT *
FROM directory,category
LEFT JOIN property ON directory.dir_pro = property.pro_id
WHERE 
directory.dir_category = category.cat_id AND
directory.dir_id = $dir_id 
LIMIT 1";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows == 0) {
	echo "No matches found.";
	exit;
} else {
	while ($row = $q->fetchRow()) {			
		foreach($row as $key=>$val) {	
			$$key = $val;					
			}
		}
	}
	
			
$formData1 = array(	 
	'dir_category'=>array(
		'type'=>'select',
		'label'=>'Category',
		'value'=>$dir_category,
		'required'=>2,
		'options'=>display_tree_select(1,$dir_category,"data"),
		'attributes'=>array('style'=>'width:320px')
		),
	'dir_title'=>array(
		'type'=>'text',
		'label'=>'Business Name',
		'value'=>$dir_title,
		'required'=>2,
		'attributes'=>array('style'=>'width:320px')
		),
	'dir_name'=>array(
		'type'=>'text',
		'label'=>'Contact Name',
		'value'=>$dir_name,
		'required'=>1,
		'attributes'=>array('style'=>'width:320px'),
		'function'=>'format_name'
		),
	'dir_tel'=>array(
		'type'=>'text',
		'label'=>'Telephone',
		'value'=>$dir_tel,
		'required'=>1,
		'attributes'=>array('style'=>'width:164px','maxlength'=>30)
		),
	'dir_fax'=>array(
		'type'=>'text',
		'label'=>'Fax',
		'value'=>$dir_fax,
		'required'=>1,
		'attributes'=>array('style'=>'width:164px','maxlength'=>30)
		),
	'dir_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$dir_email,
		'required'=>1,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255)
		),
	'dir_web'=>array(
		'type'=>'text',
		'label'=>'Website',
		'value'=>$dir_web,
		'init'=>'http://',
		'required'=>1,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255)
		),
	'dir_blurb'=>array(
		'type'=>'textarea',
		'label'=>'Description',
		'value'=>$dir_blurb,
		'required'=>1,
		'attributes'=>array('style'=>'width:320px;height:80px')
		)
	)
	;
	
// make fields read only if address comes from paf
if ($pro_pcid == '-1') {
	$attribute_array = array('class'=>'addr');
	$attribute_array_pc = array('class'=>'pc','maxlength'=>9);
	} else {	
	$attribute_array = array('class'=>'addr','readonly'=>'readonly');
	$attribute_array_pc = array('class'=>'pc','maxlength'=>9,'readonly'=>'readonly');
	}
	
$formData2 = array( 
	'pro_pcid'=>array(
		'type'=>'hidden',
		'value'=>$pro_pcid
		),	 
	'pro_addr1'=>array(
		'type'=>'text',
		'label'=>'House Number',
		'value'=>$pro_addr1,
		'required'=>2,
		'attributes'=>$attribute_array,
		'function'=>'format_street'
		),
	'pro_addr2'=>array(
		'type'=>'text',
		'label'=>'Building Name',
		'value'=>$pro_addr2,
		'required'=>1,
		'attributes'=>$attribute_array,
		'function'=>'format_street'
		),
	'pro_addr3'=>array(
		'type'=>'text',
		'label'=>'Street',
		'value'=>$pro_addr3,
		'required'=>2,
		'attributes'=>$attribute_array,
		'function'=>'format_street'
		),
	'pro_addr4'=>array(
		'type'=>'text',
		'label'=>'Town or Area',
		'value'=>$pro_addr4,
		'required'=>3,
		'attributes'=>$attribute_array,
		'function'=>'format_street'
		),
	'pro_addr5'=>array(
		'type'=>'text',
		'label'=>'City or County',
		'value'=>$pro_addr5,
		'required'=>2,
		'attributes'=>$attribute_array,
		'function'=>'format_street'
		),
	'pro_postcode'=>array(
		'type'=>'text',
		'label'=>'Postcode',
		'value'=>$pro_postcode,
		'required'=>2,
		'attributes'=>$attribute_array_pc,
		'function'=>'format_postcode',
		'group'=>'Postcode'
		),
	'pro_postcode_change'=>array(
		'type'=>'button',
		'label'=>'Postcode',
		'value'=>'Change Address',
		'group'=>'Postcode',
		'attributes'=>array('class'=>'button','onClick'=>'javascript:resetDirectoryAddress('.$dir_id.');'),
		'last_in_group'=>1
		)
	)
	;




// form is not submitted, show the form
if (!$_POST["action"]) {

// start new form object 
$form = new Form();

$form->addForm("form","post",$PHP_SELF,"multipart/form-data");
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","dir_id","",$dir_id);
$form->addField("hidden","pro_id","",$pro_id);

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("<fieldset>\n");
$form->addLegend('Edit Entry');
$form->addData($formData1,$_POST);
$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("<fieldset>\n");
$form->addLegend('Address');

if (!$pro_pcid) {

$form->ajaxPostcode("by_freetext","pro");

} else {

$form->addData($formData2,$_POST);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));

}

$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");

// start a new page
$page = new HTML_Page2($page_defaults);
$page->setTitle("Directory > Edit");
$page->addStyleSheet(GLOBAL_URL.'css/styles.css');
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addScript(GLOBAL_URL.'js/scriptaculous/prototype.js');
$page->addScriptDeclaration($source['js']);
$page->setBodyAttributes(array('onLoad'=>$source['onload']));
$page->addBodyContent('<div id="content">');
$page->addBodyContent($menu);
$page->addBodyContent('<p><a href="image.php?dir_id='.$dir_id.'">Images</a></p>');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} else { // if the form has been submitted



$result = new Validate();
$results = $result->process($formData1,$_POST);
$db_data = $results['Results'];

$result2 = new Validate();
$results2 = $result2->process($formData2,$_POST);
$db_data2 = $results2['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
if ($cli_id) {
	$results['Results']['dir_id'] = $dir_id;
	}
if ($viewForm) {
	$results['Results']['viewForm'] = $viewForm;
	}
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}
	
if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}

#print_r($db_data);
#print_r($db_data2);
# add any additional fields to data array
if ($pro_pro_id) {$db_data['dir_pro'] = $pro_pro_id;}

$dir_id = db_query($db_data,"UPDATE","directory","dir_id",$dir_id);
# cant edit property details from here!
# $pro_id = db_query($db_data2,"UPDATE","property","pro_id",$pro_id);
header("Location:index.php");
exit;
}










	
break;
###########################################################
# default 
###########################################################
default:
   
}
?>