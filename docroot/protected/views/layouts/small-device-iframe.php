<?php
/**
 * @var $this CController
 * @var $content String
 * @var $cs      CClientScript
 */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/jquery.latest.js');
//$cs->registerScriptFile('/js/jquery.easing-1.3.pack.js');
//$cs->registerScriptFile('/js/jquery.scrollTo-1.4.2-min.js');
$cs->registerScriptFile('/js/jquery.localscroll-1.2.7-min.js');
//$cs->registerScriptFile('/js/imageScrollGalery.js');
$cs->registerScriptFile('/js/jquery.mousewheel.min.js');
$cs->registerScriptFile('/js/jquery.slider.js');
$cs->registerScriptFile('/js/Util-head.js',CClientScript::POS_HEAD);
$cs->registerScriptFile('/js/Util.js',CClientScript::POS_END);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
	<?php
	/** @var  $detector \Device */
	$isMobile = Yii::app()->device->isDevice('mobile');
	if ($isMobile) {
		echo '<link type="text/css" href="/css/public/mobile.css" rel="stylesheet"/>';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"/>';
	} else {
		echo '<link type="text/css" rel="stylesheet" href="/css/public/main.css">';
	}
	?>

	<meta name = "viewport" content = "width=device-width, minimum-scale=1, maximum-scale=1">
    <link rel="shortcut icon" href="/images/sys/favicon-woosterstock.ico" type="icon/vnd.microsoft">
</head>
<body style="overflow: hidden; background: #f3f3f3; height: 100%">
<?php echo $content ?>
</body>
</html>