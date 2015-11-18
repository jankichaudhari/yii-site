<?php
require_once("inx/global.inc.php");

// list users, with edit and add pages

// only accesible to SuperAdmin and Administrator
pageAccess($_SESSION["auth"]["roles"],array('SuperAdmin','Administrator'));


$page = new HTML_Page2($page_defaults);


if ($_GET["stage"]) {
	$stage = $_GET["stage"];
	} elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
	} else {
	$stage = 1;
	}


switch ($stage) {
###########################################################
# stage 1 - list
###########################################################
case 1:



if ($_GET["use_status"]) {
	$return["use_status"] = $_GET["use_status"];
	$q[] = "use_status = '".$_GET["use_status"]."' AND ";
	} else {
	//$q[] = "use_status != 'Archived' AND ";
	}

if ($_GET["orderby"]) {
	$orderby = $_GET["orderby"];
	$return["orderby"] = $orderby;
	} else {
	$orderby = 'use_name';
	}
if ($_GET['direction']) {
	$direction = $_GET['direction'];
	} else {
	$direction = 'ASC';
	}

if ($return) {$returnLink = '?'.http_build_query($return); }
$searchLink = $_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']);
if ($sql) {
foreach ($q AS $statement){
	$sql .= $statement." ";
	}
	}
$sql = remove_lastchar($sql,"AND");
$sql = remove_lastchar($sql,"OR");

if ($sql) {
	$sql = "WHERE $sql";
	}

$sql = "SELECT
CONCAT(use_fname,' ',use_sname) AS use_name,
user.*,
branch.bra_title
FROM
user
LEFT JOIN branch ON user.use_branch = branch.bra_id

$sql
ORDER BY $orderby $direction";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {

		$data[] = '
	  <tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" valign="top">
		<td width="10" style="background-color: #'.$row["use_colour"].'"><label><input type="checkbox" name="dea_id[]" id="check_row_'.$row["use_id"].'" value="'.$row["use_id"].'"></label></td>
		<td class="bold" width="200" style="padding-top:5px" onmousedown="document.getElementById(\'check_row_'.$row["use_id"].'\').checked = (document.getElementById(\'check_row_'.$row["use_id"].'\').checked ? false : true);">'.$row["use_name"].'</td>
		<td style="padding-top:5px" onmousedown="document.getElementById(\'check_row_'.$row["use_id"].'\').checked = (document.getElementById(\'check_row_'.$row["use_id"].'\').checked ? false : true);">'.$row["use_status"].'</td>
		<td width="130" style="padding-top:5px" onmousedown="document.getElementById(\'check_row_'.$row["use_id"].'\').checked = (document.getElementById(\'check_row_'.$row["use_id"].'\').checked ? false : true);">'.$row["use_mobile"].'</td>
		<td style="padding-top:5px" onmousedown="document.getElementById(\'check_row_'.$row["use_id"].'\').checked = (document.getElementById(\'check_row_'.$row["use_id"].'\').checked ? false : true);">'.$row["bra_title"].'</td>
		<td width="50" style="padding-top:5px" nowrap="nowrap">
		<a href="user_admin.php?stage=2&use_id='.$row["use_id"].'&searchLink='.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this property" /></a>
	  </td>';
		}

	}


require_once 'Pager/Pager.php';
$params = array(
    'mode'       => 'Sliding',
    'perPage'    => 26,
    'delta'      => 2,
    'itemData'   => $data
);
$pager = & Pager::factory($params);
$data  = $pager->getPageData();
$links = $pager->getLinks();

// convert the querystring into hidden fields
$qs = parse_str($_SERVER['QUERY_STRING'], $output);
foreach ($output AS $key=>$val) {
	if ($key !== "setPerPage") {
		$hidden_fields .= '<input type="hidden" name="'.$key.'" value="'.$val.'">';
		}
	}
/*
$perpage = '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="GET">
'.$pager->getperpageselectbox().'
'.$hidden_fields.'
<input type="submit" value="Go" class="button" />
</form>';
*/
//$links is an ordered+associative array with 'back'/'pages'/'next'/'first'/'last'/'all' links.
//(page '.$pager->getCurrentPageID().' of '.$pager->numPages().')
if (!$links['back']) {
	$back = "&laquo;";
	} else {
	$back = $links['back'];
	}
