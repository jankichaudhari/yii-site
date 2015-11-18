<?php
/*
company
page for adding a company.
we have also the contact id so grab existing values (addr, tel) and suggest

new property not being added properly
*/

require_once("inx/global.inc.php");


if ($_GET["searchLink"]) {
	$searchLink = $_GET["searchLink"];
	} elseif ($_POST["searchLink"]) {
	$searchLink = $_POST["searchLink"];
	}

if ($_GET["con_id"]) {
	$sql = "SELECT
	contact.*,CONCAT(con_fname,' ',con_sname) AS con_name,pro_id,
	CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
	tel_number
	FROM contact
	LEFT JOIN pro2con ON pro2con.p2c_con = contact.con_id
	LEFT JOIN property ON pro2con.p2c_pro = property.pro_id
	LEFT JOIN tel ON contact.con_id = tel.tel_con AND tel_ord = 1
	WHERE con_id = ".$_GET["con_id"];
	//echo $sql;
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {

		$con_name = $row["con_name"];
		$con_type = $row["con_type"];
		$pro_addr = $row["pro_addr"];
		$pro_id = $row["pro_id"];
		$telephone = $row["tel_number"];

		if ($row["pro_id"]) {
			$associated_property[] = $row["pro_id"];
			$render .= '
			<tr>
			<td><label for="'.$row["pro_id"].'"><input type="radio" name="pro_id" value="'.$row["pro_id"].'" id="'.$row["pro_id"].'" onClick="document.forms[0].submit();">
			'.$row["pro_addr"].'</label></td>
			</tr>';
			}
		}
	}


#overwrite database values with POST values (probably empty)
foreach ($_POST AS $key=>$val) {
	$$key = $val;
	}
#overwrite database values with GET values when returning from error message
foreach ($_GET AS $key=>$val) {
	$$key = $val;
	}



$sql = "SELECT * FROM ctype ORDER BY cty_title";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	$ctype[$row["cty_id"]] = $row["cty_title"];
	}


$formData1 = array(
	'com_title'=>array(
		'type'=>'text',
		'label'=>'Company Name',
		'value'=>format_name($com_title),
		'required'=>2,
		'attributes'=>array('style'=>'width:320px'),
		'function'=>'format_name'
		),
	'com_type'=>array(
		'type'=>'select',
		'label'=>'Business Type',
		'value'=>$con_type,
		'required'=>2,
		'attributes'=>array('style'=>'width:320px'),
		'options'=>$ctype
		),
	'com_tel'=>array(
		'type'=>'tel',
		'label'=>'Telephone',
		'value'=>$telephone
		),
	'com_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$con_email,
		'required'=>3,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255),
		'tooltip'=>'Must be a valid email address'
		),
	'com_web'=>array(
		'type'=>'text',
		'label'=>'Website',
		'value'=>$con_web,
		'init'=>'http://',
		'required'=>1,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255)
		)
	)
	;

