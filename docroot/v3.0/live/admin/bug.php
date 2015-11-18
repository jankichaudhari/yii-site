<?php
require_once("inx/global.inc.php");

// bug report, suggest a feature

// edit bug
if ($_GET["bug_id"]) {

$bug_id = $_GET["bug_id"];

$sql = "SELECT bug.*,DATE_FORMAT(bug_date, '%W %D %M %Y %T') AS bug_date,
CONCAT(use_fname,' ',use_sname) AS use_name,use_email
FROM bug
LEFT JOIN user ON bug.bug_user = user.use_id WHERE bug_id = $bug_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	foreach($row as $key=>$val) {
		$$key = $val;
		}
	}

if ($bug_page) {
	$bug_page_split = explode("?",$bug_page);
	$bug_page = '<a href="'.$bug_page.'">'.str_replace("/v3.0/live/admin/","",$bug_page_split[0]).'</a>';
	}
$infotable = '
<table>
  <tr>
	<td class="label">Date</td>
	<td>'.$bug_date.'</td>
  </tr>
  <tr>
	<td class="label">User</td>
	<td>'.$use_name.'</td>
  </tr>
  <tr>
	<td class="label">Page</td>
	<td>'.$bug_page.'</td>
  </tr>
</table>

';

$formData1 = array(
	'bug_type'=>array(
		'type'=>'radio',
		'label'=>'Type',
		'value'=>$bug_type,
		'options'=>db_enum("bug","bug_type","array"),
		'default'=>'Bug Report'
		),
	'bug_blurb'=>array(
		'type'=>'textarea',
		'label'=>'Description',
		'value'=>$bug_blurb,
		'required'=>1,
		'attributes'=>array('style'=>'width:400px;height:80px')
		),
	'bug_response'=>array(
		'type'=>'textarea',
		'label'=>'Response',
		'value'=>$bug_response,
		'required'=>1,
		'attributes'=>array('style'=>'width:400px;height:80px')
		),
	'bug_priority'=>array(
		'type'=>'radio',
		'label'=>'Priority',
		'value'=>$bug_priority,
		'options'=>array(1=>1,2=>2,3=>3,4=>4,5=>5)
		),
	'bug_status'=>array(
		'type'=>'radio',
		'label'=>'Status',
		'value'=>$bug_status,
		'options'=>db_enum("bug","bug_status","array")
		)
	);

if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","save");
$form->addField("hidden","bug_id","",$bug_id);

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Assistance</div>');
$form->addHtml('<div id="'.$formName.'">');


$form->addHtml($infotable);
$form->addData($formData1,$_GET);
$form->addRow("radio","send","Send","No",array(),array('Yes'=>'Yes','No'=>'No'));
$buttons = $form->makeField("submit","","","Submit",array('class'=>'submit'));
$buttons .= $form->makeField("button","","","Cancel",array('class'=>'button','onClick'=>'javascript:history.back(1);'));
$form->addHtml($form->addDiv($buttons));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");




// start a new page
$page = new HTML_Page2($page_defaults);

$page->setTitle("Assistance");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

} else { // if form is submitted


$result = new Validate();
$results = $result->process($formData1,$_GET);
$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';

if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}

if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}

$db_data["bug_date"] = $date_mysql;
db_query($db_data,'UPDATE','bug','bug_id',$bug_id);

// send mail
$subject = "Update on your $bug_type";
$msg = "Dear $use_name,

You submitted a $bug_type on $bug_date. This $bug_type has been updated by ".$_SESSION["auth"]["use_fname"].' '.$_SESSION["auth"]["use_sname"].".

------------------------------------------------------------
Your original $bug_type:
".wordwrap($db_data["bug_blurb"], 65, "\n")."
------------------------------------------------------------
".$_SESSION["auth"]["use_fname"].' '.$_SESSION["auth"]["use_sname"]."'s response:
".wordwrap($db_data["bug_response"], 65, "\n")."
------------------------------------------------------------

The status of this $bug_type has now been set to ".$db_data["bug_status"];



if ($_GET["send"] == 'Yes') {
send_email($use_email,$_SESSION["auth"]["use_email"],$subject,$msg);
}
header("Location:superadmin_tools.php");

}









// submit bug
} else {


$formData1 = array(
	'bug_type'=>array(
		'type'=>'radio',
		'label'=>'Type',
		'value'=>$bug_type,
		'options'=>db_enum("bug","bug_type","array"),
		'default'=>'Question'
		),
	'bug_blurb'=>array(
		'type'=>'textarea',
		'label'=>'Description',
		'value'=>$bug_blurb,
		'required'=>2,
		'attributes'=>array('style'=>'width:400px;height:80px')
		),
	'bug_user'=>array(
		'type'=>'hidden',
		'value'=>$_SESSION["auth"]["use_id"]
		),
	'bug_page'=>array(
		'type'=>'hidden',
		'value'=>$_GET["bug_page"]
		)
	);


if (!$_GET["action"]) {

$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","save");

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Assistance</div>');
$form->addHtml('<div id="'.$formName.'">');
$form->addData($formData1,$_GET);
$buttons = $form->makeField("submit","","","Submit",array('class'=>'submit'));
$buttons .= $form->makeField("button","","","Cancel",array('class'=>'button','onClick'=>'document.location.href = \''.urldecode($_GET["bug_page"]).'\''));
$form->addHtml($form->addDiv($buttons));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");



$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($_GET["bug_page"])),
	'search'=>array('title'=>'Search','label'=>'Search','link'=>'search.php')
	);

$navbar = navbar2($navbar_array);
// start a new page
$page = new HTML_Page2($page_defaults);

$page->setTitle("Assistance");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

} else { // if form is submitted


$result = new Validate();
$results = $result->process($formData1,$_GET);
$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';

if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}

if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}

$db_data["bug_date"] = $date_mysql;
db_query($db_data,'INSERT','bug','bug_id');

header("Location:".$db_data["bug_page"]);

}

}

?>