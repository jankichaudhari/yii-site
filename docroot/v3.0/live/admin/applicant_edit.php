<?php
// applicant add proceudre but for existong clients (to re-affirm their requirements)
require_once("inx/global.inc.php");



if (!$_GET["stage"]) {
	$stage = 1;
	} else {
	$stage = $_GET["stage"];
	}

// whole page needs cli_id
if (!$_GET["cli_id"]) {
	$errors[] = "No Client ID";
	echo error_message($errors);
	exit;
	} else {
	$cli_id = $_GET["cli_id"];
	}
if ( $_GET["app_id"]) {
	$app_id = $_GET["app_id"];
	}

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage) {
###########################################################
# stage 1 - applicant details
###########################################################
case 1:




$sql = "SELECT * FROM client WHERE cli_id = $cli_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		foreach($row as $key=>$val) {
			$$key = $val;
			}
		}
	}

// show reason for showing this page
$cli_created_date = explode(" ",$cli_created);
if ($cli_created_date[0] == date('Y-m-d')) {
	$reason = 'This is a new client, please enter their property requirements below';
	} elseif ((strtotime($date_mysql)-strtotime($cli_reviewed)) > $client_review_period) {
	$aday = 60*60*24;
	$client_review_period_formatted = ($client_review_period/$aday);
	$reason = 'This is an existing client who has not updated their requirements in the past '.$client_review_period_formatted.' days.';
	}
if ($reason) {
	$reason = '<p class="appInfo">'.$reason.'</p>';
	}

// default scope
if (!$cli_sales && $_SESSION["auth"]["default_scope"] == 'Sales') {
	$cli_sales = 'Yes';
	}
if (!$cli_lettings && $_SESSION["auth"]["default_scope"] == 'Lettings') {
	$cli_lettings = 'Yes';
	}


foreach ($_GET as $key=>$val) {
	$$key = $val;
	}

// returns an array for the multi-dropdown source fields
$source = source($cli_source,$_SERVER['QUERY_STRING']);


$method_array = db_enum("client","cli_method","array");
array_pop($method_array); // removing 'Import'
# build data arrays
//if (!$cli_source) {
$formData0 = array(
	'cli_source'=>array(
		'type'=>'select_multi',
		'label'=>'How heard about us?',
		'required'=>1,
		'options'=>array('dd1'=>$source['dd1'],'dd2'=>$source['dd2'])
		)
	);
	//}

$formData1 = array(
	'cli_sales'=>array(
		'type'=>'radio',
		'label'=>'Sales',
		'value'=>$cli_sales,
		'required'=>2,
		'options'=>db_enum("client","cli_sales","array"),
		'default'=>'No',
		'attributes'=>array('onClick'=>'javascript:toggleDivRadio(\'cli_sales\',\'sale\');')
		),
	'cli_lettings'=>array(
		'type'=>'radio',
		'label'=>'Lettings',
		'value'=>$cli_lettings,
		'required'=>2,
		'options'=>db_enum("client","cli_lettings","array"),
		'default'=>'No',
		'attributes'=>array('onClick'=>'javascript:toggleDivRadio(\'cli_lettings\',\'let\');')
		)
	);

