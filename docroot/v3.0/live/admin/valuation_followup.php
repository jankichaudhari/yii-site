<?php
require_once("inx/global.inc.php");

// follow up a valuation
// enter valuation price (or range), comments, any additional property info, notes and status
if ($_POST["dea_id"]) {
	$dea_id = $_POST["dea_id"];
	} elseif ($_GET["dea_id"]) {
	$dea_id = $_GET["dea_id"];
	} else {
	echo error_message('No Property Specified');
	exit;
	}
if ($_POST["app_id"]) {
	$app_id = $_POST["app_id"];
	} elseif ($_GET["app_id"]) {
	$app_id = $_GET["app_id"];
	} else {
	echo error_message('No Appointment Specified');
	exit;
	}


$sql = "SELECT
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
deal.*,
appointment.app_id,DATE_FORMAT(appointment.app_start,'%W %D %M %H:%i') AS app_date,app_type,
GROUP_CONCAT(DISTINCT CONCAT(cli_id,'|',cli_fname,' ',cli_sname,'|',tel_number) ORDER BY client.cli_id ASC SEPARATOR '~') AS vendor


FROM deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
LEFT JOIN tel ON client.cli_id = tel.tel_cli AND tel_ord = 1
LEFT JOIN appointment ON appointment.app_id = $app_id
WHERE

deal.dea_id = $dea_id
GROUP BY deal.dea_id";


$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	foreach($row as $key=>$val) {
		$$key = $val;
		}
	}

$vendors = explode('~',$vendor);
foreach ($vendors AS $ven_detail) {
	$ven_split = explode('|',$ven_detail);

	$render_vendor .= '<a href="client_edit.php?cli_id='.$ven_split[0].'">'.$ven_split[1].' - '.$ven_split[2].'</a><br />';

	}

$render = '<table cellpadding="2" cellspacing="2" border="0">
  <tr>
	<td class="label" valign="top">Property</td>
	<td><a href="/admin4/instruction/summary/id/'.$dea_id.'">'.$pro_addr.'</a></td>
  </tr>
  <tr>
	<td class="label" valign="top">Vendor(s)</td>
	<td>'.$render_vendor.'</td>
  </tr>
  <tr>
	<td class="label" valign="top">Valuation Date</td>
	<td><a href="appointment_edit.php?app_id='.$app_id.'">'.$app_date.'</a></td>
  </tr>
</table>
<hr />';



