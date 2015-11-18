<?php
/**
 * @var $this   LunaCinemaCommand
 * @var $client Array
 */
?>
<html>
<head>
	<style type="text/css">
		.content {
			margin : 0 auto;
			width  : 600px;
		}

		a:link, a:visited, a:active {
			color : #62a6ff;
		}

		body {
			font-family : Helvetica, sans-serif;
		}

		.separator {
			border-bottom : 1px solid #a29b9d;
		}

		.footer {
			font-size : 12px;
		}
	</style>
</head>
<body style="font-family : Helvetica, sans-serif;">
<div class="content" style="margin : 0 auto; width  : 600px;">
	<img src="http://www.woosterstock.co.uk/images/lunacinema/banner.jpg" alt="" />

	<p style="margin-top: 60px;">Hello <?php echo $client['cli_fname'] ?>,</p>

	<p>Summer’s finally arrived (in style)! To celebrate this sizzling season we’re giving away tickets to a film of your choice courtesy of the Luna Outdoor Cinema.</p>

	<p>Follow <a href="<?php echo Yii::app()->createAbsoluteUrl('site/lunacinema', ['clientId' => $client['cli_id'], 'goto' => 'https://twitter.com/woosterstock']) ?>">@Woosterstock</a>
	   & <a href="<?php echo Yii::app()->createAbsoluteUrl('site/lunacinema', [
																			  'clientId' => $client['cli_id'], 'goto' => 'https://twitter.com/TheLunaCinema'
																			  ]) ?>">@TheLunaCinema</a> on Twitter for a chance to win 2 tix to
	   a cinematic
	   experience of your choice. Don't forget to retweet your entry!</p>

	<p>Good Luck!</p>

	<p style="margin-top: 120px;">
		<a href="http://www.woosterstock.co.uk"><img src="http://www.woosterstock.co.uk/images/lunacinema/woosterstock.png" alt="Wooster & Stock" /></a>&nbsp;
		<a href="<?php echo Yii::app()->createAbsoluteUrl('site/lunacinema', [
																			 'clientId' => $client['cli_id'], 'goto' => 'https://www.facebook.com/woosterstock'
																			 ]) ?>"><img src="http://www.woosterstock.co.uk/images/lunacinema/facebook.png" alt="Facebook" /></a>&nbsp;
		<a href="<?php echo Yii::app()->createAbsoluteUrl('site/lunacinema', [
																			 'clientId' => $client['cli_id'], 'goto' => 'https://twitter.com/woosterstock'
																			 ]) ?>"><img src="http://www.woosterstock.co.uk/images/lunacinema/twitter.png" alt="Twitter" /></a>&nbsp;
		<a href="<?php echo Yii::app()->createAbsoluteUrl('site/lunacinema', [
																			 'clientId' => $client['cli_id'], 'goto' => 'http://vimeo.com/user13981519'
																			 ]) ?>"><img src="http://www.woosterstock.co.uk/images/lunacinema/vimeo.png" alt="Vimeo" /></a>&nbsp;
	</p>

	<p class="separator" style="border-bottom : 1px solid #a29b9d;"></p>

	<address class="footer" style="font-size : 12px;">
		<div>Tel: 020 7708 6700</div>
		<div>Email: <a href="mailto:admin@woosterstock.co.uk">admin@woosterstock.co.uk</a></div>
		<div>Website: <a href="http://www.woosterstock.co.uk">www.woosterstock.co.uk</a></div>
	</address>
</div>
</body>
</html>