// sales form
$formData2 = array(
	'cli_salemin'=>array(
		'type'=>'select_price',
		'label'=>'Minimum Price',
		'value'=>$cli_salemin,
		'group'=>'Price Range',
		'required'=>2,
		'options'=>array('scope'=>'sales','default'=>'Minimum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_salemax'=>array(
		'type'=>'select_price',
		'label'=>'Maximum Price',
		'value'=>$cli_salemax,
		'group'=>'Price Range',
		'last_in_group'=>1,
		'required'=>2,
		'options'=>array('scope'=>'sales','default'=>'Maximum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_salebed'=>array(
		'type'=>'select_number',
		'label'=>'Minimum Beds',
		'value'=>$cli_salebed,
		'required'=>2
		),
	'cli_saleemail'=>array(
		'type'=>'radio',
		'label'=>'Email Updates',
		'value'=>$cli_saleemail,
		'required'=>2,
		'default'=>'Yes',
		'options'=>db_enum("client","cli_saleemail","array")
		)
	);
$ptype_sale = ptype("sale",explode("|",$cli_saleptype));


// lettings form
$formData3 = array(
	'cli_letmin'=>array(
		'type'=>'select_price',
		'label'=>'Minimum Price',
		'value'=>$cli_letmin,
		'group'=>'Price Range',
		'required'=>2,
		'options'=>array('scope'=>'lettings','default'=>'Minimum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_letmax'=>array(
		'type'=>'select_price',
		'label'=>'Maximum Price',
		'value'=>$cli_letmax,
		'group'=>'Price Range',
		'last_in_group'=>1,
		'required'=>2,
		'options'=>array('scope'=>'lettings','default'=>'Maximum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_letbed'=>array(
		'type'=>'select_number',
		'label'=>'Minimum Beds',
		'value'=>$cli_letbed,
		'required'=>2
		),
	'cli_letemail'=>array(
		'type'=>'radio',
		'label'=>'Email Updates',
		'value'=>$cli_letemail,
		'default'=>'Yes',
		'required'=>2,
		'options'=>db_enum("client","cli_letemail","array")
		)
	);
$ptype_let = ptype("let",explode("|",$cli_letptype));

// form is not submitted, show the form
if (!$_GET["action"]) {

// start new form object
$form = new Form();

$form->addForm("form","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","app_id","",$app_id);

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml('<div>');
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Viewer Details</div>');
$form->addHtml($reason);
//$form->addHtml($form->addDiv($form->makeField("button","","","Skip",array('class'=>'submit','onClick'=>'document.location.href = \'calendar.php?app_id='.$_GET["app_id_carry"].'\''))));

if ($formData0) {
	$form->addData($formData0,$_GET);
	}
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","toHide","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');

/////////////////////////////////////////////////////////////////////////////////
if ($cli_sales != 'Yes') { $sales_visible = "none"; }
$form->addHtml('<div id="sale" style="display:'.$sales_visible.'">');
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Sales Requirements</div>');
$form->addHtml($form->addLabel('cli_saleptype','Houses',$ptype_sale['house'],'javascript:checkAll(document.forms[0], \'sale1\');'));
$form->addHtml($form->addLabel('cli_saleptype','Apartments',$ptype_sale['apartment'],'javascript:checkAll(document.forms[0], \'sale2\');'));
$form->addHtml($form->addLabel('cli_saleptype','Others',$ptype_sale['other'],'javascript:checkAll(document.forms[0], \'sale3\');'));
$form->addData($formData2,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");


/////////////////////////////////////////////////////////////////////////////////
if ($cli_lettings <> 'Yes') { $lettings_visible = "none"; }
$form->addHtml('<div id="let" style="display:'.$lettings_visible.'">');
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Lettings Requirements</div>');
$form->addHtml($form->addLabel('cli_letptype','Houses',$ptype_let['house'],'javascript:checkAll(document.forms[0], \'let1\');'));
$form->addHtml($form->addLabel('cli_letptype','Apartments',$ptype_let['apartment'],'javascript:checkAll(document.forms[0], \'let2\');'));
$form->addHtml($form->addLabel('cli_letptype','Others',$ptype_let['other'],'javascript:checkAll(document.forms[0], \'let3\');'));
$form->addData($formData3,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");



/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("</div>\n");

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($searchLink)),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Applicant > Edit");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScriptDeclaration($source['js']);
$page->setBodyAttributes(array('onLoad'=>$source['onload']));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} else { // if the form has been submitted




// join up the arrays, depending on user selection




$formData = join_arrays(array($formData0,$formData1));


if ($_GET["cli_sales"] == 'Yes') {
	$addFormData2 = array(
		'cli_saleptype'=>array(
			'label'=>'Property Type',
			'required'=>2,
			'value'=>array2string($_GET["cli_saleptype"],"|")
			)
		);
	$formData = join_arrays(array($formData,$formData2,$addFormData2));
	}
if ($_GET["cli_lettings"] == 'Yes') {
	$addFormData3 = array(
		'cli_letptype'=>array(
			'label'=>'Property Type',
			'required'=>2,
			'value'=>array2string($_GET["cli_letptype"],"|")
			)
		);
	$formData = join_arrays(array($formData,$formData3,$addFormData3));
	}

// new source
if ($_GET["cli_source"] == "x") {
	if (!$_GET["sourceNew"]) {
		$errors[] = "Please enter a referer title or choose existing from the list";
		echo error_message($errors);
		exit;
		} else {
		// check if it already exists... (not fail-safe, but worth a try)
		// lower case all, and remove space from both new and existing for comparison
		$sql_source_check = "SELECT sou_id FROM source
		WHERE sou_type = ".$_GET["cli_source1"]." AND REPLACE(LOWER(sou_title),' ','') = '".trim(strtolower(str_replace(" ","",$_GET["sourceNew"])))."'";
		$result_source_check = mysql_query($sql_source_check);
		if (mysql_num_rows($result_source_check)) {
			while($row_source_check = mysql_fetch_array($result_source_check)) {
				$_GET["cli_source"] = $row_source_check["sou_id"];
				}
			}
		else {
			$db_data_source["sou_type"] = $_GET["cli_source1"];
			$db_data_source["sou_title"] = trim($_GET["sourceNew"]);
			db_query($db_data_source,"INSERT","source","sou_id");
			// get the id
			$sql_source = "SELECT sou_id FROM source WHERE sou_type = ".$_GET["cli_source1"]." AND sou_title = '".trim($_GET["sourceNew"])."'";
			$result_source = mysql_query($sql_source);
			while($row_source = mysql_fetch_array($result_source)) {
				$_GET["cli_source"] = $row_source["sou_id"];
				}
			}
		}
	}

$result = new Validate();
$results = $result->process($formData,$_GET);
$db_data = $results['Results'];


// build return link
$return = $_SERVER['SCRIPT_NAME'].'?app_id='.$app_id.'&cli_source='.$_GET["cli_source"].'&';
if ($cli_id) {
	$results['Results']['cli_id'] = $cli_id;
	}
if ($viewForm) {
	$results['Results']['viewForm'] = $viewForm;
	}
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}




if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}


// add any additional fields to data array
$db_data['cli_reviewed'] = $date_mysql;

// update client
db_query($db_data,"UPDATE","client","cli_id",$cli_id);

//print_r($_GET);

// if sales and lettings are No, skip areas
if ($_GET["cli_sales"] == 'No' && $_GET["cli_lettings"] == 'No') {
	header("Location:calendar.php?&app_id=$app_id");
	} else {
	header("Location:?stage=2&cli_id=$cli_id&app_id=$app_id");
	}

exit;
}







break;
###########################################################
# stage 2- areas
###########################################################
case 2:

# build data arrays
$formData1 = array(
	);

$sql = "SELECT cli_area FROM client WHERE cli_id = $cli_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		foreach($row as $key=>$val) {
			$$key = $val;
			}
		}
	}

if ($_GET["cli_area"]) {
	$cli_area = $_GET["cli_area"];
	}

if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","stage","","2");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","app_id","",$app_id);
/////////////////////////////////////////////////////////////////////////////////

$areas = area(explode("|",$cli_area));
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Areas</div>');
//$form->addHtml($form->addLabel('East Dulwich','<table><tr>'.$render[1].'</tr></table>'));
$form->addHtml('<div id="Areas" style="margin-left:10px">');
$form->addHtml('<a href="javascript:checkToggle(document.forms[0], \'branch1\');" style="margin-left:5px;"><strong>Camberwell Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:5px"><tr>'.$areas[1].'</tr></table>');
$form->addHtml('<a href="javascript:checkToggle(document.forms[0], \'branch2\');" style="margin-left:5px;"><strong>Sydenham Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:5px"><tr>'.$areas[2].'</tr></table>');
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($searchLink)),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);

$navbar = navbar2($navbar_array);
$page->setTitle("Client > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

} else {


// initiale new validate instance before anything to get acces to functions (i.e. array2string)
$result = new Validate();

// add any additional fields not in array (area, etc)
$formData = array(
	'cli_area'=>array(
		'label'=>'Areas',
		'required'=>1,
		'value'=>array2string($_GET["cli_area"],"|")
		)
	);

$results = $result->process($formData,$_GET);

$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?app_id='.$app_id.'&';
if ($stage) {
	$results['Results']['stage'] = $stage;
	}
if ($cli_id) {
	$results['Results']['cli_id'] = $cli_id;
	}
if ($viewForm) {
	$results['Results']['viewForm'] = $viewForm;
	}
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}


if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}

// add any additional fields to data array
$db_data['cli_reviewed'] = $date_mysql;

db_query($db_data,"UPDATE","client","cli_id",$cli_id);

if ($app_id) {
	header("Location:calendar.php?app_id=".$app_id);
	} else {
	header("Location:client_edit.php?cli_id=$cli_id");
	}
exit;


}



break;
###########################################################
# default
###########################################################
default:

}
?>