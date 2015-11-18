<?php
require_once("inx/global.inc.php");


/*
arrange viewing:

client lookup
select or add client (carry id)
search property (this is where sales or lettings is chosen)
select one or more properties to view
preview (add, remove properties from viewing, change order, obtain key details, viewing times etc)
create appointment
edit client's requirements (sales or lettings)


new (23/10/06) - clients stored in link table cli2app, alloowing many clients in a single viewing

new (06/06/07) - new stage after appoinment to add/edit client requirements. this only shows if client has not been "reviewed" in
aggreed time span

new (12/06/07) - if client is selected, but does not have a full address, insert stage before viewing address to enter


*/

// this page cannot be used without a cli_id
// actualy it can, when adding a property to a viewing we do not need a cli_id (12/3/07)
if (!$_GET["cli_id"] && !$_GET["app_id"]) {
	header("Location:client_lookup.php?dest=viewing&date=".$_GET["date"]);
	exit;
	}

$cli_id = $_GET["cli_id"];


// skip if LETTINGS
if ($_SESSION["auth"]["default_scope"] == 'Sales' && !$_GET["skip"]) {
// check cli has address
if ($cli_id) {
	$sql = "SELECT * FROM pro2cli WHERE p2c_cli = $cli_id";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	if (!$numRows) {
		$_GET["stage"] = 'client_address';
		}
	unset($sql,$q,$numRows);
	}
}

if ($_GET["stage"]) {
	$stage = $_GET["stage"];
	}
elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
	}
else {
	// default to viewing_address, we no longer need to stages before this as it is all done by client_lookup.php
	$stage = "viewing_address";
	}





// start a new page
$page = new HTML_Page2($page_defaults);


switch ($stage):


/////////////////////////////////////////////////////////////////////////////
// client_address
// only appears if selected client has no address
/////////////////////////////////////////////////////////////////////////////
case "client_address":

// see if we have client's old address on file and display
$sql = "SELECT cli_oldaddr FROM client WHERE cli_id = $cli_id";
$cli_oldaddr = $db->getOne($sql);



