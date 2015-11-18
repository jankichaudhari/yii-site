<?php
require_once("inx/global.inc.php");

// list users, with edit and add pages

// only accesible to SuperAdmin and Administrator
pageAccess($_SESSION["auth"]["roles"],array('SuperAdmin','Administrator'));


$page = new HTML_Page2($page_defaults);

if ($_GET["bra_id"]) {
	$bra_id = $_GET["bra_id"];
	} elseif ($_POST["bra_id"]) {
	$bra_id = $_POST["bra_id"];
	}

if ($_GET["stage"]) {
	$stage = $_GET["stage"];
	} elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
	} else {
	$stage = 1;
	}


switch ($stage) {
###########################################################
# stage 1 - list
###########################################################
case 1:




$sql = "SELECT
*
FROM  branch ";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {

		$data[] = '
	  <tr valign="top">

		<td class="bold" width="200" style="padding-top:5px">'.$row["bra_title"].'</td>
		<td width="50" style="padding-top:5px" nowrap="nowrap">
		<a href="branch.php?stage=2&bra_id='.$row["bra_id"].'&searchLink='.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this property" /></a>
	  </td>';
		}

	}


require_once 'Pager/Pager.php';
$params = array(
    'mode'       => 'Sliding',
    'perPage'    => 26,
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
/*
$perpage = '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="GET">
'.$pager->getperpageselectbox().'
'.$hidden_fields.'
<input type="submit" value="Go" class="button" />
</form>';
*/
//$links is an ordered+associative array with 'back'/'pages'/'next'/'first'/'last'/'all' links.
//(page '.$pager->getCurrentPageID().' of '.$pager->numPages().')
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
  </tr>
</table>
</div>
';



$results = '
<table>';
foreach ($data AS $output) {
	$results .= $output;
	}
$results .= '</table>
';

$footer = '
<div id="footer">
</div>
';


} else { // no results

$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="'.urldecode($returnLink).'">try again</a></strong></td>
  </tr>
</table>';
}


$form = new Form();

$form->addHtml("<div id=\"standard_form\">\n");
$form->addForm("","get",$_SERVER['PHP_SELF']);
$form->addField("hidden","searchLink","",$searchLink);
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Branch</div>');
$form->addHtml('<div id="results_table">');
$form->addHtml($header);
$form->addHtml($results);
$form->addHtml($footer);
$form->addHtml('</div>');
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink)
	);
$navbar = navbar2($navbar_array);

$page->setTitle('Branch');
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;



break;
###########################################################
# stage 2 - edit
###########################################################
case 2:



if ($_POST["viewForm"]) {
	$viewForm = $_POST["viewForm"];
	} elseif ($_GET["viewForm"]) {
	$viewForm = $_GET["viewForm"];
	} else {
	$viewForm = 1;
	}
if ($_POST["bra_id"]) {
	$bra_id = $_POST["bra_id"];
	} elseif ($_GET["bra_id"]) {
	$bra_id = $_GET["bra_id"];
	}
$action = "add";



if ($bra_id) {
	$action = "edit";
	$sql = "SELECT
	*
	FROM branch
	WHERE bra_id = ".$bra_id."";
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
	}




/*
#overwrite database values with POST values (probably empty)
foreach ($_POST AS $key=>$val) {
	$$key = $val;
	}
#overwrite database values with GET values when returning from error message
foreach ($_GET AS $key=>$val) {
	$$key = $val;
	}
*/
$form1 = array(
	'bra_title'=>array(
		'type'=>'text',
		'label'=>'Title',
		'value'=>$bra_title,
		'required'=>2,
		'attributes'=>array('style'=>'width:280px')
		),
	'bra_addr1'=>array(
		'type'=>'text',
		'label'=>'Building Number',
		'value'=>$bra_addr1,
		'attributes'=>array('style'=>'width:280px')
		),
	'bra_addr3'=>array(
		'type'=>'text',
		'label'=>'Street',
		'value'=>$bra_addr3,
		'attributes'=>array('style'=>'width:280px')
		),
	'bra_addr4'=>array(
		'type'=>'text',
		'label'=>'Area',
		'value'=>$bra_addr4,
		'attributes'=>array('style'=>'width:280px')
		),
	'bra_postcode'=>array(
		'type'=>'text',
		'label'=>'Postcode',
		'value'=>$bra_postcode,
		'attributes'=>array('style'=>'width:280px')
		),
	'bra_tel'=>array(
		'type'=>'text',
		'label'=>'Telephone',
		'value'=>$bra_tel,
		'required'=>2,
		'attributes'=>array('style'=>'width:280px')
		),
	'bra_fax'=>array(
		'type'=>'text',
		'label'=>'Fax',
		'value'=>$bra_fax,
		'attributes'=>array('style'=>'width:280px')
		),
	'bra_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$bra_email,
		'required'=>2,
		'attributes'=>array('style'=>'width:280px')
		)
	);


