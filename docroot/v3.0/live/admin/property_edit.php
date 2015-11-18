<?php

/*
Property Edit, shows deal and property info
*/

require_once("inx/global.inc.php");

if (!$_GET["stage"]) {
	$stage = 1;
	} else {
	$stage = $_GET["stage"];
	}

if (!$_GET["dea_id"]) {
	echo "error, no dea_id";
	} else {
	$dea_id = $_GET["dea_id"];
	}

// start a new page
$page = new HTML_Page2($page_defaults);

$renderx = '
<p><strong>View  deal &amp; property information:</strong></p>
<p>State of Trade (sot) history from Valuation through to current status, with dates, neg, notes.</p>
<p>Viewings, interest, hits &amp; statistics etc</p>
<p>History: date live, price changes, advertising, changes and additions. </p>
<p> </p>
<p><strong>Edit deal &amp; property information: </strong></p>
<p>Update status (restricted, examples:)</p>
<ul>
  <li><em>Valuation</em> can only be changed to <em>Instructed</em> or <em>Not Instructed</em></li>
  <li><em>Instructed</em> can only be changed to<em> Proofing</em></li>
  <li><em>Proofing </em>can only be changed to <em>Available</em> </li>
  <li>and so on... all status changes must be accompanied by notes</li>
</ul>
<p>A deal will be automatically assigned to branch by area (postcode), this can be changed</p>
<p>A deal will be automatically assigned to a neg on a rolling basis (neg works for assigned branch), and this can be changed</p>
<p>The assigned neg is responsible for vendor management, but viewings can be done by any neg</p>
<p>Once property is <em>Under Offer</em>, the neg who has it under becomes main point of contact. That neg is then responsible for managing the deal and chain. </p>
';

$sql = "SELECT
	*,
	CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr,
	CONCAT(cli_fname,' ',cli_sname) AS cli_name
FROM
	deal,property,client
WHERE
	deal.dea_id = $dea_id AND
	deal.dea_prop = property.pro_id AND
	deal.dea_vendor = client.cli_id
LIMIT 1";
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


$sql = "SELECT
	*,
	DATE_FORMAT(sot_date, '%D %M %Y') AS date
FROM
	sot
LEFT JOIN user ON user.use_id = sot.sot_user
WHERE
	sot_deal = $dea_id
ORDER BY sot_date DESC";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$sot[] = array(
		'sot_id'=>$row["sot_id"],
		'sot_status'=>$row["sot_status"],
		'sot_date'=>$row["date"]
		);


	if ($row["sot_status"] == $dea_status) {
		$indicator = '<img src="/images/sys/admin/icons/arrow_right.gif" width="16" height="16" alt="Current Status">';
		$class = 'bold';
		} else {
		$indicator = '&nbsp;';
		$class = '';
		}

	$sot_table .= '<tr class="'.$class.'">
	<!--<td width="16">'.$indicator.'</td>-->
	<td>'.$row["sot_status"].'</td>
	<td>'.$row["date"].'</td>
	<td>'.$row["use_fname"].' '.$row["use_sname"].'</td>
	</tr>';
	}
$sot_table = '<div id="innerTable">
<table cellspacing="2" cellpadding="2" width="400">
'.$sot_table.'
</table>
</div>';

// get list of negs (currently all staff)
$negotiators[] = '-- select --'; // blank option
$sql = "SELECT use_id,CONCAT(use_fname,' ',use_sname) AS use_name FROM user
WHERE use_status = 'Active'
AND use_branch = '".$dea_branch."'
ORDER BY use_sname ASC, use_sname ASC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$negotiators[$row["use_id"]] = $row["use_name"];
	}

// non editable fields
$formData1 = array(
	'dea_vendor'=>array(
		'type'=>'text',
		'label'=>'Vendor',
		'value'=>$cli_name,
		'attributes'=>array('readonly'=>'readonly','style'=>'width:400px','onClick'=>'document.location.href = \'client_edit.php?cli_id='.$cli_id.'&searchLink='.$_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']).'\';')
		),
	'dea_prop'=>array(
		'type'=>'text',
		'label'=>'Property',
		'value'=>$pro_addr,
		'attributes'=>array('readonly'=>'readonly','style'=>'width:400px')
		)
	);
// editable, branch and neg
$formData2 = array(
	'dea_branch'=>array(
		'type'=>'select',
		'label'=>'Branch',
		'value'=>$dea_branch,
		'required'=>2,
		'options'=>db_lookup('dea_branch','branch','array',$dea_branch),
		'attributes'=>array('style'=>'width: 200px')
		),
	'dea_neg'=>array(
		'type'=>'select',
		'label'=>'Negotiator',
		'value'=>$dea_neg,
		'required'=>2,
		'options'=>$negotiators,
		'attributes'=>array('style'=>'width: 200px')
		)
	);

$offers_table = '<div id="innerTable">
<table cellspacing="2" cellpadding="2" width="400">
<tr><td>Lists all offers made on this deal</td></tr>
</table>
</div>';
$statistics_table = '<div id="innerTable">
<table cellspacing="2" cellpadding="2" width="400">
<tr><td>Hits, viewings, and so on</td></tr>
</table>
</div>';

if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","stage","","1");
$form->addField("hidden","action","","update");
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Summary</div>');
$form->addData($formData1,$_GET);
$form->addData($formData2,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");


$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">State of Trade</div>');
$form->addHtml($form->addLabel('history','Status history',$sot_table));
$buttons = $form->makeField("button",$formName,"","Change Status",array('onClick'=>'javascript:document.location.href=\'deal_change.php?dea_id='.$dea_id.'&return='.$_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']).'\';','class'=>'submit'));
$form->addHtml($form->addDiv($buttons));
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Offers</div>');
$form->addHtml($form->addLabel('offers','Offers',$offers_table));
$buttons = $form->makeField("button",$formName,"","Submit Offer",array('onClick'=>'javascript:document.location.href=\'deal_offer.php?dea_id='.$dea_id.'&return='.$_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']).'\';','class'=>'submit'));
$form->addHtml($form->addDiv($buttons));
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Statistics</div>');
$form->addHtml($form->addLabel('statistics','Statistics',$statistics_table));
$form->addHtml("</fieldset>\n");

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$searchLink),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Property > Edit");
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

exit;

} elseif ($_GET["action"] == "update") {


	foreach($_GET as $key=>$val) {
		$_GET[$key] = trim($val);
		}

	if ($_GET["dea_neg"] == "0") {
		$_GET["dea_neg"] = "";
		}
	// validate
	$result = new Validate();
	$results = $result->process($formData2,$_GET);
	$db_data = $results['Results'];

	// build return link
	$return = $_SERVER['SCRIPT_NAME'].'?stage=1&dea_id='.$dea_id.'&searchLink='.$searchLink.'&';
	if (is_array($results['Results'])) {
		$return .= http_build_query($results['Results']);
		}
	if ($results['Errors']) {
		echo error_message($results['Errors'],urlencode($return));
		exit;
		}
	$dea_id = db_query($db_data,"UPDATE","deal","dea_id",$dea_id);

	// redirect
	header("Location:?$return");
	exit;


	}
?>