<?php

require_once("inx/global.inc.php");
/*
email one or more properties to client
*/

$formData1 = array(
	'cli_email'=> array(
		'type'      => 'text',
		'label'     => 'Client Email',
		'value'     => $_GET["cli_email"],
		'required'  => 2,
		'attributes'=> array('class'=> 'wide')
	),
	'cli_name' => array(
		'type'      => 'text',
		'label'     => 'Client Name',
		'value'     => $_GET["cli_name"],
		'attributes'=> array('class'=> 'wide')
	),
	'body'     => array(
		'type'      => 'textarea',
		'label'     => 'Additional Message Body',
		'value'     => $_GET["email"],
		'attributes'=> array('style'=> 'width:400px;height:100px;'),
		'tooltip'   => 'Do not add a greeting or signature, these are added automatically'
	)
);

if (!$_GET["action"] && isset($_GET["dea_id"])) {

	if (!$_GET["dea_id"]) {
		echo error_message(array('No properties selected'));
		exit;
	}

	$dea_id = explode("|", $_GET["dea_id"]);

	foreach ($dea_id AS $dea) {
		$sql = "SELECT
	CONCAT(pro_addr3,' ',LEFT(pro_postcode,4)) AS pro_addr,
	dea_id,dea_type,dea_strapline,dea_marketprice,bra_title,bra_tel
	FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN branch ON deal.dea_branch = branch.bra_id
	WHERE deal.dea_id = $dea AND (dea_status = 'Available' OR dea_status = 'Under Offer' OR dea_status = 'Under Offer with Other' OR dea_status = 'Exchanged')
	GROUP BY dea_id
	";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage() . $sql);
		}
		$numRows = $q->numRows();
		if ($numRows !== 0) {
			while ($row = $q->fetchRow()) {
				$render .= '<label for="deal_' . $row["dea_id"] . '"><input type="checkbox" name="dea_id[]" id="deal_' . $row["dea_id"] . '" value="' . $row["dea_id"] . '" checked>' . $row["pro_addr"] . " " . format_price($row["dea_marketprice"]) . "</label><br />";
			}
		}
	}

	if (!$render) {
		echo error_message(array('No properties selected (only available properties can be emailed)'));
		exit;
	}

	$form = new Form();

	$form->addForm("", "GET", $PHP_SELF);
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "action", "", "send");
	$form->addField("hidden", "dea_id_original", "", $_GET["dea_id"]);
	$form->addField("hidden", "searchLink", "", $_GET["searchLink"]);

	$formName = 'form1';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Send Email</div>');
	$form->addHtml('<div id="inset">' . $render . '</div>');
	$form->addData($formData1, $_GET);
	$form->addHtml($form->addDiv($form->makeField("submit", "", "", "Send", array('class'=> 'submit'))));
	$form->addHtml("</fieldset>\n");

	$navbar_array = array(
		'back'  => array('title'=> 'Back', 'label'=> 'Back', 'link'=> $searchLink),
		'search'=> array('title'=> 'Property Search', 'label'=> 'Property Search', 'link'=> 'property_search.php')
	);
	$navbar       = navbar2($navbar_array);

	$page = new HTML_Page2($page_defaults);
	$page->setTitle("Send Email");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addScriptDeclaration($additional_js);
	$page->setBodyAttributes(array('onLoad'=> $onLoad));
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	$page->addBodyContent('</div>');
	$page->display();

	exit;

} else {

	$result  = new Validate();
	$results = $result->process($formData1, $_GET);
	$db_data = $results['Results'];

	$return = $_SERVER['SCRIPT_NAME'] . '?dea_id=' . $_GET["dea_id_original"] . '&';

	if (is_array($results['Results'])) {
		$return .= http_build_query($results['Results']);
	}
	if ($_GET["searchLink"]) {
		$return .= '&searchLink=' . $_GET["searchLink"];
	}
	if ($results['Errors']) {
		echo error_message($results['Errors'], urlencode($return));
		exit;
	}

	if (!$_GET["dea_id"]) {
		echo error_message(array('No properties selected'));
		exit;
	}

	foreach ($_GET["dea_id"] AS $dea) {
		$sql = "SELECT
	CONCAT(pro_addr3,' ',LEFT(pro_postcode,4)) AS pro_addr,
	dea_id,dea_type,dea_strapline,dea_marketprice,bra_title,bra_tel
	FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN branch ON deal.dea_branch = branch.bra_id
	WHERE deal.dea_id = $dea AND (dea_status = 'Available' OR dea_status = 'Under Offer' OR dea_status = 'Under Offer with Other' OR dea_status = 'Exchanged')
	GROUP BY dea_id
	";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage() . $sql);
		}
		$numRows = $q->numRows();
		if ($numRows !== 0) {
			while ($row = $q->fetchRow()) {
				$props .= $row["dea_strapline"] . "\n" . $row["pro_addr"] . "\n" . number_format($row["dea_marketprice"]) . " (GBP)\n";
//				$props .= "http://www.woosterstock.co.uk/Detail.php?id=" . $row["dea_id"] . "\n\n";
				$props .= "http://www.woosterstock.co.uk/details/" . $row["dea_id"] . ".html\n\n";

			}
		}

	}

	if ($results['Results']['cli_name']) {
		$render = 'Dear ' . $results['Results']['cli_name'] . ",\n\n";
	}
	if ($results['Results']['body']) {
		$render .= $results['Results']['body'] . "\n\n";
	}
	$render .= $props;
	$render .= "Regards,\n\n" . $_SESSION["auth"]["use_fname"] . ' ' . $_SESSION["auth"]["use_sname"] . "\n\n";
	$render .= email_footer('text', $results['Results']['cli_email'], $results['Results']['cli_name']);

	$headers = "From: " . $_SESSION["auth"]["use_fname"] . " " . $_SESSION["auth"]["use_sname"] . "<" . $_SESSION["auth"]["use_email"] . ">\r\n";
	$headers .= "BCC: " . $_SESSION["auth"]["use_email"] . "\r\n";

	mail($results['Results']['cli_email'], "Wooster & Stock", $render, $headers);

//	header("Location:" . urldecode($_GET["searchLink"]));
	header("Location:" . WS_YII_URL . 'site/emailSentSuccess');
	exit;
}
?>