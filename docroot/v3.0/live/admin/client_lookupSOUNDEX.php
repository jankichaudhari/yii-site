<?php
require_once("inx/global.inc.php");

/*
27/09/06
going to use the client lookup page for ALL client lookups throughout the system...

and they are:
add client - client_lookup -> client_add.php OR client_edit.php
add viewing - client_lookup -> client_add.php -> viewing_add.php OR viewing_add.php
add valuation = client_lookup -> client_add.php -> valuation_add.php OR valuation_add.php

to aid speedy booking of viewings and valuations, property requirements are not requested during
any procedure except add sales/lettings client (register)
there will be the option to add property requirements after viewing, valuation or other has been booked


we will need page title and legend variables for each flavour
forwand urls will also be needed so we know where to send the user to after adding or selecting the client


stage 1:
client lookup form

stage 2:
client lookup results - select box diverting to $goto_found
and
simple client add form


*/

if ($_GET["stage"]){
	$stage = $_GET["stage"];
	} elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
	} else {
	$stage = 1;
	}

if (!$_GET["dest"] && !$_POST["dest"]) {
	$destination = $_GET["dest"];
	$goto = "applicant_add.php";
	$goto_found = "client_edit.php";
	$page_title = "Add Client";
	$form_title = "Add Client";
	}
elseif ($_GET["dest"] == "viewing" || $_POST["dest"] == "viewing") {
	$destination = "viewing";
	$goto = "viewing_add.php";
	$page_title = "Arrange Viewing";
	$form_title = "Arrange Viewing";
	$cli_name_label = "Viewer's Name";
	$form_title2a = 'Select Viewer';
	$form_title2b = 'New Viewer';
	// dea_id is carried through to viewing_add.php
	}
// arrange a valuation. will create a deal, or accept existing dea_id
elseif ($_GET["dest"] == "valuation" || $_POST["dest"] == "valuation") {
	$destination = "valuation";
	$goto = "valuation_add.php";
	$page_title = "New Property";
	$form_title = "New Property";
	$cli_name_label = "Vendor's Name";
	$form_title2a = 'Select Vendor';
	$form_title2b = 'New Vendor';

	// if deal is specified, get current vendors
	if ($_GET["dea_id"]) {
		$sql = "SELECT
		cli_id,CONCAT(client.cli_fname,' ',client.cli_sname) AS cli_name
		FROM deal
		LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
		LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
		WHERE
		deal.dea_id = ".$_GET["dea_id"];
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
		$numRows = $q->numRows();
		if ($numRows !== 0) {
			while ($row = $q->fetchRow()) {
				$vendors .= '<a href="'.$goto.'?dea_id='.$_GET["dea_id"].'&cli_id='.$row["cli_id"].'">'.$row["cli_name"].'</a><br />';
				}
				$vendors = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
	<td class="label" valign="top">Associated Vendor(s)</td>
	<td>'.$vendors.'</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td>Shown above are any vendors associated with this property. If the vendor isn\'t listed above, please enter their name below</td>
</table>
';
			}
		}
	}
// new instruction, as arrange valuation but skips appointment stage. will create a deal, or accept existing dea_id
elseif ($_GET["dest"] == "instruction" || $_POST["dest"] == "instruction") {
	$destination = "instruction";
	$goto = "instruction_add.php";
	$page_title = "New Instruction";
	$form_title = "New Instruction";
	$cli_name_label = "Vendor's Name";
	// if deal is specified, get current vendors
	if ($_GET["dea_id"]) {
		$sql = "SELECT
		cli_id,CONCAT(client.cli_fname,' ',client.cli_sname) AS cli_name
		FROM deal
		LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
		LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
		WHERE
		deal.dea_id = ".$_GET["dea_id"];
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
		$numRows = $q->numRows();
		if ($numRows !== 0) {
			while ($row = $q->fetchRow()) {
				$vendors .= '<a href="'.$goto.'?dea_id='.$_GET["dea_id"].'&cli_id='.$row["cli_id"].'">'.$row["cli_name"].'</a><br />';
				}
				$vendors = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
	<td class="label" valign="top">Associated Vendor(s)</td>
	<td>'.$vendors.'</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td>Shown above are any vendors associated with this property. If the vendor isn\'t listed above, please enter their name below</td>
</table>
';
			}
		}
	$additional_info = 'This form is for adding a property that we have been instructed on.<br>If you are trying to arrange a valuation, please <a href="client_lookup.php?dest=valuation">click here</a>';
	}
