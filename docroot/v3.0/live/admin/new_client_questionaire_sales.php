<?php
require_once("inx/global.inc.php");




// get existing values
if ($_GET["cli_id"]){
	$cli_id = $_GET["cli_id"];
	} elseif ($_POST["cli_id"]) {
	$cli_id = $_POST["cli_id"];
	} else {
	exit;
	}

$sql = "SELECT
client.*,property.*,
GROUP_CONCAT(DISTINCT CONCAT(tel_id,'~',tel_number,'~',tel_type,'~',tel_ord) ORDER BY tel_ord ASC SEPARATOR '|') AS tel
FROM client
LEFT JOIN tel ON client.cli_id = tel.tel_cli
LEFT JOIN pro2cli ON pro2cli.p2c_cli = client.cli_id
LEFT JOIN property ON pro2cli.p2c_pro = property.pro_id
WHERE client.cli_id = '$cli_id'
GROUP BY client.cli_id";
$q = $db->query($sql);
if ($q->numRows() == 0) {
	die("error, client not found");
	}
while ($row = $q->fetchRow()) {

	// active clients cannot complete the questionaire
	if ($row["cli_status"] == 'Active') {
		header("Location:client_edit.php?cli_id=".$row["cli_id"]);
		}


	foreach ($row as $key=>$val) {
		$$key = $val;
		}

	}

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




$form1 = array(
	'cli_salutation'=>array(
		'type'=>'select',
		'group'=>'Full Name',
		'label'=>'Salutation',
		'value'=>$cli_salutation,
		'required'=>2,
		'options'=>join_arrays(array(array(''=>''),db_enum("client","cli_salutation","array"))),
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
		'label'=>'Telephone',
		'value'=>$telephone
		),
	'cli_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$cli_email,
		'required'=>3,
		'attributes'=>array('style'=>'width:320px','maxlength'=>255)
		)
	);


