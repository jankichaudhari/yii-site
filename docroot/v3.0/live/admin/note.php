<?php
require_once("inx/global.inc.php");
// note.php
// various note related functions and actions

$not_id = $_GET["not_id"];
if (!$not_id) die("no not_id");

if ($_GET["action"] =="delete" && $not_id) {

	$db_data["not_status"] = 'Deleted';
	db_query($db_data,"UPDATE","note","not_id",$not_id);

	if ($_SERVER['HTTP_REFERER']) {
		header("Location:".$_SERVER['HTTP_REFERER']);
		} else {
		header("Location:".$_GET["return"]);
		}
	}
elseif ($_GET["action"] =="undelete" && $not_id) {

	$db_data["not_status"] = 'Active';
	db_query($db_data,"UPDATE","note","not_id",$not_id);

	if ($_GET["return"]) {
		header("Location:".$_GET["return"]);
		} else {
		header("Location:".$_SERVER['HTTP_REFERER']);
		}
	}

// edit note
else {

$sql = "SELECT not_blurb,not_status,
DATE_FORMAT(note.not_date,'%d/%m/%y') AS not_created,
DATE_FORMAT(note.not_edited,'%d/%m/%y') AS not_edited,
CONCAT(use_fname,' ',use_sname) as use_name
FROM note
LEFT JOIN user on note.not_user = user.use_id
WHERE not_id = $not_id LIMIT 1";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	$not_status = $row["not_status"];
	$not_created = $row["not_created"].' by '.$row["use_name"];
	$not_edited = $row["not_edited"];

	$formData1 = array(
		'not_id'=>array(
			'type'=>'hidden',
			'value'=>$not_id
			),
		'not_blurb'=>array(
			'type'=>'textarea',
			'label'=>'Note',
			'value'=>$row["not_blurb"],
			'attributes'=>array('class'=>'noteInput'),
			'required'=>4
			)
		);
	}




if (!$_GET["action"]) {


// get changes made to this note
$sql = "SELECT
cha_new,CONCAT(use_fname,' ',use_sname) as use_name,
DATE_FORMAT(cha_datetime,'%d/%m/%y') AS cha_date
FROM changelog
LEFT JOIN user ON changelog.cha_user = user.use_id
WHERE cha_table = 'note' AND cha_row = $not_id AND cha_field = 'not_blurb'
ORDER BY cha_datetime DESC";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows) {
	while ($row = $q->fetchRow()) {
		$changelog .= '
	<tr>
		<td class="note">'.$row["cha_date"].' - '.$row["use_name"].'<br />'.$row["cha_new"].'</td>
	</tr>';
		}

	$changelog = '<table>'.$changelog.'</table>';
	}


$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
//$form->addField("hidden","not_id","",$not_id);
$form->addField("hidden","action","","save");
$form->addField("hidden","return","",urlencode($_GET["return"]));


$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Edit Note</div>');
$form->addHtml('<div id="'.$formName.'">');

$form->addHtml('
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top">Created</td>
	<td>'.$not_created.'</td>
  </tr>
  <tr>
    <td class="label" valign="top">Last edited</td>
	<td>'.$not_edited.'</td>
  </tr>
</table>');

$form->addData($formData1,$_GET);


$buttons = $form->makeField("submit","","","Save Changes",array('class'=>'submit'));
if ($not_status == 'Deleted') {
	$buttons .= $form->makeField("button","","","UnDelete",array('class'=>'button','onClick'=>'document.location.href = \'?not_id='.$not_id.'&action=undelete&return='.urlencode($_GET["return"]).'\';'));
	} else {
	$buttons .= $form->makeField("button","","","Delete",array('class'=>'button','onClick'=>'document.location.href = \'?not_id='.$not_id.'&action=delete&return='.urlencode($_GET["return"]).'\';'));
	}
$form->addHtml($form->addDiv($buttons));
$form->addSeperator();

$form->addHtml($form->addLabel('cha','Changes History',$changelog));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");



$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($return)),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

// start a new page
$page = new HTML_Page2($page_defaults);

$page->setTitle("Edit Note");
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
	echo error_message($results['Errors']);
	exit;
	}


db_query($db_data,'UPDATE','note','not_id',$not_id);

header("Location:".urldecode($_GET["return"]));

}


}

?>