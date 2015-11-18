<?php
require_once("inx/global.inc.php");

/*
new instruction
same as valuation_add, but with different titles and skips the appointment stage
requires cli_id which is obtained from client_lookup.php
*/

// defaulting to sales for now
//$dea_type = 'Sales';


if ($_GET["stage"]) {
	$stage = $_GET["stage"];
	}
elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
	}
else {
	// default to valuation_address
	$stage = "valuation_address";
	}
// this page cannot be used without a cli_id
if (!$_GET["cli_id"]) {
	header("Location:client_lookup.php?dest=instruction");
	exit;
	} else {
	$cli_id = $_GET["cli_id"];
	}





// start a new page
$page = new HTML_Page2($page_defaults);




switch ($stage):

/////////////////////////////////////////////////////////////////////////////
// valuation_address
// search deal+property and display any linked properties
// else, enter property to be valued details
/////////////////////////////////////////////////////////////////////////////
case "valuation_address":


if ($_GET["dea_id"]) {
	$sql = "SELECT dea_prop FROM deal WHERE dea_id = ".$_GET["dea_id"];
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	$numRows = $q->numRows();
	while ($row = $q->fetchRow()) {
		$pro_id = $row["dea_prop"];
		}
	header("Location:valuation_add.php?stage=appointment&pro_id=$pro_id&cli_id=$cli_id&dea_id=$dea_id");
	exit;
	}

$formData1 = array(
	'dea_type'=>array(
		'type'=>'radio',
		'label'=>'Sales or Lettings',
		'required'=>2,
		'value'=>$_SESSION["auth"]["default_scope"],
		'options'=>db_enum('deal','dea_type','array')
		)
	);

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
		)
	)
	;





if (!$_GET["action"]) {

//get all client's addresses
$associated_property = array();

$sql = "SELECT

pro_id,pro_addr1,pro_addr2,pro_addr3,pro_addr4,pro_addr5,pro_postcode,pro_pcid,
p2c_id, p2c_type, p2c_pro
FROM client
LEFT JOIN pro2cli ON pro2cli.p2c_cli = client.cli_id
LEFT JOIN property ON pro2cli.p2c_pro = property.pro_id
WHERE cli_id = '".$cli_id."'";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	if ($row["pro_id"]) {
	$associated_property[] = $row["pro_id"];
	$render .= '
	<tr>
	<td><label for="'.$row["pro_id"].'"><input type="radio" name="pro_id" value="'.$row["pro_id"].'" onClick="document.forms[0].submit();" id="'.$row["pro_id"].'">
	'.$row["pro_addr1"].' '.$row["pro_addr2"].' '.$row["pro_addr3"].' '.$row["pro_postcode"].' ('.$row["p2c_type"].')</label></td>
	</tr>';
	}
	}

// get other properties from deal table (current deals, past deals, etc)
// need to make sure we dont double up on any of the client's assocaited addresses
$sql = "SELECT
pro_id,pro_addr1,pro_addr2,pro_addr3,pro_postcode
FROM
deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
WHERE
clientId = '".$cli_id."'
GROUP BY pro_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
	if ($row["pro_id"]) {
		if (!in_array($row["pro_id"],$associated_property)) {
			$render .= '
			<tr>
			<td><label for="'.$row["pro_id"].'"><input type="radio" name="pro_id" value="'.$row["pro_id"].'" onClick="document.forms[0].submit();" id="'.$row["pro_id"].'">
			'.$row["pro_addr1"].' '.$row["pro_addr2"].' '.$row["pro_addr3"].' '.$row["pro_postcode"].'</label></td>
			</tr>
			';
			}
		}
		}
	}

$form = new Form();
$form->addForm("form","get",$PHP_SELF);
$form->addField("hidden","stage","","valuation_address");
$form->addField("hidden","action","","new_property");
$form->addField("hidden","cli_id","",$cli_id);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Instructed Property</div>');
$form->addData($formData1,$_GET);
if ($render) {

$render .= '</table>
<div id="inset"><p>If the instructed property is not listed above, <br>please complete the following form</p></div>';
$form->addHtml($form->addLabel("existing","Associated Property",'<table>'.$render));
}

