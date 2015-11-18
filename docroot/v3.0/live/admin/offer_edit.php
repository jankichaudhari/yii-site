<?php
extract($_REQUEST);
require_once("inx/global.inc.php");

/*
Offer Edit

edit an existing offer
*/

if ($_GET["stage"]) {
	$stage = $_GET["stage"];
} elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
} else {
	$stage = 1;
}

// remove client from current viewing, delete row from cli2app table
if ($_GET["do"] == "remove_client" && $_GET["cli_id"] && $_GET["off_id"]) {
	$sql = "DELETE FROM cli2off WHERE
	cli2off.c2o_cli = " . $_GET["cli_id"] . " AND
	cli2off.c2o_off = " . $_GET["off_id"];
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	header("Location:?off_id=$off_id");
	exit;
}

$page = new HTML_Page2($page_defaults);

switch ($stage) {
###########################################################
# stage 1 - submit and save offer
###########################################################
	case 1:

		if (!$_GET["off_id"]) {
			$errors[] = "No Offer ID specified";
		} else {
			$off_id = $_GET["off_id"];
		}
		if ($errors) {
			echo error_message($errors);
			exit;
		}

// get deal info
		$sql = "SELECT
offer.*,DATE_FORMAT(off_date, '%D %M %Y') AS date,
dea_id,dea_marketprice,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
cli_id,CONCAT(cli_salutation,' ',cli_fname,' ',cli_sname) AS cli_name
FROM
offer
LEFT JOIN deal ON offer.off_deal = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2off ON cli2off.c2o_off = offer.off_id
LEFT JOIN client ON cli2off.c2o_cli = client.cli_id
WHERE offer.off_id = $off_id
GROUP BY cli_id";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage());
		}
		$numRows = $q->numRows();
		if ($numRows == 0) {
			echo "select error $sql";
//			exit;
		} else {
			while ($row = $q->fetchRow()) {
				foreach ($row as $key=> $val) {
					$$key = $val;
					$render .= $key . "->" . $val . "<br>";
				}
				if ($row["cli_id"]) {
					$clients[$row["cli_id"]] = $row["cli_name"];
				}
			}
		}

		if (!$_SESSION["auth"]["default_scope"] == 'Lettings' && $dea_type != 'Lettings') {
			// restrict this page to owner of offer and manager
			if ($off_neg == $_SESSION["auth"]["use_id"]) {
				$allowed = 1;
			}
			if (in_array('Manager', $_SESSION["auth"]["roles"])) {
				$allowed = 1;
			}
			if (!$allowed) {
				$errors[] = "You do not have sufficient permissions to access this page";
				echo error_message($errors);
				exit;
			}
		}
		/*
  // render clients
  $client_split = explode("~",$cli_name);
  foreach($client_split AS $cli) {
	  $cli_details = explode("|",$cli);
	  $render_client .= '<a href="client_edit.php?cli_id='.$cli_details[1].'&searchLink='.$_SERVER['SCRIPT_NAME'].'?'.urlencode($_SERVER['QUERY_STRING']).'">'.$cli_details[0].'</a><br />';
	  }
  */
// deal table, positioned above formData
		$deal_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" width="160">Date</td>
	<td>' . $date . '</td>
  </tr>
  <tr>
    <td class="label" width="160">Property</td>
	<td><a href="deal_summary.php?dea_id=' . $dea_id . '&searchLink=' . $searchLink . '">' . $pro_addr . '</a></td>
  </tr>
  <tr>
    <td class="label">Offer</td>
	<td class="bold">' . format_price($off_price) . '</td>
  </tr>
