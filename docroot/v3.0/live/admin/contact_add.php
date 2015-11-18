<?php
/*
contact
page for adding a contact.
we have also the contact id so grab existing values (addr, tel) and suggest

new property not being added properly
*/

require_once("inx/global.inc.php");


if ($_GET["searchLink"]) {
	$searchLink = $_GET["searchLink"];
	} elseif ($_POST["searchLink"]) {
	$searchLink = $_POST["searchLink"];
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


// update exisiting contact with new company info
if ($_GET["new_com_id"]) {
	$new_com_id = $_GET["new_com_id"];

	unset($telephone);
	$sql = "SELECT com_id,com_title,com_type,com_email,com_web,
	GROUP_CONCAT(DISTINCT CONCAT(tel_id,'~',tel_number,'~',tel_type,'~',tel_ord) ORDER BY tel_ord ASC SEPARATOR '|') AS tel,
	tel.*
	FROM company
	LEFT JOIN tel ON company.com_id = tel.tel_com
	WHERE com_id = $new_com_id
	GROUP BY com_id";
	//echo $sql;
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	while ($row = $q->fetchRow()) {
		$new_com_id = $row["com_id"];
		$com_title = $row["com_title"];
		$con_type = $row["com_type"];
		$con_email = $row["con_email"];
		$con_web = $row["con_web"];
		/*
		$tel = $row["tel"];
		$telephone[] = array(
			'id'=>$row["tel_id"],
			'number'=>$row["tel_number"],
			'type'=>$row["tel_type"],
			'order'=>$row["tel_ord"]
			);
		*/
		}
	/*
	if ($tel) {
		$tel_numbers = explode("|",$tel);
		foreach($tel_numbers as $tels) {
			$tel_detail = explode("~",$tels);
			$telephone[] = array(
				'id'=>$tel_detail[0],
				'number'=>$tel_detail[1],
				'type'=>$tel_detail[2],
				'order'=>$tel_detail[3]
				);
			}
		}
		*/
	}
//print_r($telephone);


$formData1 = array(
	'con_company'=>array(
		'type'=>'text',
		'label'=>'Company',
		'value'=>$com_title,
		'attributes'=>array('style'=>'width:320px','onFocus'=>'this.select()'),
		'function'=>'format_name'
		),
	'con_type'=>array(
		'type'=>'select',
		'label'=>'Profession',
		'value'=>$con_type,
		'required'=>2,
		'attributes'=>array('style'=>'width:320px'),
		'options'=>$ctype
		),
	'con_salutation'=>array(
		'type'=>'select',
		'group'=>'Full Name',
		'label'=>'Salutation',
		'value'=>$con_salutation,
		'required'=>2,
		'options'=>join_arrays(array(array(''=>''),db_enum("contact","con_salutation","array"))),
		'attributes'=>array('style'=>'width:60px')
		),
	'con_fname'=>array(
		'type'=>'text',
		'group'=>'Full Name',
		'label'=>'Forename',
		'value'=>$con_fname,
		'init'=>'Forename(s)',
		'required'=>2,
		'attributes'=>array('style'=>'width:94px','onFocus'=>'javascript:clearField(this,\'Forename(s)\')'),
		'function'=>'format_name'
		),
	'con_sname'=>array(
		'type'=>'text',
		'group'=>'Full Name',
		'last_in_group'=>1,
		'label'=>'Surname',
		'value'=>$con_sname,
		'init'=>'Surname',
		'required'=>2,
		'attributes'=>array('style'=>'width:152px','onFocus'=>'javascript:clearField(this,\'Surname\')'),
		'function'=>'format_name'
		),
	'con_tel'=>array(
		'type'=>'tel',
		'label'=>'Telephone',
		'value'=>$telephone
		),
	'con_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$con_email,
		'required'=>3,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255),
		'tooltip'=>'Must be a valid email address'
		),
	'con_web'=>array(
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
$form->addField("hidden","cli_id","",$_GET["cli_id"]);
$form->addField("hidden","searchLink","",$searchLink);

if ($_GET["dest"] == 'inspection_add.php') {
	$form->addField("hidden","dest","",$_GET["dest"]);
	$form->addField("hidden","dea_id","",$_GET["dea_id"]);
	$form->addField("hidden","app_subtype","",$_GET["app_subtype"]);
	$form->addField("hidden","app_id","",$_GET["app_id"]);
	if ($_GET["returnTo"]) {
		$form->addField("hidden","returnTo","",$_GET["returnTo"]);
		}
	}
//$form->addHtml('<input type="hidden" name="action" value="update">');

$form->addHtml('<h1>New Contact</h1>');

/////////////////////////////////////////////////////////////////////////////////

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">New Contact</div>');
$form->addHtml('<div id="'.$formName.'">');


$form->addData($formData1,$_POST);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Address</div>');
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

$page->setTitle("New Contact");
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
new Ajax.Autocompleter("con_company","hint","ajax_contact.php",{afterUpdateElement : getSelectionId});
function getSelectionId(text, li) {
	var url = \''.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'&new_com_id=\'+li.id;
	document.location.href = url;
	}
</script>');
$page->display();


} else { // if form is submitted

//print_r($_POST);

$result = new Validate();

// extract company, see if it exactly matches an existing company, get id if it does
if ($_POST["con_company"]) {
	$sql = "SELECT com_id FROM company WHERE com_title = '".$_POST["con_company"]."'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	if ($numRows) {
		while ($row = $q->fetchRow()) {
			$_POST["con_company"] = $row["com_id"];
			}
		} else {
		$forward_company = '1';
		}
	}


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
	$redirect .= 'con_id='.$con_id;
	}
