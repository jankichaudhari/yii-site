<?php
require_once("inx/global.inc.php");

/*
Deal change
updates the status if a deal, adding a new row to the sot table each time.

Notes field is shown at every stage, and dates are automatically added.
other fields are shown depending on the status being chosen, e.g.
Valuation -> Instructed - valuation price, agreed fee(%), sole/multi (incl. other agents if applicable) and neg
Intruction -> Proofing
Proofing -> Available - market price, fee, sole/multi (+other agents), neg, price qualifier, chainfree, conditions
Prior to being set to under offer, all offers put forward are stored in the offer table.
When an offer is accepted, the deal table is updated and linked to the accepted offer in the offer table.
The deal table also requires solicitor, lender etc.
Available -> Under Offer (this adds a row to the offer table, retaining the deal table values)
Under Offer -> Exchanged - completion date
Exchanged -> Completed


definitely not allowed:
Set to Available if curent status is anything other than Proofing - every property has to be proofed prior to being released
Properties set to (not instructed,completed,archived,sold by other) are not editable, new deals must be created if the status changes


*/




if (!$_GET["dea_id"]) {
	$errors[] = "No Deal ID specified";
	} else {
	$dea_id = $_GET["dea_id"];
	}
if ($errors) {
	echo error_message($errors);
	exit;
	}

$sql = "SELECT * FROM deal,sot WHERE deal.dea_id = $dea_id AND sot.sot_deal = deal.dea_id";
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

# build select of possible new statuses, and create any additional fields relating to the possible new statuses
switch ($dea_status) {

	case "Valuation":
	$statuses = array(
		'Valuation'=>'Valuation',
		'Instructed'=>'Instructed',
		'Not Instructed'=>'Not Instructed'
		);
	$additional_fields = array(
		'dea_valueprice'=>array(
			'type'=>'text',
			'label'=>'Valuation price',
			'value'=>$dea_valueprice,
			'required'=>3,
			'attributes'=>array('style'=>'width:100px'),
			'function'=>'numbers_only'
			),
		'dea_commission'=>array(
			'type'=>'text',
			'label'=>'Agreed commission',
			'value'=>$dea_commission,
			'required'=>3,
			'attributes'=>array('style'=>'width:100px')
			),
		'dea_conditions'=>array(
			'type'=>'textarea',
			'label'=>'Special conditions',
			'value'=>$dea_conditions,
			'attributes'=>array('style'=>'width:400px;height:40px')
			),
		'dea_qualifier'=>array(
			'type'=>'radio',
			'label'=>'Price prefix',
			'value'=>$dea_qualifier,
			'options'=>db_enum('deal','dea_qualifier','array')
			),
		'dea_chainfree'=>array(
			'type'=>'radio',
			'label'=>'Chain free',
			'value'=>$dea_chainfree,
			'required'=>3,
			'options'=>db_enum('deal','dea_chainfree','array')
			),
		'dea_share'=>array(
			'type'=>'radio',
			'label'=>'Deal share',
			'value'=>$dea_share,
			'required'=>2,
			'options'=>db_enum('deal','dea_share','array')
			),
		'dea_otheragent'=>array(
			'type'=>'textarea',
			'label'=>'Other agent(s)',
			'value'=>$dea_otheragent,
			'attributes'=>array('style'=>'width:400px;height:40px')
			)
		);
	break;

	case "Instructed":
	$statuses = array(
		'Instructed'=>'Instructed',
		'Proofing'=>'Proofing',
		'Disinstructed'=>'Disinstructed'
		);
	$additional_fields = array(
		'dea_valueprice'=>array(
			'type'=>'text',
			'label'=>'Valuation price',
			'value'=>$dea_valueprice,
			'required'=>3,
			'attributes'=>array('style'=>'width:100px'),
			'function'=>'numbers_only'
			),
		'dea_marketprice'=>array(
			'type'=>'text',
			'label'=>'Market price',
			'value'=>$dea_marketprice,
			'required'=>3,
			'attributes'=>array('style'=>'width:100px'),
			'function'=>'numbers_only'
			),
		'dea_commission'=>array(
			'type'=>'text',
			'label'=>'Agreed commission',
			'value'=>$dea_commission,
			'required'=>3,
			'attributes'=>array('style'=>'width:100px')
			),
		'dea_conditions'=>array(
			'type'=>'textarea',
			'label'=>'Special conditions',
			'value'=>$dea_conditions,
			'attributes'=>array('style'=>'width:400px;height:40px')
			),
		'dea_qualifier'=>array(
			'type'=>'radio',
			'label'=>'Price prefix',
			'value'=>$dea_qualifier,
			'options'=>db_enum('deal','dea_qualifier','array')
			),
		'dea_chainfree'=>array(
			'type'=>'radio',
			'label'=>'Chain free',
			'value'=>$dea_chainfree,
			'required'=>3,
			'options'=>db_enum('deal','dea_chainfree','array')
			),
		'dea_share'=>array(
			'type'=>'radio',
			'label'=>'Deal share',
			'value'=>$dea_share,
			'required'=>2,
			'options'=>db_enum('deal','dea_share','array')
			),
		'dea_otheragent'=>array(
			'type'=>'textarea',
			'label'=>'Other agent(s)',
			'value'=>$dea_otheragent,
			'attributes'=>array('style'=>'width:400px;height:40px')
			)
		);
	break;
	case "Proofing":
	$statuses = array(
		'Proofing'=>'Proofing',
		'Available'=>'Available'
		);
	$additional_fields = array(
		);
	break;
	case "Available":
	$statuses = array(
		'Available'=>'Available',
		'Under Offer'=>'Under Offer',
		'Under Offer with Other'=>'Under Offer with Other',
		'Withdrawn'=>'Withdrawn',
		'Disinstructed'=>'Disinstructed'
		);
	$additional_fields = array(
		);
	break;
	case "Under Offer":
	$statuses = array(
		'Under Offer'=>'Under Offer',
		'Exchanged'=>'Exchanged',
		'Collapsed'=>'Collapsed'
		);
	$additional_fields = array(
		);
	break;
	case "Exchanged":
	$statuses = array(
		'Exchanged'=>'Exchanged',
		'Completed'=>'Completed',
		'Collapsed'=>'Collapsed'
		);
	$additional_fields = array(
		'sot_nextdate'=>array(
			'type'=>'text',
			'label'=>'Completion date',
			'attributes'=>array('style'=>'width:100px')
			),
		);
	break;
	case "Completed":
	$statuses = array(
		'Completed'=>'Completed',
		'Collapsed'=>'Collapsed'
		);
	$additional_fields = array(
		);
	break;
	case "Collapsed":
	$statuses = array(
		'Collapsed'=>'Collapsed'
		);
	$additional_fields = array(
		);
	break;
	case "Not Instructed":
	$statuses = array(
		'Not Instructed'=>'Not Instructed'
		);
	$additional_fields = array(
		);
	break;
	case "Withdrawn":
	$statuses = array(
		'Withdrawn'=>'Withdrawn'
		);
	$additional_fields = array(
		);
	break;
	case "Disinstructed":
	$statuses = array(
		'Disinstructed'=>'Disinstructed'
		);
	$additional_fields = array(
		);
	break;
	case "Under Offer with Other":
	$statuses = array(
		'Under Offer with Other'=>'Under Offer with Other',
		'Sold by Other'=>'Sold by Other',
		'Collapsed'=>'Collapsed'
		);
	$additional_fields = array(
		);
	break;
	case "Sold by Other":
	$statuses = array(
		'Sold by Other'=>'Sold by Other'
		);
	$additional_fields = array(
		);
	break;
	case "Archived":
	$statuses = array(
		'Archived'=>'Archived'
		);
	$additional_fields = array(
		);
	break;
	case "Comparable":
	$statuses = array(
		'Comparable'=>'Comparable'
		);
	$additional_fields = array(
		);
	break;
	case "Chain":
	$statuses = array(
		'Chain'=>'Chain'
		);
	$additional_fields = array(
		);
	break;
	}


