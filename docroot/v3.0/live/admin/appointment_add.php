<?php
// add calendar appointment
/*
Viewing -> viewing_add.php
Valuation -> valuation_add.php


Production ... a production visit (pfp, re-visit, etc) is generally placed on an existing deal.
if trying to place on a non-existant deal, a deal must be created, so vendor details, property details etc are all required.
similar to add valuation: client lookup, select or add property.
could also show list of instructions that have not been visited? (won't work for re-visits)


Meetings ... attendees (staff), outsiders (notes field), location


Inspection -> inspection_add.php
... can only be booked on an existing deal. can only be a single deal, if a surveyor is going to more
than one property, more than appointment must be created (perhaps allow for this, create many at once?)
similar to arrange viewing, but instead of selecting a client, we select a surveyor on the first stage.
Select (search) property (link_deal_to_appointment), select (search) surveyor, add contacts (cli2ap) (vendors, tenants, others)


Interview ... attendees (staff), outsiders, location



*/
require_once("inx/global.inc.php");
if (!$_POST["app_type"]) {
	$app_type = $_GET["app_type"];
} else {
	$app_type = $_REQUEST["app_type"];
}
if ($_POST["app_date"]) {
	$app_date = $_POST["app_date"];
}
$branchId = 0;
if (isset($_REQUEST['branch']) && $_REQUEST['branch']) {
	$branchId = $_REQUEST['branch'];
}
if ($app_type == "Viewing") {
	header("Location:/admin4/AppointmentBuilder/selectClient?for=viewing&date=" . $_REQUEST["date"] . "&branchId=" . $branchId);
	exit;
} elseif ($app_type == "Valuation") {
	header("Location:/admin4/AppointmentBuilder/selectClient?for=valuation&date=" . $_REQUEST["date"] . "&branchId=" . $branchId);
	exit;
} elseif ($app_type == "Production") {

	header("Location:production_add.php?date=$date");
	exit;
} elseif ($app_type == "Inspection") {
	$date = $_POST["date"];
	header("Location:inspection_add.php?date=$date");
	exit;
} elseif ($app_type == "Lunch") {

	$formData1 = array(
		'app_user'     => array(
			'type'       => 'select_user',
			'label'      => 'User',
			'default'    => $_SESSION["auth"]["use_id"],
			'value'      => $app_user,
			'attributes' => array('class' => 'medium'),
			'options'    => $negotiators
		),
		'app_date'     => array(
			'type'       => 'datetime',
			'label'      => 'Date',
			'default'    => $date_short,
			'value'      => $_REQUEST["date"],
			'attributes' => array(
				'class'    => 'medium',
				'readonly' => 'readonly'
			)
		),
		'app_time'     => array(
			'type'    => 'time',
			'label'   => 'Start Time',
			'value'   => $app_time,
			'default' => date('G:i')
		),
		'app_duration' => array(
			'type'       => 'select_duration',
			'label'      => 'Duration',
			'value'      => $duration,
			'default'    => 60,
			'attributes' => array('class' => 'medium')
		),
		'app_notes'    => array(
			'type'       => 'textarea',
			'label'      => 'Notes',
			'value'      => $app_notes,
			'attributes' => array('class' => 'wide')
		)
	);

} elseif ($app_type == "Meeting") {

	$formData1 = array(
		'app_subject'  => array(
			'type'       => 'text',
			'label'      => 'Subject',
			'value'      => $app_subject,
			'attributes' => array('class' => 'wide'),
			'required'   => 2
		),
		'app_notes'    => array(
			'type'       => 'textarea',
			'label'      => 'Notes',
			'value'      => $app_notes,
			'attributes' => array(
				'class' => 'wide',
				'style' => 'height:50px'
			)
		),
		'calendarID'   => array(
			'type'       => 'select_branch_2',
			'label'      => 'Branch',
			'value'      => $calendarID,
			'attributes' => array('class' => 'medium'),
			'options'    => $branches
		),
		'app_user'     => array(
			'type'       => 'select_user',
			'label'      => 'Negotiator',
			'default'    => $_SESSION["auth"]["use_id"],
			'value'      => $app_user,
			'attributes' => array('class' => 'medium'),
			'options'    => $negotiators
		),
		'app_date'     => array(
			'type'       => 'datetime',
			'label'      => 'Date',
			'default'    => $date_short,
			'value'      => $_REQUEST["date"],
			'attributes' => array(
				'class'    => 'medium',
				'readonly' => 'readonly'
			)
		),
		'app_time'     => array(
			'type'    => 'time',
			'label'   => 'Start Time',
			'value'   => $app_time,
			'default' => date('G:i'),
			'group'   => 'Start Time'
		),
		'app_allday'   => array(
			'type'          => 'checkbox',
			'label'         => 'All day',
			'value'         => $app["allday"],
			'options'       => array('All day event' => 'Yes'),
			'attributes'    => array('onClick' => 'allDayCheck(\'app_allday[]\')'),
			'group'         => 'Start Time',
			'last_in_group' => 1
		),
		'app_duration' => array(
			'type'       => 'select_duration',
			'label'      => 'Duration',
			'value'      => $duration,
			'default'    => 30,
			'attributes' => array('class' => 'medium')
		)
	);

} elseif ($app_type == "Note") {
	$formData1 = array(
		'app_notetype' => array(
			'type'       => 'select',
			'label'      => 'Type',
			'value'      => $app_notetype,
			'attributes' => array('class' => 'wide'),
			'options'    => db_enum("appointment", "app_notetype", "array"),
			'required'   => 1
		),
		'app_subject'  => array(
			'type'       => 'text',
			'label'      => 'Subject',
			'value'      => $app_subject,
			'attributes' => array('class' => 'wide'),
			'required'   => 1
		),
		'app_notes'    => array(
			'type'       => 'textarea',
			'label'      => 'Notes',
			'value'      => $app_notes,
			'attributes' => array(
				'class' => 'wide',
				'style' => 'height:50px'
			)
		),
		'calendarID'   => array(
			'type'       => 'select_branch_2',
			'label'      => 'Branch',
			'value'      => $branchId,
			'attributes' => array('class' => 'medium')
		),
		'app_user'     => array(
			'type'       => 'select_user',
			'label'      => 'User',
			'default'    => $_SESSION["auth"]["use_id"],
			'value'      => $app_user,
			'attributes' => array('class' => 'medium'),
			'options'    => $negotiators
		),
		'app_date'     => array(
			'type'       => 'datetime',
			'label'      => 'Date',
			'default'    => $date_short,
			'value'      => $_REQUEST["date"],
			'attributes' => array(
				'class'    => 'medium',
				'readonly' => 'readonly'
			)
		),
		'app_time'     => array(
			'type'    => 'time',
			'label'   => 'Start Time',
			'value'   => $app_time,
			'default' => date('G:i'),
			'group'   => 'Start Time'
		),
		'app_allday'   => array(
			'type'          => 'checkbox',
			'label'         => 'All day',
			'value'         => $app["allday"],
			'options'       => array('All day event' => 'Yes'),
			'attributes'    => array('onClick' => 'allDayCheck(\'app_allday[]\')'),
			'group'         => 'Start Time',
			'last_in_group' => 1
		),
		'app_duration' => array(
			'type'       => 'select_duration',
			'label'      => 'Duration',
			'value'      => $duration,
			'default'    => 30,
			'attributes' => array('class' => 'medium')
		)
	);

} // end app_type if

