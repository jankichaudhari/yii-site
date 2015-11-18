<?php
require_once("inx/global.inc.php");

/*
Deal offer

submit an offer on selected deal
first we need to find or add the cleient (using client_lookup) - client SHOULD be in already, as they would have viewed it.
requires dea_id and cli_id

once deal info has been provided and stored, we ask user if they would like to submit the offer to
the vendor, which would produce offer letter/email and set status of offer to "submitted"

new thinking (22/03/07), more than one client can make the same offer, e.g. husband and wife or partners buying together.
so now we need cli2off link table, and stop using off_cli field in offer table
*/


if ($_GET["stage"]){
	$stage = $_GET["stage"];
	} elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
	} else {
	$stage = 1;
	}


$page = new HTML_Page2($page_defaults);


switch ($stage) {
###########################################################
# stage 1 - submit and save offer
###########################################################
case 1:

//$_GET["cli_id"] = "1|2";

if (!$_GET["dea_id"]) {
	$errors[] = "No Deal ID specified";
	} else {
	$dea_id = $_GET["dea_id"];
	}
if ($errors) {
	echo error_message($errors);
	exit;
	}
if (!$_GET["cli_id"]) {
	$errors[] = "No Client ID specified";
	} else {
	$cli_id = explode('|',$_GET["cli_id"]);
	}
if (!$cli_id) {
	$cli_id = $_GET["cli_id"];
	}
if ($errors) {
	echo error_message($errors);
	exit;
	}



// get deal info
$sql = "SELECT
dea_type,dea_marketprice,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr
FROM
deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
WHERE deal.dea_id = $dea_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows == 0) {
	echo "select error $sql";
	exit;
} else {
	while ($row = $q->fetchRow()) {
		foreach($row as $key=>$val) {
			$$key = $val;
			$render .= $key."->".$val."<br>";
			}
		}
	}

// get client info
if (is_array($cli_id)) {
	foreach($cli_id AS $client) {
		$sql = "SELECT CONCAT(cli_fname,' ',cli_sname) AS cli_name FROM client WHERE client.cli_id = $client";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		$numRows = $q->numRows();
		while ($row = $q->fetchRow()) {
			$clients[] = $row["cli_name"];
			}
		}
		foreach($clients as $name) {
			$cli_name .= $name.' &amp; ';
			}
		$cli_name = remove_lastchar(trim($cli_name),"&amp;");

	} else {
		// single client
		$sql = "SELECT CONCAT(cli_fname,' ',cli_sname) AS cli_name,cli_salestatus,cli_letstatus FROM client WHERE client.cli_id = $cli_id";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		$numRows = $q->numRows();
		while ($row = $q->fetchRow()) {
			$cli_name = $row["cli_name"];
			$cli_salestatus = $row["cli_salestatus"];
			$cli_letstatus = $row["cli_letstatus"];
			}
		}




// deal table, positioned above formData
$deal_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" width="160">Property</td>
	<td>'.$pro_addr.'</td>
  </tr>
  <tr>
    <td class="label">Asking Price</td>
	<td>'.format_price($dea_marketprice).'</td>
  </tr>
  <tr>
    <td class="label" valign="top">Client</td>
	<td>'.$cli_name.'</td>
  </tr>
</table>
';

// client status
if ($dea_type == 'Sales') {

	if (!$cli_salestatus) {
		$cstatus[] = '';
		}
	$sql = "SELECT * FROM cstatus WHERE cst_scope = 'Sales'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("insert error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$cstatus[$row["cst_id"]] = $row["cst_title"];
		}

	$formData0 = array(
		'cli_salestatus'=>array(
			'type'=>'select',
			'label'=>'Current Status',
			'value'=>$cli_salestatus,
			'required'=>3,
			'attributes'=>array('style'=>'width:300px'),
			'options'=>$cstatus
			)
		);

} elseif ($dea_type == 'Lettings') {

	if (!$cli_letstatus) {
		$cstatus[] = '';
		}
	$sql = "SELECT * FROM cstatus WHERE cst_scope = 'Lettings'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("insert error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$cstatus[$row["cst_id"]] = $row["cst_title"];
		}

	$formData0 = array(
		'cli_letstatus'=>array(
			'type'=>'select',
			'label'=>'Current Status',
			'value'=>$cli_letstatus,
			'required'=>3,
			'attributes'=>array('style'=>'width:300px'),
			'options'=>$cstatus
			)
		);

}




