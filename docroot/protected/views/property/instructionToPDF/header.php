<?php
/**
 * @var $this  PropertyController
 * @var $model Deal
 */
$price = $this->getPriceWithQualifier($model);
?>
<html>
<head>
	<?php foreach ($cssFiles as $key => $cssFileName): ?>
		<link rel="stylesheet" href="<?= $pdf->getResource('css', $cssFileName) ?>">
	<?php endforeach; ?>
	<script>
		function subst()
		{
			var vars = {};
			var x = document.location.search.substring(1).split('&');
			for (var i in x) {
				var z = x[i].split('=', 2);
				vars[z[0]] = unescape(z[1]);
			}
			var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
			for (var i in x) {
				if (x[i] == 'page' && vars[x[i]] == 1) {
					document.getElementById("front-page-header").style.display = "block";
					document.getElementById("header").style.display = "none";
					break;
				}
				var y = document.getElementsByClassName(x[i]);
				for (var j = 0; j < y.length; ++j) y[j].textContent = vars[x[i]];
			}
		}
	</script>
	<style type="text/css">
		#header {
			height : 24.7mm;
		}

		#header .block {
			background : rgb(240, 145, 0);
		}

		div#front-page-header {
			background : rgb(240, 145, 0);
			height     : 24.7mm;
			position   : relative;
			display    : none;
		}

		.main-page-logo {
			padding-top  : 9.3pt;
			padding-left : 3.5pt;
			width        : 105.6pt;
			height       : 51.7pt;
			float        : left;

		}

		.main-page-logo > img {
			width  : 105.6pt;
			height : 51.7pt;
		}

		.headerUrl {
			position    : absolute;
			margin-top  : 16.394mm;
			right       : 2.824mm;
			font-size   : 18pt;
			line-height : 21pt;
			height      : 21pt;
			font-family : 'Arial, Helvetica, sans-serif Bold';
			color       : white;
		}

	</style>
</head>
<body style="border:0; margin: 0;" onload="subst()">
<div id="header">
	<div class="block" style="height:49pt; margin-bottom:1pt">
		<div>
			<div style="float:left">
				<div style="padding-top:1mm; padding-left: 2.25mm;">
					<div class="bold" style="font-size: 19pt; line-height: 21pt"><?php echo $model->property->getAddressObject()->getLine(3) ?></div>
					<div style="font-size: 15pt; line-height: 21pt;"><?php echo $price ?>
						, <?php echo $model->property->getAddressObject()->getAreaObject() ? $model->property->getAddressObject()
																											 ->getAreaObject()->are_title . ', ' : '' ?><?php echo $model->property->getPostcodePart() ?></div>
				</div>
			</div>
			<div style="float:right; padding-top: 2.1mm; padding-right: 3mm; ">
				<img style="width:28.9mm; height: 14.1mm" src="<?php echo $pdf->addImage(Yii::app()->params['imgPath'] . '/exportToPDF/regular-page-header-logo.png') ?>" alt="">
			</div>
		</div>
	</div>
	<div class="block" style="height:20pt; font-size: 12pt; line-height: 20pt; font-family:'Arial, Helvetica, sans-serif ExtraLight'; font-style:italic; color:white; padding-left: 2.25mm;">
		Call <span style="font-family: 'Arial, Helvetica, sans-serif bold'; font-style: normal"><?php echo $model->branch->bra_tel ?></span> to view this property, or visit
		<span style="font-family: 'Arial, Helvetica, sans-serif bold'; font-style: normal">woosterstock.co.uk</span> for more details
	</div>
</div>

<div id="front-page-header">
	<div class="main-page-logo">
		<img src="<?php echo $pdf->addImage(Yii::app()->params['imgPath'] . '/exportToPDF/main-page-logo.png') ?>" alt="">
	</div>
	<div class="headerUrl">
		woosterstock.co.uk
	</div>
</div>
<div style="height: 4pt; background: white"></div>
</body>
</html>