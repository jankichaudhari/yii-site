<?php
extract($_REQUEST);
require_once("inx/global.inc.php");
/*
create valuation
requires cli_id which is obtained from client_lookup.php
*/

// defaulting to sales for now
//$dea_type = 'Sales';

if ($_GET["stage"]) {
	$stage = $_GET["stage"];
} elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
} else {
	// default to valuation_address
	$stage = "valuation_address";
}

if (!$_GET["pro_id"]) {
	$pro_id = $_POST["pro_id"];
} else {
	$pro_id = $_GET["pro_id"];
}

if (!$_GET["pro_id"]) {
	$dea_id = $_POST["dea_id"];
} else {
	$dea_id = $_GET["dea_id"];
}
// this page cannot be used without a cli_id
if (!$_GET["cli_id"]) {
	header("Location:client_lookup.php?dest=valuation&date=" . $_GET["date"]);
	exit;
} else {
	$cli_id = $_GET["cli_id"];
}

if ($_SESSION["auth"]["default_scope"] == 'Lettings') {
	$owner = 'Landlord';
} else {
	$owner = 'Vendor';
}

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage):

/////////////////////////////////////////////////////////////////////////////
// valuation_address
// search deal+property and display any linked properties
// else, enter property to be valued details
/////////////////////////////////////////////////////////////////////////////
	case "valuation_address":
		header('Location:/admin4/AppointmentBuilder/selectProperty/for/valuation/clientId/' . $_GET['cli_id']);
		exit;
	case "deal":
/////////////////////////////////////////////////////////////////////////////
// deal
// create a deal record (or check for existing, i.e. same property and same client)
/////////////////////////////////////////////////////////////////////////////

// requires a pro_id
		if (!$_GET["pro_id"]) {
			$errors[] = "No property is specified";
			echo error_message($errors);
			exit;
		} else {
			$pro_id = $_GET["pro_id"];
		}

		$dea_type   = $_GET["dea_type"];
		$dea_status = $_GET["dea_status"];

// make sure this deal dosen't already exist
// allow this stage to be skipped, creating a duplicate deal

		if ($_GET["action"] !== "skip") {

			$sql = "SELECT
dea_id,dea_status,dea_type,date_format(deal.dea_created,'%d/%m/%Y') as datecreate,
cli_id,CONCAT(cli_fname,' ',cli_sname) AS cli_name,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr
FROM
deal
LEFT JOIN property on deal.dea_prop = property.pro_id
LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
WHERE
client.cli_id = '$cli_id' AND
deal.dea_prop = '$pro_id' AND
deal.dea_type = '$dea_type'
GROUP BY deal.dea_id";

			$q = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage() . $sql);
			}
			$numRows = $q->numRows();
		} else {
			$numRows = 0;
		}
		if ($numRows !== 0) {

			while ($row = $q->fetchRow()) {
				$render .= '
		<tr>
		<td><a href="/admin4/instruction/summary/id/' . $row["dea_id"] . '">' . $row["pro_addr"] . '</a></td>
		<td>' . $row["datecreate"] . '</td>
		<td>' . $row["dea_status"] . '</td>
		</tr>';
				$cli_name = $row["cli_name"];
				$pro_addr = $row["pro_addr"];
			}

			$render = '
	<p><img src="/images/sys/admin/warning_icon.jpg" align="absmiddle" />The information you have entered appears to already be present in the system</p>
	<br clear="all" />
	<p>The following list shows deals associated to your ' . strtolower($owner) . ' (' . $cli_name . ') and that match the property you have chosen (' . $pro_addr . '). It is therefore
	quite likley that the data you are entering has already been entered. Please review the list below:</p>

	<table width="100%" border="1" cellspacing="0" cellpadding="5">
	  <tr>
	  <td>Property</td>
	  <td>Date Created</td>
	  <td>Current State of Trade</td>
	  </tr>
	' . $render . '
	</table>
	<p>If any of the deals above match what you are trying to add, please click the address. If you want to book a valuation on an existing
	property, you can do so from deal summary page. If none of the information above matches, or they are all past records and not relevant
	to what you are trying to do, you can create a new deal by clicking <a href="?' . $_SERVER['QUERY_STRING'] . '&amp;action=skip">here</a>.</p>
	';

			$page->setTitle("Valuation");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($render);
			$page->addBodyContent('</div>');
			$page->display();

			exit;
		} else {

			// create new deal
			/*
	  // get the branch from the postcode to automaticaly assign
	  $pc = explode(" ",$pro_postcode);
	  $pc1 = $pc[0];
	  $sql = "SELECT are_branch FROM area WHERE are_postcode = '$pc1' LIMIT 1";
	  //echo $sql;
	  $q = $db->query($sql);
	  if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	  $numRows = $q->numRows();
	  if ($numRows == 0) {
		  $dea_branch = 1;
		  #echo "automatic branch selection failed, branch 1 has been set";
		  } else {
		  while ($row = $q->fetchRow()) {
			  $dea_branch = $row["are_branch"];
			  }
		  }
	  */
			// just set branch to current users, can always be changed
			$dea_branch = $_SESSION["auth"]["use_branch"];

			if (!$dea_status) {
				$dea_status = 'Valuation';
			}
			// insert deal
			$db_data["dea_prop"] = $pro_id;
			$db_data["dea_type"] = $dea_type;
			#$db_data["dea_vendor"] = $cli_id; // we dont use dea_vendor any more, we now use link_client_to_instruction link table
			$db_data["dea_status"]   = $dea_status;
			$db_data["dea_branch"]   = $dea_branch;
			$db_data["dea_created "] = $date_mysql;

			$dea_id = db_query($db_data, "INSERT", "deal", "dea_id");
			//print_r($db_data);

			// insert link_client_to_instruction link
			$db_data_c2d["dealId"]   = $dea_id;
			$db_data_c2d["clientId"] = $cli_id;
			$id                      = db_query($db_data_c2d, "INSERT", "link_client_to_instruction", "id");

			// insert sot
			$db_data_sot["sot_deal"]   = $dea_id;
			$db_data_sot["sot_status"] = $dea_status;
			$db_data_sot["sot_date"]   = $date_mysql;
			$db_data_sot["sot_user"]   = $_SESSION["auth"]["use_id"];
			$sot_id                    = db_query($db_data_sot, "INSERT", "sot", "sot_id");
			//print_r($db_data_sot);

			header("Location:?stage=particulars&pro_id=$pro_id&cli_id=$cli_id&dea_id=$dea_id&dea_status=$dea_status");
		}

		break;

	case "particulars":