</table>
' . renderClientOfferTable($clients, $off_id);

		$formData1 = array(
			'off_status'    => array(
				'type'      => 'select',
				'label'     => 'Status',
				'value'     => $off_status,
				'required'  => 2,
				'attributes'=> array('style'=> 'width:300px'),
				'options'   => db_enum('offer', 'off_status', 'array')
			),
			'off_neg'       => array(
				'type'      => 'select_user',
				'label'     => 'Negotiator',
				'value'     => $off_neg,
				'options'   => $negotiators,
				'attributes'=> array('style'=> 'width:300px')
			),
			'off_conditions'=> array(
				'type'      => 'textarea',
				'label'     => 'Conditions',
				'value'     => $off_conditions,
				'attributes'=> array('style'=> 'width:300px;height:60px'),
				'tooltip'   => 'Any special conditions to this offer. This will be sent to the vendor'
			),
			'off_notes'     => array(
				'type'      => 'textarea',
				'label'     => 'Notes',
				'value'     => $off_notes,
				'attributes'=> array('style'=> 'width:300px;height:60px'),
				'tooltip'   => 'Private notes, for internal use only'
			)
		);

		if (!$_GET["action"]) {

			$form = new Form();

			$form->addForm("", "GET", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "action", "", "update");
			$form->addField("hidden", "off_id", "", $off_id);
			$form->addField("hidden", "dea_id", "", $dea_id);
			$form->addField("hidden", "searchLink", "", urlencode($_GET["searchLink"]));

			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Edit Offer</div>');
			$form->addHtml($form->addHtml($deal_table));

// if other offers are present, and current is not accepted, show advice
			$sql       = "SELECT count(*) FROM offer WHERE off_deal = $dea_id AND off_id != $off_id AND off_status = 'Accepted'";
			$off_count = $db->getOne($sql);
			if ($off_count && $off_status <> 'Accepted') {
				$form->addHtml('<p class="appInfo">There is already an Accepted offer on this property. If you change the status of this offer to Accepted, the other offer will automatically be changed to Rejected');
			}

			$form->addData($formData1, $_GET);
#$this->addRow($field_array['type'],$field_name,$field_array['label'],$field_array['value'],$attributes,$options,$field_array['tooltip']);
#$form->addRow("radio","send","Send to Vendor","Yes",array(),array('Yes'=>'Yes','No'=>'No'),'Send letter or email to vendor');
			$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class'=> 'submit'))));
			$form->addHtml("</fieldset>\n");
			$form->addHtml("</div>\n");

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => $_GET["searchLink"]),
				'search'=> array('title'=> 'Property Search',
								 'label'=> 'Property Search',
								 'link' => 'property_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Edit Offer");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
#$page->addScriptDeclaration($additional_js);
#$page->setBodyAttributes(array('onLoad'=>$onLoad));
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
#$page->addBodyContent($render);
			$page->addBodyContent('</div>');
			$page->display();

		} else { // if form is submitted

			$result = new Validate();
			$fields = join_arrays(array($formData1));

			$results = $result->process($fields, $_GET);
			$db_data = $results['Results'];

			$redirect = $_SERVER['SCRIPT_NAME'] . '?off_id=' . $off_id;
			if ($return) {
				$redirect .= '&return=' . $return;
			}
			if ($results['Errors']) {
				if (is_array($results['Results'])) {
					$redirect .= '&' . http_build_query($results['Results']);
				}
				echo error_message($results['Errors'], urlencode($redirect));
				exit;
			}

			// need to ensure only one offer is set to accepted per deal
			$sql     = "SELECT off_id FROM offer WHERE off_deal = $dea_id AND off_status = 'Accepted' AND off_id != " . $_GET["off_id"];
			$q       = $db->query($sql);
			$numRows = $q->numRows();
			while ($row = $q->fetchRow()) {
				$db_data2["off_status"] = 'Rejected';
				db_query($db_data2, "UPDATE", "offer", "off_id", $row["off_id"]);
				unset($db_data2);
			}

			db_query($db_data, "UPDATE", "offer", "off_id", $_GET["off_id"]);

			// suggest setting deal status to under offer, only if status is not already under offer and deal status is accepted

			if ($db_data["off_status"] == 'Accepted') {
				$sql = "SELECT dea_status FROM deal WHERE dea_id = " . $_GET["dea_id"];
				$q   = $db->query($sql);
				if (DB::isError($q)) {
					die("db error: " . $q->getMessage());
				}
				$numRows = $q->numRows();
				while ($row = $q->fetchRow()) {
					$dea_status = $row["dea_status"];
				}
				// prompt user to set status to under offer
				if ($dea_status == 'Available') {
					header("Location:deal_underoffer.php?dea_id=$dea_id&searchLink=" . $_GET["searchLink"]);
					exit;
				}
			}

			header("Location:" . urldecode($_GET["searchLink"]));
			#header("Location:?off_id=".$_GET["off_id"]."&searchLink=".$_GET["searchLink"]);

			exit;
		}

		break;
###########################################################
# stage 2 - submit offer to vendor
###########################################################
	case 2:

		if (!$_GET["off_id"]) {
			echo "no off_id";
			exit;
		} else {
			$off_id = $_GET["off_id"];
		}

// this is a biggy! get deal, property, neg, client (who has offered), vendor, and vendor's property
		$sql = "SELECT
