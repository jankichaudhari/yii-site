<?php
/*
This page allows adding of client, but is no longer used as the first part of this is now handled by client_lookup
The new client add page is now called applicant_add.php, and contains all that this page does except the name and address
parts.


*/
//mail('mark@woosterstock.co.uk','page used',"client_add.php\nby ".$_SESSION["use_name"]);
require_once("inx/global.inc.php");

# this page can only be accessed from client_lookup page
#if (!$_GET["lookup"]) {
#	header("Location:client_lookup.php");
#	}

if (!$_GET["stage"]) {
	$stage = 1;
	} else {
	$stage = $_GET["stage"];
	}

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage) {
###########################################################
# stage 1 - new client form
###########################################################
case 1:


foreach ($_GET AS $key=>$val) {
	$$key = trim($val);
	}
// returns an array for the multi-dropdown source fields
$source = source($cli_source,$_SERVER['QUERY_STRING']);

# build data arrays
$formData1 = array(
	'cli_method'=>array(
		'type'=>'radio',
		'label'=>'Contact Method',
		'value='=>$cli_method,
		'required'=>2,
		'options'=>db_enum("client","cli_method","array")
		),
	'cli_source'=>array(
		'type'=>'select_multi',
		'label'=>'Lead Source',
		'required'=>2,
		'options'=>array('dd1'=>$source['dd1'],'dd2'=>$source['dd2'])
		),
	'cli_sales'=>array(
		'type'=>'radio',
		'label'=>'Sales',
		'value='=>$cli_sales,
		'required'=>2,
		'options'=>db_enum("client","cli_sales","array")
		),
	'cli_lettings'=>array(
		'type'=>'radio',
		'label'=>'Lettings',
		'value='=>$cli_lettings,
		'required'=>2,
		'options'=>db_enum("client","cli_lettings","array")
		)
	);


$formData2 = array(
	'cli_salutation'=>array(
		'type'=>'select',
		'group'=>'Full Name',
		'label'=>'Salutation',
		'value'=>$cli_salutation,
		'required'=>2,
		'options'=>db_enum("client","cli_salutation","array"),
		'attributes'=>array('style'=>'width:60px')
		),
	'cli_fname'=>array(
		'type'=>'text',
		'group'=>'Full Name',
		'label'=>'Forename',
		'value'=>$cli_fname,
		'init'=>'Forename(s)',
		'required'=>2,
		'attributes'=>array('style'=>'width:100px','onFocus'=>'javascript:clearField(this,\'Forename(s)\')'),
		'function'=>'format_name'
		),
	'cli_sname'=>array(
		'type'=>'text',
		'group'=>'Full Name',
		'last_in_group'=>1,
		'label'=>'Surname',
		'value'=>$cli_sname,
		'init'=>'Surname',
		'required'=>2,
		'attributes'=>array('style'=>'width:152px','onFocus'=>'javascript:clearField(this,\'Surname\')'),
		'function'=>'format_name'
		),
	'cli_tel1'=>array(
		'type'=>'text',
		'group'=>'Primary Telephone',
		'label'=>'Telephone 1',
		'value'=>$cli_tel1,
		'required'=>2,
		'attributes'=>array('style'=>'width:164px','maxlength'=>30)
		),
	'cli_tel1type'=>array(
		'type'=>'select',
		'group'=>'Primary Telephone',
		'last_in_group'=>1,
		'label'=>'Telephone 1 Type',
		'value'=>$cli_tel1type,
		'required'=>2,
		'options'=>db_enum("client","cli_tel1type","array"),
		'attributes'=>array('style'=>'width:80px')
		),
	'cli_tel2'=>array(
		'type'=>'text',
		'group'=>'&nbsp;',
		'label'=>'&nbsp;',
		'value'=>$cli_tel2,
		'required'=>1,
		'attributes'=>array('style'=>'width:164px','maxlength'=>30)
		),
	'cli_tel2type'=>array(
		'type'=>'select',
		'group'=>'&nbsp;',
		'last_in_group'=>1,
		'label'=>'Telephone 2 Type',
		'value'=>$cli_tel2type,
		'required'=>1,
		'options'=>db_enum("client","cli_tel2type","array"),
		'attributes'=>array('style'=>'width:80px')
		),
	'cli_tel3'=>array(
		'type'=>'text',
		'group'=>'&nbsp;&nbsp;', // hack for group with no title
		'label'=>'&nbsp;&nbsp;',
		'value'=>$cli_tel3,
		'required'=>1,
		'attributes'=>array('style'=>'width:164px','maxlength'=>20)
		),
	'cli_tel3type'=>array(
		'type'=>'select',
		'group'=>'&nbsp;&nbsp;',
		'last_in_group'=>1,
		'label'=>'Telephone 3 Type',
		'value'=>$cli_tel3type,
		'required'=>1,
		'options'=>db_enum("client","cli_tel3type","array"),
		'attributes'=>array('style'=>'width:80px')
		),
	'cli_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$cli_email,
		'required'=>3,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255)
		),
	'cli_web'=>array(
		'type'=>'text',
		'label'=>'Website',
		'value'=>$cli_web,
		'init'=>'http://',
		'required'=>1,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255)
		),
	'cli_preferred'=>array(
		'type'=>'radio',
		'label'=>'Preferred Contact',
		'value'=>$cli_preferred,
		'required'=>2,
		'options'=>db_enum("client","cli_preferred","array")
		)
	)
	;

