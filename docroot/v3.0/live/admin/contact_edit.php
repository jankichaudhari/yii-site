<?php
/*

contact
page for editing contacts.
link to company for grouping

addresses and telephone numbers as client, but with different link tables (otherwise dups will be made)
telephones... separate table of phone numbers, or different field for link (tel_con instead of tel_cli) ??
*/


require_once("inx/global.inc.php");

// get existing values
if ($_GET["con_id"]){
	$con_id = $_GET["con_id"];
	} elseif ($_POST["con_id"]) {
	$con_id = $_POST["con_id"];
	} else {
	echo 'no con_id';
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
	$db_data["con_pro"] = $_GET["pro_id"];
	db_query($db_data,"UPDATE","contact","con_id",$con_id);
	header("Location:".$_GET["return"]."&viewForm=".str_replace('form','',$_GET["viewForm"]));
	}
elseif ($_GET["do"] == "addr_delete") {
	$sql = "DELETE FROM pro2con WHERE p2c_con = '".$con_id."' AND p2c_pro = '".$_GET["pro_id"]."' LIMIT 1";
	$q = $db->query($sql);
	unset($sql);
	header("Location:".$_GET["return"]."&viewForm=".str_replace('form','',$_GET["viewForm"]));
	}


$properties = array();
$default_property = array();
$sql = "SELECT
contact.*,DATE_FORMAT(con_created, '%D %M %Y') AS con_created,
pro_id,pro_addr1,pro_addr2,pro_addr3,pro_addr4,pro_addr5,pro_postcode,pro_pcid,
p2c_id, p2c_pro,p2c_type,
GROUP_CONCAT(DISTINCT CONCAT(tel_id,'~',tel_number,'~',tel_type,'~',tel_ord) ORDER BY tel_ord ASC SEPARATOR '|') AS tel,
com_id,com_title,cty_title
FROM contact
LEFT JOIN pro2con ON pro2con.p2c_con = contact.con_id
LEFT JOIN property ON pro2con.p2c_pro = property.pro_id
LEFT JOIN tel ON contact.con_id = tel.tel_con
LEFT JOIN company ON contact.con_company = company.com_id
LEFT JOIN ctype ON contact.con_type = ctype.cty_id
WHERE con_id = '".$con_id."'
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
		if ($row["pro_id"] == $row["con_pro"]) {

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

// put the default address (as defined in the con_pro row) on top of the array of properties

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
/*
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
		}
	}
*/

// make properties table

foreach ($properties AS $property_id => $property_addr) {
	if ($property_addr["p2c_pro"]) {
		// the default property
		if ($con_pro == $property_addr["p2c_pro"]) {
			$render_addresses .= '<tr>
		<td><strong>'.$property_addr["pro_addr1"].' '.$property_addr["pro_addr2"].' '.$property_addr["pro_addr3"].' '.$property_addr["pro_addr4"].' '.$property_addr["pro_addr5"].' '.$property_addr["pro_postcode"].'</strong> ('.$property_addr["p2c_type"].')</td>
		<td colspan="2" width="32">(default)</td>
		</tr>';
			} else {
			$render_addresses .= '<tr>
		<td>'.$property_addr["pro_addr1"].' '.$property_addr["pro_addr2"].' '.$property_addr["pro_addr3"].' '.$property_addr["pro_addr4"].' '.$property_addr["pro_addr5"].' '.$property_addr["pro_postcode"].' ('.$property_addr["p2c_type"].')</td>
		<td width="16"><a href="?do=addr_default&con_id='.$con_id.'&pro_id='.$property_addr["p2c_pro"].'&return='.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'&viewForm=2"><img src="/images/sys/admin/icons/tick.gif" width="16" height="16" border="0" alt="Make default" /></a></td>
		<td width="16"><a href="javascript:confirmDelete(\'Are you sure you want to delete this address?\',\'?do=addr_delete&con_id='.$con_id.'&pro_id='.$property_addr["p2c_pro"].'&return='.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'&viewForm=2\');"><img src="/images/sys/admin/icons/cross-icon.png" width="16" height="16" border="0" alt="Delete" /></a></td>
		</tr>';
			}
		}
	}
if ($render_addresses) {
$render_addresses = '<table width="95%" cellpadding="3" cellspacing="2" align="center">'.$render_addresses.'<tr><td colspan="3"><hr></td></tr></table>';
}


// summary table
$render_summary = '
<table cellpadding="2" cellspacing="2" border="0" width="100%">
  <tr>
    <td class="label">Name</td>
	<td>'.$con_fname.' '.$con_sname.'</td>
  </tr>';
  if ($com_title) {
  $render_summary .= '
  <tr>
    <td class="label">Company</td>
	<td><a href="company_edit.php?com_id='.$com_id.'">'.$com_title.'</a></td>
  </tr>';
  }
  $render_summary .= '
  <tr>
    <td class="label">Profession</td>
	<td>'.$cty_title.'</td>
  </tr>';
  if ($pro_addr) {
  $render_summary .= '
  <tr>
    <td class="label">Address</td>
	<td>'.$pro_addr.'</td>
  </tr>';
  }
  $render_summary .= '
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
  if ($con_email) {
  $render_summary .= '
  <tr>
    <td class="label">Email</td>
	<td><a href="mailto:'.$con_email.'">'.$con_email.'</a></td>
  </tr>';
  }
  if ($con_web) {
  $render_summary .= '
  <tr>
    <td class="label">Website</td>
	<td><a href="'.$con_web.'" target="_blank">'.$con_web.'</a></td>
  </tr>';
  }
  $render_summary .= '
</table>';


// get all appointments carried out by this contact
$sql = "SELECT * FROM con2app
LEFT JOIN appointment ON con2app.c2a_app = appointment.app_id
WHERE con2app.c2a_con = $con_id";

$sql = "SELECT
app_id,app_type,app_start,app_end,app_status,
CONCAT(user.use_fname,' ',user.use_sname) as use_name,CONCAT(LEFT(user.use_fname,1),LEFT(user.use_sname,1)) as use_initial,use_colour,
DATE_FORMAT(appointment.app_start, '%d/%m/%y') AS app_date,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',LEFT(pro_postcode,4)) AS pro_addr
FROM con2app
LEFT JOIN appointment ON con2app.c2a_app = appointment.app_id
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
WHERE
con2app.c2a_con = $con_id AND appointment.app_status != 'Deleted'
GROUP BY appointment.app_id
ORDER BY app_start DESC";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {

	if ($row["use_colour"]) {
		$use_colour = '<span class="use_col" style="background-color: #'.$row["use_colour"].';"><img src="/images/sys/admin/blank.gif" width="10" height="10" alt="'.$row["use_name"].'"></span>&nbsp;';
		} else { // unnassigned
		$use_colour = '<span class="use_col" style="background-color: #FFFFFF;"><img src="/images/sys/admin/blank.gif" width="10" height="10" alt="'.$row["use_name"].'"></span>&nbsp;';;
		}
	$use_name = $use_colour.$row["use_initial"];


	$appointments .= '
  <tr>
	<td width="15%" valign="top">'.$row["app_date"].'</td>
	<td valign="top"><strong>'.$row["pro_addr"].'</strong></td>
	<td width="10%" valign="top">'.$use_name.'</td>
	<td width="5%" align="right" valign="top">
	  <a href="appointment_edit.php?app_id='.$row["app_id"].'&searchLink='.$_SERVER['SCRIPT_NAME'].urlencode('?'.replaceQueryString($_SERVER['QUERY_STRING'],'viewForm').'&viewForm=0').'"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View/Edit Appointment"/></a>
	  </td>
  </tr>';

	}