// we will only have a pro_pro_id in the GET when returning from error message
// so build a form that is read-only populated with the data, and give button to change, which shows ajax screen again
if ($_GET["pro_pro_id"]) {


	$sqlP = "SELECT * FROM property WHERE pro_id = ".$_GET["pro_pro_id"]." LIMIT 1";
	$qP = $db->query($sqlP);
	if (DB::isError($qP)) {  die("db error: ".$q->getMessage()."sqlP<br>".$sqlP); }
	while ($row = $qP->fetchRow()) {

	$formData1 = array(
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

	$formData1 = array(
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





if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","stage","","client_address");
$form->addField("hidden","action","","advanced_search");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","app_id","",$_GET["app_id"]);
$form->addField("hidden","carry","",$_GET["return"]);
$form->addField("hidden","date","",$_GET["date"]);


$form->addHtml("<fieldset>\n");
//$form->addLegend('Client\'s Address');
$form->addHtml('<div class="block-header">Client\'s Address</div>');
if ($cli_oldaddr) {
	$form->addHtml('<p class="appInfo">You have chosen an existing client, but their address needs checking. Please re-enter this address in the form below</p>');
	$form->addHtml($form->addRow('textarea','cli_oldaddr','Old Address',$cli_oldaddr,array('style'=>'width:400px','readonly'=>'readonly'),'',''));
	} else {
	$form->addHtml('<p class="appInfo">You have chosen an existing client, but we do not have their address</p>');
	}
$form->addRow('radio','p2c_type','Address Type','Home','',db_enum("pro2cli","p2c_type","array"));
if (!$_GET["pro_pro_id"]) {
	$form->ajaxPostcode("by_freetext","pro");
	} else {
	$form->addData($formData,$_GET);
	$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
	}

$form->addHtml($form->addDiv($form->makeField("button","","","Skip if Address Unknown",array('onClick'=>'document.location.href = \'?'.$_SERVER['QUERY_STRING'].'&stage=viewing_address&skip=skip\'','class'=>'submit'))));

$form->addHtml("</fieldset>\n");

$form->addHtml("</div>\n");


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($searchLink)),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Arrange Viewing");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScriptDeclaration($additional_js);
$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

} else {



// validate second form, but this is only required is posctcode lookup isnt used
if (!$_GET["pro_pro_id"]) {
	$result = new Validate();
	$results = $result->process($formData1,$_GET);
	$db_data2 = $results['Results'];
	if (is_array($results['Results'])) {
		$return = http_build_query($results['Results']);
		}
	} else {
	// successfull postcode lookup, show read-only form?
	$return = "pro_pro_id=".$_GET["pro_pro_id"]."&amp;";
	}

if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}


if ($_GET["pro_pro_id"]) {
	$pro_id = $_GET["pro_pro_id"];
	$db_data3["p2c_pro"] = $pro_id;
	$db_data3["p2c_cli"] = $cli_id;
	$db_data3["p2c_type"] = $_GET["p2c_type"];
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
	$db_data3["p2c_type"] = $_GET["p2c_type"];
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




header("Location:?cli_id=$cli_id");
}



break;

/////////////////////////////////////////////////////////////////////////////
// viewing_address
// search deal+property and display any linked properties
// allow mulitple properties
// id app_id is present, add select properties to that appointment, else create new
/////////////////////////////////////////////////////////////////////////////
case "viewing_address":

// if dea_id is present (i.e. pre-selected property to view), skip property lookup
if ($_GET["dea_id"]) {
	if (is_array($_GET["dea_id"])) {
		foreach ($_GET["dea_id"] as $deal) {
			$carry_deal .= "$deal|";
			}
		} else {
		$carry_deal = $_GET["dea_id"];
		}
	header("Location:?stage=appointment&cli_id=".$_GET["cli_id"]."&dea_id=".$carry_deal);
	}


if (!$_GET["action"]) {

if (!$_GET["scope"]) {
	$_GET["scope"] = $_SESSION["auth"]["default_scope"];
	}
// disable term (pw/pcm) unless term == let
if ($_GET["scope"] == "Lettings") {
	$term_attributes = array();
	} else {
	$term_attributes = array('disabled'=>'disabled');
	}

// ensure user's brnach is NOT selected, set to ALL
if (!$_GET["branch"]) {
	$_GET["branch"] = 999;
	}
$formData1 = array(
	'scope'=>array(
		'type'=>'radio',
		'label'=>'Sales or Lettings',
		'value'=>$_GET["scope"],
		'init'=>$_SESSION["auth"]["default_scope"],
		'options'=>array('Sales'=>'sale','Lettings'=>'let'),
		'attributes'=>array('onClick'=>'javascript:disableTermField(\'scope\',\'term\');')
		),
	'keyword'=>array(
		'type'=>'text',
		'label'=>'Keyword(s)',
		'value'=>$_GET["keyword"],
		'attributes'=>array('class'=>'addr'),
		'tooltip'=>'Seperate multiple keywords with commas'
		),
	'branch'=>array(
		'type'=>'select_branch',
		'label'=>'Branch',
		'value'=>$_GET["branch"],
		'options'=>array(''=>'Any'),
		'attributes'=>array('style'=>'width:200px')
		),
	'status'=>array(
		'type'=>'select',
		'label'=>'Status',
		'value'=>$_GET["status"],
		'default'=>'Available',
		// add "any" to top of status list
		'options'=>join_arrays(array(array(''=>'Any'),db_enum("deal","dea_status","array"))),
		'attributes'=>array('style'=>'width:200px')
		/*'options'=>array(
			'Available'=>'Available',
			''=>'Any',
			'Instructed'=>'Instructed',
			'Under Offer'=>'Under Offer',
			'Under Offer with Other'=>'Under Offer with Other'
			) */
		),
	'price_min'=>array(
		'type'=>'text',
		'label'=>'Price From',
		'value'=>$_GET["price"],
		'group'=>'Price Range',
		'init'=>'(minimum)',
		'attributes'=>array('style'=>'width:100px','onFocus'=>'javascript:clearField(this,\'(minimum)\')')
		),
	'price_max'=>array(
		'type'=>'text',
		'label'=>'Price To',
		'value'=>$_GET["price"],
		'group'=>'Price Range',
		'init'=>'(maximum)',
		'attributes'=>array('style'=>'width:100px','onFocus'=>'javascript:clearField(this,\'(maximum)\')')
		),
	'term'=>array(
		'type'=>'select',
		'label'=>'Term',
		'value'=>$_GET["term"],
		'group'=>'Price Range',
		'last_in_group'=>'1',
		'attributes'=>$term_attributes,
		'options'=>array('per week'=>'per week','per month'=>'per month'),
		'tooltip'=>'If you enter a price range, properties without a price will not appear'
		),
	'bed'=>array(
		'type'=>'select_number',
		'label'=>'Minimum Beds'
		)
	);


// get any past viewings for this client
if ($cli_id) {
// get all viewings and build table

$sql = "SELECT
app_id,app_type,app_start,app_end,app_status,
CONCAT(user.use_fname,' ',user.use_sname) as use_name,CONCAT(LEFT(user.use_fname,1),LEFT(user.use_sname,1)) as use_initial,use_colour,
cli_id,GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,'(',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ', ') AS cli_name,
GROUP_CONCAT(DISTINCT CONCAT(cli_id) ORDER BY client.cli_id ASC SEPARATOR '|') AS cli,
DATE_FORMAT(appointment.app_start, '%d/%m/%y') AS app_date,
d2a_id,d2a_feedback,
CONCAT(property.pro_addr1,' ',property.pro_addr2,' ',property.pro_addr3,' ',LEFT(property.pro_postcode,4)) as pro_addr
FROM link_deal_to_appointment
LEFT JOIN appointment ON link_deal_to_appointment.d2a_app = appointment.app_id
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
WHERE
client.cli_id = $cli_id AND appointment.app_status != 'Deleted' AND appointment.app_type = 'Viewing'
GROUP BY d2a_id
ORDER BY app_start DESC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numApps = $q->numRows();
if ($numApps) {

	while ($row = $q->fetchRow()) {



		// only show feedback for appointments in the past
		if (strtotime($row["app_end"]) < strtotime($date_mysql)) {
			if (!$row["d2a_feedback"]) {
				$feedback = '(not entered)';
				} else {
				$feedback = $row["d2a_feedback"];
				}
			} else {
			$feedback = '(in future)';
			}

		// cancelled overwrites above feedback text
		if ($row["app_status"] == 'Cancelled') {
			$feedback = '(cancelled)';
			}

		if ($row["use_colour"]) {
			$use_colour = '<span class="use_col" style="background-color: #'.$row["use_colour"].';"><img src="/images/sys/admin/blank.gif" width="10" height="10" alt="'.$row["use_name"].'"></span>&nbsp;';
			}
		$use_name = $use_colour.$row["use_initial"];

		 $viewings_table .= '
  <tr>
	<td width="13%" valign="top">'.$row["app_date"].'</td>
	<td width="10%" valign="top">'.$use_name.'</td>
	<td width="57%" valign="top">'.$row["pro_addr"].'</td>
	<td width="15%" valign="top">'.$feedback.'</td>
	<td width="5%" align="right" valign="top">
	  <a href="appointment_edit.php?app_id='.$row["app_id"].'&searchLink='.$_SERVER['PHP_SELF'].urlencode('?'.replaceQueryString($_SERVER['QUERY_STRING'],'viewForm').'&viewForm=3.1').'"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View/Edit Appointment"/></a>
	  </td>
  </tr>';


		}
	}



$appointments_table = '
<div style="width:97%;height:210px;overflow:auto;margin-left:10px">
<table id="detailTable" width="97%" cellpadding="2" cellspacing="2" align="center">
  <tr>
    <th>Date</th>
    <th>Neg</th>
    <th>Property</th>
    <th colspan="2">Feedback</th>
  </tr>
'.$viewings_table.'
</table>';

}

$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","stage","","viewing_address");
$form->addField("hidden","action","","advanced_search");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","app_id","",$_GET["app_id"]);
$form->addField("hidden","carry","",$_GET["return"]);
$form->addField("hidden","date","",$_GET["date"]);
$form->addField("hidden","skip","",$_GET["skip"]);

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Property to View</div>');
$form->addHtml('<div id="'.$formName.'">');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Search",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

if ($numApps) {
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Viewing History</div>');
$form->addHtml($appointments_table);
$form->addHtml("</fieldset>\n");
}

$form->addHtml("</div>\n");



if (!$_GET["viewForm"]) {
	$viewForm = 1;
	}
$additional_js = '
if (!previousID) {
	var previousID = "form'.$viewForm.'";
	}
';

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($searchLink)),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Arrange Viewing");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScriptDeclaration($additional_js);
$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());

