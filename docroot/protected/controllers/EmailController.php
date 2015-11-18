<?php

class EmailController extends PublicController
{
	public $layout = "//layouts/default";

	public function actionUnsubscribe($id = null, $email = null)
	{
		if ($id && $email) {
			$client = Client::model()->findByAttributes(['cli_id' => $id, 'cli_email' => $email]);
			if (!$client) {
				throw new CHttpException(404, 'Client was not found');
			}
			$client->cli_saleemail = Client::EMAIL_SALES_NO;
			$client->update(['cli_saleemail']);
		}
		$this->render('unsubscribe');
	}

	public function actionSubscribe($id = null, $email = null)
	{
		if ($id && $email) {
			$client = Client::model()->findByAttributes(['cli_id' => $id, 'cli_email' => $email]);
			if (!$client) {
				throw new CHttpException(404, 'Client was not found');
			}
			$client->cli_saleemail = Client::EMAIL_SALES_YES;
			$client->update(['cli_saleemail']);
		}
		$this->render('subscribe');
	}

	public function actionOpen($client, $mail)
	{
		$hit      = new MandrillMailshotHit();
		$mailshot = MandrillMailshot::model()->findByPk($mail);
		$client   = Client::model()->findByPk($client);

		if (!$mailshot) {

			throw new CHttpException('mailshot [id : ' . $mail . '] was not found');
		}

		if (!$client) {
			throw new CHttpException('client [id : ' . $client . '] was not found');
		}
		$hit->clientId   = $client->cli_id;
		$hit->mailshotId = $mailshot->id;
		$hit->userAgent  = Yii::app()->request->getUserAgent();
		$hit->ip         = Yii::app()->request->getUserHostAddress();
		$hit->save();
		$this->redirect(['property/view', 'id' => $mailshot->instructionId]);
	}

}
