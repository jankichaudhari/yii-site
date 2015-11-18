<?php
/**
 * @var Array $app
 */
$DIT = false;
if (isset($_GET['dea_id']) && $_GET['dea_id']) {

	$deals = is_array($_GET['dea_id']) ? $_GET['dea_id'] : explode('|', $_GET['dea_id']);

	$sql     = "SELECT DIY FROM deal WHERE dea_id IN (" . implode(',', $deals) . ") AND DIY != 'None'";
	$results = $db->query($sql);

	if (DB::isError($results)) {
		die(__FILE__ . ':' . __LINE__ . ' db error: ' . $results->getMessage());
	}

	while ($row = $results->fetchRow()) {
		$DIT = true;

		if ($row['DIY'] === 'DIY') {
			echo "<pre style='color:blue' title='" . __FILE__ . "'>" . basename(__FILE__) . ":" . __LINE__ . "<br>";
			print_r($_GET);
			echo "</pre>";

			header('Location:/admin4/instruction/registerInterest?' . http_build_query($_GET));
		}

	}

	$DIT = (bool)$results->numRows();

}
if (!$_GET["action"]) {

	if (!$_GET["cli_id"]) {
		/* #echo "no cli_id";
			#exit; */
		$cli_id = $_POST["cli_id"];
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
// if we are adding additional properties to a viewing, skip the datetime bit
	if ($_GET["app_id"]) {
//echo "Location:?stage=appointment&action=update&app_id=$app_id&cli_id=$cli_id&dea_id=$dea_id&searchLink=$searchLink"; exit;
		header("Location:?stage=appointment&action=update&app_id=" . $_GET['app_id'] . "&cli_id=" . $_GET['cli_id'] . "&dea_id=$dea_id&searchLink=" . $_GET['searchLink'] . "");
	}

	if ($_GET["date"]) {
		$app_date = urldecode($_GET["date"]);
		if (strlen($app_date) != 10) {
			$app_date = date('d/m/Y');
		}
	} else {
		// default date and time set to now
		$app_date = date('d/m/Y');
		$app_time = date('G:i');
	}

// count number of deals and calculate estimated duration
	if (strstr($dea_id, "|")) {
		$dea_temp = explode("|", $dea_id);
		$duration = count($dea_temp) * $default_viewing_duration;
	} else { // single deal
		$duration = $default_viewing_duration;
	}

// change sydenham, lettins branch to camberwell, as only one calendar in use
	if ($_SESSION["auth"]["use_branch"] == 4) {
		$branch = 3;
	} else {
		$branch = $_SESSION["auth"]["use_branch"];
	}

// show (unassigned) if user is not a neg
	if (!in_array('Negotiator', $_SESSION["auth"]["roles"])) {
		$user = 0;
	} else {
		$user = $_SESSION["auth"]["use_id"];
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
					'value'      => $user,
					'attributes' => array('class' => 'medium'),
					'options'    => array('' => '(unassigned)'),
			),
			'app_date'     => array(
					'type'       => 'datetime',
					'label'      => 'Date',
					'value'      => $app_date,
					'attributes' => array('class' => 'medium', 'readonly' => 'readonly'),
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
					'tooltip'    => 'Duration is estimated at ' . $default_viewing_duration . ' minutes per property'
			),
			'notes'        => array(
					'type'       => 'textarea',
					'label'      => 'Notes',
					'value'      => $app["notes"],
					'attributes' => array('class' => 'noteInput')
			)
	);

	$form = new Form();

	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addForm("", "get");
	$form->addField("hidden", "stage", "", "appointment");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "cli_id", "", $cli_id);
	$form->addField("hidden", "app_id", "", $_GET["app_id"]);
	$form->addField("hidden", "dea_id", "", $dea_id);
	$form->addField("hidden", "searchLink", "", $searchLink);
	$form->addField("hidden", "skip", "", $_GET["skip"]);
	$form->addHtml("<fieldset class=\"" . ($DIT ? "DIT-property" : "") . "\">\n");
	$form->addHtml('<div class="block-header">Appointment</div>');
	if ($DIT) {
		$form->addHtml('<div class="dit-notification">This is an appointment for DIT instruction.</div>');

	}
	$form->addData($formData1, $_GET);
	$form->addHtml($form->addDiv($form->makeField("submit", "submit", "", "Save Changes", array('class' => 'submit'))));
	$form->addHtml("</fieldset>\n");
	$form->addHtml("</div>\n");

	$navbar_array = array(
			'back'   => array('title' => 'Back', 'label' => 'Back', 'link' => urldecode($searchLink)),
			'search' => array('title' => 'Property Search', 'label' => 'Property Search', 'link' => 'property_search.php')
	);
	$navbar       = navbar2($navbar_array);

	$page->setTitle("Arrange Viewing");
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
		$db_data["app_type"] = 'Viewing';

		$date_parts = explode("/", $_GET["app_date"]);
		$day        = $date_parts[0];
		$month      = $date_parts[1];
		$year       = $date_parts[2];

		$app_date      = $year . '-' . $month . '-' . $day;
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

		if ($DIT) {
			$db_data['DIT'] = 1;
		}

		$app_id = db_query($db_data, "INSERT", "appointment", "app_id");
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
		/*
// no longer using this as the neg is added to the appointment table, and use2app is used for additional attendees
// add to use2app table
// if user is a negotiator, add them as first user, else add none (not done yet)
$db_data["u2a_use"] = $_SESSION["auth"]["use_id"];
$db_data["u2a_app"] = $app_id;
db_query($db_data,"INSERT","use2app","u2a_id");
unset($db_data);
*/
		// count is used to number the viewings in link_deal_to_appointment table
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

	if (!$_GET["app_id"]) {
		notify($db_response, 'add');
	} else {
		//sprint_r($db_response);
		$db_response["array"]["add_property"] = array('new' => "Additional property added");
		notify($db_response, 'edit');
	}

// adding properties to appointment, forward to appointment page
	if ($_GET["app_id"]) {
		parse_str($_GET["searchLink"], $output);
		header("Location:appointment_edit.php?app_id=$app_id&searchLink=" . $output["carry"]);
		exit;
	} else {

		// if client has not been reviewed in the past $client_review_period, go to edit page
		$sql = "SELECT cli_reviewed FROM client WHERE cli_id = $cli_id";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage());
		}
		while ($row = $q->fetchRow()) {
			$cli_reviewed = $row["cli_reviewed"];
		}
		header("Location:calendar.php?app_id=$app_id");

		exit;
	}

}