$page->addBodyContent('</div>');
$page->display();

exit;

} else { // if form is submitted



// construct sql
if ($_GET["scope"] == "sale") {
	$q[] = "dea_type = 'Sales' AND ";
	$return["scope"] = 'Sales';
	$db_data['cli_sales'] = 'Yes';

	} elseif ($_GET["scope"] == "let") {
	$q[] = "dea_type = 'Lettings' AND ";
	$return["scope"] = 'Lettings';
	$db_data['cli_lettings'] = 'Yes';

	} else {
	echo "You must choose Sales or Lettings";
	exit;
	}

# now we know if the client is sales or lettings applicant, we must ensure their record reflects that
if ($db_data && $cli_id) {
	db_query($db_data,"UPDATE","client","cli_id",$cli_id);
	unset($db_data);
	}
/*
if ($_GET["keyword"]) {
	$return["keyword"] = $_GET["keyword"];
	#$keyword = str_replace(" ",",",$_GET["keyword"]);
	$keywords = explode(",",$keyword);
	foreach ($keywords AS $keyword) {
		$keyword = format_data($keyword);
		$keyword_sql .= "pro_addr1 LIKE '%$keyword%' OR pro_addr2 LIKE '%$keyword%' OR pro_addr3 LIKE '%$keyword%' OR
		pro_addr4 LIKE '%$keyword%' OR pro_addr5 LIKE '%$keyword%' OR pro_postcode LIKE '%$keyword%' OR
		cli_fname LIKE '%$keyword%' OR cli_sname LIKE '%$keyword%' OR concat_ws(' ',cli_fname, cli_sname)  LIKE '%$keyword%' OR ";
		}
	$keyword_sql = "(".remove_lastchar($keyword_sql,"OR").") AND ";
	$q[] = $keyword_sql;
	}
*/

if ($_GET["keyword"]) {
	$return["keyword"] = $_GET["keyword"];

	#$keyword = str_replace(" ",",",$_GET["keyword"]);
	$keywords = explode(",",$keyword);
	foreach ($keywords AS $keyword) {

		if (strlen($keyword) > 1) { // ignoring words 2 or less
			$keyword = trim($keyword);
			// get rid of st, temporary solution
			$keyword = str_ireplace(" st ","",$keyword);

			$keyword_sql .= "CONCAT(pro_addr1,' ',pro_addr3) LIKE '%$keyword%' OR ";
			// remove period from street names i.e. st. street
			$keyword_sql .= "REPLACE(pro_addr3, '.', '') LIKE '%$keyword%' OR ";
			// remove period from street names i.e. st. street
			$keyword_sql .= "REPLACE(pro_addr3, '''', '') LIKE '%$keyword%' OR ";

			$keyword_sql .= "pro_addr1 LIKE '%$keyword%' OR pro_addr2 LIKE '%$keyword%' OR pro_addr3 LIKE '%$keyword%' OR ";
			$keyword_sql .= "pro_addr4 LIKE '%$keyword%' OR pro_addr5 LIKE '%$keyword%' OR pro_postcode LIKE '%$keyword%' OR ";
			$keyword_sql .= "dea_keywords LIKE '%$keyword%' OR dea_strapline LIKE '%$keyword%' OR are_title LIKE '%$keyword%' OR ";
			$keyword_sql .= "cli_fname LIKE '%$keyword%' OR cli_sname LIKE '%$keyword%' OR concat_ws(' ',cli_fname, cli_sname)  LIKE '%$keyword%' OR ";
			}

		}
	$keyword_sql = "(".remove_lastchar($keyword_sql,"OR").") AND ";
	$q[] = $keyword_sql;
	}


if ($_GET["price_min"] && $_GET["price_min"] !== '(minimum)') {
	$return["price_min"] = $_GET["price_min"];
	$q[] = "dea_marketprice > '".numbers_only($_GET["price_min"])."' AND ";
	}

if ($_GET["price_max"] && $_GET["price_max"] !== '(maximum)') {
	$return["price_max"] = $_GET["price_max"];
	$q[] = "dea_marketprice < '".numbers_only($_GET["price_max"])."' AND ";
	}

if ($_GET["branch"]) {
	$return["branch"] = $_GET["branch"];
	$q[] = "dea_branch = '".$_GET["branch"]."' AND ";
	}

// status needs to be multi checkboxes
if ($_GET["status"]) {
	$return["status"] = $_GET["status"];
	$q[] = "dea_status = '".$_GET["status"]."' AND ";
	}

if ($_GET["bed"]) {
	$return["bed"] = $_GET["bed"];
	$q[] = "pro_bedroom >= '".$_GET["bed"]."' AND ";
	}


if ($_GET["orderby"]) {
	$orderby = $_GET["orderby"];
	$return["orderby"] = $orderby;
	} else {
	$orderby = 'pro_addr3';
	}
if ($_GET['direction']) {
	$direction = $_GET['direction'];
	} else {
	$direction = 'ASC';
	}

if (!$q) {
	$errors[] = 'Please enter some search criteria';
	echo error_message($errors);
	exit;
	}
$returnLink = '?stage=viewing_address&cli_id='.$cli_id.'&app_id='.$app_id.'&'.http_build_query($return);
$searchLink = $_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']);
foreach ($q AS $statement){
	$sql .= $statement." ";
	}
$sql = remove_lastchar($sql,"AND");
$sql = remove_lastchar($sql,"OR");
/*
$sql = "SELECT
dea_id,dea_prop,dea_status,dea_marketprice,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr,
bra_id,bra_title,
cli_id,CONCAT(cli_fname,' ',cli_sname) AS cli_name
FROM
deal,property,branch,client
WHERE deal.dea_vendor = client.cli_id AND
deal.dea_branch = branch.bra_id AND
deal.dea_prop = property.pro_id AND
$sql
ORDER BY $orderby $direction";
*/
$sql = "SELECT
dea_id,dea_prop,dea_status,dea_marketprice,dea_valueprice,dea_type,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr,
bra_id,bra_title,
cli_id,GROUP_CONCAT(CONCAT(cli_fname,' ',cli_sname))  AS cli_name
FROM deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id
LEFT JOIN area ON property.pro_area = area.are_id
WHERE
$sql
GROUP BY deal.dea_id
ORDER BY $orderby $direction";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
	// onClick="trClick(\'client_edit.php?cli_id='.$row["cli_id"].'&searchLink='.$searchLink.'\');"

		if ($row["dea_type"] == 'Lettings') {
			$price_suffix = ' p/w';
			} elseif ($row["dea_qualifier"]) {
			//$price_suffix = ' ('.$row["dea_qualifier"].')';
			}

		if ($row["dea_marketprice"]) {
			$price = format_price($row["dea_marketprice"]).$price_suffix;
			} elseif ($row["dea_valueprice"] && !$row["dea_marketprice"]) {
			$price = format_price($row["dea_valueprice"]).$price_suffix;
			} else {
			$price = 'n/a';
			}
		$cli_name = str_replace(",",", ",$row["cli_name"]);
		$data[] = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">
		<td width="10"><label><input type="checkbox" name="dea_id[]" id="check_deal_'.$row["dea_id"].'" value="'.$row["dea_id"].'"></label></td>
		<td class="bold" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$row["pro_addr"].'</td>
		<td width="200" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$cli_name.'</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$row["dea_status"].'</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$price.'</td>
		<!--<td width="100" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$row["bra_title"].'</td>-->
		<td width="50" nowrap="nowrap">
		<a href="deal_summary.php?dea_id='.$row["dea_id"].'&searchLink='.$searchLink.'"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this property" /></a>
		<a href="javascript:dealPrint(\''.$row["dea_id"].'\');"><img src="/images/sys/admin/icons/print-icon.png" border="0" width="16" height="16" hspace="1" alt="Print this property" /></a>
		</td>
		</tr>';
		/*
		$data[] = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">
		<td width="10"><label><input type="checkbox" name="dea_id[]" id="check_deal_'.$row["dea_id"].'" value="'.$row["dea_id"].'"></label></td>
		<td class="bold" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$row["pro_addr"].'</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.format_price($row["dea_marketprice"]).'</td>
		<td width="75" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$row["dea_status"].'</td>
		<td width="125" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$row["cli_name"].'</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked = (document.getElementById(\'check_deal_'.$row["dea_id"].'\').checked ? false : true);">'.$row["bra_title"].'</td>
		</tr>';
		*/
		}
	}