$formData1 = array(
	'off_price'=>array(
		'type'=>'text',
		'label'=>'Offer',
		'value'=>$off_price,
		'required'=>2,
		'attributes'=>array('style'=>'width:300px'),
		'function'=>'numbers_only'
		),
	'off_neg'=>array(
		'type'=>'select_user',
		'label'=>'Negotiator',
		'value'=>$_SESSION["auth"]["use_id"],
		'attributes'=>array('style'=>'width:300px')
		),
	'off_conditions'=>array(
		'type'=>'textarea',
		'label'=>'Conditions',
		'value'=>$off_conditions,
		'attributes'=>array('style'=>'width:300px;height:60px'),
		'tooltip'=>'Any special conditions to this offer. This will be sent to the vendor'
		),
	'off_notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$off_notes,
		'attributes'=>array('style'=>'width:300px;height:60px'),
		'tooltip'=>'Private notes, for internal use only'
		)
	);







if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","cli_id","",$_GET["cli_id"]);
$form->addField("hidden","app_id","",$_GET["app_id"]);
$form->addField("hidden","return","",$return);

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Submit Offer</div>');
$form->addHtml($form->addHtml($deal_table));
$form->addData($formData0,$_GET);
$form->addData($formData1,$_GET);
//$form->addRow("radio","send","Send to Vendor","Yes",array(),array('Yes'=>'Yes','No'=>'No'),'Send letter or email to vendor');
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$_GET["return"]),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Submit Offer");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
#$page->addScriptDeclaration($additional_js);
#$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
#$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();




} else { // if form is submitted

	// first deal with the client status. if mulitple clients are linked to deal, update all
	if ($dea_type == 'Sales') {
		if ($_GET["cli_salestatus"]) {
			$db_data_cli["cli_salestatus"] = $_GET["cli_salestatus"];
			}
		}
	elseif ($dea_type == 'Lettings') {
		if ($_GET["cli_letstatus"]) {
			$db_data_cli["cli_letstatus"] = $_GET["cli_letstatus"];
			}
		}

	$result = new Validate();
	$fields = join_arrays(array($formData1));

	$results = $result->process($fields,$_GET);
	$db_data = $results['Results'];


	$cli_id = array2string($cli_id);

	$redirect = $_SERVER['SCRIPT_NAME'].'?dea_id='.$dea_id.'&cli_id='.$cli_id;
	if ($return) {
		$redirect .= '&return='.$return;
		}
	if ($results['Errors']) {
		if (is_array($results['Results'])) {
			$redirect .= '&'.http_build_query($results['Results']);
			}
		echo error_message($results['Errors'],urlencode($redirect));
		exit;
		}


	$db_data["off_deal"] = $_GET["dea_id"];
	$db_data["off_date"] = $date_mysql;
	$db_data["off_app"] = $_GET["app_id"];
	$off_id = db_query($db_data,"INSERT","offer","off_id");
	unset($db_data);


	$cli_id = explode("|",$_GET["cli_id"]);
	foreach($cli_id as $cli) {
		// insert into cli2off link table
		$db_data["c2o_off"] = $off_id;
		$db_data["c2o_cli"] = $cli;
		$c2o_id = db_query($db_data,"INSERT","cli2off","c2o_id");
		unset($db_data);

		if ($db_data_cli) {
			db_query($db_data_cli,"UPDATE","client","cli_id",$cli);
			}
		}



	if ($_GET["send"] == "Yes") {
		header("Location:?stage=2&off_id=$off_id&return=".$_GET["return"]);
		} else {
		//if (!$_GET["return"]) {
			header("Location:deal_summary.php?dea_id=$dea_id&viewForm=7");
			//} else {
			//header("Location:".urldecode($_GET["return"])."&viewForm=4");
			//}
		}
	exit;
	}


break;
###########################################################
# stage 2 - submit offer to vendor
###########################################################
case 2:

if (!$_GET["off_id"]) {
	echo "no off_id";
	exit;
	} else {
	$off_id = $_GET["off_id"];
	}