if (!$links['next']) {
	$next = "&raquo;";
	} else {
	$next = $links['next'];
	}

if ($pager->numItems()) {

$header = '
<div id="header">
<table>
  <tr>
    <td>'.$pager->numItems().' records found';
	if ($pager->numPages() > 1) {
		$header .= ' - Page: '.$back.' '.str_replace("&nbsp;&nbsp;&nbsp;","&nbsp;",$links['pages']).' '.$next.'';
		}
	$header .='</td>
	<td align="right"><a href="?stage=3">New User</a></td>
  </tr>
</table>
</div>
';



$results = '
<table>
  <tr>
    '.columnHeader(array(
	array('title'=>'User','column'=>'use_name','colspan'=>'2'),
	array('title'=>'Status','column'=>'use_status'),
	array('title'=>'Mobile'),
	array('title'=>'Branch','column'=>'bra_title'),
	array('title'=>'&nbsp;')
	),$_SERVER["QUERY_STRING"]).'
  </tr>';
foreach ($data AS $output) {
	$results .= $output;
	}
$results .= '</table>
';

$footer = '
<div id="footer">
<table>
  <tr>
    <td>With selected:
	<input type="submit" name="action" value="View" class="button">
	<input type="submit" name="action" value="Locate" class="button"></td>
  </tr>
</table>
</div>
';


} else { // no results

$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="'.urldecode($returnLink).'">try again</a></strong></td>
  </tr>
</table>';
}


$form = new Form();

$form->addHtml("<div id=\"standard_form\">\n");
$form->addForm("","get",$_SERVER['PHP_SELF']);
$form->addField("hidden","searchLink","",$searchLink);
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Users</div>');
$form->addHtml('<div id="results_table">');
$form->addHtml($header);
$form->addHtml($results);
$form->addHtml($footer);
$form->addHtml('</div>');
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink)
	);
$navbar = navbar2($navbar_array);

$page->setTitle('Users');
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;



break;
###########################################################
# stage 2 - edit
###########################################################
case 2:


if ($_GET["do"] == "addr_default") {
	$db_data["use_pro"] = $_GET["pro_id"];
	db_query($db_data,"UPDATE","user","use_id",$_GET["use_id"]);
	header("Location:".$_GET["return"]."&viewForm=".str_replace('form','',$_GET["viewForm"]));
	}
elseif ($_GET["do"] == "addr_delete") {
	$sql = "DELETE FROM pro2use WHERE p2u_use = '".$use_id."' AND p2u_pro = '".$_GET["pro_id"]."' LIMIT 1";
	$q = $db->query($sql);
	unset($sql);
	header("Location:".$_GET["return"]."&use_id=".$_GET["use_id"]."&viewForm=".str_replace('form','',$_GET["viewForm"]));
	}


if ($_POST["viewForm"]) {
	$viewForm = $_POST["viewForm"];
	} elseif ($_GET["viewForm"]) {
	$viewForm = $_GET["viewForm"];
	} else {
	$viewForm = 1;
	}
if ($_POST["use_id"]) {
	$use_id = $_POST["use_id"];
	} elseif ($_GET["use_id"]) {
	$use_id = $_GET["use_id"];
	}
$action = "add";

$properties = array();
$default_property = array();
$current_roles = array();
$new_roles = array();

if ($use_id) {
	$action = "edit";
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
	'use_status'=>array(
		'type'=>'radio',
		'label'=>'Status',
		'value'=>$use_status,
		'required'=>2,
		'options'=>db_enum("user","use_status","array")
		),
	'use_username'=>array(
		'type'=>'text',
		'label'=>'Username',
		'value'=>$use_username,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_username'
		),
#	'use_password'=>array(
#		'type'=>'text',
#		'label'=>'Password',
#		'value'=>$use_password,
#		'required'=>2,
#		'attributes'=>array('class'=>'addr'),
#		'function'=>'encrypt_password'
#		),
	'use_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$use_email,
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		),
	'use_branch'=>array(
		'type'=>'select_branch',
		'label'=>'Branch',
		'value'=>$use_branch,
		'required'=>2,
		'attributes'=>array('class'=>'medium')
		),
	'use_ext'=>array(
		'type'=>'text',
		'label'=>'Extension',
		'value'=>$use_ext,
		'required'=>1,
		'attributes'=>array('class'=>'medium')
		),
	'use_scope'=>array(
		'type'=>'radio',
		'label'=>'Default Scope',
		'value'=>$use_scope,
		'required'=>2,
		'options'=>db_enum("user","use_scope","array")
		),
	'use_colour'=>array(
		'type'=>'text',
		'label'=>'Colour',
		'value'=>$use_colour,
		'attributes'=>array('readonly'=>'readonly','class'=>'color','style'=>'font-weight:bold;')
		),
