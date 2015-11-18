<?php
require_once("inx/global.inc.php");

// user self-edit page


$page = new HTML_Page2($page_defaults);


$use_id = $_SESSION["auth"]["use_id"];



if ($_GET["do"] == "addr_default") {
	$db_data["use_pro"] = $_GET["pro_id"];
	db_query($db_data,"UPDATE","user","use_id",$use_id);
	header("Location:".$_GET["return"]."&viewForm=".str_replace('form','',$_GET["viewForm"]));
	}
elseif ($_GET["do"] == "addr_delete") {
	$sql = "DELETE FROM pro2use WHERE p2u_use = '".$use_id."' AND p2u_pro = '".$use_id."' LIMIT 1";
	$q = $db->query($sql);
	unset($sql);
	header("Location:".$_GET["return"]."&use_id=".$use_id."&viewForm=".str_replace('form','',$_GET["viewForm"]));
	}


if ($_POST["viewForm"]) {
	$viewForm = $_POST["viewForm"];
	} elseif ($_GET["viewForm"]) {
	$viewForm = $_GET["viewForm"];
	} else {
	$viewForm = 1;
	}



$properties = array();
$default_property = array();


$sql = "SELECT
user.*,
pro_id,pro_addr1,pro_addr2,pro_addr3,pro_addr4,pro_addr5,pro_postcode,pro_pcid,
p2u_id, p2u_type, p2u_pro,
rol_id,rol_title
FROM user
LEFT JOIN pro2use ON pro2use.p2u_use = user.use_id
LEFT JOIN property ON pro2use.p2u_pro = property.pro_id
LEFT JOIN link_user_to_role ON user.use_id = link_user_to_role.u2r_use
LEFT JOIN role ON link_user_to_role.u2r_rol = role.rol_id
WHERE use_id = ".$use_id."";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		$current_roles[$row["rol_id"]] = $row["rol_title"];
		foreach($row as $key=>$val) {
			$$key = $val;
			}

		// put all associated properties into an array, with default at the top
	if ($row["pro_id"] == $row["use_pro"]) {

		$default_property = array(
			'pro_addr1'=>$row["pro_addr1"],
			'pro_addr2'=>$row["pro_addr2"],
			'pro_addr3'=>$row["pro_addr3"],
			'pro_addr4'=>$row["pro_addr4"],
			'pro_addr5'=>$row["pro_addr5"],
			'pro_postcode'=>$row["pro_postcode"],
			'pro_pcid'=>$row["pro_pcid"],
			'p2u_type'=>$row["p2u_type"],
			'p2u_id'=>$row["p2u_id"],
			'p2u_pro'=>$row["p2u_pro"]
			);

		} else {

		$properties[$row["p2u_id"]] = array(
			'pro_addr1'=>$row["pro_addr1"],
			'pro_addr2'=>$row["pro_addr2"],
			'pro_addr3'=>$row["pro_addr3"],
			'pro_addr4'=>$row["pro_addr4"],
			'pro_addr5'=>$row["pro_addr5"],
			'pro_postcode'=>$row["pro_postcode"],
			'pro_pcid'=>$row["pro_pcid"],
			'p2u_type'=>$row["p2u_type"],
			'p2u_id'=>$row["p2u_id"],
			'p2u_pro'=>$row["p2u_pro"]
			);
		}


		}
	}


// put the default address (as defined in the use_pro row) on top of the array of properties
array_unshift($properties,$default_property);


// make properties table

