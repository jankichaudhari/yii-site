<?php
/**
 * @var $this CController
 */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/jquery.latest.js');
$cs->registerScriptFile('/js/jquery.easing-1.3.pack.js');
$cs->registerScriptFile('/js/jquery.scrollTo-1.4.2-min.js');
$cs->registerScriptFile('/js/jquery.localscroll-1.2.7-min.js');
$cs->registerScriptFile('/js/imageScrollGalery.js');
$cs->registerScriptFile('/js/jquery.mousewheel.min.js');
$cs->registerScriptFile('/js/jquery.slider.js');

/** @var $device \device */
$device = Yii::app()->device;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8>
	<?php
	$isMobile = $device->isDevice('mobile');
	if ($isMobile) {
		echo '<link type="text/css" href="/css/public/mobile.css" rel="stylesheet"/>';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"/>';
		$cs->registerScriptFile('/js/jquery.jpanelmenu.js', CClientScript::POS_HEAD);
		$cs->registerScriptFile('/js/Util-mobile.js', CClientScript::POS_END);
	} else {
		echo '<link rel="stylesheet" href="/css/public/main.css">';
		echo '<link rel="shortcut icon" href="/images/sys/favicon-woosterstock.ico" type="icon/vnd.microsoft">';
	}
	?>
</head>
<body style="overflow: hidden; background: #ffffff; height: 100%">
<?php echo $content ?>
</body>
<script type="text/javascript">
	$("body").on('click', '.closeDOMWindow', function ()
	{
		window.parent.window.$.closeDOMWindow();
	});
</script>
</html>