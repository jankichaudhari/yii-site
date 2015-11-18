<?php

class EmailHelper
{
	const TYPE_HTML = 'html';
	const TYPE_TEXT = 'text';

	static function signature($type = self::TYPE_HTML)
	{

		$officeData = Office::model()->active()->findAll();

		if ($type == self::TYPE_HTML) {
			$emailMediaImageUrl = "http://" . Yii::app()->params['hostname'] . "/images/email/";
			$emailMediaLinks    = '
					<div>
					<a href="https://www.woosterstock.co.uk/"><img src="' . $emailMediaImageUrl . 'Wooster-Logo-Orange.png" alt="Wooster&Stock"/></a>&nbsp;
					<a href="https://www.facebook.com/woosterstock"><img src="' . $emailMediaImageUrl . 'facebook_square_black.png" alt=""></a>&nbsp;
					<a href="https://twitter.com/woosterstock"><img src="' . $emailMediaImageUrl . 'ttwitter_square_black.png" alt=""></a>&nbsp;
					<a href="http://vimeo.com/user13981519""><img src="' . $emailMediaImageUrl . 'vimeo_square_black.png" alt=""></a>
					</div>
				';
			$officeFooter       = '<table class="valuation-email-footer" style="width: 600px; font-size: 12px; color: #555; font-family:Arial, Helvetica, sans-serif; "><tr>';

			foreach ($officeData as $value) {
				$officeFooter .= '<td style="vertical-align:top">
							<div>' . $value->shortTitle . '</div>
							<div><a href = "mailto:' . $value->email . '"> ' . $value->email . '</a></div>
							<div>Phone: ' . $value->phone . '</div>
							</td> ';
			}
			$officeFooter .= '</tr></table> ';

			return '<div style = "font-family:Arial, Helvetica, sans-serif; font-size:15px; font-weight: bold; color:#FF9900">' . $emailMediaLinks . '</div>
					' . $officeFooter;
		} else {
			$officeFooter = '';

			foreach ($officeData as $value) {
				$officeFooter .= "
							{$value->shortTitle}\n
							" . ($value->address ? $value->address->getFullAddressString() . "\n" : "") .
						"Phone: {$value->phone}\n\n";
			}

			return 'Wooster & Stock
					woosterstock.co.uk

					' . $officeFooter;
		}

	}

	static function disclaimer($recipient, $type = self::TYPE_HTML)
	{
		if ($type === self::TYPE_HTML) {
			return '<div style = "width:600px;">
									<span style = "font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#666666"> This email and any files transmitted with it are confidential and intended
									solely for ' . $recipient . ' If you are not the named addressee you should not disseminate, distribute, copy or alter this email . Any views or opinions presented in this email are
									solely those of the author and might not represent those of Wooster & Stock.</span>
									</div>';
		} else {
			return 'This email and any files transmitted with it are confidential and intended solely for ' . $recipient . ' .
					If you are not the named addressee you should not disseminate, distribute, copy or alter this email . Any views or
					opinions presented in this email are solely those of the author and might not represent those of Wooster & Stock .';
		}
	}

}
