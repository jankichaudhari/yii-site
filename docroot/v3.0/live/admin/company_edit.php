<?php
/*

company
page for editing companies


*/


require_once("inx/global.inc.php");

// get existing values
if ($_GET["com_id"]){
	$com_id = $_GET["com_id"];
	} elseif ($_POST["com_id"]) {
	$com_id = $_POST["com_id"];
	} else {
	echo 'no com_id';
	exit;
	}

if ($_GET["searchLink"]) {
	$searchLink = $_GET["searchLink"];
	} elseif ($_POST["searchLink"]) {
	$searchLink = $_POST["searchLink"];
	}

if ($_POST["viewForm"]) {
	$viewForm = $_POST["viewForm"];
	} elseif ($_GET["viewForm"]) {
	$viewForm = $_GET["viewForm"];
	} else {
	$viewForm = 0;
	}



if ($_GET["do"] == "addr_default") {
	$db_data["com_pro"] = $_GET["pro_id"];
	db_query($db_data,"UPDATE","company","com_id",$com_id);
	header("Location:".$_GET["return"]."&viewForm=".str_replace('form','',$_GET["viewForm"]));
	}
elseif ($_GET["do"] == "addr_delete") {
	$sql = "DELETE FROM pro2com WHERE p2c_com = '".$com_id."' AND p2c_pro = '".$_GET["pro_id"]."' LIMIT 1";
	$q = $db->query($sql);
	unset($sql);
	header("Location:".$_GET["return"]."&viewForm=".str_replace('form','',$_GET["viewForm"]));
	}


$properties = array();
$default_property = array();
$sql = "SELECT
company.*,DATE_FORMAT(com_created, '%D %M %Y') AS com_created,
pro_id,pro_addr1,pro_addr2,pro_addr3,pro_addr4,pro_addr5,pro_postcode,pro_pcid,
p2c_id, p2c_pro,p2c_type,
cty_title,
GROUP_CONCAT(DISTINCT CONCAT(tel_id,'~',tel_number,'~',tel_type,'~',tel_ord) ORDER BY tel_ord ASC SEPARATOR '|') AS tel
FROM company
LEFT JOIN pro2com ON pro2com.p2c_com = company.com_id
LEFT JOIN property ON pro2com.p2c_pro = property.pro_id
LEFT JOIN tel ON company.com_id = tel.tel_com
LEFT JOIN ctype ON company.com_type = ctype.cty_id
WHERE com_id = '".$com_id."'
GROUP BY pro_id";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();

if ($numRows == 0) {
	echo "no rows";
	exit;
} else {
	while ($row = $q->fetchRow()) {

		foreach($row as $key=>$val) {
			$$key = $val;
			}

		// put all associated properties into an array, with default at the top
		if ($row["pro_id"] == $row["com_pro"]) {

			// used in summary page, shows defaiult address only
			$pro_addr = $row["pro_addr1"].' '.$row["pro_addr2"].' '.$row["pro_addr3"].' '.$row["pro_postcode"];

			$default_property = array(
				'pro_addr1'=>$row["pro_addr1"],
				'pro_addr2'=>$row["pro_addr2"],
				'pro_addr3'=>$row["pro_addr3"],
				'pro_addr4'=>$row["pro_addr4"],
				'pro_addr5'=>$row["pro_addr5"],
				'pro_postcode'=>$row["pro_postcode"],
				'pro_pcid'=>$row["pro_pcid"],
				'p2c_type'=>$row["p2c_type"],
				'p2c_id'=>$row["p2c_id"],
				'p2c_pro'=>$row["p2c_pro"]
				);

			} else {

			$properties[$row["p2c_id"]] = array(
				'pro_addr1'=>$row["pro_addr1"],
				'pro_addr2'=>$row["pro_addr2"],
				'pro_addr3'=>$row["pro_addr3"],
				'pro_addr4'=>$row["pro_addr4"],
				'pro_addr5'=>$row["pro_addr5"],
				'pro_postcode'=>$row["pro_postcode"],
				'pro_pcid'=>$row["pro_pcid"],
				'p2c_type'=>$row["p2c_type"],
				'p2c_id'=>$row["p2c_id"],
				'p2c_pro'=>$row["p2c_pro"]
				);
			}
		}
	}

// put the default address (as defined in the com_pro row) on top of the array of properties

