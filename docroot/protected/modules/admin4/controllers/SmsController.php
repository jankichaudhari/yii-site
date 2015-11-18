<?php

class SmsController extends AdminController
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionConfirm($app)
	{

		/** @var Appointment $model */
		$model = Appointment::model()->findByPk($app);
		if (!$model) {
			throw new CHttpException(404, 'Appointment [id: ' . $app . '] not found');
		}

		if ($_POST) {
			/** @var Telephone[] $phones */
			$phones = []; // mapping phones for fast access
			foreach (Telephone::model()->findAllByPk($_POST['phones']) as $value) {
				$phones[$value->tel_id] = $value;
			}

			/** @var Deal[] $instructions */
			$instructions = [];
			foreach ($model->instructions as $instruction) {
				$instructions[$instruction->dea_id] = $instruction; // mapping iinstructions
			}

			/** @var Client[] $clients */
			$clients    = [];
			$clientKeys = array_merge(
				(isset($_POST['vendors']) ? array_keys($_POST['vendors']) : []),
				(isset($_POST['clients']) ? array_keys($_POST['clients']) : [])
			);

			foreach (Client::model()->findAllByPk($clientKeys) as $key => $value) {
				$clients[$value->cli_id] = $value;
			}

			$sendMessage = function ($clientId, $data) use ($phones, $instructions, $clients, $model) {
				if (!$data['send'] || !$data['send_to'] || !isset($phones[$data['send_to']])) {
					return;
				}
				$sms           = new Sms();
				$sms->toNumber = $phones[$data['send_to']]->tel_number;
				$sms->clientId = $clientId;
				$sms->text     = $data['text'];
				$sms->send();
				$model->addTextMessage($sms);
			};

			$errors = [];
			if (isset($_POST['vendors'])) {
				foreach ($_POST['vendors'] as $vendorId => $data) {
					try {
						$sendMessage($vendorId, $data);
					} catch (Exception $e) {
						$errors[] = $e;
					}
				}
			}
			if (isset($_POST['clients'])) {
				foreach ($_POST['clients'] as $vendorId => $data) {
					try {
						$sendMessage($vendorId, $data);
					} catch (Exception $e) {
						$errors[] = $e;
					}
				}
			}
			$model->saveTextMessages();
			if (!$errors) {
				Yii::app()->user->setFlash('messages-sent', 'Text messages successfully sent.');
			}
		}

		$this->render('confirm', compact('model'));
	}

	public function actionIncoming()
	{
		$model = new Sms('search');
		if (isset($_GET['Sms']) && $_GET['Sms']) {
			$model->attributes = $_GET['Sms'];
		}
		$this->render('incoming', compact('model'));
	}

	public function actionInfo($id)
	{
		if (Yii::app()->request->isAjaxRequest) {
			header('Content-type:text/json');
			try {
				$model = $this->loadModel($id);
				if (isset($_GET['markAsRead']) && !$model->isRead()) {
					$model->markRead(Yii::app()->user->id);
				}
				$data                  = $model->attributes;
				$data['created']       = Date::formatDate('d/m/Y H:i', $data['created']);
				$data['createdByName'] = $model->sender ? $model->sender->getFullName() : '';
				$data['readBy']        = $model->reader ? $model->reader->getFullName() : 'Not read yet';
				$data['readAt']        = $model->isRead() ? Date::formatDate('d/m/Y H:i', $data['readAt']) : '';
				echo json_encode($data);
			} catch (Exception $e) {
				header('HTTP/1.0 404 Not Found');
				echo json_encode(['message' => $e->getMessage()]);
			}
		}
	}

	public function actionSend()
	{
		if (!isset($_POST['text']) || !$_POST['text']) {
			throw new CHttpException(400, 'text must be passed');
		}

		if (!isset($_POST['to']) || !$_POST['to']) {
			throw new CHttpException(400, 'number must be passed');
		}

		$sms = new Sms();
		if (isset($_POST['clientId']) && $_POST['clientId']) {
			$sms->clientId = $_POST['clientId'];
		}
		$sms->toNumber = $_POST['to'];
		$sms->text     = $_POST['text'];
		$sms->send();

		header('Content-type:text/json');
		echo json_encode($sms->attributes);
	}

	/**
	 * @param $id
	 * @return Sms
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = Sms::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, "message [id: " . $id . "] is not found");
		}

		return $model;
	}
}