foreach ($properties AS $property_id => $property_addr) {
	if ($property_addr["p2u_pro"]) {
		// the default property
		if ($use_pro == $property_addr["p2u_pro"]) {
			$render_addresses .= '<tr>
		<td><strong>'.$property_addr["pro_addr1"].' '.$property_addr["pro_addr2"].' '.$property_addr["pro_addr3"].' '.$property_addr["pro_addr4"].' '.$property_addr["pro_addr5"].' '.$property_addr["pro_postcode"].'</strong> ('.$property_addr["p2u_type"].')</td>
		<td colspan="2" width="32">(default)</td>
		</tr>';
			} else {
			$render_addresses .= '<tr>
		<td>'.$property_addr["pro_addr1"].' '.$property_addr["pro_addr2"].' '.$property_addr["pro_addr3"].' '.$property_addr["pro_addr4"].' '.$property_addr["pro_addr5"].' '.$property_addr["pro_postcode"].' ('.$property_addr["p2u_type"].')</td>
		<td width="16"><a href="?do=addr_default&stage=2&viewForm=2&use_id='.$use_id.'&pro_id='.$property_addr["p2u_pro"].'&return='.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'&viewForm=2"><img src="/images/sys/admin/icons/tick.gif" width="16" height="16" border="0" alt="Make default" /></a></td>
		<td width="16"><a href="javascript:confirmDelete(\'Are you sure you want to delete this address?\',\'?do=addr_delete&stage=2&viewForm=2&use_id='.$use_id.'&pro_id='.$property_addr["p2u_pro"].'&return='.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'&viewForm=2\');"><img src="/images/sys/admin/icons/cross-icon.png" width="16" height="16" border="0" alt="Delete" /></a></td>
		</tr>';
			}
		}
	}
if ($render_addresses) {
$render_addresses = '<table width="95%" cellpadding="3" cellspacing="2" align="center">'.$render_addresses.'<tr><td colspan="3"><hr></td></tr></table>';
}


// create roles table
$render_role = '<label for="role" class="formLabel" id="label">Role(s)</label>
<div class="inset">';
$sql = "SELECT * FROM role";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
while ($row = $q->fetchRow()) {

	$all_roles[$row["rol_id"]] = $row["rol_title"];

	$render_role .= '<label for="role'.$row["rol_id"].'"><input type="checkbox" name="role['.$row["rol_id"].']" id="role'.$row["rol_id"].'" value="'.$row["rol_title"].'"';
	if ($current_roles) {
		if (array_key_exists($row["rol_id"],$current_roles)) {
			$render_role .= ' checked';
			}
		}
	$render_role .= ' />'.$row["rol_title"].'</label><br />'."\n";
	}

$render_role .= '</div>';

/*
#overwrite database values with POST values (probably empty)
foreach ($_POST AS $key=>$val) {
	$$key = $val;
	}
#overwrite database values with GET values when returning from error message
foreach ($_GET AS $key=>$val) {
	$$key = $val;
	}
*/
$form1 = array(
	'use_salutation'=>array(
		'type'=>'select',
		'group'=>'Full Name',
		'label'=>'Salutation',
		'value'=>$use_salutation,
		'required'=>2,
		'options'=>db_enum("user","use_salutation","array"),
		'attributes'=>array('style'=>'width:60px')
		),
	'use_fname'=>array(
		'type'=>'text',
		'group'=>'Full Name',
		'label'=>'Forename',
		'value'=>$use_fname,
		'default'=>'Forename(s)',
		'required'=>2,
		'attributes'=>array('style'=>'width:100px','onFocus'=>'javascript:clearField(this,\'Forename(s)\')'),
		'function'=>'format_name'
		),
	'use_sname'=>array(
		'type'=>'text',
		'group'=>'Full Name',
		'last_in_group'=>1,
		'label'=>'Surname',
		'value'=>$use_sname,
		'default'=>'Surname',
		'required'=>2,
		'attributes'=>array('style'=>'width:152px','onFocus'=>'javascript:clearField(this,\'Surname\')'),
		'function'=>'format_name'
		),
	'use_ext'=>array(
		'type'=>'text',
		'label'=>'Extension',
		'value'=>$use_ext,
		'required'=>1,
		'attributes'=>array('class'=>'medium')
		),
	'use_mobile'=>array(
		'type'=>'text',
		'label'=>'Mobile',
		'value'=>$use_mobile,
		'attributes'=>array('class'=>'medium')
		),
	'use_hometel'=>array(
		'type'=>'text',
		'label'=>'Home Tel',
		'value'=>$use_hometel,
		'attributes'=>array('class'=>'medium')
		)
	);

