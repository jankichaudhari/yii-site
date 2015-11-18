<?php
require_once("inx/global.inc.php");

/*
contacts

list / search contacts and companies

*/




if (!$_GET["action"]) {


$sql = "SELECT * FROM ctype ORDER BY cty_title";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$ctype[] = '(any)';
while ($row = $q->fetchRow()) {
	$ctype[$row["cty_id"]] = $row["cty_title"];
	}


$formData1 = array(
	'keyword'=>array(
		'type'=>'text',
		'label'=>'Keyword(s)',
		'value'=>$_GET["keyword"],
		'attributes'=>array('class'=>'addr'),
		'tooltip'=>'Seperate multiple keywords with commas'
		),
	'type'=>array(
		'type'=>'select',
		'label'=>'Type',
		'value'=>$_GET["type"],
		'attributes'=>array('class'=>'addr'),
		'options'=>$ctype
		)
	);

$form = new Form();

$form->addForm("contact_form","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","stage","","1");
$form->addField("hidden","action","","advanced_search");


// adding an insepctor to an appointment
$form->addField("hidden","app_id","",$_GET["app_id"]);

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Search Contacts</div>');
$form->addHtml('<div id="'.$formName.'">');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Search",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");
/*
$areas = area(explode("|",$_GET["cli_area"]));
$formName = 'form2';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Areas</div>');
$form->addHtml('<div id="' . $formName . '">');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch1\');"><strong>East Dulwich Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[1].'</tr></table>');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch2\');"><strong>Sydenham Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[2].'</tr></table>');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch3\');"><strong>Shad Thames Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[3].'</tr></table>');
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Search",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");
*/
if (!$_GET["viewForm"]) {
	$viewForm = 1;
	}
$additional_js = '
if (!previousID) {
	var previousID = "form'.$viewForm.'";
	}
';

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$searchLink),
	'search'=>array('title'=>'Contact Search','label'=>'Contact Search','link'=>'contact.php')
	);
$navbar = navbar2($navbar_array);
$page = new HTML_Page2($page_defaults);
$page->setTitle("Contacts");
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


if ($_GET["type"]) {
	$return["type"] = $_GET["type"];
	$type = $_GET["type"];
	$sql1 .= "(com_type = $type) AND ";
	$sql2 .= "(con_type = $type) AND ";
	$sql3 .= "(com_type = $type) AND ";

	}

if ($_GET["keyword"]) {
	$return["keyword"] = $_GET["keyword"];
	$keyword = str_replace(" ",",",$_GET["keyword"]);
	$keywords = explode(",",$keyword);
	foreach ($keywords AS $keyword) {
		$keyword = trim($keyword);
		$sql1 .= " (com_title LIKE '%$keyword%' OR con_fname LIKE '%$keyword%' OR con_sname LIKE '%$keyword%') OR ";
		$sql2 .= " (con_fname LIKE '%$keyword%' OR con_sname LIKE '%$keyword%') OR ";
		$sql3 .= " (com_title LIKE '%$keyword%') OR ";
		}
	}


if ($_GET["orderby"]) {
	$orderby = $_GET["orderby"];
	$return["orderby"] = $orderby;
	} else {
	$orderby = 'con_name';
	}
if ($_GET['direction']) {
	$direction = $_GET['direction'];
	} else {
	$direction = 'ASC';
	}


if ($return) {
	$returnLink = '?'.http_build_query($return);
	}
if ($sql1) {
$sql1 = remove_lastchar($sql1,"AND");
$sql1 = remove_lastchar($sql1,"OR");
$sql1 = "AND $sql1 ";
}

if ($sql2) {
$sql2 = remove_lastchar($sql2,"AND");
$sql2 = remove_lastchar($sql2,"OR");
$sql2 = "AND $sql2 ";
}

if ($sql3) {
$sql3 = remove_lastchar($sql3,"AND");
$sql3 = remove_lastchar($sql3,"OR");
$sql3 = "AND $sql3 ";
}
$searchLink = $_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']);


$sql = "
SELECT com_id,com_title,
GROUP_CONCAT(DISTINCT CONCAT(e.con_fname,' ',e.con_sname,'(',e.con_id,')') ORDER BY e.con_fname ASC SEPARATOR ', ') AS con_name,
con_id,cty_title,
com_title AS ord
FROM contact AS e
LEFT OUTER JOIN company ON com_id = e.con_company
LEFT JOIN ctype ON con_type = ctype.cty_id
WHERE (con_company > 0) $sql1
GROUP BY com_id