// adding an additional vendor to a deal, requires a dea_id
elseif ($_GET["dest"] == "add_vendor" || $_POST["dest"] == "add_vendor") {
	if ($_GET["dea_id"]) {
		$dea_id = $_GET["dea_id"];
		} elseif ($_POST["dea_id"]) {
		$dea_id = $_POST["dea_id"];
		} else {
		echo "no dea_id (top)";
		exit;
		}
	$destination = "add_vendor";
	$goto = "add_vendor_to_deal.php";
	$page_title = "Add Vendor";
	$form_title = "Add Vendor";
	$cli_name_label = "Vendor's Name";
	$form_title2a = 'Select Vendor';
	$form_title2b = 'New Vendor';
	$carry = urlencode($_GET["return"]);
	}
// adding an additional viewer of vendor to an appointment, requires app_id
elseif ($_GET["dest"] == "add_client_to_appointment" || $_POST["dest"] == "add_client_to_appointment") {
	if ($_GET["app_id"]) {
		$app_id = $_GET["app_id"];
		} elseif ($_POST["app_id"]) {
		$app_id = $_POST["app_id"];
		} else {
		echo "no app_id (top)";
		exit;
		}
	$destination = "add_client_to_appointment";
	$goto = "add_client_to_appointment.php";
	$page_title = "Add Client to Appointment";
	$form_title = "Add Client to Appointment";
	$carry = urlencode($_GET["return"]);
	}
// submit an offer, requires dea_id
elseif ($_GET["dest"] == "offer" || $_POST["dest"] == "offer") {
	if ($_GET["dea_id"]) {
		$dea_id = $_GET["dea_id"];
		} elseif ($_POST["dea_id"]) {
		$dea_id = $_POST["dea_id"];
		} else {
		echo "no dea_id (top)";
		exit;
		}
	// already have cliennt id, skip to offer page
	if ($_GET["cli_id"]) {
		header("Location:offer_submit.php?dea_id=$dea_id&cli_id=".$_GET["cli_id"]);
		}
	$destination = "offer";
	$goto = "offer_submit.php";
	$page_title = "Submit Offer";
	$form_title = "Submit Offer";
	$carry = urlencode($_GET["return"]);
	}



if (!$cli_name_label) {
	$cli_name_label = 'Client Name';
	}
# start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage) {
###########################################################
# stage 1 - basic client search
###########################################################
case 1:


$formData1 = array(
	'cli_name'=>array(
		'type'=>'text',
		'label'=>$cli_name_label,
		'value'=>$_GET["cli_name"],
		'attributes'=>array('class'=>'addr')
		)/*,
	'cli_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$_GET["cli_email"],
		'attributes'=>array('class'=>'addr')
		)*/
	);

$form = new Form();

$form->addForm("","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","stage","","2");
$form->addField("hidden","dest","",$destination);
$form->addField("hidden","dea_id","",$_GET["dea_id"]);
$form->addField("hidden","app_id","",$_GET["app_id"]);
$form->addField("hidden","carry","",$carry);


$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $form_title . '</div>');

if ($vendors) {
	$form->addHtml($vendors);
	}
$form->addData($formData1,$_GET);

