<?php
/**
 * @var $this    PropertyController
 * @var $model   Deal
 * @var $offices Office[]
 */
?>
<html>
<head>
	<style type="text/css">
		#footer {
			font-family   : 'Arial, Helvetica, sans-serif Bold';
			border-bottom : 1pt solid rgb(244, 145, 30);
			font-size     : 10pt;
			line-height   : 14pt;
			margin-top    : 20pt;
		}

		span.sep-pipe {
			height     : 7.90pt;
			display    : inline-block;
			width      : .7mm;
			background : rgb(252, 196, 136);
			margin     : 0 7pt;
		}

	</style>
</head>
<body style="border:0; margin: 0;">
<div id="footer">
	<span style="color:rgb(244,145,30); font-family: 'Arial, Helvetica, sans-serif Bold'">woosterstock.co.uk</span>
	<?php foreach ($offices as $office): ?>
		<span class="sep-pipe"></span>
		<span style="font-family: 'Arial, Helvetica, sans-serif Bold';"><?php echo $office->shortTitle ?></span>
		<span><?php echo $office->phone ?></span>
	<?php endforeach; ?>

</div>
</body>
</html>