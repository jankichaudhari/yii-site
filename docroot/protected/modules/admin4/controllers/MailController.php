<?php

/**
 * To work mainly with mandrill emails
 * Class MailController
 */
class MailController extends AdminController
{

	public function actionMessageDetails($id)
	{
		$model = MandrillMessage::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Mandrill Message [id: ' . $id . '] was not found');
		}
		$dataProviderModel            = new MandrillEmail('search');
		$dataProviderModel->messageId = $model->id;

		if (isset($_GET['MandrillEmail']) && $_GET['MandrillEmail']) {
			$dataProviderModel->attributes = $_GET['MandrillEmail'];
		}

		$this->render('messageDetails', compact('model', 'dataProviderModel'));
	}

	public function actionDetails($id)
	{
		$model = MandrillEmail::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Mandrill email [id: ' . $id . '] was not found');
		}
		$this->render('details', array());
	}

}