if (!$pro_pcid) {

$form->ajaxPostcode("by_freetext","pro");

} else {

$form->addData($formData3,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
}

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$searchLink),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("New Instruction");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();


} else { // if form is submitted

#print_r($_GET);

$dea_type = $_GET["dea_type"];
// skip if property id is specified
// (new property added via ajax)
if ($_GET["pro_pro_id"]) {
	#echo "Location:?stage=deal&pro_id=$pro_pro_id&cli_id=$cli_id";
	header("Location:?stage=deal&pro_id=$pro_pro_id&cli_id=$cli_id&dea_type=$dea_type");
	exit;
	}
// (existing property)
if ($_GET["pro_id"]) {
	#echo "Location:?stage=deal&pro_id=$pro_pro_id&cli_id=$cli_id";
	header("Location:?stage=deal&pro_id=$pro_id&cli_id=$cli_id&dea_type=$dea_type");
	exit;
	}

// skip if pcid is already present in property table, unless it is -1 (manually added property)
if ($_GET["pro_pcid"] && $_GET["pro_pcid"] !== '-1') {
	$sql = "SELECT pro_id,pro_pcid FROM property WHERE pro_pcid = '".$_GET["pro_pcid"]."'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {
			header("Location:?stage=deal&pro_id=".$row["pro_id"]."&cli_id=$cli_id&dea_type=$dea_type");
			}
		}
	}

// validate
$formData = join_arrays(array($formData2,$formData3));
$result = new Validate();
$results = $result->process($formData,$_GET);
$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?stage=valuation_address&';
if ($cli_id) {
	$results['Results']['cli_id'] = $cli_id;
	}
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}
if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}


# add any additional fields to data array
#$db_data['pro_created'] = $date_mysql;
# enter new client and redirect to valuation_address
$pro_id = db_query($db_data,"INSERT","property","pro_id");

if ($db_data['pro_pcid'] == "-1") {
	admin_notify(array('subject'=>'Manual Property Entry','content'=>'Property ID:'.$pro_id."\n".GLOBAL_URL."postcode_tools.php?pro_id=$pro_id\n".print_r($db_data2,true)));
	}

header("Location:?stage=deal&pro_id=$pro_id&cli_id=$cli_id");

}






break;




case "deal":
/////////////////////////////////////////////////////////////////////////////
// deal
// create a deal record (or check for existing, i.e. same property and same client)
/////////////////////////////////////////////////////////////////////////////


// requires a pro_id
if (!$_GET["pro_id"]) {
	$errors[] = "No property is specified";
	echo error_message($errors);
	exit;
	} else {
	$pro_id = $_GET["pro_id"];
	}




// make sure this deal dosen't already exist
// allow this stage to be skipped, creating a duplicate deal