/////////////////////////////////////////////////////////////////////////////
// property particulars
//
/////////////////////////////////////////////////////////////////////////////

// get property details (also need area title in list of possible areas)
// property particualrs no longer stored in property table, so select them from newest deal
// record associated with the current property
		$sql = "SELECT deal.dea_ptype,dea_psubtype,dea_floors,dea_floor,dea_reception,dea_bedroom,dea_bathroom,
property.pro_area,property.pro_postcode,area.are_id, area.are_title
FROM deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
WHERE dea_prop = " . $pro_id . "
ORDER BY dea_created ASC LIMIT 1";

		$q = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage() . $sql);
		}
		$numRows = $q->numRows();
		while ($row = $q->fetchRow()) {
			foreach ($row as $key => $val) {
				$$key = $val;
			}
		}

// get property types
		$ptype = ptype2($dea_ptype, $dea_psubtype);

// get matching areas
		$pc1           = explode(" ", $pro_postcode);
		$pc1           = $pc1[0];
		$matched_areas = array();
		$sql           = "SELECT are_id, are_title, are_postcode FROM area WHERE are_postcode = '$pc1'";
		$q             = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage() . $sql);
		}
		$numRows = $q->numRows();
		while ($row = $q->fetchRow()) {
			$matched_areas[$row["are_title"]] = $row["are_id"];
			if ($numRows == 1) {
				$default_area = $row["are_title"];
			}
		}

		if ($are_title) {
			$default_area = $are_title;
		}
		if ($matched_areas) {
			$formDataArea = array(
				'pro_area' => array(
					'type'    => 'radio',
					'label'   => 'Area',
					'value'   => $default_area,
					'options' => $matched_areas
				)
				/*,
					'pro_areanew'=>array(
						'type'=>'button',
						'label'=>'New Area',
						'value'=>'New Area',
						'attributes'=>array('class'=>'button','onClick'=>'javascript:addArea(\''.$pc1.'\',\''.urlencode($_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']).'\')')
						)*/
			);
		} else {
			$formDataArea = array(
				'pro_areanew' => array(
					'type'       => 'button',
					'label'      => 'New Area',
					'value'      => 'New Area',
					'attributes' => array(
						'class'   => 'button',
						'onClick' => 'javascript:addArea(\'' . $pc1 . '\',\'' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '\')'
					)
				)
			);
		}