require_once 'Pager/Pager.php';


$pager_params = array(
    'mode'     => 'Sliding',
    'append'   => false,  //don't append the GET parameters to the url
    'path'     => '',
    'fileName' => 'javascript:showResultPage(%d)',  //Pager replaces "%d" with the page number...
    'perPage'  => 20, //show n items per page
    'delta'    => 100,
    'itemData' => $data,
);
$pager = & Pager::factory($pager_params);
$n_pages = $pager->numPages();
$links = $pager->getLinks();

if (!$links['back']) {
	$back = "&laquo;";
	} else {
	$back = $links['back'];
	}
if (!$links['next']) {
	$next = "&raquo;";
	} else {
	$next = $links['next'];
	}

if (!$links['last']) {
	$last = "&raquo;";
	} else {
	$last = $links['last'];
	}



if ($n_pages) {

for ($i=1; $i <= $n_pages; ++$i) {

	$pageNum = $i;

	$results .= '<div class="page" id="page'.$pageNum.'">';

	$nav[$pageNum] = str_replace(
		array(
			'<b><u>1</u></b>',
			$pageNum.'</a>',
			"&nbsp;&nbsp;&nbsp;",
			"page"
			),
		array(
			'<a href="javascript:showResultPage(1)" title="page 1">1</a>',
			'<b>'.$pageNum.'</b></a>',
			"&nbsp;",
			"Page"
			),
		$links["pages"]
		);

	$results .= '

<div id="header">
<table>
  <tr>
	<td>'.$numRows.' records found';
	if ($nav[$pageNum]) {
		$results .= ' - Page: '.$nav[$pageNum];
		}
	$results .= '</td>
    <td align="right"><a href="'.urldecode($returnLink).'">Modify Search</a> / <a href="property_search.php">New Search</a></td>
  </tr>
</table>
</div>

	';

$results .= '<table>
  <tr>
    '.columnHeader(array(
	array('title'=>'Address','column'=>'pro_addr3','colspan'=>'2'),
	array('title'=>'Vendor(s)','column'=>'cli_name'),
	array('title'=>'Status','column'=>'dea_status'),
	array('title'=>'Price'),
	#array('title'=>'Branch','column'=>'dea_branch'),
	array('title'=>'&nbsp;')
	),$_SERVER["QUERY_STRING"]).'
  </tr>';
    foreach ($pager->getPageData($i) as $item) {
        $results .= $item;
    	}
	$results .= '</table>';
    $results .= '</div>'."\n\n";
	}


