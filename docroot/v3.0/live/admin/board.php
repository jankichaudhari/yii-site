<?php
require_once("inx/global.inc.php");

if ($_GET["action"] == "update") {
	foreach($_GET["board"] as $key=>$val) {
		$db_data["dea_board"] = $val;
		db_query($db_data,"UPDATE","deal","dea_id",$key);
		unset($db_data);
		}
	}






// get all currently available deals, and display in list with board status; updatable




// construct sql
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


$sql = "SELECT
deal.*,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
bra_id,bra_title
FROM
deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id
WHERE
(dea_status = 'Production' OR dea_status = 'Proofing' OR dea_status = 'Available' OR dea_status = 'Under Offer' OR dea_status = 'Exchanged' OR dea_status = 'Under Offer with Other')
ORDER BY $orderby $direction";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {

		$board_status = db_enum('deal','dea_board','array');
		foreach($board_status AS $key=>$val) {
			$status_select .= '<option value="'.$key.'"';
			if ($row["dea_board"] == $val) {
				$status_select .= ' selected';
				}
			$status_select .= '>'.$val.'</option>'."\n";
			}
		$status_select = '<select name="board['.$row["dea_id"].']">'.$status_select.'</select>'; // onChange="document.boardForm.submit();"

		$data[] = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">
		<td class="bold">'.$row["pro_addr"].'</td>
		<td width="100">'.$row["dea_status"].'</td>
		<td width="150">'.$row["bra_title"].'</td>
		<td width="80">'.$status_select.'</td>
		<td width="50" nowrap="nowrap">
		<a href="/admin4/instruction/summary/id/'.$row["dea_id"].'"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this property" /></a>
		<a href="javascript:dealPrint(\''.$row["dea_id"].'\');"><img src="/images/sys/admin/icons/print-icon.png" border="0" width="16" height="16" hspace="1" alt="Print this property" /></a>
		</td>
		</tr>';

		unset($status_select);

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

$results .= '<div id="loading"><h1 style="padding-left: 20px;"><img src="/images/sys/admin/ajax-loader.gif" /> Loading</h1></div>';

for ($i=1; $i <= $n_pages; ++$i) {

	$pageNum = $i;

	$results .= '<div class="page" id="page'.$pageNum.'" style="display: none">';

	$nav[$pageNum] = str_replace(
		array(
			'<b><u>1</u></b>',
			'>'.$pageNum.'</a>',
			"&nbsp;&nbsp;&nbsp;",
			"page"
			),
		array(
			'<a href="javascript:showResultPage(1)" title="page 1">1</a>',
			'><b>'.$pageNum.'</b></a>',
			"&nbsp;",
			"Page"
			),
		$links["pages"]
		);


$return = 'property_search.php?'.replaceQueryString($_SERVER['QUERY_STRING'],'action');
$results .= '

<div id="header">
<table>
  <tr>
	<td>'.$numRows.' records found';
	if ($nav[$pageNum]) {
		$results .= ' - Page: '.$nav[$pageNum];
		}
	$results .= '</td>
  </tr>
</table>
</div>

	';


	$results .= '<table>
  <tr>
    '.columnHeader(array(
	array('title'=>'Address','column'=>'pro_addr3'),
	array('title'=>'Deal Status','column'=>'dea_status'),
	array('title'=>'Branch','column'=>'dea_branch'),
	array('title'=>'Board Status','column'=>'dea_board'),
	array('title'=>'&nbsp;')
	),$_SERVER["QUERY_STRING"]).'
  </tr>';
    foreach ($pager->getPageData($i) as $item) {
        $results .= $item;
    	}
	$results .= '</table>';
    $results .= '</div>'."\n\n";
	}





$results .= '
<div id="footer">
<table>
  <tr>
    <td>
	<input type="submit" value="Update" class="button" style="width:150px;">
	</td>
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

$form->addForm("boardForm","get",$_SERVER['PHP_SELF']);
$form->addField("hidden","action","","update");
$form->addField("hidden","orderby","",$orderby);
$form->addField("hidden","direction","",$direction);
$form->addField("hidden","searchLink","",$searchLink);
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Board Management</div>');
$form->addHtml('<div id="results_table">');
$form->addHtml($header);
$form->addHtml($results);
#$form->addHtml($footer);
$form->addHtml('</div>');
$form->addHtml("</fieldset>\n");


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php'),
	'print'=>array('title'=>'Print','label'=>'Print','link'=>'javascript:windowPrint();')
	);
$navbar = navbar2($navbar_array);

// js to control recordset divs
$additional_js = '
var n_pages = '.$n_pages.';
function showResultPage(n)	{
	for (var count = 1; count <= n_pages; count++) {
		document.getElementById("page"+count).style.display = \'none\';
		}
	document.getElementById("page"+n).style.display = \'block\';
	document.getElementById("loading").style.display = \'none\';
	currentPage = n;
	}
';
$page = new HTML_Page2($page_defaults);
$page->setTitle("Board Management");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addSCriptDeclaration($additional_js);
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content_wide">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->addBodyContent('<script type="text/javascript" language="javascript">showResultPage(1);</script>');
$page->display();
exit;
?>