# build data arrays for property particulars
		$formData = array(
			'dea_ptype'     => array(
				'type'     => 'select_multi',
				'label'    => 'Property Type',
				'required' => 2,
				'options'  => array(
					'dd1' => $ptype['dd1'],
					'dd2' => $ptype['dd2']
				)
			),
			'dea_bedroom'   => array(
				'type'       => 'select_number',
				'label'      => 'Bedrooms',
				'value'      => $dea_bedroom,
				'attributes' => array('class' => 'narrow'),
				'options'    => array('blank' => 'blank')
			),
			'dea_reception' => array(
				'type'       => 'select_number',
				'label'      => 'Receptions',
				'value'      => $dea_reception,
				'attributes' => array('class' => 'narrow'),
				'options'    => array('blank' => 'blank')
			),
			'dea_bathroom'  => array(
				'type'       => 'select_number',
				'label'      => 'Bathrooms',
				'value'      => $dea_bathroom,
				'attributes' => array('class' => 'narrow'),
				'options'    => array('blank' => 'blank')
			),
			'dea_floor'     => array(
				'type'       => 'select',
				'label'      => 'Floor',
				'value'      => $dea_floor,
				'options'    => join_arrays(array(array('blank' => ''), db_enum("deal", "dea_floor", "array"))),
				'attributes' => array('class' => 'medium')
			),
			'dea_floors'    => array(
				'type'       => 'select_number',
				'label'      => 'Floors',
				'options'    => array(
					'blank' => '',
					'min'   => '1'
				),
				'value'      => $dea_floors,
				'attributes' => array('class' => 'narrow')
			)
		);

		$formData1 = array(
			'dea_branch' => array(
				'type'       => 'select_branch',
				'label'      => 'Branch',
				'value'      => $_SESSION["auth"]["use_branch"],
				'required'   => 2,
				'attributes' => array('class' => 'medium')
			),
			'dea_neg'    => array(
				'type'       => 'select_user',
				'label'      => 'Negotiator',
				'value'      => '',
				'required'   => 1,
				'attributes' => array('class' => 'medium'),
				'options'    => array('' => '(unassigned)')
			)
		);

		if ($dea_status == 'Valuation') {
			unset($formData1["dea_neg"]);
		}

		if (!$_GET["action"]) {

// start new form object
			$form = new Form();

			$form->addForm("form", "get", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "stage", "", "particulars");
			$form->addField("hidden", "action", "", "update");
			$form->addField("hidden", "cli_id", "", $cli_id);
			$form->addField("hidden", "pro_id", "", $pro_id);
			$form->addField("hidden", "dea_id", "", $dea_id);
			$form->addField("hidden", "dea_status", "", $dea_status);

			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Property Particulars</div>');
			$form->addData($formData1, $_GET);
			$form->addData($formDataArea, $_GET);
			$form->addData($formData, $_GET);
			$form->addHtml($form->addDiv($form->makeField("submit", "", "", "Save Changes", array('class' => 'submit'))));
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

			$page->setTitle("New Property");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addScript('js/scriptaculous/prototype.js');
			$page->addScriptDeclaration($ptype['js']);
			$page->setBodyAttributes(array('onLoad' => $ptype['onload']));
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($render);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div>');
			$page->display();

		} else {
// if form is submitted

			$formData = join_arrays(array($formData1, $formData));

// validate (dea)
			$result  = new Validate();
			$results = $result->process($formData, $_GET);
			$db_data = $results['Results'];

// build return link
			$return = $_SERVER['SCRIPT_NAME'] . '?stage=valuation_address&';
			if ($cli_id) {
				$results['Results']['cli_id'] = $cli_id;
			}
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			if ($results['Errors']) {
				echo error_message($results['Errors'], urlencode($return));
				exit;
			}
			$db_data["dea_psubtype"] = $_GET["dea_psubtype"];

			db_query($db_data, "UPDATE", "deal", "dea_id", $dea_id);

			if ($_GET["pro_area"]) {
				db_query(array('pro_area' => $_GET["pro_area"]), "UPDATE", "property", "pro_id", $pro_id);
			}

			header("Location:?stage=appointment&pro_id=$pro_id&cli_id=$cli_id&dea_id=$dea_id&dea_type=$dea_type&dea_status=$dea_status");

		}

		break;

// appointment
	case "appointment":

		/*
  create appointment and link to deals via link_deal_to_appointment link table...
  also add in the vendors to cli2app table
  */

		if (!$_GET["cli_id"]) {
			echo "no cli_id";
			exit;
		} else {
			$cli_id = $_GET["cli_id"];
		}
		if (!$_GET["dea_id"]) {
			/* echo "no dea_id";
				exit; */
			$dea_id = array2string($_POST["dea_id"]);
		} else {
			$dea_id = array2string($_GET["dea_id"]);
		}
		if (!$dea_id) {
			$dea_id = $_GET["dea_id"];
		}