$form2 = array(
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
		'options'=>db_lookup("use_country","country","array"),
		'attributes'=>array('class'=>'addr')
		)
	)
	;


if (!$_POST["action"]) {


$form = new Form();
$form->addForm("form","post",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","searchLink","",$_GET["searchLink"]);


/////////////////////////////////////////////////////////////////////////////////

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">User</div>');
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
$form->addRow('radio','p2u_type','Type','Home','',db_enum("pro2use","p2u_type","array"));
$form->ajaxPostcode("by_freetext","pro");

$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////



$onLoad .= 'showForm('.$viewForm.'); ';

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
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($_GET["searchLink"]))
	);
$navbar = navbar2($navbar_array);

$page->setTitle("User");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
$page->addScriptDeclaration($additional_js);
$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
if ($msg) {
	$page->addBodyContent($msg);
	}
$page->display();
exit;


} else {





$result = new Validate();

if ($_POST["form1"]) {
	$fields = $form1;
	$viewForm = 1;
	}


elseif ($_POST["form2"]) {
	$viewForm = 2;
	// addresses from postcode lookup will already be stored in table, and will provide pro_pro_id
	// this needs to be stored in the link table pro2use
	if ($_POST["pro_pro_id"]) {
		$pro_id = $_POST["pro_pro_id"];
		$db_data["p2u_pro"] = $pro_id;
		$db_data["p2u_use"] = $use_id;
		$db_data["p2u_type"] = $_POST["p2u_type"];
		// check to prevent duplicates
		$sql = "SELECT p2u_pro,p2u_use,p2u_type
		FROM pro2use
		WHERE p2u_pro = '$pro_id' AND p2u_use = '".$use_id."' AND p2u_type = '".$_POST["p2u_type"]."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		if (!$q->numRows()) {
			db_query($db_data,"INSERT","pro2use","p2u_id");
			}

		// if client has not default address, make the above property it
		$sql = "SELECT use_pro FROM user WHERE use_id = '".$use_id."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			if ($row["use_pro"] == 0) {
				$db_dataD["use_pro"] = $pro_id;
				db_query($db_dataD,"UPDATE","user","use_id",$use_id);
				}
			}



	} else {
		// if the manual input form is used, put values into array and insert into property table
		// all manual entries are inserted with -1 as pcid, and should be checked by admin until a script does it automatically


		$results = $result->process($form2,$_POST);
		$db_data = $results['Results'];

		// build return link
		$redirect = $_SERVER['SCRIPT_NAME'].'?stage=2&';
		if ($use_id) {
			$redirect .= 'use_id='.$use_id;
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

		// insert into pro2use table linkage
		$db_data2["p2u_use"] = $use_id;
		$db_data2["p2u_pro"] = $pro_id;
		$db_data2["p2u_type"] = $_POST["p2u_type"];
		db_query($db_data2,"INSERT","pro2use","p2u_id");

		// if client has not default address, make the above property it
		$sql = "SELECT use_pro FROM user WHERE use_id = '".$use_id."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			if ($row["use_pro"] == 0) {
				$db_dataD["use_pro"] = $pro_id;
				db_query($db_dataD,"UPDATE","user","use_id",$use_id);
				}
			}

		$msg = urlencode('Update Successful');
		header("Location:$redirect&msg=$msg");
		exit;
		}
	}



if ($viewForm == 1) {
$results = $result->process($fields,$_POST);
$db_data = $results['Results'];
}


// build return link
$redirect = $_SERVER['SCRIPT_NAME'].'?';

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

if ($viewForm == 1) {
db_query($db_data,"UPDATE","user","use_id",$use_id);
}

$msg = urlencode('Update Successful');
header("Location:$redirect&msg=$msg");
exit;



}


?>