$form->addHtml($form->addDiv($form->makeField("submit","","","  Proceed  ",array('class'=>'submit'))));
if ($additional_info) {
	$form->addHtml('<div id="inset">'.$additional_info.'</div>');
	}$form->addHtml("</fieldset>\n");

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$_GET["searchLink"]),
	'search'=>array('title'=>'Client Search','label'=>'Client Search','link'=>'client_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle($page_title);
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->setBodyAttributes(array('onLoad'=>'document.forms[0].cli_name.focus();'));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;




break;
###########################################################
# stage 2 - show existing clients and add client form
###########################################################
case 2:
$terms = array();
// build sql
if (trim($_GET["cli_email"])) {
	$sql_email = " cli_email LIKE '%".trim($_GET["cli_email"])."%' OR ";
	}


$string = trim($_GET["cli_name"]);
// if more than one name is entered, split
if (strpos($string," ")) {
	$string = explode(" ",$string);
	} else {
	$string = array($string);
	}
$keyword_count = count($string);


// get all potentially matching clients into array
$sql = "SELECT cli_id,cli_fname,cli_sname,cli_email,cli_timestamp,cli_oldaddr,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr
FROM client
LEFT JOIN property ON property.pro_id = client.cli_pro
WHERE
$sql_email ";
foreach ($string AS $str) {
	$sql .= "cli_fname LIKE '$str%' OR cli_sname LIKE '$str%' OR soundex(cli_fname) = soundex('$str') OR soundex(cli_sname) = soundex('$str') OR ";
	}
$sql = remove_lastchar($sql,"OR");

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()."sql<br>".$sql); }
while ($row = $q->fetchRow()) {
	// clean up the address
	if ($row["pro_addr"]) {
		$row["pro_addr"] = ' ('.trim(str_replace(array(",,","   ","  "),array(","," "," "),ucwords($row["pro_addr"]))).')';
		} elseif ($row["cli_oldaddr"]) {
		$row["pro_addr"] = ' ('.trim(str_replace(array(",,","   ","  "),array(","," "," "),ucwords($row["cli_oldaddr"]))).')';
		} elseif ($row["cli_email"]) {
		$row["pro_addr"] = ' ('.$row["cli_email"].')';
		}
	$all_clients[] = array(
		'cli_id'=>$row["cli_id"],
		'cli_fname'=>$row["cli_fname"],
		'cli_sname'=>$row["cli_sname"],
		'cli_name'=>$row["cli_fname"].' '.$row["cli_sname"],
		'cli_addr'=>$row["pro_addr"],
		'cli_timestamp'=>strtotime($row["cli_timestamp"]) // timestamp could be used for further sorting on last active date
		);
	}

//print_r($all_clients);
if ($all_clients) {

// now need to sort this array by relevance...
// 1: full match on first and surname
// 2: full match on first name soundex AND surname sondex
// 3: match on first or surname
// 4: match on soundex first or surname

// loop through the results, and compare to $string array to assign score
foreach($all_clients as $key=>$val) {

	// assume $string[0] is fname and $string[($keyword_count-1)] is sname
	// cli_fname could be more than one name, compare only the length of the $str.
	// in essence, ignoring any middle names
	// this salso means that part names will match as i am only comparing to first part of string
	// if $str is mar, it will match mark and margaret
	// to overcome this, add a space to the end of str and cli_fname field - NO
	$fname_length = strlen($string[0]);
	$sname_length = strlen($string[($keyword_count-1)]);
	// exact match, fname AND sname
	if (
		strtolower($string[0].' ') == strtolower(substr($val["cli_fname"].' ', 0, ($fname_length+1))) &&
		strtolower($string[($keyword_count-1)]) == strtolower(substr($val["cli_sname"], 0, $sname_length))
		) {
		$results[0][] = $val;
		}
	// match to first part of fname AND sname
	elseif (
		strtolower($string[0]) == strtolower(substr($val["cli_fname"], 0, $fname_length)) &&
		strtolower($string[($keyword_count-1)]) == strtolower(substr($val["cli_sname"], 0, $sname_length))
		) {
		$results[1][] = $val;
		}
	// match soundex of fname AND exact sname
	elseif (
		strtolower(soundex($string[0])) == strtolower(soundex(substr($val["cli_fname"], 0, $fname_length))) &&
		strtolower($string[($keyword_count-1)]) == strtolower(substr($val["cli_sname"], 0, $sname_length))
		) {
		$results[2][] = $val;
		}
	// match exact fname AND soundex of sname
	elseif (
		strtolower($string[0]) == strtolower(substr($val["cli_fname"], 0, $fname_length)) &&
		strtolower(soundex($string[($keyword_count-1)])) == strtolower(soundex(substr($val["cli_sname"], 0, $sname_length)))
		) {
		$results[3][] = $val;
		}
	// match exact fname OR exact sname
	elseif (
		strtolower($string[0]) == strtolower(substr($val["cli_fname"], 0, ($fname_length+1))) ||
		strtolower($string[($keyword_count-1)]) == strtolower($val["cli_sname"])
		) {
		$results[4][] = $val;
		}
	// match to soundex of fname AND soundex of sname
	elseif (
		strtolower(soundex($string[0])) == strtolower(soundex(substr($val["cli_fname"], 0, ($fname_length+1)))) &&
		strtolower(soundex($string[($keyword_count-1)])) == strtolower(soundex(substr($val["cli_sname"], 0, $sname_length)))
		) {
		$results[5][] = $val;
		}
	// match to first part of fname OR to first part of sname
	elseif (
		strtolower($string[0].' ') == strtolower(substr($val["cli_fname"].' ', 0, ($fname_length+1))) ||
		strtolower($string[($keyword_count-1)].' ') == strtolower(substr($val["cli_sname"].' ', 0, $sname_length))
		) {
		$results[6][] = $val;
		}
	// match to soundex of fname OR soundex of sname
	elseif (
		strtolower(soundex($string[0])) == strtolower(soundex(substr($val["cli_fname"], 0, ($fname_length+1)))) ||
		strtolower(soundex($string[($keyword_count-1)])) == strtolower(soundex(substr($val["cli_sname"], 0, $sname_length)))
		) {
		$results[7][] = $val;
		}
	// all the rest
	else {
		$results[8][] = $val;
		}


	}



ksort($results);

foreach ($results as $key=>$val) {
	$result_count = $result_count+count($val);

	foreach ($val as $subarray=>$values) {
		$options[$values['cli_id']] = $values['cli_name'].$values['cli_addr'];
		}
	}

}

