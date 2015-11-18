<?php
$countClientsSales = 0;
$countClientsLettings = 0;
$sql = "SELECT COUNT(*) AS clientCount FROM client WHERE cli_status = 'Pending_New_Client_Sales'";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$countClientsSales = $row['clientCount'];
}

$sql = "SELECT COUNT(*) AS clientCount FROM client WHERE cli_status = 'Pending_New_Client_Lettings'";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$countClientsLettings = $row['clientCount'];
}
$sql = ''; // this is necessary because we use $sql variable all across the system and it is not declared anywhere.
$header_and_menu = '
<div class="page-header">
<div class="cell image">
<img src="/images/sys/admin/admin-top-logo.png" alt="">
</div>
<div class="cell">
<a href="' . WS_YII_URL . 'user/UserPreferences' . '">
' . $_SESSION['auth']['use_fname'] . ' ' . $_SESSION['auth']['use_sname'] . '
</a>
</div>
<div class="cell">
' . date("l jS F Y") . ' <span id="timecontainer"></span>
</div>
</div>
';

$menu = array(
		['htmlOptions' => ['class' => 'header'], 'url' => GLOBAL_URL . 'home.php', 'label' => 'Home Page'],
		'separator',
		['htmlOptions' => ['class' => 'header'], 'url' => GLOBAL_URL . 'calendar.php', 'label' => 'Calendar'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'AppointmentBuilder/selectClient/for/viewing', 'label' => 'Arrange Viewing'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'instruction/vendorCare', 'label' => 'Vendor Care'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'AppointmentBuilder/selectClient/for/valuation', 'label' => 'Arrange Valuation'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'Appointment/Search', 'label' => 'Search Calendar'],
		'separator',
		['htmlOptions' => [], 'url' => WS_YII_URL . 'property/select', 'label' => 'Property'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'Instruction/search', 'label' => 'Search Instruction'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'property/select', 'label' => 'Search Property'],
		'separator',
		['htmlOptions' => ['class' => 'header'], 'url' => WS_YII_URL . 'client/search', 'label' => 'Clients'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'client/create/', 'label' => 'Add Client'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'client/newlyRegistered', 'label' => 'Newly Registered'],
		['htmlOptions' => [], 'url' => WS_YII_URL . 'sms/incoming', 'label' => 'Incoming Messages'],
		'separator',
		['htmlOptions' => ['class' => 'header'], 'url' => GLOBAL_URL . 'contact.php', 'label' => 'Contacts'],
		['htmlOptions' => [], 'url' => GLOBAL_URL . 'contact_add.php', 'label' => 'New Contact'],
		['htmlOptions' => [], 'url' => GLOBAL_URL . 'company_add.php', 'label' => 'New Company'],
		'separator',
		['htmlOptions' => ['class' => 'header'], 'url' => GLOBAL_URL . 'tools.php', 'label' => 'Tools'],
		'separator',
		['htmlOptions' => ['class' => 'header'], 'url' => '/admin/index.php', 'label' => 'Old Admin'],
);

if (in_array('SuperAdmin', $_SESSION["auth"]["roles"])) {
	$menu[] = 'separator';
	$menu[] = [
			'htmlOptions' => ['class' => 'header', 'style' => 'color:red'],
			'url'         => WS_YII_URL . 'SuperAdmin',
			'label'       => 'Super Admin'
	];
}
$menu[] = 'separator';
$menu[] = ['htmlOptions' => ['class' => 'header'], 'url' => '#', 'label' => 'Listing'];
$menu[] = ['htmlOptions' => [], 'url' => WS_YII_URL . 'LocalEvent', 'label' => 'Local Events'];
$menu[] = ['htmlOptions' => [], 'url' => WS_YII_URL . 'Place', 'label' => 'Parks'];
$menu[] = ['htmlOptions' => [], 'url' => WS_YII_URL . 'Blog', 'label' => 'Blog'];
$menu[] = ['htmlOptions' => [], 'url' => WS_YII_URL . 'PropertyCategory', 'label' => 'Property Category'];
$menu[] = ['htmlOptions' => [], 'url' => WS_YII_URL . 'OuterLink', 'label' => 'Links'];
$menu[] = 'separator';
$menu[] = ['htmlOptions' => ['class' => 'header'], 'url' => '#', 'label' => 'Quick Reports'];
$menu[] = ['htmlOptions' => [], 'url' => WS_YII_URL . 'QuickReport/InstructionsWithoutEpc', 'label' => 'Mising EPCs'];
$menu[] = ['htmlOptions' => [], 'url' => WS_YII_URL . 'instruction/missedFollowUpReport', 'label' => 'Missed Follow ups'];
$menu[] = ['htmlOptions' => [], 'url' => WS_YII_URL . 'site/logout', 'label' => 'Logout'];
ob_start();
?>
	<div class="left-menu">
		<?php
		foreach ($menu as $entry) {
			if ($entry === 'separator') {
				echo '<div class="separator"></div>';
				continue;
			}

			$add = '';
			foreach ($entry['htmlOptions'] as $key => $value) {
				$add .= $key . '="' . $value . '" ';

			}

			echo '<a href="' . $entry['url'] . '" ' . $add . '>' . $entry['label'] . '</a>';
		}
		?>
	</div>
<?php
$header_and_menu .= ob_get_clean();