$formData1 = array(
	'dea_status'=>array(
		'type'=>'select',
		'label'=>'Status',
		'value'=>$dea_status,
		'options'=>$statuses,
		'attributes'=>array('readonly'=>'readonly','style'=>'width:300px')
		)
	);

$formData2 = array(
	'sot_notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$sot_notes,
		'attributes'=>array('style'=>'width:400px;height:80px')
		)
	);



if (!$_GET["action"]) {



$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","return","",urlencode($_GET["return"]));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Change Status</div>');
#$form->addHtml($form->addLabel('current_status','Current status',$form->makeField("text","","",$dea_status,array('class'=>'inputInvisible'))));
$form->addData($formData1,$_GET);
if (is_array($additional_fields)) {
	$form->addData($additional_fields,$_GET);
	}
$form->addData($formData2,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
/*
$render = '
<div id="content">
<h3>Status change to "'.$sot.'"</h3>
<form method="get">
<input type="hidden" name="submitted" value="yes">
<input type="hidden" name="dea_id" value="'.$dea_id.'">
<input type="hidden" name="sot" value="'.$sot.'">
<input type="hidden" name="return" value="'.$_GET["return"].'">
<table width="100%" cellpadding="5" cellspacing="0" border="0">
';
if ($sot == "Instructed" || $sot == "Available") {
$render .= '
<tr>
<td>Valuation Price</td>
<td><input type="text" name="price"></td>
</tr>
'; }
$render .= '
<tr>
<td>Notes:</td>
<td><textarea name="notes" style="width:200px;height:60px"></textarea></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" value="Change Status"></td>
</tr>
</table>
</form>
</div>';
*/
}

else { // if form is submitted



	$result = new Validate();
	$fields = join_arrays(array($formData1,$additional_fields)); #,$formData2

	$results = $result->process($fields,$_GET);
	$db_data = $results['Results'];

	$redirect = $_SERVER['SCRIPT_NAME'].'?dea_id='.$dea_id;
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

	#todo: check the new status hasnt already been set, check the status is valid

	#update the deal table
	$dea_id = db_query($db_data,"UPDATE","deal","dea_id",$dea_id);


	#insert new row into sot table if the status has been changed
	if ($db_data["dea_status"] && $db_data["dea_status"] !== $dea_status) {
		$db_data2["sot_deal"] = $dea_id;
		$db_data2["sot_status"] = $db_data["dea_status"];
		$db_data2["sot_date"] = $date_mysql;
		$db_data2["sot_notes"] = $_GET["notes"];
		$db_data2["sot_user"] = $_SESSION["auth"]["use_id"];
		$sot_id = db_query($db_data2,"INSERT","sot","sot_id");
		}


	header("Location:".urldecode($_GET["return"]));
	exit;
	}



$page = new HTML_Page2($page_defaults);

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$_GET["return"]),
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



?>