array_unshift($properties,$default_property);

// get the tels into an array ready for the form
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

		if (!$main_tel) {
			$main_tel = $tel_detail[1];
			}
		elseif ($tel_detail[2] == 'Fax') {
			$fax = $tel_detail[1];
			}
		elseif ($tel_detail[2] == 'DX') {
			$DX = $tel_detail[1];
			}
		}
	}


// make properties table

foreach ($properties AS $property_id => $property_addr) {
	if ($property_addr["p2c_pro"]) {
		// the default property
		if ($com_pro == $property_addr["p2c_pro"]) {
			$render_addresses .= '<tr>
		<td><strong>'.$property_addr["pro_addr1"].' '.$property_addr["pro_addr2"].' '.$property_addr["pro_addr3"].' '.$property_addr["pro_addr4"].' '.$property_addr["pro_addr5"].' '.$property_addr["pro_postcode"].'</strong> ('.$property_addr["p2c_type"].')</td>
		<td colspan="2" width="32">(default)</td>
		</tr>';
			} else {
			$render_addresses .= '<tr>
		<td>'.$property_addr["pro_addr1"].' '.$property_addr["pro_addr2"].' '.$property_addr["pro_addr3"].' '.$property_addr["pro_addr4"].' '.$property_addr["pro_addr5"].' '.$property_addr["pro_postcode"].' ('.$property_addr["p2c_type"].')</td>
		<td width="16"><a href="?do=addr_default&com_id='.$com_id.'&pro_id='.$property_addr["p2c_pro"].'&return='.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'&viewForm=2"><img src="/images/sys/admin/icons/tick.gif" width="16" height="16" border="0" alt="Make default" /></a></td>
		<td width="16"><a href="javascript:confirmDelete(\'Are you sure you want to delete this address?\',\'?do=addr_delete&com_id='.$com_id.'&pro_id='.$property_addr["p2c_pro"].'&return='.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'&viewForm=2\');"><img src="/images/sys/admin/icons/cross-icon.png" width="16" height="16" border="0" alt="Delete" /></a></td>
		</tr>';
			}
		}
	}
if ($render_addresses) {
$render_addresses = '<table width="95%" cellpadding="3" cellspacing="2" align="center">'.$render_addresses.'<tr><td colspan="3"><hr></td></tr></table>';
}


// get employees (may be able to incorporate into frist sql?
$sql = "SELECT
con_id,CONCAT(con_fname,' ',con_sname) AS con_name,cty_title,tel_number,tel_type
FROM contact
LEFT JOIN ctype ON contact.con_type = ctype.cty_id
LEFT JOIN tel ON contact.con_id = tel.tel_con
WHERE con_company = $com_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numEmployees = $q->numRows();
while ($row = $q->fetchRow()) {
	if ($row["tel_number"]) {
		$tel = $row["tel_number"].' ('.$row["tel_type"].')';
		} else {
		$tel = '';
		}
	$render_employees .= '
  <tr style="padding:3px 0px 3px 0px;">
	<td><a href="contact_edit.php?con_id='.$row["con_id"].'">'.$row["con_name"].'</a></td>
	<td>'.$tel.'</td>
  </tr>';
	}

$render_employees = '<table cellpadding="0" cellspacing="0" width="100%">
'.$render_employees.'
  <tr style="padding:3px 0px 3px 0px;">
    <td colspan="2"><input type="button" value="Add New Employee" class="button" onClick="document.location.href = \'contact_add.php?new_com_id='.$com_id.'\';"></td>
  </tr>
</table>';


// summary table
$render_summary = '
<table cellpadding="2" cellspacing="2" border="0" width="100%">
  <tr>
    <td class="label">Company</td>
	<td>'.$com_title.'</td>
  </tr>
  <tr>
    <td class="label">Business Type</td>
	<td>'.$cty_title.'</td>
  </tr>
  <tr>
    <td class="label">Address</td>
	<td>'.$pro_addr.'</td>
  </tr>
  <tr>
    <td class="label">Telephone</td>
	<td>'.$main_tel.'</td>
  </tr>';
  if ($fax) {
  $render_summary .= '
  <tr>
    <td class="label">Fax</td>
	<td>'.$fax.'</td>
  </tr>';
  }
  if ($DX) {
  $render_summary .= '
  <tr>
    <td class="label">DX</td>
	<td>'.$DX.'</td>
  </tr>';
  }
  if ($com_email) {
  $render_summary .= '
  <tr>
    <td class="label">Email</td>
	<td><a href="mailto:'.$com_email.'">'.$com_email.'</a></td>
  </tr>';
  }
  if ($com_web) {
  $render_summary .= '
  <tr>
    <td class="label">Website</td>
	<td><a href="'.$com_web.'" target="_blank">'.$com_web.'</a></td>
  </tr>';
  }
  $render_summary .= '
  <tr>
    <td colspan="2"><hr /></td>
  </tr>
  <tr>
    <td class="label" valign="top">Employees</td>
	<td>'.$render_employees.'</td>
  </tr>