//	'use_colour_pick'=>array(
//		'type'=>'button',
//		'label'=>'Colour',
//		'value'=>'Pick',
//		'group'=>'Colour',
//		'last_in_group'=>1,
//		'attributes'=>array('class'=>'button')
//		),
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
	'use_pcid'=>array(
		'type'=>'hidden',
		'value'=>$use_pcid
		),
	'use_addr1'=>array(
		'type'=>'text',
		'label'=>'House Number',
		'value'=>$use_addr1,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr2'=>array(
		'type'=>'text',
		'label'=>'Building Name',
		'value'=>$use_addr2,
		'required'=>1,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr3'=>array(
		'type'=>'text',
		'label'=>'Street',
		'value'=>$use_addr3,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr4'=>array(
		'type'=>'text',
		'label'=>'Town or Area',
		'value'=>$use_addr4,
		'required'=>3,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr5'=>array(
		'type'=>'text',
		'label'=>'City or County',
		'value'=>$use_addr5,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_postcode'=>array(
		'type'=>'text',
		'label'=>'Postcode',
		'value'=>$use_postcode,
		'required'=>2,
		'attributes'=>array('class'=>'pc','maxlength'=>9),
		'function'=>'format_postcode'
		),
	'use_country'=>array(
		'type'=>'select',
		'label'=>'Country',
		'value'=>$use_country,
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
$form->addField("hidden","action","",$action);
$form->addField("hidden","stage","","2");
$form->addField("hidden","use_id","",$use_id);
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


$formName = 'form3';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Roles</div>');
$form->addHtml('<div id="' . $formName . '">');

// add address table
$form->addHtml($render_role);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));

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



$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($_GET["searchLink"]))
	);
$navbar = navbar2($navbar_array);

$page->setTitle("User");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('/js/jscolor/jscolor.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
$page->addScriptDeclaration($additional_js);
$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();
exit;


} else {



$use_id = $_POST["use_id"];

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
elseif ($_POST["form3"]) {

	/*
	print_r($_POST);
	echo "<hr>all:";
	print_r($all_roles);

	echo "<hr>current:";
	print_r($current_roles);

	echo "<hr>new:";
	$new_roles = $_POST["role"];
	print_r($new_roles);
	*/
	// update roles
	$viewForm = 3;
	$new_roles = $_POST["role"];

	$use_id = $_POST["use_id"];

	// loop through all roles
	foreach ($all_roles as $key=>$val) {

		if ($current_roles && $new_roles) {

			// if val is present in CURRENT and not preset in NEW, delete
			if (in_array($val,$current_roles) && !in_array($val,$new_roles)) {
				$sql = "DELETE FROM link_user_to_role WHERE u2r_use = $use_id AND u2r_rol = $key";
				$q = $db->query($sql);
				}


			// if val is present in NEW and not preset in CURRENT, insert
			if (in_array($val,$new_roles) && !in_array($val,$current_roles)) {
				$db_data["u2r_use"] = $use_id;
				$db_data["u2r_rol"] = $key;
				db_query($db_data,"INSERT","link_user_to_role","u2r_id");
				}
			}


		/*
		// check if role already exists, hopefully not neccesary
		$sql = "SELECT * FROM link_user_to_role WHERE u2r_use = $use_id AND u2r_rol = $key";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		$numRows = $q->numRows();

		if (!$numRows) {
			$db_data["u2r_use"] = $use_id;
			$db_data["u2r_rol"] = $key;
			db_query($db_data,"INSERT","link_user_to_role","u2r_id");
			}
		*/
		}




	$redirect = $_SERVER['SCRIPT_NAME'].'?stage=2&use_id='.$use_id.'&viewForm='.$viewForm.'&';
	$msg = urlencode('Update Successful');
	header("Location:$redirect&msg=$msg");
	exit;
	}

if ($viewForm == 1) {
$results = $result->process($fields,$_POST);
$db_data = $results['Results'];
}


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

if ($viewForm == 1) {
db_query($db_data,"UPDATE","user","use_id",$use_id);
}

$msg = urlencode('Update Successful');
header("Location:$redirect&msg=$msg");
exit;



}