if ($appointments) {
$render_summary .= '
<hr />
<table cellpadding="2" cellspacing="2" border="0" width="95%" align="center">

  <tr>
	<td><table width="100%">'.$appointments.'</table></td>
  </tr>
</table>';
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
		$telephone_new[] = array(
			'id'=>$row["tel_id"],
			'number'=>$row["tel_number"],
			'type'=>$row["tel_type"],
			'order'=>$row["tel_ord"]
			);
		}

	}


$form1 = array(
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
	'con_company'=>array(
		'type'=>'text',
		'label'=>'Company',
		'group'=>'Company',
		'value'=>$com_title,
		'attributes'=>array('style'=>'width:280px','onFocus'=>'this.select()'),
		'function'=>'format_name'
		),
	'con_company_edit'=>array(
		'type'=>'button',
		'label'=>'Company',
		'group'=>'Company',
		'last_in_group'=>1,
		'value'=>'Edit',
		'attributes'=>array('class'=>'button','onClick'=>'editCompany('.$com_id.');')
		),
	'con_type'=>array(
		'type'=>'select',
		'label'=>'Profession',
		'value'=>$con_type,
		'required'=>2,
		'attributes'=>array('style'=>'width:320px'),
		'options'=>$ctype
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
$form->addField("hidden","con_id","",$con_id);
$form->addField("hidden","com_id","","");
$form->addField("hidden","new_com_id","",$new_com_id);
$form->addField("hidden","searchLink","",$searchLink);
//$form->addHtml('<input type="hidden" name="action" value="update">');

$form->addHtml('<h1>'.$con_fname.' '.$con_sname.'</h1>');

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

// show old address from import
if ($con_oldaddr && !$render_addresses) {
	$form->addHtml($form->addRow('textarea','con_oldaddr','Old Address',$con_oldaddr,array('style'=>'width:400px','readonly'=>'readonly'),'','Please re-enter this address into the form below'));

	}
// add address table
$form->addHtml($render_addresses);

// add new address
$form->addRow('radio','p2c_type','Type','Home','',db_enum("pro2con","p2c_type","array"));
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

$page->setTitle("Contact > $con_fname $con_sname");
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
	document.getElementById(\'com_id\').value = li.id;
	var msg = \'Do you want to update this contact with company info?\';
	var url = \''.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'&new_com_id=\'+li.id;
	//if (confirm(msg)) {
		//alert(url);
		document.location.href = url;
		//}
	}

function editCompany(com_id) {
	if (!com_id) {
		alert(\'Nothing to edit\');
		}
	else {
		document.location.href = \'company_edit.php?com_id=\'+com_id+\'&searchLink='.$_SERVER['SCRIPT_NAME'].'?'.urlencode($_SERVER['QUERY_STRING']).'\';
		}

	}
</script>');
if ($msg) {
	$page->addBodyContent($msg);
	}
$page->display();


/*
echo '
<script language="JavaScript" type="text/javascript">
//You should create the validator only after the definition of the HTML form

  var frmvalidator  = new Validator("testForm");
  frmvalidator.addValidation("con_fname","req","Forename(s) is required");

  frmvalidator.addValidation("con_sname","req","Surname is required");

  frmvalidator.addValidation("con_email","req","Email is required");
  frmvalidator.addValidation("con_email","email","Email is invalid");

</script>';
*/

} else { // if form is submitted


//print_r($_POST);
//exit;

$result = new Validate();

// validate the appropriate data array+form combintation, except for form2 (addresses) which is dealt with separately

if ($_POST["form1"]) {
	$fields = $form1;
	$viewForm = 1;

	if ($_POST["com_id"]) {
		$_POST["con_company"] = $_POST["com_id"];
		}
	// extract company, see if it exactly matches an existing company, get id if it does
	elseif ($_POST["con_company"]) {
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
	// if it dosent, save client and forward to add company page, auto-populate with same address as this contact


	// check if existing phone numbers have been changed and update, do not allow blanks
	if ($telephone) {

		foreach($telephone as $key=>$val) {
			$tel_count = ($key+1);

			if (($_POST["tel".$tel_count] !== $val["number"] || $_POST["tel".$tel_count."type"] !== $val["type"]) && trim($_POST["tel".$tel_count])) {
				$db_data['tel_number'] = phone_format($_POST["tel".$tel_count]);
				$db_data['tel_type'] = $_POST["tel".$tel_count."type"];
				$db_data['tel_con'] = $con_id;
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
				'tel_con'=>$con_id,
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
		$db_data["p2c_con"] = $_POST["con_id"];
		$db_data["p2c_type"] = $_POST["p2c_type"];
		// check to prevent duplicates
		$sql = "SELECT p2c_pro,p2c_con,p2c_type
		FROM pro2con
		WHERE p2c_pro = '$pro_id' AND p2c_con = '".$_POST["con_id"]."' AND p2c_type = '".$_POST["p2c_type"]."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		if (!$q->numRows()) {
			db_query($db_data,"INSERT","pro2con","p2c_id");
			}

		// if client has not default address, make the above property it
		$sql = "SELECT con_pro FROM contact WHERE con_id = '".$_POST["con_id"]."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			if ($row["con_pro"] == 0) {
				$db_dataD["con_pro"] = $pro_id;
				db_query($db_dataD,"UPDATE","contact","con_id",$_POST["con_id"]);
				}
			}



	} else {
		// if the manual input form is used, put values into array and insert into property table
		// all manual entries are inserted with -1 as pcid, and should be checked by admin until a script does it automatically
		$results = $result->process($form2,$_POST);
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

		// insert into pro2con table linkage
		$db_data2["p2c_con"] = $_POST["con_id"];
		$db_data2["p2c_pro"] = $pro_id;
		$db_data2["p2c_type"] = $_POST["p2c_type"];
		db_query($db_data2,"INSERT","pro2con","p2c_id");

		// if client has not default address, make the above property it
		$sql = "SELECT con_pro FROM contact WHERE con_id = '".$_POST["con_id"]."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			if ($row["con_pro"] == 0) {
				$db_dataD["con_pro"] = $pro_id;
				db_query($db_dataD,"UPDATE","contact","con_id",$_POST["con_id"]);
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
if ($con_id) {
	$redirect .= 'con_id='.$con_id;
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
db_query($db_data,"UPDATE","contact","con_id",$con_id);
}

// if specified company is not found, forward to company add page.
// carry with the contact id, so we can suggest contact's address, phone etc is the same for company...
if ($forward_company == '1') {
	header("Location:company_add.php?con_id=$con_id&com_title=".urlencode($_POST["con_company"]));
	exit;
	}


$msg = urlencode('Update Successful');
header("Location:$redirect&msg=$msg");
exit;


}


?>