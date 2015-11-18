<?php

/**
 * Class InstructionController
 */
class InstructionController extends AdminController
{

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param $id
	 * @throws CHttpException
	 * @internal param \the $integer ID of the model to be loaded
	 * @return Deal
	 */
	public function loadModel($id)
	{

		$model = Deal::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, "Instruction [id : {$id}] was not found");
		}
		$model->followUpDue       = $model->followUpDue ? Date::formatDate('d/m/Y', $model->followUpDue) : null; // follow up due is a very logical field. it must be either null or a real date.
		$model->valuationDate     = $model->valuationDate ? Date::formatDate('d/m/Y', $model->valuationDate) : null;
		$model->dea_compdate      = $model->dea_compdate ? Date::formatDate('d/m/Y', $model->dea_compdate) : null;
		$model->dea_exchdate      = $model->dea_exchdate ? Date::formatDate('d/m/Y', $model->dea_exchdate) : null;
		$model->dea_marketprice   = $model->dea_marketprice;
		$model->dea_valueprice    = $model->dea_valueprice;
		$model->dea_valuepricemax = $model->dea_valuepricemax;
		return $model;
	}

	public function actionIndex()
	{
		$this->actionSearch();
	}

	private function saveSummary(Deal $model)
	{
		$deal = isset($_POST['Deal']) ? $_POST['Deal'] : false;
		if ($deal) {
			$model->attributes = $deal;
			if ($model->save()) {
				Yii::app()->user->setFlash('success', 'Updated Successfully');

				$model->setCategories(isset($deal['category']) ? array_keys($deal['category']) : []);

				foreach (Note::getTypes() as $noteType) {
					$this->saveNote($_POST, $noteType, $model);
				}
				return true;
			}
		}
		return false;
	}

	private function saveProduction(Deal $model)
	{
		$deal      = isset($_POST['Deal']) ? $_POST['Deal'] : false;
		$features  = isset($deal['feature']) ? array_keys($deal['feature']) : [];
		$hasErrors = false;
		if ($deal) {
			$model->attributes = $deal;

			if (isset($_POST['textCustomFeature']) && $_POST['textCustomFeature']) {
				$customFeature = $this->createCustomFeature($_POST['textCustomFeature']);
				if ($customFeature->save()) {
					$features[] = $customFeature->fea_id;
				} else {
					Yii::app()->user->setFlash('custom-feature-error', 'Custom feature could not be saved. Contact administrator');
					$hasErrors = true;
				}
			}

			if (!$model->setFeatures($features)) {
				Yii::app()->user->setFlash('feature-error', 'Features could not be updated');
				$hasErrors = true;
			}

			if (isset($deal['video']) && $deal['video']) {
				$this->saveVideo($model->dea_id, $deal['video']);
			}

			/**
			 * taking advantage of lazy operators. We first validate (to get all validation errors.
			 * Then if there were none we check if something happened before, like custom feature was not saved.
			 * finaly if there were no errors before we save the model.
			 */
			if ($model->validate() && !$hasErrors && $model->save()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param $id
	 */
	public function actionProduction($id)
	{
		$model = $this->loadModel($id);

		if ($this->saveProduction($model)) {
			$this->redirect(['production', 'id' => $model->dea_id]);
		}

		$this->render('_production', compact('model'));
	}

	/**
	 * @param $id
	 */
	public function actionSummary($id)
	{
		$model               = $this->loadModel($id);
		$model->followUpUser = $model->followUpAppointment ? $model->followUpAppointment->app_user : Yii::app()->user->id;
		$oldStatus           = $model->dea_status;
		$oldPrice            = (float)$model->dea_marketprice;

		if ($this->saveSummary($model)) {

			$newStatus     = $model->dea_status;
			$statusChanged = $oldStatus !== $newStatus;
			$priceChanged  = $model->dea_marketprice !== $oldPrice;

			if ($newStatus === Deal::STATUS_AVAILABLE && ($statusChanged || $priceChanged)) {
				$this->redirect(['production', 'id' => $model->dea_id, '#' => '#mailshots']);
			}

			if ($statusChanged) {
				$this->sendEmailOnStatusChange($model);
			}

			$this->redirect(['summary', 'id' => $model->dea_id]);
		}

		$otherInstructions = Deal::model()->conflictingWith($model)->findAll();
		$this->render('summary', compact('model', 'otherInstructions'));
	}

	/**
	 * @param int $propertyType
	 * @return bool
	 */
	public function actionPropertySubTypes($propertyType = 0)
	{

		if (!$propertyType) {
			return false;
		}
		$propertySubTypes = PropertyType::model()->getTypes($propertyType);
		foreach ($propertySubTypes as $propertySubType) {
			echo "<option value='" . $propertySubType->pty_id . "'>" . $propertySubType->pty_title . "</option>";
		}
	}

	/**
	 * @param        $feaTitleText
	 * @param        $instructionId
	 * @param string $format
	 * @return array
	 */
	public function actionSaveCustomFeature($feaTitleText, $instructionId, $format = 'JSON')
	{

		$data = array();
		if ($feaTitleText) {
			$feature = $this->createCustomFeature($feaTitleText);
			if ($feature->save()) {
				$featureToDeal            = new LinkInstructionToFeature();
				$featureToDeal->dealId    = $instructionId;
				$featureToDeal->featureId = $feature->fea_id;
				$featureToDeal->save();
			}
			$data = $feature->attributes;
		}
		echo json_encode($data);
	}

	private function createCustomFeature($title)
	{
		$model            = new Feature('custom');
		$model->fea_title = $title;
		return $model;
	}

	/**
	 * @param        $featureId
	 * @param        $instructionId
	 * @param string $format
	 * @return int
	 */
	public function actionDeleteCustomFeature($featureId, $instructionId, $format = '')
	{

		$linkForCustomFeature = LinkInstructionToFeature::model()->findByAttributes(['dealId' => $instructionId, 'featureId' => $featureId]);
		if ($linkForCustomFeature) {
			$linkForCustomFeature->delete();
		}
		$result = Feature::model()->deleteByPk($featureId);

		if ($format == 'JSON') {
			echo json_encode($result);
		} else {
			return $result;
		}
	}

	/**
	 *
	 * @param $id
	 * @throws CHttpException
	 * @return bool
	 */
	public function actionCopyInstruction($id)
	{

		/** @var $existingInstruction Deal */
		$existingInstruction = Deal::model()->findByPk($id);
		if (!$existingInstruction) {
			throw new CHttpException(404, 'Instruction is not found [id=' . $id . ']');
		}

		if (isset($_POST['Deal']) && $_POST['Deal']) {
			$newDealType    = $_POST['Deal']['dea_type'];
			$newInstruction = $existingInstruction->copyAs($newDealType);
			if ($newInstruction) {
				$this->redirect(['instruction/summary', 'id' => $newInstruction->dea_id]);
			}
		}
		$this->render('_copyInstruction', ['model' => new Deal()]);
	}

	public function actionPreviewLink($id)
	{

		if (!$id) {
			throw new CException("Instruction id must be passed");
		}

		$model       = Deal::model()->findByPk($id);
		$emailString = $model->emailLinkString;

		if (!$emailString) {
			$previewString = md5(Util::getRandomString(10));
			$updatedDeal   = Deal::model()->updateByPk($id, ['emailLinkString' => $previewString]);
			$emailString   = $updatedDeal ? $previewString : '';
		}

		if (!$emailString) {
			echo "Error! Link not generated";
		} else {
			echo $this->createAbsoluteUrl('/property/view', ['id' => $id, 'preview' => $emailString]);
		}
	}

	public function actionDisablePreviewLink($id)
	{

		if (!$id) {
			throw new CException("Instruction id must be passed");
		}

		echo Deal::model()->updateByPk($id, ['emailLinkString' => ""]);
	}

	/**
	 * @param $instructionId
	 * @param $columnName
	 * @throws CHttpException
	 * @return bool
	 */
	public function  actionShowChangeLogs($instructionId, $columnName)
	{

		$this->layout = "//layouts/new/popup";
		if (!$instructionId) {
			throw new CHttpException(404, 'Instruction id must be passed');
		}
		$historyCriteria = new CDbCriteria();
		$historyCriteria->compare('cha_row', $instructionId, false, 'AND', false);
		$historyCriteria->compare('cha_field', $columnName, false, 'AND', false);
		$historyCriteria->order = 'cha_datetime DESC';
		$dealChangeLog          = Changelog::model()->findAll($historyCriteria);
		$this->render('_changeLogs', ['dealChangeLog' => $dealChangeLog]);
	}

	/**
	 * @param        $id
	 * @param string $type
	 */
	public function actionMatchByClient($id, $type = Deal::TYPE_SALES)
	{

		$client = Client::model()->findByPk($id);
		if (isset($_POST['Client'])) {
			$client->attributes = $_POST['Client'];
		}
		if (isset($_POST['propertyTypesSales'])) {
			$client->cli_saleptype = implode("|", $_POST['propertyTypesSales']);
		}
		$model = new Deal('search');
		$this->render('MatchByClient', array(
				'model'  => $model,
				'client' => $client,
		));
	}

	/**
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionMatchClients($id)
	{

		$instruction = Deal::model()->findByPk($id);

		if (!$instruction) {
			throw new CHttpException('404', 'Instruction [id: ' . $id . '] not found');
		}

		$this->layout = '//layouts/adminDefault';

		$model = new Client('instruction-matching');

		$criteria = $model->getDbCriteria();
		$criteria->compare('cli_saleemail', 'yes');

		$this->render('matchClients', ['model' => $model, 'instruction' => $instruction]);

	}

	/**
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionCustomMailshot($id)
	{

		$model = Deal::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException("Instruction not found");
		}
		$command = Yii::getPathOfAlias("application.commands") . "/customMailshot.php";
		if (isset($_POST['mailshot']) && $_POST['mailshot']) {
			$file = CUploadedFile::getInstanceByName('mailshot[file]');

			$filepath = '';
			if ($file && $file->saveAs(Yii::app()->params['filePath'] . '/' . $file->getName())) {
				$filepath = Yii::app()->params['filePath'] . '/' . $file->getName();
			}

			$mailshot             = new Mailshot();
			$mailshot->mai_deal   = $id;
			$mailshot->mai_type   = $_POST['mailshot']['type'];
			$mailshot->mai_status = Mailshot::STATUS_PENDIG;
			if ($mailshot->save()) {
				exec("php " . $command . " " . $mailshot->mai_id . ' "' . $_POST['mailshot']['body'] . '" "' . (((isset($_POST['mailshot']['include_link']) ? $_POST['mailshot']['include_link'] : "") ? 1 : 0)) . '" "' . $filepath . '" "' . $_POST['mailshot']['debugEmail'] . '" > /dev/null &', $output);
			}

		}
		$this->render("customMailshot", ['model' => $model]);
	}

	/**
	 * @param $instructionId
	 * @throws CHttpException
	 */
	public function actionEditPdfSettings($instructionId)
	{

		$this->layout = "//layouts/new/popup";

		$model = Deal::model()->findByPk($instructionId);
		if (!$model) {
			throw new CHttpException('404', 'Instruction [id: ' . $instructionId . '] not found');
		}

		$settings = InstructionToPdfSettings::model()->findByAttributes(array('instructionId' => $instructionId));

		if (!$settings) {
			$settings = new InstructionToPdfSettings();
		}

		$settings->instructionId = $instructionId;
		if (isset($_POST['InstructionToPdfSettings']) && $_POST['InstructionToPdfSettings']) {
			$settings->attributes = $_POST['InstructionToPdfSettings'];
			if ($settings->save()) {
				Yii::app()->user->setFlash('editPdfSettings-success', 'PDF settings updated successfully');
				$this->redirect(['editPdfSettings', 'instructionId' => $instructionId]);
			}
		}

		$this->render('editPdfSettings', ['model' => $model, 'settings' => $settings]);
	}

	/**
	 *
	 */
	public function actionSearch()
	{

		$model                          = new Deal('search');
		$model->getDbCriteria()->with[] = 'negotiator';
		$model->DIY                     = [Deal::DIY_NONE, Deal::DIY_DIY, Deal::DIY_DIT];

		/** @var $user User */
		$user = Yii::app()->user->getUserObject();
		if (!isset($_GET['Deal'])) {
			if (!$model->dea_type) {
				$model->dea_type = [$user->use_scope];
			}

			if (!$model->dea_status) {
				$model->dea_status = array(
						Deal::STATUS_AVAILABLE,
						Deal::STATUS_PRODUCTION,
						Deal::STATUS_PROOFING,
						Deal::STATUS_UNDER_OFFER,
						Deal::STATUS_UNDER_OFFER_WITH_OTHER
				);
			}
		}

		$this->render('search', ['model' => $model]);
	}

	public function actionMissedFollowUpReport()
	{

		$model = new Deal('search');
		if (isset($_GET['Deal']) && $_GET['Deal']) {
			$model->attributes = $_GET['Deal'];
		}

		$this->render('reports/missedFollowUp', compact('model'));
	}

	public function actionVendorCare()
	{

		$branchCondition = "";
		if (isset($_GET['Deal']['dea_branch']) && $_GET['Deal']['dea_branch']) {
			$selectedBranches = $_GET['Deal']['dea_branch'];
			$branchCondition  = "AND dea_branch IN (" . implode(',', $selectedBranches) . ")";
		}

		$query = "
		SELECT
		ad.searchString as propertyAddress,
		GROUP_CONCAT(DISTINCT CONCAT(c.cli_fname,' ', c.cli_sname)) as vendorsNames,
		c.lastContacted as vendorLastContacted,
		GROUP_CONCAT(DISTINCT c.cli_id) AS vendorsIds,
		COUNT(DISTINCT o.off_id) as totalOffers,
		max(a.app_start) as latestViewing,
		COUNT(DISTINCT a.app_id) as totalViewings,
		COUNT(DISTINCT(CASE WHEN
			a.app_start > now() THEN a.app_id
		END)) as futureViewings,
		COUNT(DISTINCT(CASE WHEN
				a.app_start <= now() THEN a.app_id
		END)) as finishedViewings,
		TIMESTAMPDIFF(DAY,d.dea_launchdate,now()) as timeOnMarket,
		d.dea_board,d.dea_boardtype,
		d.dea_id,a.app_id,c.cli_id,a.app_type

		FROM deal d
		LEFT JOIN link_client_to_instruction lc ON (d.dea_id = lc.dealId AND lc.capacity = 'owner')
		LEFT JOIN client c ON (c.cli_id = lc.clientId)
		LEFT JOIN property p ON (p.pro_id = d.dea_prop)
		LEFT JOIN address ad ON (ad.id = p.addressId)
		LEFT JOIN offer o ON o.off_deal = d.dea_id AND o.off_status != 'Deleted'

		LEFT JOIN link_deal_to_appointment l1 ON (d.dea_id=l1.d2a_dea)
		LEFT JOIN appointment a ON (a.app_id=l1.d2a_app) AND (a.app_type = '" . Appointment::TYPE_VIEWING . "' AND a.app_status = '" . Appointment::STATUS_ACTIVE . "')

		WHERE dea_status IN ('" . Deal::STATUS_AVAILABLE . "') " . $branchCondition . "
		GROUP BY d.dea_id ";

		$count        = Yii::app()->db->createCommand('SELECT COUNT(*) FROM (' . $query . ') as totalViewing')->queryScalar();
		$dataProvider = new CSqlDataProvider($query, array(
				'totalItemCount' => $count,
				'pagination'     => ["pageSize" => 30],
				'keyField'       => 'dea_id',
				'keys'           => ['app_id', 'vendorsIds'],
				'sort'           => array(
						'defaultOrder' => 'latestViewing DESC',
						'attributes'   => array(
								'propertyAddress',
								'vendorsNames',
								'totalOffers',
								'dea_board',
								'latestViewing',
								'finishedViewings',
								'futureViewings',
								'timeOnMarket',
								'vendorLastContacted'
						),
						//'multiSort'  => true
				),
		));

		$this->render('vendorCare', array(
				'dataProvider' => $dataProvider,
		));

	}

	public function actionLatestApp($id)
	{

		$sql   = "SELECT app_id FROM appointment a INNER JOIN link_deal_to_appointment l1 ON a.app_id = l1.d2a_app AND a.app_type = 'Viewing' AND l1.d2a_dea = :id ORDER BY a.app_start DESC LIMIT 1";
		$appId = Yii::app()->db->createCommand($sql)->queryScalar(['id' => $id]);
		$this->redirect(['appointment/update', 'id' => $appId]);
	}

	public function actionDetailSearch($clientId = 0)
	{

		$model             = new Deal('search');
		$model->dea_status = [Deal::STATUS_AVAILABLE];
		$model->dea_type   = Deal::TYPE_SALES;
		$title             = "";

		$dealAttributes = isset($_GET['Deal']) && $_GET['Deal'] ? $_GET['Deal'] : [];

		if (!$dealAttributes && $clientId) {
			/** @var Client $clientModel */
			$clientModel = Client::model()->with('matchingPostcodes')->findByPk($clientId);

			$title = $clientModel->getFullName(true);

			$minimumPrices   = array_keys(Util::getPropertyPrices("minimum"));
			$model->minPrice = $clientModel->cli_salemin ? Util::getNearestArrayKey($minimumPrices, $clientModel->cli_salemin) : '';

			$maximumPrices   = array_keys(Util::getPropertyPrices("maximum"));
			$model->maxPrice = $clientModel->cli_salemax ? Util::getNearestArrayKey($maximumPrices, $clientModel->cli_salemax) : '';

			$model->minBedrooms = $clientModel->cli_salebed ? $clientModel->cli_salebed : '';

			$clientPTypes = $clientModel->getPropertyTypes();
			if ($clientPTypes && $clientPTypes[0]) {
				$model->dea_ptype    = $clientPTypes;
				$model->dea_psubtype = $clientPTypes;
			}
			$clientPostcodes = $clientModel->getMatchingPostcodes();
			if ($clientPostcodes && $clientPostcodes[key($clientPostcodes)]) {
				$model->matchingPostcodes = $clientPostcodes;
			}
		}

		if ($dealAttributes) {
			$model->attributes = $_GET['Deal'];
		}

		$this->render('detailSearch', ['model' => $model, 'title' => $title]);
	}

	/**
	 * Returns a link to instruction edit screen. later will be replaced with a new link
	 *
	 * @param $id
	 * @deprecated
	 * @return string
	 */
	public static function generateLinkToInstruction($id)
	{

		return Yii::app()->createUrl('admin4/instruction/summary', ['id' => $id]);
	}

	/**
	 * @param $price
	 * @return string
	 */
	private function formatPrice($price)
	{

		return $price ? html_entity_decode(Locale::formatPrice($price)) : '';
	}

	/**
	 * @param $data
	 * @param $noteType
	 * @param $instruction
	 */
	private function saveNote($data, $noteType, $instruction)
	{

		if (!isset($data[$noteType]['not_blurb']) || !$data[$noteType]['not_blurb']) return;

		$model = new Note();
		if (isset($data[$noteType]['not_id']) && $data[$noteType]['not_id']) {
			$model = Note::model()->findByPk($data[$noteType]['not_id']);
		}
		$model->not_blurb = $data[$noteType]['not_blurb'];
		$model->not_row   = $instruction->dea_id;
		$model->not_type  = $noteType;
		if (!$model->save()) {
			Yii::app()->user->setFlash('error', 'note [type = ' . $noteType . '] is not saved.');
		}
	}

	/**
	 * @param $instruction
	 */
	private function sendEmailOnStatusChange(Deal $instruction)
	{

		if ($instruction) {
			/** @var  $instruction Deal [ ] */
			$status      = $instruction->dea_status;
			$address     = $instruction->property->address->getFullAddressString(', ');
			$userConfigs = UserConfig::model()->findAll("configType =:configType AND configKey=:configKey AND configValue = :configValue",
														array(
																'configType'  => UserConfig::TYPE_EMAIL_ALERT,
																'configKey'   => UserConfig::KEY_EMAIL_ALERT_DEAL_STATUS,
																'configValue' => $status,
														));
			$userIds     = [];
			foreach ($userConfigs as $userConfig) {
				$userIds[] = $userConfig->userId;
			}

			$criteria         = new CDbCriteria();
			$criteria->scopes = ["emailAlertsForDealStatus"];
			$criteria->addInCondition('use_id', $userIds);
			$users = User::model()->findAll($criteria);
			foreach ($users as $user) {
				/** @var $user User[ ] */
				if ($user->use_email) {
					$fromEmail    = "admin@woosterstock.co.uk";
					$recipient    = $user->use_email;
					$emailMessage = "From:\t" . $fromEmail . "\n\n";
					$emailMessage .= "Dear " . ($user->fullName ? $user->fullName : $user->use_fname) . ",\n\n";
					$emailMessage .= "Following property status has changed to " . $status . " \n\n";
					$emailMessage .= $address;
					$emailMessage .= "\n\nSent:\t" . date("d/m/Y H:i");

					try {
						include_once("Zend/Mail.php");

						$mailToUser = new Zend_Mail("UTF-8");
						$mailToUser->addTo($recipient);
						$mailToUser->setFrom($fromEmail);
						$mailToUser->setSubject("Property status changed");
						$mailToUser->setBodyText($emailMessage);
						$mailToUser->send();

					} catch (Exception $e) {
					}
				}
			}
		}
	}

	/**
	 * @param $instructionId
	 * @param $video
	 * @throws CException
	 * @return bool
	 */
	private function saveVideo($instructionId, $video)
	{

		if (!$instructionId) {
			throw new CException("Instruction id must be passed");
		}

		$videoId = $video['videoId'];
		if (isset($video['featureVideo'])) {
			$featureVideo = $video['featureVideo'];
		}
		if (isset($video['displayOnSite'])) {
			$displayOnSite = $video['displayOnSite'];
		}

		$instructionVideo = InstructionVideo::model()->findByAttributes(['instructionId' => $instructionId]);
		if ($instructionVideo) {
			InstructionVideo::model()->findByAttributes(['instructionId' => $instructionId])->delete();
		}

		if (!isset($videoId) || !$videoId) {
			return false;
		}

		if (!($data = @file_get_contents('http://vimeo.com/api/v2/video/' . $videoId . '.json'))) {
			return false;
		}
		$data = json_decode($data, true);
		if (!$data[0]) {
			return false;
		}
		$newInstructionVideo                = new InstructionVideo();
		$newInstructionVideo->instructionId = $instructionId;
		$newInstructionVideo->videoId       = $videoId;
		if (!empty($featureVideo)) {
			InstructionVideo::model()->updateAll(['featuredVideo' => 0]);
			$newInstructionVideo->featuredVideo = 1;
		}
		if (!empty($displayOnSite)) {
			$newInstructionVideo->displayOnSite = 1;
		}
		$newInstructionVideo->host      = 'vimeo';
		$newInstructionVideo->videoData = json_encode(str_replace(["\n", "\r"], "", $data[0]));

		return $newInstructionVideo->save();
	}

	/**
	 *
	 * @throws CHttpException
	 */
	public function actionRegisterInterest()
	{
		$clientId = isset($_GET['cli_id']) ? $_GET['cli_id'] : null;
		$deals = isset($_GET['dea_id']) ? (array)$_GET['dea_id'] : []; // input is unpredictable

		$model = Deal::model()->findByPk(reset($deals));

		$client = Client::model()->findByPk($clientId);

		if (!$client) {
			throw new CHttpException(404, 'client [id: ' . $clientId . '] was not found');
		}

		if (!$model || !$model->isDIY(Deal::DIY_DIY)) {
			throw new CHttpException('Model is not a DIY instruction');
		}

		$model->registerInterest($clientId);

		$this->render('registerInterest', compact('model', 'client'));
	}

	public function actionInterestVendorNotified()
	{
		$dealId   = (isset($_POST['dealId']) ? $_POST['dealId'] : "");
		$clientId = (isset($_POST['clientId']) ? $_POST['clientId'] : "");
		$type     = (isset($_POST['type']) ? $_POST['type'] : "");

		$model = Deal::model()->findByPk($dealId);
		if (!$model) {
			return json_encode(['error' => true, 'message' => 'deal is not found']);
		}

		$model->registerInterest($clientId, $type);
		echo json_encode(['updated' => true]);
	}

}