$formData3 = array(
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
		),
	'pro_country'=>array(
		'type'=>'select',
		'label'=>'Country',
		'value'=>$pro_country,
		'required'=>2,
		'options'=>db_lookup("cli_country","country","array"),
		'attributes'=>array('class'=>'addr')
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
$form->addField("hidden","cli_id","",$cli_id);

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Add Client</div>');
$form->addData($formData1,$_GET);
$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Contact Info</div>');
$form->addData($formData2,$_GET);
$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Address</div>');

$form->addRow('radio','p2c_type','Type','Home','',db_enum("pro2cli","p2c_type","array"));
if (!$pro_pcid) {
$form->ajaxPostcode("by_freetext","pro");
} else {
$form->addData($formData3,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));

}

$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");


$page->setTitle("Client > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScriptDeclaration($source['js']);
$page->setBodyAttributes(array('onLoad'=>$source['onload']));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} else { // if the form has been submitted

#print_r($_GET);
#exit;



// join up the arrays (have removed address form ($formData3) as it is processed separately
$formData = join_arrays(array($formData1,$formData2));

$result = new Validate();
$results = $result->process($formData,$_GET);
$db_data = $results['Results'];

if (!$_GET["cli_pro_id"]) {
	// manual form is used, so we can generate an error message before the client is created

	// first we need to add a pcid so ensure manual input form is displayed on return
	$result2 = new Validate();
	$results2 = $result2->process($formData3,$_GET);
	$db_data2 = $results2['Results'];
	if (is_array($results2['Results'])) {
		$return2 = http_build_query($results2['Results']);
		}
	}


// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
if ($cli_id) {
	$results['Results']['cli_id'] = $cli_id;
	}
if ($viewForm) {
	$results['Results']['viewForm'] = $viewForm;
	}
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}

$return .= '&'.$return2;

if ($results['Errors'] && $results2['Errors']) {
	$errors = join_arrays(array($results['Errors'],$results2['Errors']));
	echo error_message($errors,urlencode($return));
	exit;
	}
elseif ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}
elseif ($results2['Errors']) {
	echo error_message($results2['Errors'],urlencode($return));
	exit;
	}


// add any additional fields to data array
$db_data['cli_created'] = $date_mysql;
$db_data['cli_regd'] = $_SESSION["auth"]["use_id"];

// insert client
$cli_id = db_query($db_data,"INSERT","client","cli_id");