if ($_GET["action"] !== "skip") {

$sql = "SELECT
dea_id,dea_status,dea_type,date_format(deal.dea_created,'%d/%m/%Y') as datecreate,
cli_id,CONCAT(cli_fname,' ',cli_sname) AS cli_name,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr
FROM
deal
LEFT JOIN property on deal.dea_prop = property.pro_id
LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
WHERE
client.cli_id = '$cli_id' AND
deal.dea_prop = '$pro_id' AND
deal.dea_type = '$dea_type'
GROUP BY deal.dea_id";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
} else {
$numRows = 0;
}
if ($numRows !== 0) {

	while ($row = $q->fetchRow()) {
		$render .= '
		<tr>
		<td><a href="/admin4/instruction/summary/id/'.$row["dea_id"].'">'.$row["pro_addr"].'</a></td>
		<td>'.$row["datecreate"].'</td>
		<td>'.$row["dea_status"].'</td>
		</tr>';
		$cli_name = $row["cli_name"];
		$pro_addr = $row["pro_addr"];
		}

	$render = '
	<p><img src="/images/sys/admin/warning_icon.jpg" align="absmiddle" />The information you have entered appears to already be present in the system</p>
	<br clear="all" />
	<p>The following list shows deals associated to your vendor ('.$cli_name.') and that match the property you have chosen ('.$pro_addr.'). It is therefore
	quite likley that the data you are entering has already been entered. Please review the list below:</p>

	<table width="100%" border="1" cellspacing="0" cellpadding="5">
	  <tr>
	  <td>Property</td>
	  <td>Date Created</td>
	  <td>Current State of Trade</td>
	  </tr>
	'.$render.'
	</table>
	<p>If any of the deals above match what you are trying to add, please click the address. If you want to book a valuation on an existing
	property, you can do so from deal summary page. If none of the information above matches, or they are all past records and not relevant
	to what you are trying to do, you can create a new deal by clicking <a href="?'.$_SERVER['QUERY_STRING'].'&amp;action=skip">here</a>.</p>
	';

	$page->setTitle("Instruction");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($render);
	$page->addBodyContent('</div>');
	$page->display();


	exit;
	}
else {

	// create new deal
	// get the branch from the postcode to automaticaly assign
	$pc = explode(" ",$pro_postcode);
	$pc1 = $pc[0];
	$sql = "SELECT are_branch FROM area WHERE are_postcode = '$pc1' LIMIT 1";
	//echo $sql;
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	if ($numRows == 0) {
		$dea_branch = 1;
		#echo "automatic branch selection failed, branch 1 has been set";
		} else {
		while ($row = $q->fetchRow()) {
			$dea_branch = $row["are_branch"];
			}
		}

	// insert deal
	$db_data["dea_prop"] = $pro_id;
	$db_data["dea_type"] = $dea_type;
	#$db_data["dea_vendor"] = $cli_id; // we dont use dea_vendor any more, we now use link_client_to_instruction link table
	$db_data["dea_status"] = "Instructed";
	$db_data["dea_branch"] = $dea_branch;
	$db_data["dea_created "] = $date_mysql;
	$dea_id = db_query($db_data,"INSERT","deal","dea_id");
	//print_r($db_data);

	// insert link_client_to_instruction link
	$db_data_c2d["dealId"] = $dea_id;
	$db_data_c2d["clientId"] = $cli_id;
	$id = db_query($db_data_c2d,"INSERT","link_client_to_instruction","id");

	// insert sot
	$db_data_sot["sot_deal"] = $dea_id;
	$db_data_sot["sot_status"] = "Instructed";
	$db_data_sot["sot_date"] = $date_mysql;
	$db_data_sot["sot_user"] = $_SESSION["auth"]["use_id"];
	$sot_id = db_query($db_data_sot,"INSERT","sot","sot_id");
	//print_r($db_data_sot);

	header("Location:?stage=particulars&pro_id=$pro_id&cli_id=$cli_id&dea_id=$dea_id");
	}








break;

case "particulars":
/////////////////////////////////////////////////////////////////////////////
// property particulars
//
/////////////////////////////////////////////////////////////////////////////


// get property details (also need area title in list of possible areas)
// property particualrs no longer stored in property table, so select them from newest deal
// record associated with the current property
$sql = "SELECT deal.dea_ptype,dea_psubtype,dea_floors,dea_floor,dea_reception,dea_bedroom,dea_bathroom,
property.pro_area,property.pro_postcode,area.are_id, area.are_title
FROM deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
WHERE dea_prop = ".$pro_id."
ORDER BY dea_created ASC LIMIT 1";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	foreach($row as $key=>$val) {
		$$key = $val;
		}
	}

// get property types
$ptype = ptype2($dea_ptype,$dea_psubtype);

// get matching areas
$pc1 = explode(" ",$pro_postcode);
$pc1 = $pc1[0];
$matched_areas = array();
$sql = "SELECT are_id, are_title, are_postcode FROM area WHERE are_postcode = '$pc1'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$matched_areas[$row["are_title"]] = $row["are_id"];
	if ($numRows == 1) {
		$default_area = $row["are_title"];
		}
	}
