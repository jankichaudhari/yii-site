<?php

require_once("inx/global.inc.php");

if ($_GET["cli_id"]) {
$cli_id = $_GET["cli_id"]; }
else
{ $cli_id = $_POST["cli_id"]; }


if (!$_GET["stage"]) {
	$stage = 1;
	} else {
	$stage = $_GET["stage"];
	}

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage) {
###########################################################
# stage 1 - applicant details
###########################################################
case 1:

// must have cli_id, obtained from client_lookup.php
if (!$_GET["cli_id"]) {
	header("Location:client_lookup.php");
	exit;
	}


// default scope
if (!$_GET["cli_sales"] && $_SESSION["auth"]["default_scope"] == 'Sales') {
	$_GET["cli_sales"] = 'Yes';
	}
if (!$_GET["cli_lettings"] && $_SESSION["auth"]["default_scope"] == 'Lettings') {
	$_GET["cli_lettings"] = 'Yes';
	}

foreach ($_GET AS $key=>$val) {
	$$key = trim($val);
	}
// returns an array for the multi-dropdown source fields
$source = source($cli_source,$_SERVER['QUERY_STRING']);

if (!$cli_salestatus) {
	$salestatus_array[] = '';
	}
if (!$cli_letstatus) {
	$letstatus_array[] = '';
	}
$sql = "SELECT * FROM cstatus";
$q = $db->query($sql);
while ($row = $q->fetchRow()) {
	if ($row["cst_scope"] == 'Sales') {
		$salestatus_array[$row["cst_id"]] = $row["cst_title"];
		} elseif ($row["cst_scope"] == 'Lettings') {
		$letstatus_array[$row["cst_id"]] = $row["cst_title"];
		}
	}


$method_array = db_enum("client","cli_method","array");
array_pop($method_array); // removing 'Import'
# build data arrays
$formData1 = array(
	'cli_method'=>array(
		'type'=>'radio',
		'label'=>'Contact Method',
		'value'=>$cli_method,
		'required'=>2,
		'options'=>$method_array
		),
	'cli_source'=>array(
		'type'=>'select_multi',
		'label'=>'Lead Source',
		'required'=>2,
		'options'=>array('dd1'=>$source['dd1'],'dd2'=>$source['dd2'])
		),
	'cli_branch'=>array(
		'type'=>'select_branch',
		'label'=>'Branch',
		'required'=>2,
		'attributes'=>array('style'=>'width:200px;')
		),
	'cli_neg'=>array(
		'type'=>'select_neg',
		'label'=>'Negotiator',
		'required'=>2,
		'value'=>$_SESSION["auth"]["use_id"],
		'attributes'=>array('style'=>'width:200px;')
		),
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
		'group'=>'Price Range',
		'required'=>2,
		'options'=>array('scope'=>'sales','default'=>'Minimum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_salemax'=>array(
		'type'=>'select_price',
		'label'=>'Maximum Price',
		'group'=>'Price Range',
		'last_in_group'=>1,
		'required'=>2,
		'options'=>array('scope'=>'sales','default'=>'Maximum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_salebed'=>array(
		'type'=>'select_number',
		'label'=>'Minimum Beds',
		'required'=>2
		),
	'cli_saleemail'=>array(
		'type'=>'radio',
		'label'=>'Email Updates',
		'required'=>2,
		'default'=>'Yes',
		'options'=>db_enum("client","cli_saleemail","array")
		),
	'cli_salestatus'=>array(
	 	'type'=>'select',
		'label'=>'Current Status',
		'value'=>$cli_salestatus,
		'options'=>$salestatus_array,
		'required'=>2,
		'attributes'=>array('class'=>'wide')
		)
	);
$ptype_sale = ptype("sale",explode("|",$_GET["cli_saleptype"]));


// lettings form
$formData3 = array(
	'cli_letmin'=>array(
		'type'=>'select_price',
		'label'=>'Minimum Price',
		'group'=>'Price Range',
		'required'=>2,
		'options'=>array('scope'=>'lettings','default'=>'Minimum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_letmax'=>array(
		'type'=>'select_price',
		'label'=>'Maximum Price',
		'group'=>'Price Range',
		'last_in_group'=>1,
		'required'=>2,
		'options'=>array('scope'=>'lettings','default'=>'Maximum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_letbed'=>array(
		'type'=>'select_number',
		'label'=>'Minimum Beds',
		'required'=>2
		),
	'cli_letemail'=>array(
		'type'=>'radio',
		'label'=>'Email Updates',
		'default'=>'Yes',
		'required'=>2,
		'options'=>db_enum("client","cli_letemail","array")
		),
	'cli_letstatus'=>array(
	 	'type'=>'select',
		'label'=>'Current Status',
		'value'=>$cli_letstatus,
		'options'=>$letstatus_array,
		'required'=>2,
		'attributes'=>array('class'=>'wide')
		)
	);
$ptype_let = ptype("let",explode("|",$_GET["cli_letptype"]));

// form is not submitted, show the form
if (!$_GET["action"]) {

// start new form object
$form = new Form();

$form->addForm("form","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","cli_id","",$cli_id);

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml('<div>');
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Add Applicant</div>');
$form->addData($formData1,$_GET);
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');

/////////////////////////////////////////////////////////////////////////////////
if ($cli_sales !== 'Yes') { $sales_visible = "none"; }
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
if ($cli_lettings !== 'Yes') { $lettings_visible = "none"; }
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


$page->setTitle("Applicant > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScriptDeclaration($source['js']);
$page->setBodyAttributes(array('onLoad'=>$source['onload']));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} else { // if the form has been submitted



// join up the arrays, depending on user selection



$formData = $formData1;
if ($cli_sales == 'Yes') {
	$addFormData2 = array(
		'cli_saleptype'=>array(
			'label'=>'Property Type',
			'required'=>2,
			'value'=>array2string($_GET["cli_saleptype"],"|")
			)
		);
	$formData = join_arrays(array($formData,$formData2,$addFormData2));
	}
if ($cli_lettings == 'Yes') {
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
$return = $_SERVER['SCRIPT_NAME'].'?';
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
/*
// if being reg'd by neg, assign
// assigned neg is now a form element
if (in_array('Negotiator',$_SESSION["auth"]["roles"])) {
	$db_data["cli_neg"] = $_SESSION["auth"]["use_id"];
	}
*/
// insert client
$cli_id = db_query($db_data,"UPDATE","client","cli_id",$cli_id);

#print_r($db_data);

header("Location:?stage=3&cli_id=$cli_id");
exit;
}








break;
###########################################################
# stage 2 - property requirements
###########################################################
case 2:

# we need to know if the client is sale, letting or both
$cli_id = $_GET["cli_id"];
$sql = "SELECT cli_sales, cli_lettings FROM client WHERE cli_id = $cli_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	$cli_sales = $row["cli_sales"];
	$cli_lettings = $row["cli_lettings"];
	}

# build data arrays

if ($cli_sales == "Yes") {
	$formData1 = array(
		'cli_salemin'=>array(
			'type'=>'select_price',
			'label'=>'Minimum Price',
			'group'=>'Price Range',
			'required'=>2,
			'options'=>array('scope'=>'sales','default'=>'Minimum'),
			'attributes'=>array('style'=>'width:120px')
			),
		'cli_salemax'=>array(
			'type'=>'select_price',
			'label'=>'Maximum Price',
			'group'=>'Price Range',
			'last_in_group'=>1,
			'required'=>2,
			'options'=>array('scope'=>'sales','default'=>'Maximum'),
			'attributes'=>array('style'=>'width:120px')
			),
		'cli_salebed'=>array(
			'type'=>'select_number',
			'label'=>'Minimum Beds',
			'required'=>2
			),
		'cli_saleemail'=>array(
			'type'=>'radio',
			'label'=>'Email Updates',
			'required'=>2,
			'options'=>db_enum("client","cli_saleemail","array")
			)
		);
	$ptype_sale = ptype("sale",explode("|",$_GET["cli_saleptype"]));
	}

if ($cli_lettings == "Yes") {
	$formData2 = array(
		'cli_letmin'=>array(
			'type'=>'select_price',
			'label'=>'Minimum Price',
			'group'=>'Price Range',
			'required'=>2,
			'options'=>array('scope'=>'lettings','default'=>'Minimum'),
			'attributes'=>array('style'=>'width:120px')
			),
		'cli_letmax'=>array(
			'type'=>'select_price',
			'label'=>'Maximum Price',
			'group'=>'Price Range',
			'last_in_group'=>1,
			'required'=>2,
			'options'=>array('scope'=>'lettings','default'=>'Maximum'),
			'attributes'=>array('style'=>'width:120px')
			),
		'cli_letbed'=>array(
			'type'=>'select_number',
			'label'=>'Minimum Beds',
			'required'=>2
			),
		'cli_letemail'=>array(
			'type'=>'radio',
			'label'=>'Email Updates',
			'required'=>2,
			'options'=>db_enum("client","cli_letemail","array")
			)
		);
	$ptype_let = ptype("let",explode("|",$_GET["cli_letptype"]));
	}


if (!$_GET["action"]) {


$form = new Form();

$form->addForm("","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","stage","","2");
$form->addField("hidden","cli_id","",$cli_id);
/////////////////////////////////////////////////////////////////////////////////
if ($cli_sales == "Yes") {
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Sales Requirements</div>');
	$form->addHtml($form->addLabel('cli_saleptype','Houses',$ptype_sale['house'],'javascript:checkAll(document.forms[0], \'sale1\');'));
	$form->addHtml($form->addLabel('cli_saleptype','Apartments',$ptype_sale['apartment'],'javascript:checkAll(document.forms[0], \'sale2\');'));
	$form->addHtml($form->addLabel('cli_saleptype','Others',$ptype_sale['other'],'javascript:checkAll(document.forms[0], \'sale3\');'));
	$form->addData($formData1,$_GET);
	$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
	$form->addHtml("</fieldset>\n");
	}
/////////////////////////////////////////////////////////////////////////////////
if ($cli_lettings == "Yes") {
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Lettings Requirements</div>');
	$form->addHtml($form->addLabel('cli_letptype','Houses',$ptype_let['house'],'javascript:checkAll(document.forms[0], \'let1\');'));
	$form->addHtml($form->addLabel('cli_letptype','Apartments',$ptype_let['apartment'],'javascript:checkAll(document.forms[0], \'let2\');'));
	$form->addHtml($form->addLabel('cli_letptype','Others',$ptype_let['other'],'javascript:checkAll(document.forms[0], \'let3\');'));
	$form->addData($formData2,$_GET);
	$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
	$form->addHtml("</fieldset>\n");
	}
$form->addHtml("</div>\n");

$page->setTitle("Client > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} else { // if form is submitted

// initiale new validate instance before anything to get acces to functions (i.e. array2string)
$result = new Validate();

// add any additional fields not in array (ptype, reqs, etc)
if ($cli_sales == "Yes") {
	$addFormData1 = array(
		'cli_saleptype'=>array(
			'label'=>'Property Type',
			'required'=>2,
			'value'=>array2string($_GET["cli_saleptype"],"|")
			)
		);
	}

if ($cli_lettings == "Yes") {
	$addFormData2 = array(
		'cli_letptype'=>array(
			'label'=>'Property Type',
			'required'=>2,
			'value'=>array2string($_GET["cli_letptype"],"|")
			)
		);
	}

// join the arrays
$formData = join_arrays(array($formData1,$formData2,$addFormData1,$addFormData2));

$results = $result->process($formData,$_GET);

$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
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


$cli_id = db_query($db_data,"UPDATE","client","cli_id",$cli_id);
header("Location:?stage=3&cli_id=$cli_id");
exit;

}









break;
###########################################################
# stage 3 - areas
###########################################################
case 3:

# build data arrays
$formData1 = array(
	);

$areas = area($_GET["cli_area"]);

if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","stage","","3");
$form->addField("hidden","cli_id","",$cli_id);
/////////////////////////////////////////////////////////////////////////////////

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Areas</div>');

$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch1\');"><strong>Camberwell Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[1].'</tr></table>');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch2\');"><strong>Sydenham Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[2].'</tr></table>');
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");

$page->setTitle("Client > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

} else {


	$new_areas = $_GET["cli_area"];
	$current_areas = explode("|",$areas);

	// if no areas are chosen, delete all
	if (!$new_areas) {
		$sql = "DELETE FROM are2cli WHERE a2c_cli = $cli_id";
		$q = $db->query($sql);
		}
	else {

		// get all areas into array
		$sql = "SELECT are_id FROM area";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			$all_areas[] = $row["are_id"];
			}

		foreach ($all_areas as $val) {

			if ($current_areas && $new_areas) {

				// if val is present in CURRENT and not preset in NEW, delete
				if (in_array($val,$current_areas) && !in_array($val,$new_areas)) {
					$sql = "DELETE FROM are2cli WHERE a2c_cli = $cli_id AND a2c_are = $val";
					$q = $db->query($sql);
					}


				// if val is present in NEW and not preset in CURRENT, insert
				if (in_array($val,$new_areas) && !in_array($val,$current_areas)) {
					$db_data["a2c_cli"] = $cli_id;
					$db_data["a2c_are"] = $val;
					db_query($db_data,"INSERT","are2cli","a2c_id");
					}
				}
			}
		}

/*
// initiale new validate instance before anything to get acces to functions (i.e. array2string)
$result = new Validate();

// add any additional fields not in array (area, etc)
$formData = array(
	'cli_area'=>array(
		'label'=>'Areas',
		'required'=>2,
		'value'=>array2string($_GET["cli_area"],"|")
		)
	);

$results = $result->process($formData,$_GET);

$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
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
*/
// add any additional fields to data array
unset($db_data);
$db_data['cli_reviewed'] = $date_mysql;
$cli_id = db_query($db_data,"UPDATE","client","cli_id",$cli_id);
header("Location:client_edit.php?cli_id=$cli_id");
exit;


}





break;
###########################################################
# stage 4 -
###########################################################
case 4:

header("Location:client_edit.php?cli_id=$cli_id");
exit;
$render = '
<p>Add Client Process Complete</p>
<p>This client\'s id number is: '.$cli_id.'</p>
<p>What now?<br>
<li><a href="property_search.php?cli_id='.$cli_id.'">View properties matching this client\'s critera</a></li>
<li><a href="client_edit.php?cli_id='.$cli_id.'">Edit this client</a></li>
<li><a href="client_lookup.php">Add another new client</a></li>
<li><a href="home.php">Back to main menu</a></li>';

$page->setTitle("Client > Add");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();


break;
###########################################################
# default
###########################################################
default:

}
?>