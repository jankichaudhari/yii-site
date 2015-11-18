<?php
class OfferController extends AdminController
{
	public $popupMode = false;

	public function actionDisplaySave($id = 0, $instructionId = 0)
	{

		$this->layout = '//layouts/new/popup';
		$cliId        = isset($_GET['cliId']) ? $_GET['cliId'] : 0;

		$model = new Offer();

		if ($id) {
			$model         = Offer::model()->findByPk($id);
			$instructionId = empty($instructionId) ? $model->off_deal : $instructionId;
			if (!$cliId) {
				$this->actionSaveClientToOffer($cliId, $id);
			}
		}

		if (isset($_POST['delete']) && $_POST['delete'] || isset($_POST['restore']) && $_POST['restore']) {

			$model->off_status = isset($_POST['delete']) ? Offer::STATUS_DELETED : Offer::STATUS_SUBMITTED;
			if ($model->save()) {
				if (isset($_POST['delete'])) {
					Yii::app()->user->setFlash('offer-deleted', 'Offer is deleted!');
				} else {
					Yii::app()->user->setFlash('offer-restored', 'Offer is restored!');
				}

				Yii::app()->user->setFlash('offer-callback', (isset($_GET['callback']) ? $_GET['callback'] : ""));
				$this->redirect(array(
									 'displaySave',
									 'id'            => $id,
									 'instructionId' => $instructionId,
									 'callback'      => isset($_GET['callback']) ? $_GET['callback'] : "",
									 'close'         => isset($_POST['close']),
								));
			}

		} else {
			if (isset($_POST['Offer']) && $_POST['Offer']) {
				$model->attributes = $_POST['Offer'];

				if ($model->save()) {
					if (isset($_POST['Offer']['clientId']) && $_POST['Offer']['clientId']) {
						$this->actionSaveClientToOffer($_POST['Offer']['clientId'], $model->off_id);
					}

					if (isset($_POST['Offer']['clientStatus']) && $_POST['Offer']['clientStatus']) {
						$clientStatus     = $_POST['Offer']['clientStatus'];
						$clientStatusType = Deal::model()->findByPk($instructionId)->dea_type == Deal::TYPE_SALES ? 'cli_salestatus' : 'cli_letstatus';
						foreach ($clientStatus as $clientId => $clientStatusId) {
							Client::model()->updateByPk($clientId, [$clientStatusType => $clientStatusId]);
						}
					}

					Yii::app()->user->setFlash('offer-updated', 'Saved!');
					Yii::app()->user->setFlash('offer-callback', (isset($_GET['callback']) ? $_GET['callback'] : ""));
					$this->redirect(array(
										 'displaySave',
										 'id'            => $model->off_id,
										 'instructionId' => $instructionId,
										 'callback'      => (isset($_GET['callback']) ? $_GET['callback'] : ""),
										 'close'         => (isset($_POST['close']) ? true : false),
									));
				}
			}
		}

		if (Yii::app()->user->hasFlash('offer-callback')) {
			$callback    = Yii::app()->user->getFlash('offer-callback');
			$callbackObj = new PopupCallback($callback);

			if ($callback == 'showInstructionOffers') {
				$callbackObj->run(array($instructionId), isset($_GET['close']) && $_GET['close']);
			} else {
				$callbackObj->run(array($model->off_id), isset($_GET['close']) && $_GET['close']);
			}
		}

		$this->render('edit', ['model' => $model, 'instructionId' => $instructionId, 'clientId' => $cliId]);
	}

	public function actionSaveClientToOffer($cliId, $id)
	{

		$existClientToOffer = ClientToOffer::model()->findByAttributes(["c2o_cli" => $cliId, "c2o_off" => $id]);
		if (count($existClientToOffer) == 0) {
			$modelClientToOffer          = new ClientToOffer();
			$modelClientToOffer->c2o_cli = $cliId;
			$modelClientToOffer->c2o_off = $id;
			if ($modelClientToOffer->save()) {
				Yii::app()->user->setFlash('offer-updated', 'Saved!');
			}
		}
	}