/*

$params = array(
    'mode'       => 'Sliding',
    'perPage'    => 20,
    'delta'      => 2,
    'itemData'   => $data
);
$pager = & Pager::factory($params);
$data  = $pager->getPageData();
$links = $pager->getLinks();

// convert the querystring into hidden fields
$qs = parse_str($_SERVER['QUERY_STRING'], $output);
foreach ($output AS $key=>$val) {
	if ($key !== "setPerPage") {
		$hidden_fields .= '<input type="hidden" name="'.$key.'" value="'.$val.'">';
		}
	}

if (!$links['back']) {
	$back = "&laquo;";
	} else {
	$back = $links['back'];
	}
if (!$links['next']) {
	$next = "&raquo;";
	} else {
	$next = $links['next'];
	}



if ($pager->numItems()) {

$header = '
<div id="header">
<table>
  <tr>
    <td>'.$pager->numItems().' records found';
	if ($pager->numPages() > 1) {
		$header .= ' - Page: '.$back.' '.str_replace("&nbsp;&nbsp;&nbsp;","&nbsp;",$links['pages']).' '.$next.'';
		}
	$header .='</td>
	<td align="right"><a href="'.urldecode($returnLink).'">Modify Search</a></td>
  </tr>
</table>
</div>
';



$results = '
<table>
  <tr>
    '.columnHeader(array(
	array('title'=>'Address','column'=>'pro_addr3','colspan'=>'2'),
	array('title'=>'Vendor','column'=>'cli_name'),
	array('title'=>'Status','column'=>'dea_status'),
	array('title'=>'Price','column'=>'dea_marketprice'),
	array('title'=>'&nbsp;')
	#array('title'=>'Branch','column'=>'dea_branch')
	),$_SERVER["QUERY_STRING"]).'
  </tr>';
foreach ($data AS $output) {
	$results .= $output;
	}
$results .= '</table>
';
*/