$cli_sname = array_pop($string);
foreach($string as $val) {
	$cli_fname .= $val.' ';
	}
$cli_fname = trim($cli_fname);
/*
echo "
Results: ".print_r($results,true)."
All client = ".count($all_clients)."
In array = ".$result_count."
Options: ".print_r($options,true);


exit;
*/

/*
if (trim($_GET["cli_name"])) {
	$cli_sname = trim(strrchr($_GET["cli_name"]," "));
	$terms["cli_sname"] = $cli_sname;
	$cli_fname = trim(str_replace($cli_sname,"",$_GET["cli_name"]));
	$terms["cli_fname"] = $cli_fname;
	$sql .= " (cli_fname LIKE '%".$cli_fname."%' ";
	if ($cli_sname) {
		$sql .= "AND cli_sname LIKE '%".$cli_sname."%'";
		}
	$sql .= ") OR ";
	}
if (trim($_GET["cli_email"])) {
	$sql .= " cli_email LIKE '%".trim($_GET["cli_email"])."%' OR ";
	$terms["cli_email"] = trim($_GET["cli_email"]);
	}

$sql_1 = $sql;
$sql = '';

if (trim($_GET["cli_name"])) {
	$cli_sname = trim(strrchr($_GET["cli_name"]," "));
	$terms["cli_sname"] = $cli_sname;
	$cli_fname = trim(str_replace($cli_sname,"",$_GET["cli_name"]));
	$terms["cli_fname"] = $cli_fname;
	$sql .= " (cli_fname LIKE '%".$cli_fname."%' OR cli_sname LIKE '%".$cli_fname."%' OR soundex(cli_fname) = soundex('".$cli_fname."') OR soundex(cli_sname) = soundex('".$cli_fname."') ";
	if ($cli_sname) {
		$sql .= "OR cli_sname LIKE '%".$cli_sname."%' OR soundex(cli_sname) = soundex('".$cli_sname."') ";
		}
	$sql .= ") OR ";
	}
if ($cli_fname) {
	$sql .= " (cli_fname LIKE '%".$cli_fname."%' OR soundex(cli_fname) = soundex('".$cli_fname."')) OR (cli_sname LIKE '%".$cli_fname."%' OR soundex(cli_sname) = soundex('".$cli_fname."')) OR ";
	$terms["cli_fname"] = $cli_fname;
	}
if ($cli_sname) {
	$sql .= " (cli_sname LIKE '%".$cli_sname."%' OR soundex(cli_sname) = soundex('".$cli_sname."')) OR ";
	$terms["cli_sname"] = $cli_sname;
	}
if (trim($_GET["cli_email"])) {
	$sql .= " cli_email LIKE '%".trim($_GET["cli_email"])."%' OR ";
	$terms["cli_email"] = trim($_GET["cli_email"]);
	}


$sql_2 = $sql;
$sql = '';

*/




# build data arrays
$formData1 = array(
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
	'cli_tel'=>array(
		'type'=>'tel',
		'label'=>'Telephone 1',
		'required'=>2,
		'value'=>$_GET["telnew"]
		),
	'cli_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$cli_email,
		'required'=>3,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255)
		),
	'cli_preferred'=>array(
		'type'=>'radio',
		'label'=>'Preferred Contact',
		'value'=>$cli_preferred,
		'required'=>2,
		'default'=>'Telephone',
		'options'=>db_enum("client","cli_preferred","array")
		)
	)
	;

