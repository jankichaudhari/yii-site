<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link rel="icon" type="image/vnd.microsoft.icon" href="/images/sys/favicon-admin.ico" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/gray/default.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/gray/grid-view/style.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/gray/pager.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui-timepicker-addon.css" />
	<script type="text/javascript" src="/js/functions.js"></script>
	<?php
	/** @var $clientScript CClientScript */
	$clientScript = Yii::app()->getClientScript();

	$clientScript->registerCoreScript('jquery');
	$clientScript->registerCoreScript('jquery.ui');
	$clientScript->registerCoreScript('jquery');
	$clientScript->registerScriptFile('/js/User.js');
	$clientScript->registerScriptFile('/js/Popup.js');
	$clientScript->registerScriptFile('/js/jquery-ui-timepicker-addon.js');
	$clientScript->registerScriptFile('//js/jquery-ui-sliderAccess.js');

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