// this is a biggy! get deal, property, neg, client (who has offered), vendor, and vendor's property
$sql = "SELECT
deal.*,
CONCAT(property.pro_addr1,' ',property.pro_addr2,' ',property.pro_addr3,' ',property.pro_postcode) AS pro_addr,
CONCAT(user.use_fname,' ',user.use_sname) AS use_name,
client.cli_id,GROUP_CONCAT(DISTINCT CONCAT(client.cli_salutation,' ',client.cli_fname,' ',client.cli_sname,'(',client.cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ' &amp; ') AS cli_name,

GROUP_CONCAT(DISTINCT CONCAT(vendor.cli_fname,' ',vendor.cli_sname) SEPARATOR ' &amp; ') AS vendor_name,
vendor_property.pro_addr1 AS vendor_addr1,vendor_property.pro_addr2 AS vendor_addr2,vendor_property.pro_addr3 AS vendor_addr3,
vendor_property.pro_addr4 AS vendor_addr4,vendor_property.pro_addr5 AS vendor_addr5,vendor_property.pro_postcode AS vendor_postcode,
offer.off_price,offer.off_conditions,DATE_FORMAT(off_date, '%D %M %Y') AS date

FROM
offer

LEFT JOIN deal ON offer.off_deal = deal.dea_id
LEFT JOIN property ON property.pro_id = deal.dea_prop
LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id
LEFT JOIN property AS vendor_property ON vendor.cli_pro = vendor_property.pro_id
LEFT JOIN cli2off ON offer.off_id = cli2off.c2o_off
LEFT JOIN client ON cli2off.c2o_cli = client.cli_id
LEFT JOIN user ON offer.off_neg = user.use_id

WHERE off_id = $off_id
GROUP BY offer.off_id";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows == 0) {
	echo "select error";
	exit;
} else {
	while ($row = $q->fetchRow()) {
		foreach($row as $key=>$val) {
			$$key = $val;
			$render .= $key."->".$val."<br>";
			}
		}
	}

if (!$off_conditions) {
	$off_conditions = 'None';
	}

$nw = new Numbers_Words();
#$off_price_word = $nw->toWords(numbers_only(format_price($off_price)),'en_GB');
$off_price_word = $nw->toCurrency($off_price,'en_GB','GBP') ;


$offer_text = str_replace("  "," ",$vendor_name.'
'.$vendor_addr1.' '.$vendor_addr2.' '.$vendor_addr3.'
'.$vendor_addr5.'
'.$vendor_postcode.'

'.date('l jS F Y').'

Dear '.$vendor_name.',

Re: '.$pro_addr.'

Further to our recent conversation, we write to confirm the following offer made on the above property: -

Offer made by:   '.preg_replace("/\([a-z0-9\ ]+\)/", "", $cli_name).'
Date of offer:   '.$date.'
Amount:          '.format_price($off_price).' ('.$off_price_word.')
Conditions:      '.$off_conditions.'

With kind regards.

Yours sincerely,

'.$use_name);

#echo "<pre>".$offer_text."</pre>";
#exit;

if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","stage","","2");
$form->addField("hidden","off_id","",$off_id);
$form->addField("hidden","return","",$_GET["return"]);

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Submit Offer</div>');
#$form->addHtml($form->addHtml($deal_table));
#$form->addData($formData1,$_GET);
$form->addRow("textarea","send","Letter to Vendor",$offer_text ,array('style'=>'width:450px;height:450px'));
$form->addRow("checkbox","send","Send By",array("Post","Email"),array(),array('Post'=>'Post','Email'=>'Email'));

$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Send and Save",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");







$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$_GET["return"]),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Submit Offer");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
#$page->addScriptDeclaration($additional_js);
#$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
#$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();

} else { // if form is submitted

// send email




// update offer status
$db_data["off_status"] = "Submitted";
$off_id = db_query($db_data,"UPDATE","offer","off_id",$_GET["off_id"]);

// back to deal summary and view offers form
//if (!$_GET["return"]) {
	header("Location:deal_summary.php?dea_id=$dea_id&viewForm=7");
	//} else {
	//header("Location:".urldecode($_GET["return"])."&viewForm=4");
	//}


}

break;
###########################################################
# default
###########################################################
default:

}

?>