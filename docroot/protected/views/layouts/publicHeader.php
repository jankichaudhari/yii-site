<?php
/**
 * @var $this    CController
 * @var $content String
 * @var $cs CClientScript
 */
$pageTitle = isset($this) ? $this->pageTitle : (isset($pageTitle) ? $pageTitle : '');
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/jquery.latest.js');
$cs->registerScriptFile('/js/jquery.scrollTo-1.4.2-min.js');
$cs->registerScriptFile('/js/jquery.localscroll-1.2.7-min.js');
$cs->registerScriptFile('/js/imageScrollGalery.js');
$cs->registerScriptFile('/js/jquery.fancybox-1.3.4.pack.js');
$cs->registerScriptFile('/js/jquery.easing-1.3.pack.js');
$cs->registerScriptFile('/js/jquery.mousewheel.min.js');

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8>
	<link rel="stylesheet" href="/css/public/styles.css">
	<link rel="shortcut icon" href="/images/sys/favicon-woosterstock.ico" type="icon/vnd.microsoft">
	<link rel="stylesheet" href="/css/public/fancybox/jquery.fancybox-1.3.4.css">
	<?php

	?>
	<title>Wooster & Stock <?php echo $pageTitle ? ' - ' . $pageTitle : '' ?></title>
</head>
<body>
<div id="header">
	<div class="container">
		<div id="logo">
			<a href="/"><img src="/images/sys/wooster-stock-logo.gif" style="vertical-align: bottom;" alt="Wooster & Stock"></a>
		</div>
		<div id="info">
			<h4>CALL US ON 020 7708 6700</h4>
		</div>
	</div>
</div>
<div style="background-color:#FFF;margin:0 auto;text-align:left;width:1044px;position: relative;z-index: 11">
	<div class="navigation">
		<ul>
			<li>
				<a href="/">Home</a>
			</li>
			<li>
				<a href="/sales">Sales</a>
			</li>
			<li>
				<a href="/register" class="register">Register</a>
			</li>
			<li>
				<a href="/valuations">Valuations</a>
			</li>
			<li>
				<a href="/local-events">Local events</a>
			</li>
			<li>
				<a href="/contact">Contact</a>
			</li>
			<li>
				<a href="/parks">Parks</a>
			</li>
		</ul>
	</div>