deal.*,
CONCAT(property.pro_addr1,' ',property.pro_addr2,' ',property.pro_addr3,' ',property.pro_postcode) AS pro_addr,
CONCAT(user.use_fname,' ',user.use_sname) AS use_name,
CONCAT(client.cli_salutation,' ',client.cli_fname,' ',client.cli_sname) AS cli_name,
CONCAT(vendor.cli_fname,' ',vendor.cli_sname) AS vendor_name,
vendor_property.pro_addr1 AS vendor_addr1,vendor_property.pro_addr2 AS vendor_addr2,vendor_property.pro_addr3 AS vendor_addr3,
vendor_property.pro_addr4 AS vendor_addr4,vendor_property.pro_addr5 AS vendor_addr5,vendor_property.pro_postcode AS vendor_postcode,
offer.off_price,offer.off_conditions,DATE_FORMAT(off_date, '%D %M %Y') AS date

FROM
offer

LEFT JOIN deal ON offer.off_deal = deal.dea_id
LEFT JOIN property ON property.pro_id = deal.dea_prop
LEFT JOIN client AS vendor ON deal.dea_vendor = vendor.cli_id
LEFT JOIN property AS vendor_property ON vendor.cli_pro = vendor_property.pro_id

LEFT JOIN client ON offer.off_client = client.cli_id
LEFT JOIN user ON offer.off_neg = user.use_id

WHERE off_id = $off_id";

		$q = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage());
		}
		$numRows = $q->numRows();
		if ($numRows == 0) {
			echo "select error";
			exit;
		} else {
			while ($row = $q->fetchRow()) {
				foreach ($row as $key=> $val) {
					$$key = $val;
					$render .= $key . "->" . $val . "<br>";
				}
			}
		}

		if (!$off_conditions) {
			$off_conditions = 'None';
		}

		$nw = new Numbers_Words();
#$off_price_word = $nw->toWords(numbers_only(format_price($off_price)),'en_GB');
		$off_price_word = $nw->toCurrency($off_price, 'en_GB', 'GBP');

		$offer_text = str_replace("  ", " ", $vendor_name . '
' . $vendor_addr1 . ' ' . $vendor_addr2 . ' ' . $vendor_addr3 . '
' . $vendor_addr5 . '
' . $vendor_postcode . '

' . date('l jS F Y') . '

Dear ' . $vendor_name . ',

Re: ' . $pro_addr . '

Further to our recent conversation, we write to confirm the following offer made on the above property: -

Offer made by:   ' . $cli_name . '
Date of offer:   ' . $date . '
Amount:          ' . format_price($off_price) . ' (' . $off_price_word . ')
Conditions:      ' . $off_conditions . '

With kind regards.

Yours sincerely,

' . $use_name);

#echo "<pre>".$offer_text."</pre>";
#exit;

		if (!$_GET["action"]) {

			$form = new Form();

			$form->addForm("", "GET", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "action", "", "update");
			$form->addField("hidden", "stage", "", "2");
			$form->addField("hidden", "off_id", "", $off_id);
			$form->addField("hidden", "return", "", $_GET["return"]);

			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Submit Offer</div>');
#$form->addHtml($form->addHtml($deal_table));
#$form->addData($formData1,$_GET);
			$form->addRow("textarea", "send", "Letter to Vendor", $offer_text, array('style'   => 'width:500px;height:450px',
																					 'readonly'=> 'readonly'));
			$form->addRow("checkbox", "send", "Send By", array("Post", "Email"), array(), array('Post' => 'Post',
																								'Email'=> 'Email'));

			$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Send and Save", array('class'=> 'submit'))));
			$form->addHtml("</fieldset>\n");
			$form->addHtml("</div>\n");

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => $_GET["return"]),
				'search'=> array('title'=> 'Property Search',
								 'label'=> 'Property Search',
								 'link' => 'property_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Submit Offer");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
#$page->addScriptDeclaration($additional_js);
#$page->setBodyAttributes(array('onLoad'=>$onLoad));
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
#$page->addBodyContent($render);
			$page->addBodyContent('</div>');
			$page->display();

		} else { // if form is submitted

// send email

// update offer status
			$db_data["off_status"] = "Submitted";
			$off_id                = db_query($db_data, "UPDATE", "offer", "off_id", $_GET["off_id"]);

// back to deal summary and view offers form
			if (!$_GET["return"]) {
				header("Location:deal_summary.php?dea_id=$dea_id&viewForm=4");
			} else {
				header("Location:" . urldecode($_GET["return"]) . "&viewForm=4");
			}

		}

		break;
###########################################################
# default
###########################################################
	default:

}

?>