if (!$_POST["action"]) {


$form = new Form();
$form->addForm("form","post",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","",$action);
$form->addField("hidden","stage","","2");
$form->addField("hidden","bra_id","",$bra_id);
$form->addField("hidden","searchLink","",$_GET["searchLink"]);


/////////////////////////////////////////////////////////////////////////////////

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Branch</div>');
$form->addHtml('<div id="'.$formName.'">');
$form->addData($$formName,$_POST);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////


$onLoad .= 'showForm('.$viewForm.'); ';

// start a new page
$page = new HTML_Page2($page_defaults);

$additional_js = '
if (!previousID) {
	var previousID = "form'.$viewForm.'";
	}
';



$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($_GET["searchLink"]))
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Branch");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
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



$bra_id = $_POST["bra_id"];

$result = new Validate();


$fields = $form1;
$viewForm = 1;
$results = $result->process($fields,$_POST);
$db_data = $results['Results'];


// build return link
$redirect = $_SERVER['SCRIPT_NAME'].'?stage=2&';
if ($bra_id) {
	$redirect .= 'bra_id='.$bra_id;
	}
if ($viewForm) {
	$redirect .= '&viewForm='.$viewForm;
	}
if ($searchLink) {
	$redirect .= '&searchLink='.urlencode($searchLink);
	}
if ($results['Errors']) {
	if (is_array($results['Results'])) {
		$redirect .= '&'.http_build_query($results['Results']);
		}
	echo error_message($results['Errors'],urlencode($redirect));
	exit;
	}


db_query($db_data,"UPDATE","branch","bra_id",$bra_id);


$msg = urlencode('Update Successful');
header("Location:$redirect&msg=$msg");
exit;



}


break;
###########################################################
# stage 3 - add
###########################################################
case 3:


$form1 = array(
	'use_salutation'=>array(
		'type'=>'select',
		'group'=>'Full Name',
		'label'=>'Salutation',
		'value'=>$use_salutation,
		'required'=>2,
		'options'=>db_enum("user","use_salutation","array"),
		'attributes'=>array('style'=>'width:60px')
		),
	'use_fname'=>array(
		'type'=>'text',
		'group'=>'Full Name',
		'label'=>'Forename',
		'value'=>$use_fname,
		'init'=>'Forename(s)',
		'required'=>2,
		'attributes'=>array('style'=>'width:100px','onFocus'=>'javascript:clearField(this,\'Forename(s)\')'),
		'function'=>'format_name'
		),
	'use_sname'=>array(
		'type'=>'text',
		'group'=>'Full Name',
		'last_in_group'=>1,
		'label'=>'Surname',
		'value'=>$use_sname,
		'init'=>'Surname',
		'required'=>2,
		'attributes'=>array('style'=>'width:152px','onFocus'=>'javascript:clearField(this,\'Surname\')'),
		'function'=>'format_name'
		),
	'use_status'=>array(
		'type'=>'radio',
		'label'=>'Status',
		'value'=>$use_status,
		'required'=>2,
		'options'=>db_enum("user","use_status","array")
		),
	'use_username'=>array(
		'type'=>'text',
		'label'=>'Username',
		'value'=>$use_username,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_username'
		),
	'use_password'=>array(
		'type'=>'text',
		'label'=>'Password',
		'default'=>random_string(30),
		'value'=>$use_password,
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		),
	'use_email'=>array(
		'type'=>'text',
		'label'=>'Email',
		'value'=>$use_email,
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		),
	'use_branch'=>array(
		'type'=>'select',
		'label'=>'Branch',
		'value'=>$use_branch,
		'required'=>2,
		'options'=>db_lookup("use_branch","branch","array"),
		'attributes'=>array('class'=>'medium')
		),
	'use_scope'=>array(
		'type'=>'radio',
		'label'=>'Default Scope',
		'value'=>$use_scope,
		'required'=>2,
		'options'=>db_enum("user","use_scope","array")
		),
	'use_colour'=>array(
		'type'=>'text',
		'label'=>'Colour',
		'value'=>$use_colour,
		'group'=>'Colour',
		'attributes'=>array('class'=>'medium','style'=>'font-weight:bold; color: #'.$use_colour.'; background-color: #'.$use_colour)
		),
	'use_colour_pick'=>array(
		'type'=>'button',
		'label'=>'Colour',
		'value'=>'Pick',
		'group'=>'Colour',
		'last_in_group'=>1,
		'attributes'=>array('class'=>'button','onClick'=>'javascript:colourPickWindow();')
		),
	'use_mobile'=>array(
		'type'=>'text',
		'label'=>'Mobile',
		'value'=>$use_mobile,
		'attributes'=>array('class'=>'medium')
		),
	'use_hometel'=>array(
		'type'=>'text',
		'label'=>'Home Tel',
		'value'=>$use_hometel,
		'attributes'=>array('class'=>'medium')
		)
	);

