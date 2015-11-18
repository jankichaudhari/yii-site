<?php
/**
 * @var $fixed boolean|null
 * @see
 */
$pendSales = Client::model()->pendingCount("sales");
$pendLet = Client::model()->pendingCount("lettings");
$fixed = isset($fixed) ? $fixed : null;
$menu = array(
		['htmlOptions' => ['class' => 'header'], 'url' => Yii::app()->params['globalUrlOld'] . 'home.php', 'label' => 'Home Page'],
		'separator',
		['htmlOptions' => ['class' => 'header'], 'url' => Yii::app()->params['globalUrlOld'] . 'calendar.php', 'label' => 'Calendar'],
		['htmlOptions' => [], 'url' => ['AppointmentBuilder/selectClient', 'for' => 'viewing'], 'label' => 'Arrange Viewing'],
		['htmlOptions' => [], 'url' => ['instruction/vendorCare'], 'label' => 'Vendor Care'],
		['htmlOptions' => [], 'url' => ['AppointmentBuilder/selectClient', 'for' => 'valuation'], 'label' => 'Arrange Valuation'],
		['htmlOptions' => [], 'url' => ['Appointment/search'], 'label' => 'Search Calendar'],
		'separator',
		['htmlOptions' => [], 'url' => ['property/select'], 'label' => 'Property'],
		[
				'htmlOptions' => [],
				'url'         => [
						'instruction/search',
						AdminFilterForm::generateResetParam('instruction-filter-form') => 'true'
				],
				'label'       => 'Search Instruction'
		],
		['htmlOptions' => [], 'url' => ['property/select'], 'label' => 'Search Property'],
		'separator',
		['htmlOptions' => ['class' => 'header'], 'url' => array('client/search'), 'label' => 'Clients'],
		['htmlOptions' => [], 'url' => ['client/create'], 'label' => 'Add Client'],
		['htmlOptions' => [], 'url' => ['client/newlyRegistered'], 'label' => 'Newly Registered'],
		['htmlOptions' => [], 'url' => ['sms/incoming'], 'label' => 'Incoming Messages'],
		'separator',
		[
				'htmlOptions' => ['class' => 'header'],
				'url'         => Yii::app()->params['globalUrlOld'] . 'contact.php',
				'label'       => 'Contacts'
		],
		['htmlOptions' => [], 'url' => Yii::app()->params['globalUrlOld'] . 'contact_add.php', 'label' => 'New Contact'],
		['htmlOptions' => [], 'url' => Yii::app()->params['globalUrlOld'] . 'company_add.php', 'label' => 'New Company'],
		'separator',
		[
				'htmlOptions' => ['class' => 'header'],
				'url'         => Yii::app()->params['globalUrlOld'] . 'tools.php',
				'label'       => 'Tools'
		],
		'separator',
		['htmlOptions' => ['class' => 'header'], 'url' => '/admin/index.php', 'label' => 'Old Admin'],
);

if (Yii::app()->user->is("SuperAdmin")) {
	$menu[] = 'separator';
	$menu[] = [
			'htmlOptions' => ['class' => 'header', 'style' => 'color:red'],
			'url'         => Array('SuperAdmin/'),
			'label'       => 'Super Admin'
	];
}
$menu[] = 'separator';
$menu[] = ['htmlOptions' => ['class' => 'header'], 'url' => '#', 'label' => 'Listing'];
$menu[] = ['htmlOptions' => [], 'url' => ['LocalEvent/'], 'label' => 'Local Events'];
$menu[] = ['htmlOptions' => [], 'url' => ['Place/'], 'label' => 'Parks'];
$menu[] = ['htmlOptions' => [], 'url' => ['Blog/'], 'label' => 'Blog'];
$menu[] = ['htmlOptions' => [], 'url' => ['PropertyCategory/'], 'label' => 'Property Category'];
$menu[] = ['htmlOptions' => [], 'url' => ['OuterLink/'], 'label' => 'Links'];
$menu[] = 'separator';
$menu[] = ['htmlOptions' => ['class' => 'header'], 'url' => '#', 'label' => 'Quick Reports'];
$menu[] = ['htmlOptions' => [], 'url' => ['QuickReport/instructionsWithoutEPC'], 'label' => 'Mising EPCs'];
$menu[] = ['htmlOptions' => [], 'url' => ['instruction/missedFollowUpReport'], 'label' => 'Missed Follow ups'];
$menu[] = ['htmlOptions' => [], 'url' => ['site/logout'], 'label' => 'Logout'];

?>
<div class="left-menu <?php echo $fixed ? 'fixed' : '' ?>">
	<?php
	foreach ($menu as $entry) {
		if ($entry === 'separator') {
			echo '<div class="separator"></div>';
			continue;
		}

		if (is_array($entry['url'])) {
			$t = $entry['url'];
			if ($_SERVER['REQUEST_URI'] == Yii::app()->createUrl(array_shift($t), $t)) {
				if (!isset($entry['htmlOptions']['class'])) {
					$entry['htmlOptions']['class'] = '';
				}
				$entry['htmlOptions']['class'] .= ' active';
			}

		}

		echo CHtml::link($entry['label'], $entry['url'], $entry['htmlOptions']);
	}
	?>
</div>