</table>';

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



$form1 = array(
	'com_type'=>array(
		'type'=>'select',
		'label'=>'Business Type',
		'value'=>$com_type,
		'required'=>2,
		'attributes'=>array('style'=>'width:320px'),
		'options'=>$ctype
		),
	'com_title'=>array(
		'type'=>'text',
		'label'=>'Company Name',
		'value'=>$com_title,
		'attributes'=>array('style'=>'width:320px'),
		'function'=>'format_name'
		),
	'com_tel'=>array(
		'type'=>'tel',
		'label'=>'Telephone',
		'value'=>$telephone
		),
	'com_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$com_email,
		'required'=>3,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255),
		'tooltip'=>'Must be a valid email address'
		),
	'com_web'=>array(
		'type'=>'text',
		'label'=>'Website',
		'value'=>$com_web,
		'init'=>'http://',
		'required'=>1,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255)
		)
	)
	;

// address, this is only used for manual input resulting from ajax input (validation only)
$form2 = array(
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
$form->addField("hidden","com_id","",$com_id);
$form->addField("hidden","searchLink","",$searchLink);
//$form->addHtml('<input type="hidden" name="action" value="update">');

$form->addHtml('<h1>'.$com_title.'</h1>');

/////////////////////////////////////////////////////////////////////////////////

$formName = 'form0';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Summary</div>');
$form->addHtml('<div id="' . $formName . '">');

$form->addHtml($render_summary);
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");


$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Edit</div>');
$form->addHtml('<div id="' . $formName . '">');
$form->addData($$formName,$_POST);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////


$formName = 'form2';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Address</div>');
$form->addHtml('<div id="' . $formName . '">');

// add address table
$form->addHtml($render_addresses);

// add new address
$form->addField("hidden","p2c_type","",'Work');
$form->ajaxPostcode("by_freetext","pro");

$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");



$onLoad .= 'showForm('.$viewForm.');self.focus; ';

// start a new page
$page = new HTML_Page2($page_defaults);

$additional_js = '
if (!previousID) {
	var previousID = "form'.$viewForm.'";
	}

';
if ($_GET["msg"]) {
	$onLoad .= 'javascript:hideMsg();';
	$msg = '
	<script type="text/javascript" language="javascript">
	<!--
	function hideMsg(){
		setTimeout("hideMsgDiv()",1500);
		}
	function hideMsgDiv() {
		new Effect.Fade("floating_message");
		}
	-->
	</script><div id="notify"><div id="floating_message">'.urldecode($_GET["msg"]).'</div></div>';
	}

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$searchLink),
	'search'=>array('title'=>'Contact Search','label'=>'Contact Search','link'=>'contact.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Company");
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
if ($msg) {
	$page->addBodyContent($msg);
	}
$page->display();


/*
echo '
<script language="JavaScript" type="text/javascript">
//You should create the validator only after the definition of the HTML form

  var frmvalidator  = new Validator("testForm");
  frmvalidator.addValidation("com_fname","req","Forename(s) is required");

  frmvalidator.addValidation("com_sname","req","Surname is required");

  frmvalidator.addValidation("com_email","req","Email is required");
  frmvalidator.addValidation("com_email","email","Email is invalid");

</script>';
*/

} else { // if form is submitted




$result = new Validate();

// validate the appropriate data array+form combintation, except for form2 (addresses) which is dealt with separately

if ($_POST["form1"]) {
	$fields = $form1;
	$viewForm = 1;



	// check if existing phone numbers have been changed and update, do not allow blanks
	if ($telephone) {
		foreach($telephone as $key=>$val) {
			$tel_count = ($key+1);
			if (($_POST["tel".$tel_count] !== $val["number"] || $_POST["tel".$tel_count."type"] !== $val["type"]) && trim($_POST["tel".$tel_count])) {
				$db_data['tel_number'] = phone_format($_POST["tel".$tel_count]);
				$db_data['tel_type'] = $_POST["tel".$tel_count."type"];
				$db_data['tel_com'] = $com_id;
				db_query($db_data,"UPDATE","tel","tel_id",$val['id']);
				}
			}
		}

	// check if new phone has been entrered
	// new phones perhaps should go at the top, reordering the rest down by 1 ? for the time being, they are put at the bottom
	if ($telephone) {
		$ord = (count($telephone)+1);
		} else {
		$ord = 1;
		}
	if ($_POST["telnew"]) {
		if (phone_validate($_POST["telnew"])) {
			$db_data = array(
				'tel_number'=>phone_format($_POST["telnew"]),
				'tel_type'=>$_POST["telnewtype"],
				'tel_com'=>$com_id,
				'tel_ord'=>$ord
				);
			db_query($db_data,"INSERT","tel","tel_id");
			unset($db_data);
			} else {
			$errors[] = 'Please enter a valid phone number';
			}
		}



	}


elseif ($_POST["form2"]) {
	$viewForm = 2;
	// addresses from postcode lookup will already be stored in table, and will provide pro_pro_id
	// this needs to be stored in the link table pro2cli
	if ($_POST["pro_pro_id"]) {
		$pro_id = $_POST["pro_pro_id"];
		$db_data["p2c_pro"] = $pro_id;
		$db_data["p2c_com"] = $_POST["com_id"];
		$db_data["p2c_type"] = $_POST["p2c_type"];
		// check to prevent duplicates
		$sql = "SELECT p2c_pro,p2c_com,p2c_type
		FROM pro2com
		WHERE p2c_pro = '$pro_id' AND p2c_com = '".$_POST["com_id"]."' AND p2c_type = '".$_POST["p2c_type"]."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		if (!$q->numRows()) {
			db_query($db_data,"INSERT","pro2com","p2c_id");
			}

		// if client has not default address, make the above property it
		$sql = "SELECT com_pro FROM company WHERE com_id = '".$_POST["com_id"]."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			if ($row["com_pro"] == 0) {
				$db_dataD["com_pro"] = $pro_id;
				db_query($db_dataD,"UPDATE","company","com_id",$_POST["com_id"]);
				}
			}



	} else {
		// if the manual input form is used, put values into array and insert into property table
		// all manual entries are inserted with -1 as pcid, and should be checked by admin until a script does it automatically
		$results = $result->process($form2,$_POST);
		$db_data = $results['Results'];

		// build return link
		$redirect = $_SERVER['SCRIPT_NAME'].'?';
		if ($com_id) {
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
			echo error_message($results['Errors'],urlencode($redirect));
			exit;
			}

		// here, in fuure, we should check table for existing properties to prevent duplicates
		$db_data["pro_pcid"] = '-1';
		$pro_id = db_query($db_data,"INSERT","property","pro_id");

		// insert into pro2com table linkage
		$db_data2["p2c_com"] = $_POST["com_id"];
		$db_data2["p2c_pro"] = $pro_id;
		$db_data2["p2c_type"] = $_POST["p2c_type"];
		db_query($db_data2,"INSERT","pro2com","p2c_id");

		// if client has not default address, make the above property it
		$sql = "SELECT com_pro FROM company WHERE com_id = '".$_POST["com_id"]."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			if ($row["com_pro"] == 0) {
				$db_dataD["com_pro"] = $pro_id;
				db_query($db_dataD,"UPDATE","company","com_id",$_POST["com_id"]);
				}
			}

		$msg = urlencode('Update Successful');
		header("Location:$redirect&msg=$msg");
		exit;
		}
	}

if ($viewForm !== 2 && $viewForm !== 7) {

$results = $result->process($fields,$_POST);
$db_data = $results['Results'];
}


// build return link
$redirect = $_SERVER['SCRIPT_NAME'].'?';
if ($com_id) {
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


if ($viewForm !== 2 && $viewForm !== 7) {
db_query($db_data,"UPDATE","company","com_id",$com_id);
}



$msg = urlencode('Update Successful');
header("Location:$redirect&msg=$msg");
exit;


}


?>