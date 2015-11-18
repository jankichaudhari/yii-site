<?php
require_once("../inx/global.inc.php");
require_once("../inx/dbtree.inc.php");
include("menu.php");

if (!$_GET["stage"]) {
	$stage = 1;
	} else {
	$stage = $_GET["stage"];
	}

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage) {
###########################################################
# stage 1 - new directory entry
###########################################################
case 1:

foreach ($_GET AS $key=>$val) {
	$$key = trim($val);
	}
		
		
$formData1 = array(	 
	'dir_category'=>array(
		'type'=>'select',
		'label'=>'Category',
		'value'=>$_GET["node_id"],
		'required'=>2,
		'options'=>display_tree_select(1,"","data"),
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
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'pro_addr2'=>array(
		'type'=>'text',
		'label'=>'Building Name',
		'value'=>$pro_addr2,
		'required'=>1,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'pro_addr3'=>array(
		'type'=>'text',
		'label'=>'Street',
		'value'=>$pro_addr3,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'pro_addr4'=>array(
		'type'=>'text',
		'label'=>'Town or Area',
		'value'=>$pro_addr4,
		'required'=>3,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'pro_addr5'=>array(
		'type'=>'text',
		'label'=>'City or County',
		'value'=>$pro_addr5,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'pro_postcode'=>array(
		'type'=>'text',
		'label'=>'Postcode',
		'value'=>$pro_postcode,
		'required'=>2,
		'attributes'=>array('class'=>'pc','maxlength'=>9),
		'function'=>'format_postcode'
		)
	)
	;



// form is not submitted, show the form
if (!$_GET["action"]) {

// start new form object 
$form = new Form();

$form->addForm("form","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","dir_id","",$cli_id);

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("<fieldset>\n");
$form->addLegend('Add Entry');
$form->addData($formData1,$_GET);
$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("<fieldset>\n");
$form->addLegend('Address');

if (!$cli_pcid) {

$form->ajaxPostcode("by_freetext","pro");

} else {

$form->addData($formData2,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));

}

$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");


$page->setTitle("Directory > Add");
$page->addStyleSheet(GLOBAL_URL.'css/styles.css');
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addScript(GLOBAL_URL.'js/scriptaculous/prototype.js');
$page->addScriptDeclaration($source['js']);
$page->setBodyAttributes(array('onLoad'=>$source['onload']));
$page->addBodyContent('<div id="content">');
$page->addBodyContent($menu);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} else { // if the form has been submitted

// join up the arrays
$formData = join_arrays(array($formData1,$formData2));

$result = new Validate();
$results = $result->process($formData1,$_GET);
$db_data = $results['Results'];



// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
if ($dir_id) {
	$results['Results']['dir_id'] = $dir_id;
	}

if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}
	
if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}

# add address manually, if postcode lookup is not used
if (!$pro_pro_id) {
	$result2 = new Validate();
	$results2 = $result2->process($formData2,$_GET);
	$db_data2 = $results2['Results'];
	$db_data2["pro_pcid"] = '-1';
	$pro_pro_id = db_query($db_data2,"INSERT","property","pro_id");
	}
# add any additional fields to data array
$db_data['dir_created'] = $date_mysql;
$db_data['dir_pro'] = $pro_pro_id;


$dir_id = db_query($db_data,"INSERT","directory","dir_id");

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