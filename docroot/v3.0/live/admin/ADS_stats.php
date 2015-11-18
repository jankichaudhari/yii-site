<?php

require_once("inx/global.inc.php");



$advertId = $_GET["advertId"];



if ($advertId) {





	$sql = "SELECT * FROM ADS_adverts WHERE id = $advertId";

	$q = $db->query($sql);

	while ($row = $q->fetchRow()) {

		$render = '<h3>Company: '.$row["company"].'</h3><h3>Advert: '.$row["title"].'</h3>';

		}



	// show all hits for current advert



	$sql = "SELECT * FROM ADS_clicks WHERE advert = $advertId ORDER BY date DESC LIMIT 300";

	$q = $db->query($sql);

	$numRows = $q->numRows();

	$render .= '<h3>'.$numRows.' clicks</h3>';



	while ($row = $q->fetchRow()) {



		$renderTable .= '

	<tr>

		<td>'.$row["date"].'</td>

		<td>'.$row["ip"].'</td>

		<td>'.$row["agent"].'</td>

		<td>'.$row["ref"].'</td>

	</tr>';



		}



	$render .= '<table border="1" cellspacing="0" cellpadding="3">

	<tr>

		<th>Date</th>

		<th>IP</th>

		<th>User Agent</th>

		<th>Referrer </th>

	</tr>'.$renderTable.'

</table>';







	} else {

	// show all adverts

	$sql = "SELECT ADS_adverts.*, COUNT( ADS_clicks.id ) AS hits

	FROM ADS_adverts

	LEFT JOIN ADS_clicks ON ADS_adverts.id = ADS_clicks.advert

	GROUP BY ADS_adverts.id";

	$q = $db->query($sql);

	while ($row = $q->fetchRow()) {

		$renderTable .= '

	<tr>

		<td>'.$row["company"].'</td>

		<td>'.$row["title"].'</td>

		<td>'.$row["link"].'</td>

		<td>'.$row["hits"].'</td>

		<td><a href="?advertId='.$row["id"].'">View Clicks</a></td>

	</tr>';



}

	$render = '<h1>Adverts</h1>

	<table border="1" cellspacing="0" cellpadding="3">

	<tr>

		<th>Company</th>

		<th>Ad Title</th>

		<th>Link</th>

		<th>Clicks</th>

		<th>&nbsp;</th>

	</tr>'.$renderTable.'

</table>';

	}



$navbar_array = array(

	'back'=>array('title'=>'Back','label'=>'Back','link'=>'javascript:history.go(-1);'),

	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')

	);

$navbar = navbar2($navbar_array);



$page = new HTML_Page2($page_defaults);

$page->setTitle("Ad stats");

$page->addStyleSheet(getDefaultCss());

$page->addScript('js/global.js');

$page->addBodyContent($header_and_menu);

$page->addBodyContent('<div id="content_wide">');

$page->addBodyContent($navbar);

$page->addBodyContent($render);

$page->addBodyContent('</div>');

$page->display();



exit;







?>