// saving address in property table, need cli_id to do this so happens after client has been inserted
if ($_GET["cli_pro_id"]) {
	$cli_pro = $_GET["cli_pro_id"];
	$db_data2["p2c_cli"] = $cli_id;
	$db_data2["p2c_pro"] = $cli_pro;
	$db_data2["p2c_type"] = $_GET["p2c_type"];
	db_query($db_data2,"INSERT","pro2cli","p2c_id");

	} else {

	// if manual form is used

	// here, in future, we should check table for existing properties to prevent duplicates
	$db_data2["pro_pcid"] = '-1';
	$cli_pro = db_query($db_data2,"INSERT","property","pro_id");

	// add client and property id to link table
	$db_data3["p2c_cli"] = $cli_id;
	$db_data3["p2c_pro"] = $cli_pro;
	$db_data3["p2c_type"] = $_GET["p2c_type"];
	db_query($db_data3,"INSERT","pro2cli","p2c_id");
	}


// add this address as client's default
$db_data4["cli_pro"] = $cli_pro;
db_query($db_data4,"UPDATE","client","cli_id",$cli_id);

header("Location:?stage=2&cli_id=$cli_id");
exit;
}








break;
###########################################################
# stage 2 - property requirements
###########################################################
case 2:

# we need to know if the client is sale, letting or both
$cli_id = $_GET["cli_id"];
$sql = "SELECT cli_sales, cli_lettings FROM client WHERE cli_id = $cli_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	$cli_sales = $row["cli_sales"];
	$cli_lettings = $row["cli_lettings"];
	}

# build data arrays

if ($cli_sales == "Yes") {
	$formData1 = array(
		'cli_salemin'=>array(
			'type'=>'select_price',
			'label'=>'Minimum Price',
			'group'=>'Price Range',
			'required'=>2,
			'options'=>array('scope'=>'sales','default'=>'Minimum'),
			'attributes'=>array('style'=>'width:120px')
			),
		'cli_salemax'=>array(
			'type'=>'select_price',
			'label'=>'Maximum Price',
			'group'=>'Price Range',
			'last_in_group'=>1,
			'required'=>2,
			'options'=>array('scope'=>'sales','default'=>'Maximum'),
			'attributes'=>array('style'=>'width:120px')
			),
		'cli_salebed'=>array(
			'type'=>'select_number',
			'label'=>'Minimum Beds',
			'required'=>2
			),
		'cli_saleemail'=>array(
			'type'=>'radio',
			'label'=>'Email Updates',
			'required'=>2,
			'options'=>db_enum("client","cli_saleemail","array")
			)
		);
	$ptype_sale = ptype("sale",explode("|",$_GET["cli_saleptype"]));
	}

if ($cli_lettings == "Yes") {
	$formData2 = array(
		'cli_letmin'=>array(
			'type'=>'select_price',
			'label'=>'Minimum Price',
			'group'=>'Price Range',
			'required'=>2,
			'options'=>array('scope'=>'lettings','default'=>'Minimum'),
			'attributes'=>array('style'=>'width:120px')
			),
		'cli_letmax'=>array(
			'type'=>'select_price',
			'label'=>'Maximum Price',
			'group'=>'Price Range',
			'last_in_group'=>1,
			'required'=>2,
			'options'=>array('scope'=>'lettings','default'=>'Maximum'),
			'attributes'=>array('style'=>'width:120px')
			),
		'cli_letbed'=>array(
			'type'=>'select_number',
			'label'=>'Minimum Beds',
			'required'=>2
			),
		'cli_letemail'=>array(
			'type'=>'radio',
			'label'=>'Email Updates',
			'required'=>2,
			'options'=>db_enum("client","cli_letemail","array")
			)
		);
	$ptype_let = ptype("let",explode("|",$_GET["cli_letptype"]));
	}


