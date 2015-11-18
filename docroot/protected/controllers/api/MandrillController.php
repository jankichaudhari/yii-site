<?php

class MandrillController extends PublicController
{
	protected function beforeAction($action)
	{
		$sql  = "INSERT INTO mandrillWebhookLog SET `GET` = :GET, `POST` = :POST, `SERVER`=:SERVER, `headers` = :headers";
		$cmnd = Yii::app()->db->createCommand($sql);
		$cmnd->execute(['GET' => print_r($_GET, true), 'POST' => print_r($_POST, true), 'SERVER' => print_r($_SERVER, true), 'headers' => print_r(apache_request_headers(), true)]);
		return parent::beforeAction($action);
	}

	private static function parseSpamEvent($value)
	{
		if ($model = MandrillEmail::model()->findByPk($value['msg']['_id'])) {
			$model->status = MandrillEmail::STATUS_SPAM;
			$model->save();
		}
	}

	private static function parseBouncedEvent($value)
	{
		if ($model = MandrillEmail::model()->findByPk($value['msg']['_id'])) {
			$model->status = MandrillEmail::STATUS_BOUNCED;
			$model->save();

			if ($value['msg']['email']) {
				Client::model()->updateAll(['invalidEmail' => Client::INVALID_EMAIL], 'cli_email = :email', ['email' => $value['msg']['email']]);
			}

		}
	}

	private static function parseRejectedEvent($value)
	{
		if ($model = MandrillEmail::model()->findByPk($value['msg']['_id'])) {
			$model->status = MandrillEmail::STATUS_REJECTED;
			$model->save();
		}
	}

	private static function parseOpenEvent($value)
	{
		if ($model = MandrillEmail::model()->findByPk($value['msg']['_id'])) {
			$model->status = $model->status !== MandrillEmail::STATUS_SPAM ? MandrillEmail::STATUS_OPEN : MandrillEmail::STATUS_SPAM;
			$model->opened += 1;
			$model->save();
		}
		$openTrack             = new MandrillTrackOpen();
		$openTrack->emailId    = $value['msg']['_id'];
		$openTrack->attributes = $value['location'];
		$openTrack->attributes = $value['user_agent_parsed'];
		$openTrack->save();
	}

	private static function parseSentEvent($value)
	{
		if ($model = MandrillEmail::model()->findByPk($value['msg']['_id'])) {
			$model->status = MandrillEmail::STATUS_SENT;
			if (isset($value['msg']['metadata']['clientId']) && $value['msg']['metadata']['clientId']) {
				$model->clientId = $value['msg']['metadata']['clientId']; // not sure
			}
			return $model->save();
		}
		return false;
	}

	public function actionIndex()
	{
		if (Yii::app()->request->requestType === 'HEAD') return;
		if (!isset($_POST['mandrill_events']) || !$_POST['mandrill_events']) {
			return '';
		}
		$filename = Yii::app()->params['logDirPath'] . '/mandrill-request.log';
		file_put_contents($filename, "request " . date("Y-m-d H:i:s") . "\n\n");
		file_put_contents($filename, print_r(json_decode($_POST['mandrill_events']), true) . "\n\n", FILE_APPEND);

		$data = json_decode($_POST['mandrill_events'], true);
		foreach ($data as $key => $value) {
			switch ($value['event']) {
				case "send" :
					self::parseSentEvent($value);
					break;
				case "open" :
					self::parseOpenEvent($value);
					break;
				case "reject" :
					self::parseRejectedEvent($value);
					break;
				case "hard_bounce" :
				case "soft_bounce" :
					self::parseBouncedEvent($value);
					break;
				case "spam" :
					self::parseSpamEvent($value);
					break;
			}
		}

	}
}
