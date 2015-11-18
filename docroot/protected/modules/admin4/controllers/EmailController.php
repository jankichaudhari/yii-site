<?php

class EmailController extends AdminController
{

	public function actionSend()
	{
		$name    = isset($_POST['to_name']) ? $_POST['to_name'] : "";
		$email   = isset($_POST['to_email']) ? $_POST['to_email'] : "";
		$body    = isset($_POST['body']) ? $_POST['body'] : "";
		$subject = isset($_POST['subject']) ? $_POST['subject'] : "";

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			header('Content-type: application/json');
			echo json_encode(['message' => 'invalid email']);
			return;
		}

		if (!$body) {
			header('Content-type: application/json');
			echo json_encode(['message' => 'email body must be passed']);
			return;
		}

		$message = new MandrillMessage();

		if (isset(Yii::app()->params['mandrill']['test_run']) && Yii::app()->params['mandrill']['test_run']) {
			$message->enableTest();
		}

		$message->setFrom(Yii::app()->params['email']['sender_email'], Yii::app()->params['email']['sender_name']);
		$message->addTo($email, $name);
		$message->setHtmlBody($body);
		$message->setSubject($subject);
		if ($message->send()) {
			header('Content-type: application/json');
			echo json_encode($message->attributes);
		}

	}

}