$form2 = array(
	'use_pcid'=>array(
		'type'=>'hidden',
		'value'=>$use_pcid
		),
	'use_addr1'=>array(
		'type'=>'text',
		'label'=>'House Number',
		'value'=>$use_addr1,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr2'=>array(
		'type'=>'text',
		'label'=>'Building Name',
		'value'=>$use_addr2,
		'required'=>1,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr3'=>array(
		'type'=>'text',
		'label'=>'Street',
		'value'=>$use_addr3,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr4'=>array(
		'type'=>'text',
		'label'=>'Town or Area',
		'value'=>$use_addr4,
		'required'=>3,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_addr5'=>array(
		'type'=>'text',
		'label'=>'City or County',
		'value'=>$use_addr5,
		'required'=>2,
		'attributes'=>array('class'=>'addr'),
		'function'=>'format_street'
		),
	'use_postcode'=>array(
		'type'=>'text',
		'label'=>'Postcode',
		'value'=>$use_postcode,
		'required'=>2,
		'attributes'=>array('class'=>'pc','maxlength'=>9),
		'function'=>'format_postcode'
		),
	'use_country'=>array(
		'type'=>'select',
		'label'=>'Country',
		'value'=>$use_country,
		'required'=>2,
		'options'=>db_lookup("use_country","country","array"),
		'attributes'=>array('class'=>'addr')
		)
	)
	;


if (!$_POST["action"]) {


$form = new Form();
$form->addForm("form","post",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","",'update');
$form->addField("hidden","stage","","3");


/////////////////////////////////////////////////////////////////////////////////

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">User</div>');
$form->addData($$formName,$_POST);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");


/////////////////////////////////////////////////////////////////////////////////





// start a new page
$page = new HTML_Page2($page_defaults);



$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>'user_admin.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("User");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
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

// validate and insert first form, get cli_id before adding property data (this may mean we lose address)
$result = new Validate();
$results = $result->process($form1,$_POST);
$db_data = $results['Results'];

// build return link
$redirect = $_SERVER['SCRIPT_NAME'].'?stage=3&';
if ($use_id) {
	$redirect .= 'use_id='.$use_id;
	}
if ($viewForm) {
	$redirect .= '&viewForm='.$viewForm;
	}
if ($searchLink) {
	$redirect .= '&searchLink='.urlencode($searchLink);
	}
if ($results['Errors']) {
	if (is_array($results['Results'])) {
		$redirect .= '&'.http_build_query($results['Results']);
		}
	echo error_message($results['Errors'],urlencode($redirect));
	exit;
	}

// validate password strength
$password = $db_data["use_password"];

// create salt
$salt = random_string(30);

$db_data["use_password"] =  encrypt_password($password,$salt);

$db_data["use_salt"] = $salt;



$use_id = db_query($db_data,"INSERT","user","use_id");

header("Location:?stage=2&use_id=$use_id");
exit;





}


###########################################################
# default
###########################################################
default:

}
?>