break;
###########################################################
# stage 3 - add
###########################################################
case 3:


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
		'init'=>'Forename(s)',
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
		'init'=>'Surname',
		'required'=>2,
		'attributes'=>array('style'=>'width:152px','onFocus'=>'javascript:clearField(this,\'Surname\')'),
		'function'=>'format_name'
		),
	'use_status'=>array(
		'type'=>'radio',
		'label'=>'Status',
		'value'=>$use_status,
		'required'=>2,
		'options'=>db_enum("user","use_status","array")
		),
	'use_username'=>array(
		'type'=>'text',
		'label'=>'Username',
		'value'=>$use_username,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_username'
		),
	'use_password'=>array(
		'type'=>'text',
		'label'=>'Password',
		'default'=>random_string(30),
		'value'=>$use_password,
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		),
	'use_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$use_email,
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		),
	'use_branch'=>array(
		'type'=>'select',
		'label'=>'Branch',
		'value'=>$use_branch,
		'required'=>2,
		'options'=>db_lookup("use_branch","branch","array"),
		'attributes'=>array('class'=>'medium')
		),
	'use_scope'=>array(
		'type'=>'radio',
		'label'=>'Default Scope',
		'value'=>$use_scope,
		'required'=>2,
		'options'=>db_enum("user","use_scope","array")
		),
	'use_colour'=>array(
		'type'=>'text',
		'label'=>'Colour',
		'value'=>$use_colour,
		'group'=>'Colour',
		'attributes'=>array('class'=>'medium','style'=>'font-weight:bold; color: #'.$use_colour.'; background-color: #'.$use_colour)
		),
	'use_colour_pick'=>array(
		'type'=>'button',
		'label'=>'Colour',
		'value'=>'Pick',
		'group'=>'Colour',
		'last_in_group'=>1,
		'attributes'=>array('class'=>'button','onClick'=>'javascript:colourPickWindow();')
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
	'use_pcid'=>array(
		'type'=>'hidden',
		'value'=>$use_pcid
		),
	'use_addr1'=>array(
		'type'=>'text',
		'label'=>'House Number',
		'value'=>$use_addr1,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr2'=>array(
		'type'=>'text',
		'label'=>'Building Name',
		'value'=>$use_addr2,
		'required'=>1,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr3'=>array(
		'type'=>'text',
		'label'=>'Street',
		'value'=>$use_addr3,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr4'=>array(
		'type'=>'text',
		'label'=>'Town or Area',
		'value'=>$use_addr4,
		'required'=>3,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr5'=>array(
		'type'=>'text',
		'label'=>'City or County',
		'value'=>$use_addr5,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_postcode'=>array(
		'type'=>'text',
		'label'=>'Postcode',
		'value'=>$use_postcode,
		'required'=>2,
		'attributes'=>array('class'=>'pc','maxlength'=>9),
		'function'=>'format_postcode'
		),
	'use_country'=>array(
		'type'=>'select',
		'label'=>'Country',
		'value'=>$use_country,
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
$form->addField("hidden","action","",'update');
$form->addField("hidden","stage","","3");


/////////////////////////////////////////////////////////////////////////////////

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">User</div>');
$form->addData($$formName,$_POST);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");


/////////////////////////////////////////////////////////////////////////////////





// start a new page
$page = new HTML_Page2($page_defaults);



$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>'user_admin.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("User");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('/js/jscolor/jscolor.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
$page->addScriptDeclaration($additional_js);
$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();
exit;


} else { // if form is submitted

// validate and insert first form, get cli_id before adding property data (this may mean we lose address)
$result = new Validate();
$results = $result->process($form1,$_POST);
$db_data = $results['Results'];

// build return link
$redirect = $_SERVER['SCRIPT_NAME'].'?stage=3&';
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

// validate password strength
$password = $db_data["use_password"];

// create salt
$salt = random_string(30);

$db_data["use_password"] =  encrypt_password($password,$salt);

$db_data["use_salt"] = $salt;



$use_id = db_query($db_data,"INSERT","user","use_id");

header("Location:?stage=2&use_id=$use_id");
exit;





}


###########################################################
# default
###########################################################
default:

}
?>