if (!$_GET["action"]) {


$form = new Form();

$form->addForm("","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","stage","","2");
$form->addField("hidden","cli_id","",$cli_id);
/////////////////////////////////////////////////////////////////////////////////
if ($cli_sales == "Yes") {
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Sales Requirements</div>');
	$form->addHtml($form->addLabel('cli_saleptype','Houses',$ptype_sale['house'],'javascript:checkAll(document.forms[0], \'sale1\');'));
	$form->addHtml($form->addLabel('cli_saleptype','Apartments',$ptype_sale['apartment'],'javascript:checkAll(document.forms[0], \'sale2\');'));
	$form->addHtml($form->addLabel('cli_saleptype','Others',$ptype_sale['other'],'javascript:checkAll(document.forms[0], \'sale3\');'));
	$form->addData($formData1,$_GET);
	$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
	$form->addHtml("</fieldset>\n");
	}
/////////////////////////////////////////////////////////////////////////////////
if ($cli_lettings == "Yes") {
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Lettings Requirements</div>');
	$form->addHtml($form->addLabel('cli_letptype','Houses',$ptype_let['house'],'javascript:checkAll(document.forms[0], \'let1\');'));
	$form->addHtml($form->addLabel('cli_letptype','Apartments',$ptype_let['apartment'],'javascript:checkAll(document.forms[0], \'let2\');'));
	$form->addHtml($form->addLabel('cli_letptype','Others',$ptype_let['other'],'javascript:checkAll(document.forms[0], \'let3\');'));
	$form->addData($formData2,$_GET);
	$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
	$form->addHtml("</fieldset>\n");
	}
$form->addHtml("</div>\n");

$page->setTitle("Client > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} else { // if form is submitted

// initiale new validate instance before anything to get acces to functions (i.e. array2string)
$result = new Validate();

// add any additional fields not in array (ptype, reqs, etc)
if ($cli_sales == "Yes") {
	$addFormData1 = array(
		'cli_saleptype'=>array(
			'label'=>'Property Type',
			'required'=>2,
			'value'=>array2string($_GET["cli_saleptype"],"|")
			)
		);
	}

if ($cli_lettings == "Yes") {
	$addFormData2 = array(
		'cli_letptype'=>array(
			'label'=>'Property Type',
			'required'=>2,
			'value'=>array2string($_GET["cli_letptype"],"|")
			)
		);
	}

// join the arrays
$formData = join_arrays(array($formData1,$formData2,$addFormData1,$addFormData2));

$results = $result->process($formData,$_GET);

$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
if ($stage) {
	$results['Results']['stage'] = $stage;
	}
if ($cli_id) {
	$results['Results']['cli_id'] = $cli_id;
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
$cli_id = db_query($db_data,"UPDATE","client","cli_id",$cli_id);
header("Location:?stage=3&cli_id=$cli_id");
exit;

}









break;
###########################################################
# stage 3 - areas
###########################################################
case 3:

# build data arrays
$formData1 = array(
	);

$areas = area($_GET["cli_area"]);

if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","stage","","3");
$form->addField("hidden","cli_id","",$cli_id);
/////////////////////////////////////////////////////////////////////////////////

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Areas</div>');
//$form->addHtml($form->addLabel('East Dulwich','<table><tr>'.$render[1].'</tr></table>'));

$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch1\');"><strong>East Dulwich Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[1].'</tr></table>');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch2\');"><strong>Sydenham Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[2].'</tr></table>');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch3\');"><strong>Shad Thames Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[3].'</tr></table>');
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");

$page->setTitle("Client > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

} else {


// initiale new validate instance before anything to get acces to functions (i.e. array2string)
$result = new Validate();

// add any additional fields not in array (area, etc)
$formData = array(
	'cli_area'=>array(
		'label'=>'Areas',
		'required'=>2,
		'value'=>array2string($_GET["cli_area"],"|")
		)
	);

$results = $result->process($formData,$_GET);

$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
if ($stage) {
	$results['Results']['stage'] = $stage;
	}
if ($cli_id) {
	$results['Results']['cli_id'] = $cli_id;
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
$cli_id = db_query($db_data,"UPDATE","client","cli_id",$cli_id);
header("Location:?stage=4&cli_id=$cli_id");
exit;


}





break;
###########################################################
# stage 4 -
###########################################################
case 4:

$render = '
<p>Add Client Process Complete</p>
<p>This client\'s id number is: '.$cli_id.'</p>
<p>What now?<br>
<li><a href="property_search.php?cli_id='.$cli_id.'">View properties matching this client\'s critera</a></li>
<li><a href="client_edit.php?cli_id='.$cli_id.'">Edit this client</a></li>
<li><a href="client_lookup.php">Add another new client</a></li>
<li><a href="home.php">Back to main menu</a></li>';

$page->setTitle("Client > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();


break;
###########################################################
# default
###########################################################
default:

}
?>