// address, this is only used for manual input resulting from ajax input (validation only)
$formData2 = array(
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
if (!$_POST["action"]) {

// start new form object
$form = new Form();

$form->addForm("testForm","post",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","con_id","",$con_id);
$form->addField("hidden","searchLink","",$searchLink);
$form->addField("hidden","returnTo","",$_GET["returnTo"]);
//$form->addHtml('<input type="hidden" name="action" value="update">');

$form->addHtml('<h1>New Company for '.$con_name.'</h1>');

/////////////////////////////////////////////////////////////////////////////////

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">New Company</div>');
$form->addHtml('<div id="'.$formName.'">');


$form->addData($formData1,$_POST);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Address</div>');
if ($associated_property) {
	$form->addHtml($form->addLabel("existing","Business Address",'<table cellspacing="0" cellpadding="2">'.$render.'</table>
	<div id="inset"><p>If the business address is not listed above, <br>please complete the following form</p></div>'));
	}
$form->ajaxPostcode("by_freetext","pro");
$form->addField("hidden","p2c_type","",'Work');

$form->addHtml("</fieldset>\n");



// start a new page
$page = new HTML_Page2($page_defaults);


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$searchLink),
	'search'=>array('title'=>'Contact Search','label'=>'Contact Search','link'=>'contact.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("New Company");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
$page->addScriptDeclaration($additional_js);
$page->setBodyAttributes(array('onLoad'=>$onLoad)); //,'onKeyPress'=>'keyPressShowDiv(event.keyCode)'
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');

$page->addBodyContent('<div id="hint"></div><script type="text/javascript">
new Ajax.Autocompleter("com_title","hint","ajax_company.php");
</script>');
$page->display();


} else { // if form is submitted

//print_r($_POST);

$result = new Validate();

if ($_POST["telnew"]) {
	if (!phone_validate($_POST["telnew"])) {
		$errors[] = 'Please enter a valid phone number';
		}
	}

$results = $result->process($formData1,$_POST);
$db_data = $results['Results'];


// build return link
$redirect = $_SERVER['SCRIPT_NAME'].'?';
if ($con_id) {
	$redirect .= 'com_id='.$com_id;
	}
if ($viewForm) {
	$redirect .= '&viewForm='.$viewForm;
	}
if ($searchLink) {
	$redirect .= '&searchLink='.urlencode($searchLink);
	}
if ($results['Errors'] || $errors) {
	if (is_array($results['Results'])) {
		$redirect .= '&'.http_build_query($results['Results']);
		}
	echo error_message(join_arrays(array($results['Errors'],$errors)),urlencode($redirect));
	exit;
	}

$com_id = db_query($db_data,"INSERT","company","com_id");
unset($db_data);


// pro_pro_id means successful ajax postcode lookup, and property is stored.
if ($_POST["pro_pro_id"]) {
	$_POST["pro_id"] = $_POST["pro_pro_id"];
	}
if ($_POST["pro_id"]) {
	$pro_id = $_POST["pro_id"];
	$db_data["p2c_pro"] = $pro_id;
	$db_data["p2c_com"] = $com_id;
	$db_data["p2c_type"] = $_POST["p2c_type"];
	// check to prevent duplicates
	$sql = "SELECT p2c_pro,p2c_com,p2c_type
	FROM pro2com
	WHERE p2c_pro = '$pro_id' AND p2c_com = '".$com_id."' AND p2c_type = '".$_POST["p2c_type"]."'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	if (!$q->numRows()) {
		db_query($db_data,"INSERT","pro2com","p2c_id");
		}

	// if client has not default address, make the above property it
	$sql = "SELECT com_pro FROM company WHERE com_id = '".$com_id."'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		if ($row["com_pro"] == 0) {
			$db_dataD["com_pro"] = $pro_id;
			db_query($db_dataD,"UPDATE","company","com_id",$com_id);
			}
		}



} else {
	// if the manual input form is used, put values into array and insert into property table
	// all manual entries are inserted with -1 as pcid, and should be checked by admin until a script does it automatically
	$results = $result->process($formData2,$_POST);
	$db_data = $results['Results'];

	// build return link
	$redirect = $_SERVER['SCRIPT_NAME'].'?';
	if ($con_id) {
		$redirect .= 'com_id='.$com_id;
		}
	if ($viewForm) {
		$redirect .= '&viewForm='.$viewForm;
		}
	if ($searchLink) {
		$redirect .= '&searchLink='.urlencode($searchLink);
		}
	if ($results['Errors']) {
		if (is_array($results['Results'])) {
			$redirect .= '&'.http_build_query($results['Results']);
			}
		//echo error_message($results['Errors'],urlencode($redirect));
		//exit;
		}
	else {

	// here, in fuure, we should check table for existing properties to prevent duplicates
	$db_data["pro_pcid"] = '-1';
	$pro_id = db_query($db_data,"INSERT","property","pro_id");

	// insert into pro2con table linkage
	$db_data2["p2c_com"] = $com_id;
	$db_data2["p2c_pro"] = $pro_id;
	$db_data2["p2c_type"] = $_POST["p2c_type"];
	db_query($db_data2,"INSERT","pro2com","p2c_id");

	// if client has not default address, make the above property it
	$sql = "SELECT com_pro FROM company WHERE com_id = '".$com_id."'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		if ($row["com_pro"] == 0) {
			$db_dataD["com_pro"] = $pro_id;
			db_query($db_dataD,"UPDATE","company","com_id",$com_id);
			}
		}
		}
	$msg = urlencode('Update Successful');
	//header("Location:$redirect&msg=$msg");
	//exit;
	}


// add telephone
if ($_POST["telnew"]) {
	if (phone_validate($_POST["telnew"])) {
		$db_data_tel = array(
			'tel_number'=>phone_format($_POST["telnew"]),
			'tel_type'=>$_POST["telnewtype"],
			'tel_com'=>$com_id,
			'tel_ord'=>1
			);
		db_query($db_data_tel,"INSERT","tel","tel_id");
		}
	}

unset($db_data);
if ($con_id) {
	// now update client with this company
	$db_data["con_company"] = $com_id;
	db_query($db_data,"UPDATE","contact","con_id",$con_id);
	$redirect = "contact_edit.php?con_id=$con_id";
	} else {
	$redirect = "company_edit.php?com_id=$com_id";
	}

$msg = urlencode('Update Successful');

if ($_POST["returnTo"]) {
	$redirect = urldecode($_POST["returnTo"]);
	}
header("Location:$redirect&msg=$msg");
exit;


}


?>