$form2 = array(
	'pro_addr1'=>array(
		'type'=>'text',
		'label'=>'House Number',
		'value'=>$pro_addr1,
		'required'=>2,
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
	);




$sql = "SELECT * FROM cstatus WHERE cst_scope = 'Sales'";
$q = $db->query($sql);
while ($row = $q->fetchRow()) {
	if ($row["cst_id"] == $cli_salestatus) {
		$checked = ' checked="checked"';
		} else {
		unset($checked);
		}
	$statusRender .= '<div style="overflow:auto;">
<label class="formLabel">'.$row["cst_title"].'</label>
<span class="required">
<input type="radio" name="cli_saleletstatus" value="'.$row["cst_id"].'"'.$checked.' />
</span>

</div>
';

	}




$areas = array('Camberwell'=>'1','Sydenham'=>'2');

$sql = "SELECT * FROM ptype WHERE pty_type IS NULL";
$q = $db->query($sql);
while ($row = $q->fetchRow()) {
	$propertytypes[$row["pty_title"]] = $row["pty_id"];
	}

// sales specific forms


$price_brackets = array(
	'0-250000'=>'&pound;0 to &pound;250k',
	'250000-350000'=>'&pound;250k to &pound;350k',
	'350000-500000'=>'&pound;350k to &pound;500k',
	'500000-750000'=>'&pound;500k to &pound;750k',
	'750000-1000000'=>'&pound;750k to &pound;1 million',
	'1000000-2000000'=>'&pound;1 million to &pound;2 million'
	);
$deposits = array (
	'0-15'=>'0-15%',
	'16-25'=>'16-25%',
	'26-50'=>'26-50%',
	'51-75'=>'51-75%',
	'100'=>'100%'
	);

$form3 = array(
	'cli_selling'=>array(
		'type'=>'radio',
		'label'=>'Are you selling?',
		'required'=>2,
		'options'=>array('Yes'=>'Yes','No'=>'No'),
		'value'=>$cli_selling
		),
	'cli_renting'=>array(
		'type'=>'radio',
		'label'=>'Are you renting?',
		'required'=>2,
		'options'=>array('Yes'=>'Yes','No'=>'No'),
		'value'=>$cli_renting
		),
	'cli_valuation'=>array(
		'type'=>'radio',
		'label'=>'Requires Valuation?',
		'required'=>2,
		'options'=>array('Yes'=>'Yes','No'=>'No'),
		'value'=>$cli_valuation
		),
	'cli_wparents'=>array(
		'type'=>'radio',
		'label'=>'Living with parents?',
		'required'=>2,
		'options'=>array('Yes'=>'Yes','No'=>'No'),
		'value'=>$cli_wparents
		)
	);

$form5 = array(
	'cli_area'=>array(
		'type'=>'checkbox',
		'label'=>'Area(s)',
		'required'=>2,
		'options'=>$areas
		),
	'cli_propertytype'=>array(
		'type'=>'checkbox',
		'label'=>'Property Type(s)',
		'required'=>2,
		'options'=>$propertytypes
		),
	'cli_price_bracket'=>array(
		'type'=>'select',
		'label'=>'Price Bracket',
		'required'=>2,
		'options'=>$price_brackets,
		'attributes'=>array('style'=>'width:250px')
		),
	'cli_deposit'=>array(
		'type'=>'select',
		'label'=>'Deposit',
		'required'=>2,
		'options'=>$deposits,
		'attributes'=>array('style'=>'width:100px'),
		'value'=>$cli_deposit
		),
	'cli_salebed'=>array(
		'type'=>'select_number',
		'value'=>$cli_salebed,
		'label'=>'Minimum Beds',
		'required'=>2
		),
	'cli_saleemail'=>array(
		'type'=>'radio',
		'value'=>$cli_saleemail,
		'label'=>'Email Updates',
		'required'=>2,
		'options'=>db_enum("client","cli_saleemail","array")
		)
	);









$sql = "SELECT * FROM branch WHERE bra_title LIKE '%Sales%'";
$q = $db->query($sql);
while ($row = $q->fetchRow()) {
	$branches[$row["bra_id"]] = $row["bra_title"];
	}
$form6 = array(
	'cli_branch'=>array(
		'type'=>'select',
		'label'=>'Branch',
		'required'=>2,
		'options'=>$branches,
		'attributes'=>array('style'=>'width:250px'),
		'value'=>$_SESSION["auth"]["use_branch"]
		),
	'cli_neg'=>array(
		'type'=>'select_neg',
		'label'=>'Negotiator',
		'required'=>2,
		'attributes'=>array('style'=>'width:250px'),
		'value'=>$_SESSION["auth"]["use_id"]
		),
	'cli_notes'=>array(
		'type'=>'textarea',
		'label'=>'General Notes',
		'required'=>2,
		'attributes'=>array('style'=>'width:450px;height:150px;')
		)
	);



if (!$_POST) {


// start new form object
$form = new Form();

$form->addForm("testForm","post",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","searchLink","",$searchLink);
//$form->addHtml('<input type="hidden" name="action" value="update">');

$form->addHtml('<h1>New Client Questionaire</h1>');

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Personal Details</div>');
$form->addHtml('<div>');
$form->addData($form1,$_POST);
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Address</div>');
$form->addHtml('<div>');
$form->addData($form2,$_POST);
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Living position</div>');
$form->addHtml('<div>');
$form->addData($form3,$_POST);
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Buying position</div>');
$form->addHtml('<div>');
$form->addHtml($statusRender);
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Property Requirements</div>');
$form->addHtml('<div>');
$form->addData($form5,$_POST);
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Office Use</div>');
$form->addHtml('<div>');
$form->addData($form6,$_POST);
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");



$buttons = $form->makeField("submit",'activate',"","Save Changes and Activate",array('class'=>'submit','style'=>'color:red;font-size:14px;font-weight:bold;margin-left:223px'));
//$buttons .= $form->makeField("submit",'save',"","Save Changes and Leave in Pending",array('class'=>'button'));
$form->addHtml($buttons);


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink),
	'search'=>array('title'=>'Client Search','label'=>'Client Search','link'=>'client_search.php')
	);
$navbar = navbar2($navbar_array);

$page = new HTML_Page2($page_defaults);
$page->setTitle("Client > New Client Questionaire");
$page->addStyleSheet(getDefaultCss());
$page->addStyleDeclaration('#standard_form .formLabel { width:200px; } #standard_form .required label input { vertical-align:middle; }');
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div><!--'.$sql.'-->');
$page->display();


} else {



	$result = new Validate();


	if ($_POST["cli_notes"]) {
		$notes = $_POST["cli_notes"];
		$db_data_note = array(
			'not_blurb'=>$notes,
			'not_row'=>$cli_id,
			'not_type'=>'client_general',
			'not_user'=>$_SESSION["auth"]["use_id"],
			'not_date'=>$date_mysql
			);
		db_query($db_data_note,"INSERT","note","not_id");
		}
	unset($form6["cli_notes"]);


	// check if existing phone numbers have been changed and update, do not allow blanks
	if ($telephone) {
		foreach($telephone as $key=>$val) {
			$tel_count = ($key+1);
			if (($_POST["tel".$tel_count] !== $val["number"] || $_POST["tel".$tel_count."type"] !== $val["type"]) && trim($_POST["tel".$tel_count])) {
				$db_data['tel_number'] = phone_format($_POST["tel".$tel_count]);
				$db_data['tel_type'] = $_POST["tel".$tel_count."type"];
				$db_data['tel_cli'] = $cli_id;
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
				'tel_cli'=>$cli_id,
				'tel_ord'=>$ord
				);
			db_query($db_data,"INSERT","tel","tel_id");
			unset($db_data);
			} else {
			$errors[] = 'Please enter a valid phone number';
			}
		}


	//////// address ////////////
	$results = $result->process($form2,$_POST);
	$db_data = $results['Results'];

	if ($db_data["pro_addr3"] || $db_data["pro_postcode"]) {

		// here, in fuure, we should check table for existing properties to prevent duplicates
		$db_data["pro_pcid"] = '-1';
		$pro_id = db_query($db_data,"INSERT","property","pro_id");

		// insert into pro2cli table linkage
		$db_data2["p2c_cli"] = $_POST["cli_id"];
		$db_data2["p2c_pro"] = $pro_id;
		$db_data2["p2c_type"] = 'Home';
		db_query($db_data2,"INSERT","pro2cli","p2c_id");
		unset($db_data2);
		// if client has not default address, make the above property it
		$sql = "SELECT cli_pro FROM client WHERE cli_id = '".$_POST["cli_id"]."'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			if ($row["cli_pro"] == 0) {
				$db_dataD["cli_pro"] = $pro_id;
				db_query($db_dataD,"UPDATE","client","cli_id",$_POST["cli_id"]);
				}
			}

		}
	unset($db_dataD,$db_data,$form2);


	// forms that need no crowbarring

	$forms = $form1+$form3;

	$results = $result->process($forms,$_POST);
	$db_data = $results['Results'];

	$db_data["cli_moveby"] = date('Y-m-d',strtotime($db_data["cli_moveby"]));




	// requiremenets $form5 //
	if ($_POST["cli_area"]) {

		foreach ($_POST["cli_area"] as $ptype) {
			$sql = "SELECT * FROM area WHERE are_branch = $ptype";
			$q = $db->query($sql);
			while ($row = $q->fetchRow()) {

				$db_data_area["a2c_are"] = $row["are_id"];
				$db_data_area["a2c_cli"] = $cli_id;
				db_query($db_data_area,"INSERT","are2cli","a2c_id","");
				unset($db_data_area);
				}
			}
		}


	if ($_POST["cli_propertytype"]) {

		foreach ($_POST["cli_propertytype"] as $ptype) {
			$sql = "SELECT * FROM ptype WHERE pty_type = $ptype";
			$q = $db->query($sql);
			while ($row = $q->fetchRow()) {
				$cli_ptype .= $row["pty_id"].'|';
				}
			}


		}

	if ($_POST["cli_price_bracket"]) {
		$parts = explode('-',$_POST["cli_price_bracket"]);
		$minprice = $parts[0];
		$maxprice = $parts[1];
		}

	$db_data["cli_deposit"] = $_POST["cli_deposit"];

	// dept specific //

	$db_data["cli_salestatus"] = $_POST["cli_saleletstatus"];
	$db_data["cli_salebed"] = $_POST["cli_salebed"];
	$db_data["cli_saleemail"] = $_POST["cli_saleemail"];
	$db_data["cli_sales"] = 'Yes';
	$db_data["cli_saleptype"] = remove_lastchar($cli_ptype,'|');
	$db_data["cli_salemin"] = $minprice;
	$db_data["cli_salemax"] = $maxprice;



	// office $form6 //
	$db_data["cli_branch"] = $_POST["cli_branch"];
	$db_data["cli_neg"] = $_POST["cli_neg"];
	// notes...



	// other
	$db_data["cli_regd"] = $_SESSION["auth"]["use_id"];
	$db_data["cli_reviewed"] = date('Y-m-d H:i:s');
	if ($_POST["activate"]) {
		$db_data["cli_status"] = 'Active';
		}

	//print_r($db_data);

	db_query($db_data,"UPDATE","client","cli_id",$cli_id);

	unset($db_data);

	$db_data["userId"] = $_SESSION["auth"]["use_id"];
	$db_data["date"] = date('Y-m-d H:i:s');
	$db_data["clientId"] = $_POST["cli_id"];
	$db_data["comment"] = 'Activated by '.$_SESSION["auth"]["use_fname"].' '.$_SESSION["auth"]["use_sname"];
	$db_data["method"] = 'Telephone';
	db_query($db_data,"INSERT","contactLog","id");

	if ($_POST["cli_valuation"] == 'Yes' && $pro_id) {
		header("Location:valuation_add.php?stage=deal&pro_id=$pro_id&cli_id=$cli_id&dea_type=Sales&dea_status=Valuation");
		} else {
		header("Location:new_client_pending.php");
		}
	}
?>