<?php
/**
 * This file is not used due to the fact that grey-smooth theme is currently in use.
 * @see /themes/grey-smooth/views/layouts/new/main.php
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link rel="icon" type="image/vnd.microsoft.icon" href="/images/sys/favicon-admin.ico" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/default.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui-timepicker-addon.css" />
	<?php
	/** @var $clientScript CClientScript */
	$clientScript = Yii::app()->getClientScript();
	$clientScript->registerScriptFile('/js/functions.js');
	$clientScript->registerCoreScript('jquery');
	$clientScript->registerCoreScript('jquery.ui');
	$clientScript->registerCoreScript('jquery');
	$clientScript->registerScriptFile('/js/User.js');
	$clientScript->registerScriptFile('/js/Popup.js');
	$clientScript->registerScriptFile('/js/jquery-ui-timepicker-addon.js');
	$clientScript->registerScriptFile('/js/jquery-ui-sliderAccess.js');
	$clientScript->registerScriptFile('/js/adminUtilHead.js');
	$clientScript->registerScriptFile('/js/adminUtil.js');


	$clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css');
	Yii::app()->clientScript->registerScript(
		'myHideEffect',
		'$(".flash.remove").animate({opacity: 1.0}, 3500).fadeOut("slow");',
		CClientScript::POS_READY
	);
	?>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<script type="text/javascript">
		$.datepicker.setDefaults({'dateFormat' : 'dd/mm/yy', 'showOn' : 'button', 'buttonImage' : '/images/sys/calendar-icon.png', 'buttonImageOnly' : true, 'numberOfMonths' : 3})
	</script>
</head>
<body>
<?php echo $content ?>
</body>
</html>