if ($dea_type == 'Sales') {
$formData1 = array(
	'dea_status'=>array(
		'type'=>'select',
		'label'=>'State of Trade',
		'value'=>$dea_status,
		'options'=>array('Valuation'=>'Valuation','Instructed'=>'Instructed','Not Instructed'=>'Not Instructed'),
		'attributes'=>array('style'=>'width: 216px'),
		'tooltip'=>'You can leave the status as Valuation if the vendor has not yet reached a decision'
		),
	'dea_valueprice'=>array(
		'type'=>'text',
		'label'=>'Valuation Price',
		'group'=>'Valuation Price',
		'value'=>$dea_valueprice,
		'attributes'=>array('style'=>'width: 100px','maxlength'=>12,'onFocus'=>'javascript:clearField(this,\'(min)\')'),
		'function'=>'numbers_only',
		'init'=>'(min)'
		),
	'dea_valuepricemax'=>array(
		'type'=>'text',
		'label'=>'Valuation Price',
		'group'=>'Valuation Price',
		'last_in_group'=>1,
		'value'=>$dea_valuepricemax,
		'attributes'=>array('style'=>'width: 100px','maxlength'=>12,'onFocus'=>'javascript:clearField(this,\'(max)\')'),
		'function'=>'numbers_only',
		'tooltip'=>'You can enter a price range, or just enter a single price in the first field',
		'init'=>'(max)'
		),
	'dea_commission'=>array(
		'type'=>'text',
		'label'=>'Commission',
		'group'=>'Commission',
		'value'=>$dea_commission,
		'attributes'=>array('style'=>'width: 55px','maxlength'=>9),
		'function'=>'numbers_only'
		),
	'dea_commissiontype'=>array(
		'type'=>'select',
		'label'=>'Commission',
		'group'=>'Commission',
		'last_in_group'=>1,
		'value'=>$dea_commissiontype,
		'options'=>db_enum('deal','dea_commissiontype','array')
		),
	'dea_share'=>array(
		'type'=>'select',
		'label'=>'Deal Share',
		'value'=>$dea_share,
		'attributes'=>array('style'=>'width: 70px'),
		'options'=>db_enum('deal','dea_share','array')
		),
	'dea_chainfree'=>array(
		'type'=>'radio',
		'label'=>'Chain Free',
		'value'=>$dea_chainfree,
		'options'=>db_enum('deal','dea_chainfree','array')
		),
	'dea_tenure'=>array(
		'type'=>'select',
		'label'=>'Tenure',
		'value'=>$dea_tenure,
		'options'=>db_enum("deal","dea_tenure","array"),
		'attributes'=>array('style'=>'width: 165px')
		),
	'dea_leaseend'=>array(
		'type'=>'text',
		'label'=>'Lease Expires',
		'value'=>$dea_leaseend,
		'attributes'=>array('style'=>'width: 165px'),
		'tooltip'=>'This must be the year the lease expires, must be 4 digits long e.g. 2010'
		),
	'dea_board'=>array(
		'type'=>'radio',
		'label'=>'Board Requirement',
		'value'=>$dea_board,
		'options'=>array('Wanted'=>'Wanted','Not Wanted'=>'Not Wanted')
		)
	);
} elseif ($dea_type == 'Lettings') {
$formData1 = array(
	'dea_status'=>array(
		'type'=>'select',
		'label'=>'State of Trade',
		'value'=>$dea_status,
		'options'=>array('Valuation'=>'Valuation','Instructed'=>'Instructed','Not Instructed'=>'Not Instructed'),
		'attributes'=>array('style'=>'width: 216px'),
		'tooltip'=>'You can leave the status as Valuation if the vendor has not yet reached a decision'
		),
	'dea_valueprice'=>array(
		'type'=>'text',
		'label'=>'Valuation Price',
		'group'=>'Valuation Price',
		'value'=>$dea_valueprice,
		'attributes'=>array('style'=>'width: 100px','maxlength'=>12,'onFocus'=>'javascript:clearField(this,\'(min)\')'),
		'function'=>'numbers_only',
		'init'=>'(min)'
		),
	'dea_valuepricemax'=>array(
		'type'=>'text',
		'label'=>'Valuation Price',
		'group'=>'Valuation Price',
		'last_in_group'=>1,
		'value'=>$dea_valuepricemax,
		'attributes'=>array('style'=>'width: 100px','maxlength'=>12,'onFocus'=>'javascript:clearField(this,\'(max)\')'),
		'function'=>'numbers_only',
		'tooltip'=>'You can enter a price range, or just enter a single price in the first field',
		'init'=>'(max)'
		),
	'dea_commission'=>array(
		'type'=>'text',
		'label'=>'Commission',
		'group'=>'Commission',
		'value'=>$dea_commission,
		'attributes'=>array('style'=>'width: 55px','maxlength'=>9),
		'function'=>'numbers_only'
		),
	'dea_commissiontype'=>array(
		'type'=>'select',
		'label'=>'Commission',
		'group'=>'Commission',
		'last_in_group'=>1,
		'value'=>$dea_commissiontype,
		'options'=>db_enum('deal','dea_commissiontype','array')
		),
	'dea_share'=>array(
		'type'=>'select',
		'label'=>'Deal Share',
		'value'=>$dea_share,
		'attributes'=>array('style'=>'width: 70px'),
		'options'=>db_enum('deal','dea_share','array')
		),
	'dea_board'=>array(
		'type'=>'radio',
		'label'=>'Board Requirement',
		'value'=>$dea_board,
		'options'=>array('Wanted'=>'Wanted','Not Wanted'=>'Not Wanted')
		)
	);
}


if (!$_POST["action"]) {

$form = new Form();

$form->addForm("app_form","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));

$form->addHtml("<fieldset>\n");
//$form->addLegend("Valuation Follow Up");
$form->addHtml('<div class="block-header">Valuation Follow Up</div>');
$form->addHtml($render);
$form->addData($formData1,$_GET);
$form->addHtml($form->addRow('textarea','dea_notes','Notes','',array('class'=>'noteInput'),'',''));
$form->addHtml(renderNotes('deal_general',$dea_id));

$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$_GET["searchLink"]),
	'search'=>array('title'=>'Appointment Search','label'=>'Appointment Search','link'=>'appointment_search.php')
	);
$navbar = navbar2($navbar_array);

$page = new HTML_Page2($page_defaults);
$page->setTitle("Valuation Follow Up");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

} else {


$result = new Validate();
$results = $result->process($formData1,$_POST);
$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
if ($dea_id) {
	$results['Results']['dea_id'] = $dea_id;
	}
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}
$return .= '&searchLink='.$_POST["searchLink"];
if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}


// extract notes from POST and store in notes table
if ($_POST["dea_notes"]) {
	$notes = $_POST["dea_notes"];
	$db_data2 = array(
		'not_blurb'=>$notes,
		'not_row'=>$dea_id,
		'not_type'=>'deal_general',
		'not_user'=>$_SESSION["auth"]["use_id"],
		'not_date'=>$date_mysql
		);
	db_query($db_data2,"INSERT","note","not_id");
	}
	unset($_POST["dea_notes"],$db_data2);


db_query($db_data,"UPDATE","deal","dea_id",$dea_id);

if ($db_data["dea_status"] && $db_data["dea_status"] !== $dea_status) {
	$db_data2["sot_deal"] = $dea_id;
	$db_data2["sot_status"] = $db_data["dea_status"];
	$db_data2["sot_date"] = $date_mysql;
	$db_data2["sot_user"] = $_SESSION["auth"]["use_id"];
	$sot_id = db_query($db_data2,"INSERT","sot","sot_id");
	}


if ($_POST["searchLink"]) {
	header("Location:".urldecode($_POST["searchLink"]));
	}  else {
	header("Location:/admin4/instruction/summary/id/$dea_id");
	}
exit;
}
?>