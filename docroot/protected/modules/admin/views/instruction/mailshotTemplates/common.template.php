<?php
/**
 * @var $this  CController
 * @var $title String
 * @var $text  String
 */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title ?></title>
</head>
<body>
<table style="width: 98%;font-family : Helvetica,Sans-Serif;font-size: 20px;font-weight : bold;color: #333333; border-spacing: 10px;">
	<tr>
		<td></td>
		<td style="width:650px; background-color: #f58233; padding: 25px;">
			<h1 style="font-family: Helvetica, sans-serif; font-weight: bold; color: white; font-size: 24px;"><?php echo $title ?></h1>

			<div style="color: #333333"><p style="margin-top: 10px; margin-bottom: 10px"><?php echo $text ?></p>

				<p style="margin-top: 10px; margin-bottom: 10px">*|INSTRUCTION_STRAPLINE|*, *|INSTRUCTION_TITLE|*, <span style="color: white;">*|INSTRUCTION_PRICE|*</span></p>

				<p style="margin-top: 10px; margin-bottom: 10px">To arrange a viewing please call our *|OFFICE_TITLE|* on <span style="color: white">*|OFFICE_NUMBER|*</span>
				</p>

				<div>
					<table style="width: 100%">
						<tr>
							<td style="width:50%; background-color: #333; text-align: center; padding: 10px;">
								<a style="color: white;text-decoration : none; font-size: 30px; font-weight: bold; font-family: Helvetica, sans-serif" href="<?php echo isset($this) ? urldecode($this->createAbsoluteUrl('/email/open', [
																																																										 'client' => '*|CLIENT_ID|*',
																																																										 'mail'   => '*|MAILSHOT_ID|*'
																																																										 ])) : '#' ?>">VIEW
																																																													   ON
																																																													   WEBSITE</a>
							</td>
							<td></td>
						</tr>
					</table>
				</div>
			</div>
		</td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td style="background-color: #f58233; padding: 15px 25px; color: white; font-size: 14px;">
			To unsubscribe from all future emails please click <a style="color: #333" href="<?php echo isset($this) ? urldecode($this->createAbsoluteUrl('/email/unsubscribe', [
																																											   'id'    => '*|CLIENT_ID|*',
																																											   'email' => '*|CLIENT_EMAIL|*'
																																											   ])) : '#' ?>">here</a>
		</td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<table style="border-color: red; width:100%;">
				<td style="width: 28px; padding-right: 5px;">
					<a href="https://www.facebook.com/woosterstock"><img src="http://www.woosterstock.co.uk/images/lunacinema/facebook.png" alt="Facebook" /></a>
				</td>
				<td style="width: 28px; padding-right: 5px;">
					<a href="https://twitter.com/woosterstock"><img src="http://www.woosterstock.co.uk/images/lunacinema/twitter.png" alt="Twitter" /></a></td>
				<td style="width: 28px; padding-right: 5px;">
					<a href="http://vimeo.com/user13981519"><img src="http://www.woosterstock.co.uk/images/lunacinema/vimeo.png" alt="Vimeo" /></a>
				</td>
				<td style="text-align: right">
					<a href="http://www.woosterstock.co.uk"><img src="http://www.woosterstock.co.uk/images/lunacinema/woosterstock.png" alt="Wooster & Stock" /></a></td>
			</table>
		</td>
		<td></td>
	</tr>
</table>
</body>
</html>