$footer = '
<div id="footer">
<table>
  <tr>
    <td>
	<input type="submit" value="Use Selected Properties" class="button"></td>
  </tr>
</table>
</div>
';

} else {
// no results
$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="'.urldecode($returnLink).'">try again</a></strong></td>
  </tr>
</table>';
}

$form = new Form();

$form->addHtml("<div id=\"standard_form\">\n");

$form->addForm("deals","get","","",'onSubmit="return validateForm();"');
$form->addField("hidden","stage","","appointment");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","app_id","",$_GET["app_id"]);
$form->addField("hidden","searchLink","",$searchLink);
$form->addField("hidden","date","",$_GET["date"]);
$form->addField("hidden","skip","",$_GET["skip"]);
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Property to View</div>');
$form->addHtml('<div id="results_table">');
$form->addHtml($header);
$form->addHtml($results);
$form->addHtml($footer);
$form->addHtml('</div>');
$form->addHtml("</fieldset>\n");


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$additional_js = '
var n_pages = '.$n_pages.';
function showResultPage(n)	{
	for (var count = 1; count <= n_pages; count++) {
		document.getElementById("page"+count).style.display = \'none\';
		}
	document.getElementById("page"+n).style.display = \'block\';

	currentPage = n;
	}


function validateForm() {

var checkDeal = false;
for (var counter=0; counter < document.forms.deals.length; counter++) 	{
		if ((document.forms.deals.elements[counter].name == "dea_id[]") && (document.forms.deals.elements[counter].checked == true)) {
		checkDeal = true;
		}
	}

if (checkDeal == false) {
alert(\'You must select at least one property to view\');
return false;
}
else return true;
}
';

$page->setTitle("Arrange Viewing");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScriptDeclaration($additional_js);
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content_wide">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->addBodyContent('<script type="text/javascript" language="javascript">showResultPage(1);</script>');
$page->display();

exit;


}





break;


// appointment
case "appointment":

/*

create appointment and link to deals via link_deal_to_appointment link table...

maybe here we should skip to separate appointment page, which will be used to view/edit
all appointments? this would allow user to add/remove deals from the apointment now and
at any future time.... leave it here for now to develop, move later

the appointment page (or this stage) would require cli_id, dea_id(array)

ch-ch-changes (25/10/06)
dont save anything to appointment table until date and time have been entered, just carry the dea_id array over.....

*/


if (!$_GET["action"]) {

if (!$_GET["cli_id"]) {
	#echo "no cli_id";
	#exit;
	} else {
	$cli_id = $_GET["cli_id"];
	}

if (!$_GET["dea_id"]) {
	echo "no dea_id";
	exit;
	} else {
	$dea_id = array2string($_GET["dea_id"]);
	}
if (!$dea_id) {
	$dea_id = $_GET["dea_id"];
	}
// if we are adding additional properties to a viewing, skip the datetime bit
if ($_GET["app_id"]) {
	header("Location:?stage=appointment&action=update&app_id=$app_id&cli_id=$cli_id&dea_id=$dea_id&searchLink=$searchLink");
	}



if ($_GET["date"]) {
	$app_date = urldecode($_GET["date"]);
	// check date is valid, it should be nn/nn/nnnn - dirty fix
	if (strlen($app_date) != 10) {
		$app_date = date('d/m/Y');
		}
	}
else {
	// default date and time set to now
	$app_date = date('d/m/Y');
	$app_time = date('G:i');
	}

// count number of deals and calculate estimated duration
if (strstr($dea_id,"|")) {
	$dea_temp = explode("|",$dea_id);
	$duration = count($dea_temp) * $default_viewing_duration;
	} else { // single deal
	$duration = $default_viewing_duration;
	}

// change sydenham, lettins branch to camberwell, as only one calendar in use
if ($_SESSION["auth"]["use_branch"] == 4) {
	$branch = 3;
	} else {
	$branch = $_SESSION["auth"]["use_branch"];
	}

// show (unassigned) if user is not a neg
if (!in_array('Negotiator',$_SESSION["auth"]["roles"])) {
	$user = 0;
	} else {
	$user = $_SESSION["auth"]["use_id"];
	}

$formData1 = array(
	'calendarID'=>array(
		'type'=>'select_branch_2',
		'label'=>'Branch',
		'value'=>$branch,
		'attributes'=>array('class'=>'medium')
		),
	'app_user'=>array(
		'type'=>'select_user',
		'label'=>'Negotiator',
		'value'=>$user,
		'attributes'=>array('class'=>'medium'),
		'options'=>array(''=>'(unassigned)')
		),
	'app_date'=>array(
		'type'=>'datetime',
		'label'=>'Date',
		'value'=>$app_date,
		'attributes'=>array('class'=>'medium','readonly'=>'readonly'),
		'tooltip'=>'Today\'s date is selected by default'
		),
	'app_time'=>array(
		'type'=>'time',
		'label'=>'Start Time',
		'value'=>$app_time
		),
	'app_duration'=>array(
		'type'=>'select_duration',
		'label'=>'Estimated Duration',
		'value'=>$duration,
		'attributes'=>array('class'=>'medium'),
		'tooltip'=>'Duration is estimated at '.$default_viewing_duration.' minutes per property'
		),
	'notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$app["notes"],
		'attributes'=>array('class'=>'noteInput')
		)
	);





