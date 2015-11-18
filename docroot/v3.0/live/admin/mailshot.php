<?php
require_once("inx/global.inc.php");

pageAccess($_SESSION["auth"]["roles"], array('Mailshot'));

if ($_GET["dea_id"]) {
	$dea_id = $_GET["dea_id"];
} elseif ($_POST["dea_id"]) {
	$dea_id = $_POST["dea_id"];
} else {
	$errors[] = "No property selected";
	echo error_message($errors);
	exit;
}

// get past mailshots for this deal - to prevent duplicates, resitrict to one per day
$sql = "SELECT mai_date FROM mailshot WHERE mai_deal = $dea_id";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {

	$mai_date = explode(" ", $row["mai_date"]);
	if ($mai_date[0] == date('Y-m-d')) {
		$errors[] = "There has already been a mailshot sent today for this property";
		echo '<div style="color:red">There has already been a mailshot sent today for this property</div>';
//		exit;
	}

}

// get property details
$sql = "SELECT dea_marketprice,dea_type,dea_strapline,dea_bedroom,dea_ptype,dea_psubtype,dea_status,
pro_area,CONCAT(pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode,4)) AS pro_addr, underTheRadar
FROM deal
LEFT JOIN property ON dea_prop = property.pro_id
WHERE dea_id = $dea_id";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {

	if ($row["dea_status"] <> 'Available') {
		$errors[] = "You can only send mailshots for Available property";
		echo error_message($errors);
		exit;
	}
	if ($row["underTheRadar"]) {
		$errors[] = "This property is marked as under the radar.";
		echo error_message($errors);
		exit;
	}

	$type     = $row["dea_type"];
	$price    = $row["dea_marketprice"];
	$pro_addr = $row["pro_addr"];

	// construct the sql

	if ($row["dea_marketprice"]) {
		$sql_inner_sales .= " ((cli_salemin * 0.9) < " . round($row["dea_marketprice"]) . " AND (cli_salemax * 1.1) > " . round($row["dea_marketprice"]) . ") AND ";
		$sql_inner_lettings .= " ((cli_letmin * 0.9) < " . round($row["dea_marketprice"]) . " AND (cli_letmax * 1.1) > " . round($row["dea_marketprice"]) . ") AND ";
	}
	if ($row["dea_bedroom"]) {
		$sql_inner_sales .= " cli_salebed <= " . $row["dea_bedroom"] . " AND ";
		$sql_inner_lettings .= " cli_letbed <= " . $row["dea_bedroom"] . "	AND ";
	}
	if ($row["dea_ptype"]) {
		$sql_ptype_sales    = " CONCAT('|',cli_saleptype,'|') LIKE '%|" . $row["dea_ptype"] . "|%' ";
		$sql_ptype_lettings = " CONCAT('|',cli_letptype,'|') LIKE '%|" . $row["dea_ptype"] . "|%' ";
	}
	if ($row["dea_psubtype"]) {
		$sql_psubtype_sales    = " CONCAT('|',cli_saleptype,'|') LIKE '%|" . $row["dea_psubtype"] . "|%' ";
		$sql_psubtype_lettings = " CONCAT('|',cli_letptype,'|') LIKE '%|" . $row["dea_psubtype"] . "|%' ";
	}

	if ($sql_ptype_sales && $sql_psubtype_sales) {
		$sql_inner_sales .= "($sql_ptype_sales OR $sql_psubtype_sales) AND ";
	}
	if ($sql_ptype_lettings && $sql_psubtype_lettings) {
		$sql_inner_lettings .= "($sql_ptype_lettings OR $sql_psubtype_lettings) AND ";
	}

	if ($row["pro_area"]) {
//		$sql_inner_sales .= " (CONCAT('|',cli_area,'|') LIKE '%|" . $row["pro_area"] . "|%' OR cli_area = '')";
//		$sql_inner_lettings .= " (CONCAT('|',cli_area,'|') LIKE '%|" . $row["pro_area"] . "|%' OR cli_area = '')";
	}

}

// matching to: type, emailalert is yes and email is present, price, beds, ptype+psubtype, area
if ($type == 'Sales') {
	$sql = "SELECT cli_id, CONCAT(cli_fname,' ',cli_sname) AS cli_name, cli_email FROM client WHERE
	cli_status != 'Archived' AND cli_sales = 'Yes' AND cli_saleemail = 'Yes' AND cli_email != '' AND
	" . rtrim($sql_inner_sales, " AND") . "
	";

}
elseif ($type == 'Lettings') {
	$sql = "SELECT cli_id, CONCAT(cli_fname,' ',cli_sname) AS cli_name, cli_email FROM client WHERE
	cli_status != 'Archived' AND cli_lettings = 'Yes' AND cli_letemail = 'Yes' AND cli_email != '' AND
	" . rtrim($sql_inner_lettings, " AND") . "
	";

}
$count = $db->getOne($sql);

if (!$_POST["action"]) {

	$form = new Form();

	$form->addForm("", "POST", $PHP_SELF, "multipart/form-data");
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "action", "", "send");
	$form->addField("hidden", "dea_id", "", $dea_id);
	$form->addField("hidden", "searchLink", "", urlencode($searchLink));

	$form->addHtml("<fieldset>\n");

	$form->addHtml('<div class="block-header">Mailshot</div>');
	$form->addHtml('<div id="' . $formName . '">');

	$form->addHtml('<p class="appInfo">' . $count . ' matching clients found</p>');
	$form->addHtml($form->addRow('select', 'mailshot_type', 'Type', '', '', $mailshot_types, ''));
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Send Now!", array('class' => 'submit'))));

	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$navbar_array = array(
		'back'   => array(
			'title' => 'Back',
			'label' => 'Back',
			'link'  => $searchLink
		),
		'search' => array(
			'title' => 'Property Search',
			'label' => 'Property Search',
			'link'  => 'property_search.php'
		)
	);
	$navbar       = navbar2($navbar_array);

	$page = new HTML_Page2($page_defaults);
	$page->setTitle("Mailshot");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	$page->addBodyContent('<a href="/admin4/instruction/customMailshot/id/' . $_GET['dea_id'] . '">Custom Mailshot</a>');
	$page->addBodyContent('</div>');
	$page->display();

	exit;

} elseif ($_POST["action"] == "send") {

	$mailshot_type = $_POST["mailshot_type"];

// mailshot sent, add to database
	$db_data["mai_deal"]  = $dea_id;
	$db_data["mai_type"]  = $mailshot_type;
	$db_data["mai_count"] = $count;
	$db_data["mai_user"]  = $_SESSION["auth"]["use_id"];
	$db_data["mai_date"]  = $date_mysql;
	$mai_id               = db_query($db_data, "INSERT", "mailshot", "mai_id");

	if (!$mai_id) {
		$errors[] = "There was a problem sending your mailshot";
		echo error_message($errors);
		exit;
	} else {
		// FORK IT
		exec("php " . dirname(__FILE__) . "/mailshot_cl.php $mai_id > /dev/null &");
//		header("Location:deal_production.php?dea_id=$dea_id&viewForm=6");
		header("Location:/admin4/instruction/production/id/".$dea_id."#mailshots");
		exit;
	}

}

?>