// we will only have a pro_pro_id in the GET when returning from error message
// so build a form that is read-only populated with the data, and give button to change, which shows ajax screen again
if ($_GET["pro_pro_id"]) {


	$sqlP = "SELECT * FROM property WHERE pro_id = ".$_GET["pro_pro_id"]." LIMIT 1";
	$qP = $db->query($sqlP);
	if (DB::isError($qP)) {  die("db error: ".$q->getMessage()."sqlP<br>".$sqlP); }
	while ($row = $qP->fetchRow()) {

		$formData2 = array(
		'pro_pro_id'=>array(
			'type'=>'hidden',
			'value'=>$row["pro_id"]
			),
		'pro_pcid'=>array(
			'type'=>'hidden',
			'value'=>$row["pro_pcid"]
			),
		'pro_addr1'=>array(
			'type'=>'text',
			'label'=>'House Number',
			'value'=>$row["pro_addr1"],
			'required'=>1,
			'attributes'=>array('class'=>'addr','readonly'=>'readonly'),
			'function'=>'format_street'
			),
		'pro_addr2'=>array(
			'type'=>'text',
			'label'=>'Building Name',
			'value'=>$row["pro_addr2"],
			'required'=>1,
			'attributes'=>array('class'=>'addr','readonly'=>'readonly'),
			'function'=>'format_street'
			),
		'pro_addr3'=>array(
			'type'=>'text',
			'label'=>'Street',
			'value'=>$row["pro_addr3"],
			'required'=>1,
			'attributes'=>array('class'=>'addr','readonly'=>'readonly'),
			'function'=>'format_street'
			),
		'pro_addr5'=>array(
			'type'=>'text',
			'label'=>'City or County',
			'value'=>$row["pro_addr5"],
			'required'=>1,
			'attributes'=>array('class'=>'addr','readonly'=>'readonly'),
			'function'=>'format_street'
			),
		'pro_postcode'=>array(
			'type'=>'text',
			'label'=>'Postcode',
			'value'=>$row["pro_postcode"],
			'required'=>1,
			'attributes'=>array('class'=>'pc','maxlength'=>9,'readonly'=>'readonly'),
			'function'=>'format_postcode',
			'group'=>'Postcode'
			),
		'pro_postcode_change'=>array(
			'type'=>'button',
			'label'=>'Postcode',
			'value'=>'Change Address',
			'group'=>'Postcode',
			'attributes'=>array('class'=>'button','onClick'=>'javascript:document.location.href = \''.$_SERVER['SCRIPT_NAME'].'?'.replaceQueryString($_SERVER['QUERY_STRING'],'pro_pro_id').'\';'),
			'last_in_group'=>1
			)
		)
		;

		}



	// manually inputted address
	} else {

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
	}