	/**
	 * Lists all offers excluding deleted. if negotiator is specified then lists only offers of this neg
	 * @param      $instructionId
	 * @param null $negotiator
	 * @throws CHttpException
	 */
	public function actionListOffers($instructionId, $negotiator = null)
	{

		if (!$instructionId) {
			throw new CHttpException('Instruction ID must be passed');
		}
		$offers = Offer::model()->nonDeleted()->findAllByAttributes(array_filter(array(
																					  'off_deal' => $instructionId,
																					  'off_neg'  => $negotiator,
																				 )));

		$this->renderPartial('listOffers', array(
												'offers'        => $offers,
												'instructionId' => $instructionId
										   ));
	}

	public function actionDeleteClient($offerClientId)
	{

		echo ClientToOffer::model()->deleteByPk($offerClientId);
	}

	public function actionShowOfferClients($id = 0)
	{

		if ($id) {
			echo json_encode(array('html' => ''));
			Yii::app()->end();
		}
		$instructionId       = Offer::model()->findByPk($id)->off_deal;
		$instructionType     = Deal::model()->findByPk($instructionId)->dea_type;
		$clientStatusType    = ($instructionType == 'Sales') ? 'cli_salestatus' : 'cli_letstatus';
		$offerClientCriteria = new CDbCriteria();
		$offerClientCriteria->compare('c2o_off', $id, false, 'AND', false);
		$offerClientList = ClientToOffer::model()->findAll($offerClientCriteria);
		$this->renderPartial('_clients', ['offerClientList' => $offerClientList, 'instructionType' => $instructionType, 'clientStatusType' => $clientStatusType]);
	}

	public function actionDeleteOffer()
	{

		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		if (!$id) {
			throw new BadMethodCallException('id must be passed');
		}
		/** @var $model Offer */
		$model             = Offer::model()->findByPk($id);
		$model->off_status = 'Deleted';
		if (!$model->save()) {
			return false;
		}
		return true;
	}

	public function actionCreate($instructionId = null, $feedbackId = null)
	{

		/** @var $feedback LinkDealToAppointment */

		if (isset($_GET['popup'])) {
			$this->asPopup();
		}

		$model             = new Offer();
		$model->off_deal   = $instructionId;
		$model->off_date   = date('d/m/Y');
		$model->off_status = Offer::STATUS_NOT_SUBMITTED;

		if ($feedbackId) {
			$feedback        = LinkDealToAppointment::model()->findByPk($feedbackId); // we get clients and instruction out of feedback;
			$model->off_deal = $feedback->deal->dea_id;
			$model->clients  = $feedback->appointment->clients;
			$model->off_app  = $feedback->appointment->app_id;
		}

		if (!$model->instruction) {
			throw new BadMethodCallException('Offer must be assigned to instruction');
		}

		$this->edit($model);

	}

	public function actionUpdate($id)
	{

		if ($_GET['popup']) {
			$this->asPopup();
		}
		$model = Offer::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Offer [id = ' . $id . '] is not found');
		}
		$this->edit($model);
	}

	private function asPopup()
	{

		$this->layout    = '//layouts/new/popup';
		$this->popupMode = true;
	}

	private function edit(Offer $model)
	{

		if (isset($_POST['restore'])) {
			if ($model->restore()) {
				Yii::app()->user->setFlash('offer-restored', 'Offer is restored!');
			}
		}

		if (isset($_POST['delete'])) {
			$model->deactivate();
		}

		if ($model->off_status == Offer::STATUS_DELETED) { // status becomes read-only. until restored
			unset($_POST['Offer']['off_status']);
		}

		if (isset($_POST['Offer']) && $_POST['Offer']) {
			$model->attributes = $_POST['Offer'];
			if (isset($_POST['client']) && $_POST['client']) {
				$model->setClients($_POST['client']);
			}
			if ($model->save()) {
				if ($_POST['clientStatus']) {
					foreach ($model->clients as $client) {
						$client->cli_salestatus = $_POST['clientStatus'];
						$client->save();
					}
				}
				Yii::app()->user->setFlash('offer-success', 'Offer is saved!');
				$this->redirect(['update', 'id' => $model->off_id, 'popup' => $this->popupMode, 'callback' => (isset($_GET['callback']) ? $_GET['callback'] : "")]);
			}
		}

		if (isset($_GET['callback']) && $_GET['callback']) {
			$callback = new PopupCallback($_GET['callback']);
			$callback->run([], isset($_POST['close']));
		} else {
			if (isset($_POST['close'])) {
				echo '<script type="text/javascript">window.close()</script>';
				Yii::app()->end();
			}
		}

		$this->render('edit', array(
								   'model'         => $model
							  ));
	}
}