UNION ALL

SELECT con_id,'' AS empty,
CONCAT(e.con_fname,' ',e.con_sname) AS con_name,
con_id,cty_title,
con_fname AS ord
FROM contact AS e
LEFT JOIN ctype ON con_type = ctype.cty_id
WHERE (con_company = 0) $sql2
GROUP BY con_id

UNION ALL

SELECT com_id,com_title,'' AS empty,'' AS empty2,cty_title,
com_title AS ord
FROM company as sc
LEFT JOIN ctype ON com_type = ctype.cty_id
WHERE com_id NOT IN ( SELECT con_company from contact ) $sql3
GROUP BY com_id

ORDER BY ord

";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {

		$row["con_name"] = preg_replace("/\([a-z0-9\ ]+\)/", "", $row["con_name"]);

		if (!$row["com_title"]) {
			$row["com_title"] = $row["con_name"];
			$row["con_name"] = '';
			$editlink = 'contact_edit.php?con_id='.$row["con_id"];
			$identifier = 'con_'.$row["con_id"];
			} else {
			$editlink = 'company_edit.php?com_id='.$row["com_id"];
			$identifier = 'com_'.$row["com_id"];
			}
		if ($_GET["dest"] == 'inspection_add.php') {
			$checkRadio = '<input type="radio" name="con_id" id="'.$identifier.'" value="'.$row["con_id"].'">';
			} else {
			$checkRadio = '<input type="checkbox" name="con_id[]" id="'.$identifier.'" value="'.$row["con_id"].'">';
			}

		$data[] = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">
		<td width="10"><label>'.$checkRadio.'</label></td>
		<td class="bold" onmousedown="document.getElementById(\''.$identifier.'\').checked = (document.getElementById(\''.$identifier.'\').checked ? false : true);">'.$row["com_title"].'</td>
		<td onmousedown="document.getElementById(\''.$identifier.'\').checked = (document.getElementById(\''.$identifier.'\').checked ? false : true);">'.$row["cty_title"].'</td>
		<td onmousedown="document.getElementById(\''.$identifier.'\').checked = (document.getElementById(\''.$identifier.'\').checked ? false : true);">'.$row["con_name"].'</td>
		<td width="50" nowrap="nowrap">
		<a href="'.$editlink.'&searchLink='.$searchLink.'"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this contact" /></a>
		</td>
		</tr>';
		}
	}


require_once 'Pager/Pager.php';
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
	<td align="right"><a href="contact_add.php?searchLink='.$searchLink.'">Add Contact</a> / <a href="company_add.php?searchLink='.$searchLink.'">Add Company</a></td>
  </tr>
</table>
</div>
';



$results = '
<table>
  <tr>
    '.columnHeader(array(
	array('title'=>'Company or Name','colspan'=>'2'),
	array('title'=>'Type'),
	array('title'=>'Employees'),
	array('title'=>'&nbsp;')
	#array('title'=>'Branch','column'=>'dea_branch')
	),$_SERVER["QUERY_STRING"]).'
  </tr>';
foreach ($data AS $output) {
	$results .= $output;
	}
$results .= '</table>
';

if ($_GET["dest"] == 'inspection_add.php') {
	$formAction = "inspection_add.php?stage=appointment&dea_id=$dea_id&app_subtype=$app_subtype&app_id=$app_id";
	$footer = '
	<div id="footer">
	<table>
	  <tr>
		<td>
		<input type="submit" value="Use Selected Inspector" class="button" /></td>
	  </tr>
	</table>
	</div>
	';
	}
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

$form->addForm("","get",$formAction);
$form->addField("hidden","stage","","appointment");
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","dest","",$_GET["dest"]);
$form->addField("hidden","app_id","",$_GET["app_id"]);
$form->addField("hidden","app_subtype","",$_GET["app_subtype"]);
$form->addField("hidden","searchLink","",$searchLink);
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Contacts</div>');
$form->addHtml('<div id="results_table">');
$form->addHtml($header);
$form->addHtml($results);
$form->addHtml($footer);
$form->addHtml('</div>');
$form->addHtml("</fieldset>\n");


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink),
	'search'=>array('title'=>'Contact Search','label'=>'Contact Search','link'=>'contact.php')
	);
$navbar = navbar2($navbar_array);

$page = new HTML_Page2($page_defaults);
$page->setTitle("Contacts");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content_wide">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

}

?>