$form = new Form();

$form->addHtml("<div id=\"standard_form\">\n");
$form->addForm("","get");
$form->addField("hidden","stage","","appointment");
$form->addField("hidden","action","","update");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","app_id","",$_GET["app_id"]);
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","searchLink","",$searchLink);
$form->addField("hidden","skip","",$_GET["skip"]);
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Appointment</div>');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","submit","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");




$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($searchLink)),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Arrange Viewing");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/CalendarPopup.js');
$page->addScriptDeclaration('document.write(getCalendarStyles());var popcalapp_date = new CalendarPopup("popCalDivapp_date");popcalapp_date.showYearNavigation(); ');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} elseif ($_GET["action"] == "update") { //if form is submitted





// multiple deals selected, delimiited with pipe (this comes from property search page)
if (strstr($_GET["dea_id"],"|")) {
	$dea_id = explode("|",$_GET["dea_id"]);
	} else {
	$dea_id = $_GET["dea_id"];
	}

#print_r($dea_id);


// if appointment dosen't already exists...
if (!$_GET["app_id"]) {

	// create appointment row
	$db_data["app_type"] = 'Viewing';

	$date_parts = explode("/",$_GET["app_date"]);
	$day = $date_parts[0];
	$month = $date_parts[1];
	$year = $date_parts[2];

	$app_date = $year.'-'.$month.'-'.$day;
	$app_start = $app_date.' '.$app_time_hour.':'.$app_time_min.':00';

	$app_start = strtotime($app_start);
	$app_end = $app_start + ($_GET["app_duration"] * 60);

	$db_data["app_start"] = date('Y-m-d G:i:s',$app_start);
	$db_data["app_end"] = date('Y-m-d G:i:s',$app_end);
	$db_data["calendarID"] = $_GET["calendarID"];

	#$db_data["app_client"] = $cli_id; // lead client (also stored in cli2app table), maybe not use this in future (delete field)
	$db_data["app_bookedby"] = $_SESSION["auth"]["use_id"]; // booked by
	$db_data["app_user"] = $_GET["app_user"]; // lead neg
	$db_data["app_created"] = $date_mysql;
	$app_id = db_query($db_data,"INSERT","appointment","app_id");

	unset($db_data);

	// add to cli2app table
	$db_data["c2a_cli"] = $cli_id;
	$db_data["c2a_app"] = $app_id;
	db_query($db_data,"INSERT","cli2app","c2a_id");
	unset($db_data);

	// extract notes from _GET and store in notes table
	if ($_GET["notes"]) {
		$notes = clean_input($_GET["notes"]);
		unset($db_data["notes"]);
		if ($notes) {
			$db_data2 = array(
				'not_blurb'=>$notes,
				'not_row'=>$app_id,
				'not_type'=>'appointment',
				'not_user'=>$_SESSION["auth"]["use_id"],
				'not_date'=>$date_mysql
				);
			db_query($db_data2,"INSERT","note","not_id");
			}
		}
	/*
	// no longer using this as the neg is added to the appointment table, and use2app is used for additional attendees
	// add to use2app table
	// if user is a negotiator, add them as first user, else add none (not done yet)
	$db_data["u2a_use"] = $_SESSION["auth"]["use_id"];
	$db_data["u2a_app"] = $app_id;
	db_query($db_data,"INSERT","use2app","u2a_id");
	unset($db_data);
	*/
	// count is used to number the viewings in link_deal_to_appointment table
	$count = 1;

} else { // if appointment already stored (i.e. we have chosed to add more properties to it)



	$app_id = $_GET["app_id"];
	// get highest count and increment from that in link_deal_to_appointment table
	$sql = "SELECT d2a_ord FROM link_deal_to_appointment WHERE d2a_app = $app_id";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	$count = $q->numRows()+1;

}


// create link_deal_to_appointment row(s), do not allow duplicates
// if multiple properties (deals) are selected)
if (is_array($dea_id)) {
	foreach ($dea_id AS $deal) {
		// checking for duplicates
		$sql = "SELECT * FROM link_deal_to_appointment WHERE d2a_dea = '$deal' AND d2a_app = '$app_id'";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
		if (!$q->numRows()) {
			$db_data["d2a_dea"] = $deal;
			$db_data["d2a_app"] = $app_id;
			$db_data["d2a_ord"] = $count;
			db_query($db_data,"INSERT","link_deal_to_appointment","d2a_id");
			unset($db_data);
			$count++;
			}
		}
	}
// single deal
else {
	// checking for duplicates
	$sql = "SELECT * FROM link_deal_to_appointment WHERE d2a_dea = '$dea_id' AND d2a_app = '$app_id'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	if (!$q->numRows()) {
		$db_data["d2a_dea"] = $dea_id;
		$db_data["d2a_app"] = $app_id;
		$db_data["d2a_ord"] = $count;
		db_query($db_data,"INSERT","link_deal_to_appointment","d2a_id");
		unset($db_data);
		}
	}