if ($viewForm) {
	$redirect .= '&viewForm='.$viewForm;
	}
if ($searchLink) {
	$redirect .= '&searchLink='.urlencode($searchLink);
	}
if ($_POST["returnTo"]) {
	$redirect .= '&returnTo='.$_POST["returnTo"];
	}
if ($results['Errors'] || $errors) {
	if (is_array($results['Results'])) {
		$redirect .= '&'.http_build_query($results['Results']);
		}
	echo error_message(join_arrays(array($results['Errors'],$errors)),urlencode($redirect));
	exit;
	}

$con_id = db_query($db_data,"INSERT","contact","con_id");
unset($db_data);


// pro_pro_id means successful ajax postcode lookup, and property is stored.
if ($_POST["pro_pro_id"]) {
	$_POST["pro_id"] = $_POST["pro_pro_id"];
	}
if ($_POST["pro_id"]) {
	$pro_id = $_POST["pro_id"];
	$db_data["p2c_pro"] = $pro_id;
	$db_data["p2c_con"] = $con_id;
	$db_data["p2c_type"] = $_POST["p2c_type"];
	// check to prevent duplicates
	$sql = "SELECT p2c_pro,p2c_con,p2c_type
	FROM pro2con
	WHERE p2c_pro = '$pro_id' AND p2c_con = '".$con_id."' AND p2c_type = '".$_POST["p2c_type"]."'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	if (!$q->numRows()) {
		db_query($db_data,"INSERT","pro2con","p2c_id");
		}

	// if client has not default address, make the above property it
	$sql = "SELECT con_pro FROM contact WHERE con_id = '".$con_id."'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		if ($row["con_pro"] == 0) {
			$db_dataD["con_pro"] = $pro_id;
			db_query($db_dataD,"UPDATE","contact","con_id",$con_id);
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
		$redirect .= 'con_id='.$con_id;
		}
	if ($viewForm) {
		$redirect .= '&viewForm='.$viewForm;
		}
	if ($searchLink) {
		$redirect .= '&searchLink='.urlencode($searchLink);
		}
	if ($_POST["returnTo"]) {
		$redirect .= '&returnTo='.$_POST["returnTo"];
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
		$db_data2["p2c_con"] = $con_id;
		$db_data2["p2c_pro"] = $pro_id;
		$db_data2["p2c_type"] = $_POST["p2c_type"];
		db_query($db_data2,"INSERT","pro2con","p2c_id");

		// if client has not default address, make the above property it
		$sql = "SELECT con_pro FROM contact WHERE con_id = '".$con_id."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			if ($row["con_pro"] == 0) {
				$db_dataD["con_pro"] = $pro_id;
				db_query($db_dataD,"UPDATE","contact","con_id",$con_id);
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
			'tel_con'=>$con_id,
			'tel_ord'=>1
			);
		db_query($db_data_tel,"INSERT","tel","tel_id");
		}
	}

unset($db_data);

/*
if ($_POST["dest"] == 'inspection_add.php') {
	header("Location:inspection_add.php?stage=appointment&con_id=$con_id&dea_id=".$_POST["dea_id"]."&app_subtype=".$_POST["app_subtype"]."&app_id=".$_POST["app_id"]);
	exit;
	}
*/

// if cli_id is present, ie we are adding a new contact (solicitor) and likning to client (cli_solicitor)
if ($_POST["cli_id"]) {
	$db_data2["cli_solicitor"] = $con_id;
	db_query($db_data2,"UPDATE","client","cli_id",$_POST["cli_id"]);
	header("Location:client_edit.php?cli_id=".$_POST["cli_id"]."&viewForm=8");
	exit;
	}


// if specified company is not found, forward to company add page.
// carry with the contact id, so we can suggest contact's address, phone etc is the same for company...
if ($forward_company == '1') {
	header("Location:company_add.php?con_id=$con_id&com_title=".urlencode($_POST["con_company"])."&returnTo=".urlencode('inspection_add.php?stage=appointment&dea_id='.$_POST["dea_id"].'&app_id=&app_subtype='.$_POST["app_subtype"].'&con_id='.$con_id));
	exit;
	}


$redirect = "contact_edit.php?con_id=$con_id";
if ($_POST["returnTo"]) {
$redirect = 'inspection_add.php?stage=appointment&dea_id='.$_POST["dea_id"].'&app_id=&app_subtype='.$_POST["app_subtype"].'&con_id='.$con_id;
}
header("Location:$redirect");
exit;


}


?>