if (!$_POST["action"]) {



if (!$_GET["cli_name"]) {
	$errors[] = 'Please enter a client name';
	echo error_message($errors);
	exit;
	}


$sql_1 = remove_lastchar(trim($sql_1),"OR");
$sql_2 = remove_lastchar(trim($sql_2),"OR");
if (!$_GET["return"]) {
	$return = $_SERVER['SCRIPT_NAME'].'?'.http_build_query(array('cli_name'=>$_GET["cli_name"],'cli_email'=>$_GET["cli_email"],'dest'=>$_GET["dest"],'dea_id'=>$_GET["dea_id"]));
	} else {
	$return = $_GET["return"];
	}

/*
if ($sql_1) {


	// first, match both first AND surnames and put at top of list
	$sql = "SELECT cli_id,cli_fname,cli_sname,pro_addr1,pro_addr2,pro_addr3,pro_postcode FROM client
	LEFT JOIN property ON property.pro_id = client.cli_pro
	WHERE ".$sql_1."
	ORDER BY cli_fname ASC, cli_sname ASC";

	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()."sql_1<br>".$sql); }
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {
			$options[$row["cli_id"]] = $row["cli_fname"].' '.$row["cli_sname"].' ('.$row["pro_addr1"].' '.$row["pro_addr2"].' '.$row["pro_addr3"].' '.$row["pro_postcode"].')';
			}
		}

	}
if ($sql_2) {
	// second, match first OR surnames
	$sql = "SELECT cli_id,cli_fname,cli_sname,pro_addr1,pro_addr2,pro_addr3,pro_postcode FROM client
	LEFT JOIN property ON property.pro_id = client.cli_pro
	WHERE ".$sql_2."
	ORDER BY cli_fname ASC, cli_sname ASC";

	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {
			$options[$row["cli_id"]] = $row["cli_fname"].' '.$row["cli_sname"].' ('.$row["pro_addr1"].' '.$row["pro_addr2"].' '.$row["pro_addr3"].' '.$row["pro_postcode"].')';
			}
		}
	}
else { // no $sql
	$errors[] = "Please fill in at least one of the fields";
	echo error_message($errors,$return);
	exit;
	}
*/
$form = new Form();

if ($options) {
	if ($goto_found) {
	$form->addForm("existing_clients_form","GET",$goto_found,"",'onSubmit="return validateForm();"');
	if ($_SESSION["auth"]["default_scope"] == 'Sales') {
		$form->addField("hidden","viewForm","",4);
		$form->addField("hidden","cli_sales","",'Yes');
		$form->addField("hidden","cli_saleemail","",'Yes');
		} elseif ($_SESSION["auth"]["default_scope"] == 'Lettings') {
		$form->addField("hidden","viewForm","",5);
		$form->addField("hidden","cli_lettings","",'Yes');
		$form->addField("hidden","cli_letemail","",'Yes');
		}
	} else {
	$form->addForm("existing_clients_form","GET",$goto,"",'onSubmit="return validateForm();"');
	}
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden","dest","",$_GET["dest"]);
	// dea_id is used when arranging a viewing (optional) or when adding a vendor to a deal (required)
	$form->addField("hidden","dea_id","",$_GET["dea_id"]);
	// app_id is used when arranging a viewing, adding more clients to the viewing (optional)
	$form->addField("hidden","app_id","",$_GET["app_id"]);
	$form->addField("hidden","searchLink","",$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']);
	// carry is for carrying return link through (e.g. when adding vendor to deal, we still want to retain the searchLink)
	$form->addField("hidden","carry","",$_GET["carry"]);
	$form->addHtml("<fieldset>\n");
	if ($form_title2a) {
		$form->addHtml('<div class="block-header">' . $form_title2a . '</div>');
		} else {
		$form->addHtml('<div class="block-header">Select Client</div>');
		}

	$form->addHtml($form->addLabel('cli_id','Existing Clients',$form->makeField("select","cli_id","","",array('size'=>'6','style'=>'width:400px;','onDblClick'=>'document.forms[0].submit();'),$options) ));
	$form->addHtml($form->addDiv($form->makeField("submit","","","Use Selected Client",array('class'=>'submit'))));
	#$form->addHtml($form->addDiv($form->makeField("button","","","Create New Client",array('class'=>'submit','onClick'=>'location.href=\''.$goto_notfound.'?'.http_build_query($terms).'&dest='.$_GET["dest"].'\';'))));
	$form->addHtml("</fieldset>\n");
	$form->addHtml("</div>\n");
	}

$form2 = new Form();
$form2->addForm("form","post",$PHP_SELF);
$form2->addHtml("<div id=\"standard_form\">\n");
$form2->addField("hidden","stage","","2");
$form2->addField("hidden","action","","new_client");
$form2->addField("hidden","dest","",$_GET["dest"]);
$form2->addField("hidden","dea_id","",$_GET["dea_id"]);
$form2->addField("hidden","app_id","",$_GET["app_id"]);
$form2->addField("hidden","carry","",$_GET["carry"]);
$form2->addField("hidden","cli_name","",$_GET["cli_name"]);
$form2->addHtml("<fieldset>\n");
if ($form_title2b) {
	$form2->addLegend($form_title2b);
	} else {
	$form2->addLegend('New Client');
	}
$form2->addData($formData1,$_GET);
$form2->addSeperator();
$form2->addRow('radio','p2c_type','Address Type','Home','',db_enum("pro2cli","p2c_type","array"));
if (!$_GET["pro_pro_id"]) {
	$form2->ajaxPostcode("by_freetext","pro");
	} else {
	$form2->addData($formData2,$_GET);
	$form2->addHtml($form2->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
	}
$form2->addHtml("</fieldset>\n");
$form2->addHtml("</div>\n");

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$return),
	'search'=>array('title'=>'Client Search','label'=>'Client Search','link'=>'client_search.php')
	);
$navbar = navbar2($navbar_array);


$additional_js = '
function validateForm() {
if (document.forms.existing_clients_form.cli_id.value == "") {
alert(\'Please select a client from the list\');
return false;
}
else return true;
}
';


$page->setTitle($page_title);
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScriptDeclaration($additional_js);
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
if ($form) {
	$page->addBodyContent($form->renderForm());
	}
$page->addBodyContent($form2->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

} else {
// if form is submitted

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?stage=2&amp;dest='.$_POST["dest"].'&amp;cli_name='.$_POST["cli_name"].'&amp;';


// if phone number is supplied, remove from initial db_data array
if (phone_validate($_POST["telnew"])) {
	unset($formData1["cli_tel"]);
	}
$return .= "telnew=".$_POST["telnew"]."&amp;dea_id=$dea_id&amp;";

// validate first form
$result = new Validate();
$results = $result->process($formData1,$_POST);
$db_data = $results['Results'];
//print_r($results);
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}

// validate second form, but this is only required is posctcode lookup isnt used
if (!$_POST["pro_pro_id"]) {
	$results2 = $result->process($formData2,$_POST);
	$db_data2 = $results2['Results'];
	if (is_array($results2['Results'])) {
		$return2 = http_build_query($results2['Results']);
		}
	} else {
	// successfull postcode lookup, show read-only form?
	$return2 = "pro_pro_id=".$_POST["pro_pro_id"]."&amp;";

	}


$return .= '&amp;'.$return2;

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

// no errors, continue

unset($formData1["cli_tel"]);

$db_data['cli_created'] = $date_mysql;
$cli_id = db_query($db_data,"INSERT","client","cli_id");

$db_dataTel = array(
	'tel_number'=>phone_format($_POST["telnew"]),
	'tel_type'=>$_POST["telnewtype"],
	'tel_cli'=>$cli_id,
	'tel_ord'=>1
	);
db_query($db_dataTel,"INSERT","tel","tel_id");
unset($db_dataTel);

if ($_POST["pro_pro_id"]) {
	$pro_id = $_POST["pro_pro_id"];
	$db_data3["p2c_pro"] = $pro_id;
	$db_data3["p2c_cli"] = $cli_id;
	$db_data3["p2c_type"] = $_POST["p2c_type"];
	db_query($db_data3,"INSERT","pro2cli","p2c_id");
	}
else {
	// if the manual input form is used, put values into array and insert into property table
	// all manual entries are inserted with -1 as pcid, and should be checked by admin until a script does it automatically

	// here, in fuure, we should check table for existing properties to prevent duplicates
	$db_data2["pro_pcid"] = '-1';
	$pro_id = db_query($db_data2,"INSERT","property","pro_id");

	// notify admin of manual address input
	admin_notify(array('subject'=>'Manual Property Entry','content'=>'Property ID:'.$pro_id."\n".GLOBAL_URL."postcode_tools.php?pro_id=$pro_id\n".print_r($db_data2,true)));

	// insert into pro2cli table linkage
	$db_data3["p2c_cli"] = $cli_id;
	$db_data3["p2c_pro"] = $pro_id;
	$db_data3["p2c_type"] = $_POST["p2c_type"];
	db_query($db_data3,"INSERT","pro2cli","p2c_id");
	}


// if client has not default address, make the above property it
$sql = "SELECT cli_pro FROM client WHERE cli_id = '".$cli_id."'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	if ($row["cli_pro"] == 0) {
		$db_data4["cli_pro"] = $pro_id;
		db_query($db_data4,"UPDATE","client","cli_id",$cli_id);
		}
	}
#echo $dest;
#echo $goto;

header("Location:$goto?cli_id=$cli_id&dea_id=$dea_id&app_id=$app_id");
exit;












// now onto the destination...
if ($_POST["dest"] == "viewing") {
	header("Location:viewing_add.php?stage=viewing_address&cli_id=$cli_id");
	}
elseif ($_POST["dest"] == "valuation") {
	header("Location:valuation_add.php?stage=valuation_address&cli_id=$cli_id");
	}
elseif ($_POST["dest"] == "add_vendor") {
	$db_data_add["clientId"] = $cli_id;
	$db_data_add["dealId"] = $dea_id;
	db_query($db_data_add,"INSERT","link_client_to_instruction","id");
	header("Location:deal_summary.php?dea_id=$dea_id");
	}
}




break;
###########################################################
# default
###########################################################
default:

}
?>