if ($are_title) {
	$default_area = $are_title;
	}
if ($matched_areas) {
	$formDataArea = array(
	'pro_area'=>array(
		'type'=>'radio',
		'label'=>'Area',
		'value'=>$default_area,
		'options'=>$matched_areas
		),
	'pro_areanew'=>array(
		'type'=>'button',
		'label'=>'New Area',
		'value'=>'New Area',
		'attributes'=>array('class'=>'button','onClick'=>'javascript:addArea(\''.$pc1.'\',\''.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'\')')
		)
	);
} else {
	$formDataArea = array(
	'pro_areanew'=>array(
		'type'=>'button',
		'label'=>'New Area',
		'value'=>'New Area',
		'attributes'=>array('class'=>'button','onClick'=>'javascript:addArea(\''.$pc1.'\',\''.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'\')')
		)
	);
	}


# build data arrays for property particulars
$formData = array(
	'dea_ptype'=>array(
		'type'=>'select_multi',
		'label'=>'Property Type',
		'required'=>2,
		'options'=>array('dd1'=>$ptype['dd1'],'dd2'=>$ptype['dd2'])
		),
	'dea_bedroom'=>array(
		'type'=>'select_number',
		'label'=>'Bedrooms',
		'value'=>$dea_bedroom,
		'attributes'=>array('class'=>'narrow'),
		'options'=>array('blank'=>'blank')
		),
	'dea_reception'=>array(
		'type'=>'select_number',
		'label'=>'Receptions',
		'value'=>$dea_reception,
		'attributes'=>array('class'=>'narrow'),
		'options'=>array('blank'=>'blank')
		),
	'dea_bathroom'=>array(
		'type'=>'select_number',
		'label'=>'Bathrooms',
		'value'=>$dea_bathroom,
		'attributes'=>array('class'=>'narrow'),
		'options'=>array('blank'=>'blank')
		),
	'dea_floor'=>array(
		'type'=>'select',
		'label'=>'Floor',
		'value'=>$dea_floor,
		'options'=>join_arrays(array(array('blank'=>''),db_enum("deal","dea_floor","array"))),
		'attributes'=>array('class'=>'medium')
		),
	'dea_floors'=>array(
		'type'=>'select_number',
		'label'=>'Floors',
		'options'=>array('blank'=>'','min'=>'1'),
		'value'=>$dea_floors,
		'attributes'=>array('class'=>'narrow')
		)
	);

// remove area from the equation
#$formData = join_arrays(array($formDataArea,$formData));


if (!$_GET["action"]) {

// start new form object
$form = new Form();

$form->addForm("form","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","stage","","particulars");
$form->addField("hidden","action","","update");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","pro_id","",$pro_id);
$form->addField("hidden","dea_id","",$dea_id);

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Property Particulars</div>');
$form->addData($formDataArea,$_GET);
$form->addData($formData,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$searchLink),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);


$page->setTitle("New Instruction");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScriptDeclaration($ptype['js']);
$page->setBodyAttributes(array('onLoad'=>$ptype['onload']));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($render);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();


} else {
// if form is submitted

// validate (dea)
$result = new Validate();
$results = $result->process($formData,$_GET);
$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?stage=valuation_address&';
if ($cli_id) {
	$results['Results']['cli_id'] = $cli_id;
	}
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}
if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}
$db_data["dea_psubtype"] = $_GET["dea_psubtype"];

db_query($db_data,"UPDATE","deal","dea_id",$dea_id);

if ($_GET["pro_area"]) {
	db_query(array('pro_area'=>$_GET["pro_area"]),"UPDATE","property","pro_id",$pro_id);
	}

header("Location:/admin4/instruction/summary/id/$dea_id");

}


break;

/////////////////////////////////////////////////////////////////////////////
// if no stage is defined
/////////////////////////////////////////////////////////////////////////////
default:

$render = 'Nothing to do';

endswitch;
?>