if (!$_GET["action"]) {

	$form = new Form();

	$form->addForm("app_form", "GET", $PHP_SELF);
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "app_id", "", $app_id);
	$form->addField("hidden", "app_type", "", $app_type);
	$form->addField("hidden", "searchLink", "", urlencode($searchLink));

	$formName = 'form1';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Add ' . $app_type . '</div>');
	$form->addData($formData1, $_GET);
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "submit", "Save Changes", array('class' => 'submit'))));

	$form->addHtml('</div>');

	$navbar_array = array(
		#'back'=>array('title'=>'Back','label'=>'Back','link'=>str_replace("%3F","?",replaceQueryStringArray($_GET["searchLink"],array('app_id'))).'&jumpto='.$hour),
		'search' => array(
			'title' => 'Appointment Search',
			'label' => 'Appointment Search',
			'link'  => 'appointment_search.php'
		)
	);
	$navbar       = navbar2($navbar_array); //replaceQueryStringArray($_GET["searchLink"],array('app_id'))

	$page = new HTML_Page2($page_defaults);
	$page->setTitle("Appointment");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addScript('js/scriptaculous/prototype.js');
	$page->addScript('js/scriptaculous/scriptaculous.js');
	$page->addScript('js/CalendarPopup.js');
	$page->addScriptDeclaration('document.write(getCalendarStyles());var popcalapp_date = new CalendarPopup("popCalDivapp_date");popcalapp_date.showYearNavigation(); ');
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	$page->addBodyContent('</div>');
	$page->display();

	exit;

} else {
	// if form is submitted

	if ($app_type == "Meeting") {
		if (!$_GET["app_subject"]) {
			$errors[] = "Subject is required";
			echo error_message($errors);
			exit;
		}
	}

	if (!$_GET["app_allday"]) {
		$_GET["app_allday"] = 'No';
	} else {
		$_GET["app_allday"] = 'Yes';
		// hour and min are disabled for all day events, so set to 8am
		$_GET["app_time_hour"] = '08';
		$_GET["app_time_min"]  = '00';
	}

	// lunch, notes & meetings only
	$date_parts = explode("/", $_GET["app_date"]);
	$day        = $date_parts[0];
	$month      = $date_parts[1];
	$year       = $date_parts[2];

	$app_date  = $year . '-' . $month . '-' . $day;
	$app_start = $app_date . ' ' . $_GET["app_time_hour"] . ':' . $_GET["app_time_min"] . ':00';

	$app_start = strtotime($app_start);
	$app_end   = $app_start + ($_GET["app_duration"] * 60);

	// default to user's branch

	if (isset($_GET['calendarID'])) {
		$db_data['calendarID'] = $_GET['calendarID'];
	} else {

		$q = $db->query("SELECT use_branch FROM user WHERE use_id = " . $_GET["app_user"] . " LIMIT 1");
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage());
		}

		$row = $q->fetchRow();
		if ($row) {
			$db_data['calendarID'] = $row['use_branch'];
		}
	}

	$db_data['app_type']     = $_GET['app_type'];
	$db_data['app_notetype'] = $_GET['app_notetype'];
	$db_data['app_user']     = $_GET['app_user'];
	$db_data['app_start']    = date('Y-m-d G:i:s', $app_start);
	$db_data['app_end']      = date('Y-m-d G:i:s', $app_end);
	$db_data['app_subject']  = $_GET['app_subject'];
	$db_data['app_allday']   = $_GET['app_allday'];
	$db_data['app_bookedby'] = $_SESSION['auth']['use_id'];
	$db_data['app_created']  = $date_mysql;
	$app_id                  = db_query($db_data, 'INSERT', 'appointment', 'app_id');

// extract notes from db_data and store in notes table
	if ($_GET['app_notes']) {
		$notes    = $_GET['app_notes'];
		$db_data2 = array(
			'not_blurb' => $notes,
			'not_row'   => $app_id,
			'not_type'  => 'appointment',
			'not_user'  => $_SESSION['auth']['use_id'],
			'not_date'  => $date_mysql
		);

		db_query($db_data2, 'INSERT', 'note', 'not_id');
	}

	header('Location:calendar.php?app_id=' . $app_id);
}