// notify - update the app_notify field purely to create the neccesary environment to run the notify function
unset($db_data);
$db_data["app_notify"] = 'Yes';
$db_response = db_query($db_data,"UPDATE","appointment","app_id",$app_id,true);
notify($db_response,'add');



// adding properties to appointment, forward to appointment page
if ($_GET["app_id"]) {
	parse_str($_GET["searchLink"],$output);
	header("Location:appointment_edit.php?app_id=$app_id&searchLink=".$output["carry"]);
	exit;
	} else {

	// if client has not been reviewed in the past $client_review_period, go to edit page
	$sql = "SELECT cli_reviewed FROM client WHERE cli_id = $cli_id";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$cli_reviewed = $row["cli_reviewed"];
		}

	// removed applicant review
	/*
	if ((strtotime($date_mysql)-strtotime($cli_reviewed)) > $client_review_period) {
		header("Location:applicant_edit.php?cli_id=$cli_id&app_id=$app_id");
		} else {
		header("Location:calendar.php?app_id=$app_id");
		}
	*/
	header("Location:calendar.php?app_id=$app_id");

	exit;
	}

}




break;


// appointment
case "requirements":

/*

show client requirements for editing

*/


if (!$_GET["action"]) {

if (!$_GET["cli_id"]) {
	echo "no cli_id";
	exit;
	} else {
	$cli_id = $_GET["cli_id"];
	}

if (!$_GET["app_id"]) {
	echo "no app_id";
	exit;
	} else {
	$app_id = $_GET["app_id"];
	}

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


$formData1 = array(
	'cli_salemin'=>array(
		'type'=>'select_price',
		'value'=>$cli_salemin,
		'label'=>'Minimum Price',
		'group'=>'Price Range',
		'required'=>2,
		'options'=>array('scope'=>'sales','default'=>'Minimum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_salemax'=>array(
		'type'=>'select_price',
		'value'=>$cli_salemax,
		'label'=>'Maximum Price',
		'group'=>'Price Range',
		'last_in_group'=>1,
		'required'=>2,
		'options'=>array('scope'=>'sales','default'=>'Maximum'),
		'attributes'=>array('style'=>'width:120px')
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
$ptype_sale = ptype("sale",explode("|",$cli_saleptype));


$formData2 = array(
	'cli_letmin'=>array(
		'type'=>'select_price',
		'value'=>$cli_letmin,
		'label'=>'Minimum Price',
		'group'=>'Price Range',
		'required'=>2,
		'options'=>array('scope'=>'lettings','default'=>'Minimum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_letmax'=>array(
		'type'=>'select_price',
		'value'=>$cli_letmax,
		'label'=>'Maximum Price',
		'group'=>'Price Range',
		'last_in_group'=>1,
		'required'=>2,
		'options'=>array('scope'=>'lettings','default'=>'Maximum'),
		'attributes'=>array('style'=>'width:120px')
		),
	'cli_letbed'=>array(
		'type'=>'select_number',
		'value'=>$cli_letbed,
		'label'=>'Minimum Beds',
		'required'=>2
		),
	'cli_letemail'=>array(
		'type'=>'radio',
		'value'=>$cli_letemail,
		'label'=>'Email Updates',
		'required'=>2,
		'options'=>db_enum("client","cli_letemail","array")
		)
	);
$ptype_let = ptype("let",explode("|",$cli_letptype));



$form = new Form();

$form->addHtml("<div id=\"standard_form\">\n");
$form->addForm("","get");
$form->addField("hidden","stage","","requirements");
$form->addField("hidden","action","","update");
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","app_id","",$_GET["app_id"]);
$form->addField("hidden","searchLink","",$searchLink);

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Sales Requirements</div>');
$form->addHtml($form->addLabel('cli_saleptype','Houses',$ptype_sale['house'],'javascript:checkAll(document.forms[0], \'sale1\');'));
$form->addHtml($form->addLabel('cli_saleptype','Apartments',$ptype_sale['apartment'],'javascript:checkAll(document.forms[0], \'sale2\');'));
$form->addHtml($form->addLabel('cli_saleptype','Others',$ptype_sale['other'],'javascript:checkAll(document.forms[0], \'sale3\');'));
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","submit","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Lettings Requirements</div>');
$form->addHtml($form->addLabel('cli_letptype','Houses',$ptype_let['house'],'javascript:checkAll(document.forms[0], \'sale1\');'));
$form->addHtml($form->addLabel('cli_letptype','Apartments',$ptype_let['apartment'],'javascript:checkAll(document.forms[0], \'sale2\');'));
$form->addHtml($form->addLabel('cli_letptype','Others',$ptype_let['other'],'javascript:checkAll(document.forms[0], \'sale3\');'));
$form->addData($formData2,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","submit","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");

$form->addHtml("</div>\n");




$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($searchLink)),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Arrange Viewing");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


} elseif ($_GET["action"] == "update") { //if form is submitted







// creating a new appointment, forward to calendar
header("Location:calendar.php?app_id=$app_id");
exit;


}




break;

/////////////////////////////////////////////////////////////////////////////
// if no stage is defined
/////////////////////////////////////////////////////////////////////////////
default:

endswitch;
?>