// if we are adding additional properties to the appointment, skip the datetime bit
		if ($_GET["app_id"]) {
			header("Location:?stage=appointment&action=update&app_id=$app_id&cli_id=$cli_id&dea_id=$dea_id");
		}
		/*
  // only show appointment stage is booking valuation
  if ($_GET["dea_status"] <> 'Valuation') {
	  header("Location:deal_summary.php?dea_id=$dea_id");
	  }
  */
// default date and time set to now
		$app_date = date('d/m/Y');
		$app_time = date('G:i');

// count number of deals and calculate estimated duration
		if (strstr($dea_id, "|")) {
			$dea_temp = explode("|", $dea_id);
			$duration = count($dea_temp) * $default_valuation_duration;
		} else { // single deal
			$duration = $default_valuation_duration;
		}

		/*
  CANNOT use this anymore as there is no sydenham lettings calendar

  // get the dea_branch and maybe others?
  $sql = "SELECT dea_branch FROM deal WHERE dea_id = $dea_id";
  $q = $db->query($sql);
  if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
  $numRows = $q->numRows();
  while ($row = $q->fetchRow()) {
	  $dea_branch = $row["dea_branch"];
	  }
  */
// change sydenham, lettins branch to camberwell, as only one calendar in use
		if ($_SESSION["auth"]["use_branch"] == 4) {
			$branch = 3;
		} else {
			$branch = $_SESSION["auth"]["use_branch"];
		}

		$formData1 = array(
			'calendarID'   => array(
				'type'       => 'select_branch_2',
				'label'      => 'Branch',
				'value'      => $branch,
				'attributes' => array('class' => 'medium')
			),
			'app_user'     => array(
				'type'       => 'select_user',
				'label'      => 'Negotiator',
				'value'      => $_SESSION["auth"]["use_id"],
				'attributes' => array('class' => 'medium'),
				'options'    => array('' => '(unassigned)')
			),
			'app_date'     => array(
				'type'       => 'datetime',
				'label'      => 'Date',
				'value'      => $app_date,
				'attributes' => array(
					'class'    => 'medium',
					'readonly' => 'readonly'
				),
				'tooltip'    => 'Today\'s date is selected by default'
			),
			'app_time'     => array(
				'type'  => 'time',
				'label' => 'Start Time',
				'value' => $app_time
			),
			'app_duration' => array(
				'type'       => 'select_duration',
				'label'      => 'Estimated Duration',
				'value'      => $duration,
				'attributes' => array('class' => 'medium'),
				'tooltip'    => 'Duration is estimated at ' . $default_valuation_duration . ' minutes per property'
			),
			'notes'        => array(
				'type'       => 'textarea',
				'label'      => 'Notes',
				'value'      => $app["notes"],
				'attributes' => array('class' => 'noteInput')
			)
		);

		if (!$_GET["action"]) {

			$form = new Form();

			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addForm("", "get");
			$form->addField("hidden", "stage", "", "appointment");
			$form->addField("hidden", "action", "", "update");
			$form->addField("hidden", "cli_id", "", $cli_id);
			$form->addField("hidden", "app_id", "", $_GET["app_id"]);
			$form->addField("hidden", "dea_id", "", $dea_id);
			$form->addField("hidden", "searchLink", "", $searchLink);
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Valuation Appointment</div>');
			$form->addData($formData1, $_GET);
			$buttons = $form->makeField("submit", "submit", "", "Save Changes", array('class' => 'submit'));
//$buttons .= ' or ';
			$buttons .= $form->makeField("button", "button", "", "No appointment", array(
																						'class'   => 'button',
																						'onClick' => 'document.location.href = \'/admin4/instruction/summary/id/' . $dea_id . '\''
																				   ));
			$form->addHtml($form->addDiv($buttons));
			$form->addHtml("</fieldset>\n");
			$form->addHtml("</div>\n");

			$navbar_array = array(
				'back'   => array(
					'title' => 'Back',
					'label' => 'Back',
					'link'  => $returnLink
				),
				'search' => array(
					'title' => 'Property Search',
					'label' => 'Property Search',
					'link'  => 'property_search.php'
				)
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("New Property");
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

#print_r($_GET);

// multiple deals selected, delimiited with pipe (this comes from property search page)
			if (strstr($_GET["dea_id"], "|")) {
				$dea_id = explode("|", $_GET["dea_id"]);
			} else {
				$dea_id = $_GET["dea_id"];
			}

#print_r($dea_id);

// if appointment dosen't already exists...
			if (!$_GET["app_id"]) {

				// create appointment row
				$db_data["app_type"] = 'Valuation';
				$date_parts          = explode("/", $_GET["app_date"]);

				$day      = $date_parts[0];
				$month    = $date_parts[1];
				$year     = $date_parts[2];
				$app_date = (string)$year . (string)'-' . (string)$month . (string)'-' . (string)$day;

				$app_time_hour = $_GET["app_time_hour"];
				$app_time_min  = $_GET["app_time_min"];

				$app_start = $app_date . ' ' . $app_time_hour . ':' . $app_time_min . ':00';

				$app_start = strtotime($app_start);
				$app_end   = $app_start + ($_GET["app_duration"] * 60);

				$db_data["app_start"]  = date('Y-m-d G:i:s', $app_start);
				$db_data["app_end"]    = date('Y-m-d G:i:s', $app_end);
				$db_data["calendarID"] = $_GET["calendarID"];

				#$db_data["app_client"] = $cli_id; // lead client (also stored in cli2app table), maybe not use this in future (delete field)
				$db_data["app_bookedby"] = $_SESSION["auth"]["use_id"]; // booked by
				$db_data["app_user"]     = $_GET["app_user"]; // lead neg
				$db_data["app_created"]  = $date_mysql;
				$app_id                  = db_query($db_data, "INSERT", "appointment", "app_id");
				unset($db_data);

				// add to cli2app table
				$db_data["c2a_cli"] = $cli_id;
				$db_data["c2a_app"] = $app_id;
				db_query($db_data, "INSERT", "cli2app", "c2a_id");
				unset($db_data);

				// extract notes from _GET and store in notes table
				if ($_GET["notes"]) {
					$notes = clean_input($_GET["notes"]);
					unset($db_data["notes"]);
					if ($notes) {
						$db_data2 = array(
							'not_blurb' => $notes,
							'not_row'   => $app_id,
							'not_type'  => 'appointment',
							'not_user'  => $_SESSION["auth"]["use_id"],
							'not_date'  => $date_mysql
						);
						db_query($db_data2, "INSERT", "note", "not_id");
					}
				}

				$count = 1;

			} else { // if appointment already stored (i.e. we have chosed to add more properties to it)

				$app_id = $_GET["app_id"];
				// get highest count and increment from that in link_deal_to_appointment table
				$sql = "SELECT d2a_ord FROM link_deal_to_appointment WHERE d2a_app = $app_id";
				$q   = $db->query($sql);
				if (DB::isError($q)) {
					die("db error: " . $q->getMessage() . $sql);
				}
				$count = $q->numRows() + 1;

			}

// create link_deal_to_appointment row(s), do not allow duplicates
// if multiple properties (deals) are selected)
			if (is_array($dea_id)) {
				foreach ($dea_id AS $deal) {
					// checking for duplicates
					$sql = "SELECT * FROM link_deal_to_appointment WHERE d2a_dea = '$deal' AND d2a_app = '$app_id'";
					$q   = $db->query($sql);
					if (DB::isError($q)) {
						die("db error: " . $q->getMessage() . $sql);
					}
					if (!$q->numRows()) {
						$db_data["d2a_dea"] = $deal;
						$db_data["d2a_app"] = $app_id;
						$db_data["d2a_ord"] = $count;
						db_query($db_data, "INSERT", "link_deal_to_appointment", "d2a_id");
						unset($db_data);
						$count++;
					}
				}
			} // single deal
			else {
				// checking for duplicates
				$sql = "SELECT * FROM link_deal_to_appointment WHERE d2a_dea = '$dea_id' AND d2a_app = '$app_id'";
				$q   = $db->query($sql);
				if (DB::isError($q)) {
					die("db error: " . $q->getMessage() . $sql);
				}
				if (!$q->numRows()) {
					$db_data["d2a_dea"] = $dea_id;
					$db_data["d2a_app"] = $app_id;
					$db_data["d2a_ord"] = $count;
					db_query($db_data, "INSERT", "link_deal_to_appointment", "d2a_id");
					unset($db_data);
				}
			}

// notify - update the app_notify field purely to create the neccesary environment to run the notify function
			unset($db_data);
			$db_data["app_updated"] = date('Y-m-d H:i:s');
			$db_response            = db_query($db_data, "UPDATE", "appointment", "app_id", $app_id, true);
			notify($db_response, 'add');

// forward to calendar
			header("Location:calendar.php?app_id=$app_id");
			exit;

		}

		break;

/////////////////////////////////////////////////////////////////////////////
// if no stage is defined
/////////////////////////////////////////////////////////////////////////////
	default:

		$render = 'Nothing to do';

endswitch;
