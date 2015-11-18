<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link href="/v3.0/live/admin/css/styles.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/gray/adminv4.css" />
<!--	<link rel="stylesheet" type="text/css" href="--><?php //echo Yii::app()->request->baseUrl; ?><!--/css/gray/default.css" />-->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui-timepicker-addon.css" />
	<link media="screen" rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/gray/form.css" />
	<link media="handheld" rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form-handheld.css" />
	<link rel="icon" type="image/vnd.microsoft.icon" href="/images/sys/favicon-admin.ico" />
	<?php
	/** @var $clientScript CClientScript */
	$clientScript = Yii::app()->getClientScript(); ?>
	<?php $clientScript->registerCoreScript('jquery'); ?>
	<?php $clientScript->registerCoreScript('jquery.ui'); ?>
	<?php $clientScript->registerCoreScript('jquery'); ?>
	<?php $clientScript->registerScriptFile('/js/User.js') ?>
	<?php $clientScript->registerScriptFile('/js/Popup.js') ?>
	<?php $clientScript->registerScriptFile('/js/jquery-ui-timepicker-addon.js') ?>
	<?php $clientScript->registerScriptFile('/js/jquery-ui-sliderAccess.js') ?>
	<?php Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css'); ?>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<script type="text/javascript">
	$.datepicker.setDefaults({'dateFormat':'dd/mm/yy','showOn' : 'button', 'buttonImage': '/images/sys/calendar-icon.png', 'buttonImageOnly' : true, 'numberOfMonths' : 3})
</script>
<body>
<?php echo $content ?>
</body>
</html>
