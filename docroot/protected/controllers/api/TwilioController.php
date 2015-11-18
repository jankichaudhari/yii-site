<?php

class TwilioController extends PublicController
{
	public function actionReplyMessage()
	{
		if (isset(Yii::app()->params['twilio']['replyMessage']) && Yii::app()->params['twilio']['replyMessage']) {
			file_put_contents(Yii::app()->params['logDirPath'] . '/twilio_request.log', print_r($_REQUEST, true) . "\n\n", FILE_APPEND);
			$phone  = str_replace('+44', '', $_REQUEST['From']);
			$client = Client::model()->findByPhone($phone);

			$sms           = new Sms();
			$sms->clientId = $client ? $client->cli_id : 0;
			$sms->receive($_REQUEST);

			if ($client) {
				$latestText = Sms::model()->latestTextToClient($client);
			}

			header('content-type: text/xml');
			echo '<Response><Sms><![CDATA[' . Yii::app()->params['twilio']['replyMessage'] . ']]></Sms></Response>';
		}
	}
}

