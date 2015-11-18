<?php

/**
 * @package Admin.Client
 */
class ClientController extends AdminController
{

	public $layout = '//layouts/adminDefault';
	public $popupMode = false;

	public function actionPopupSelect()
	{

		$this->layout = '//layouts/new/popup';
		$model        = new Client('search');

		$model->cli_saleemail = '';
		$model->cli_created   = '';

		$model->telephones = array(new Telephone('search'));
		$this->render('selectPopup', ['model' => $model]);
	}

	public function actionSearch()
	{

		$model                      = new Client('search');
		$model->searchNoBudget      = true;
		$model->searchNoMinimumBeds = true;

		$model->cli_saleemail = '';
		$model->cli_created   = '';

		$model->telephones = array(new Telephone('search'));
		$this->render('search', ['model' => $model]);
	}

	public function actionCreate()
	{

		$model = new Client();

		if (isset($_GET['clientSearchedName']) && $_GET['clientSearchedName']) {
			$t                = explode(' ', $_GET['clientSearchedName']);
			$model->cli_fname = array_shift($t);
			$model->cli_sname = implode(' ', $t);
		}

		$this->saveClient($model);
		$this->render('update', ['model' => $model]);
	}

	private function  saveClient(Client $model)
	{

		$this->prepareModel($model);

		if (isset($_POST['Client']) && $_POST['Client']) {
			$_POST['Client']['propertyTypesIds'] = isset($_POST['Client']['propertyTypesIds']) ? $_POST['Client']['propertyTypesIds'] : [];
			$_POST['Client']['propertyCategoryIds'] = isset($_POST['Client']['propertyCategoryIds']) ? $_POST['Client']['propertyCategoryIds'] : [];

			$model->attributes = $_POST['Client'];
			if ($model->isNewRecord) {
				$model->scenario = 'validPhoneOnInsert';
			}

			$generalNoteIds = isset($_POST['noteId-' . Note::TYPE_CLIENT_GENERAL]) ? $_POST['noteId-' . Note::TYPE_CLIENT_GENERAL] : [];
			$generalNote    = isset($_POST[Note::TYPE_CLIENT_GENERAL]) ? $_POST[Note::TYPE_CLIENT_GENERAL] : [];
			$reqNoteIds     = isset($_POST['noteId-' . Note::TYPE_CLIENT_REQ]) ? $_POST['noteId-' . Note::TYPE_CLIENT_REQ] : [];
			$reqNote        = isset($_POST[Note::TYPE_CLIENT_REQ]) ? $_POST[Note::TYPE_CLIENT_REQ] : [];
			$telephones     = isset($_POST['telephones']) ? $_POST['telephones'] : [];

			$existingPhones = Telephone::model()->findAllByPk(array_filter($telephones['id']), ['index' => 'tel_id']);

			if (isset($telephones['number'][0]) && $telephones['number'][0]) {
				$phone             = new Telephone();
				$phone->tel_number = $telephones['number'][0];
				$phone->tel_type   = $telephones['type'][0];
				$model->_newPhones = [$phone];
			}

			$model->addressID       = isset($_POST['primaryAddress']['id']) ? $_POST['primaryAddress']['id'] : 0;
			$model->secondAddressID = isset($_POST['secondAddress']['id']) ? $_POST['secondAddress']['id'] : 0;

			if ($model->save()) {
				/** @var $existingPhones Telephone */
				foreach ($telephones['number'] as $key => $number) {
					$phoneId = $telephones['id'][$key];
					$type    = $telephones['type'][$key];
					if (isset($existingPhones[$phoneId])) {
						$phone = $existingPhones[$phoneId];
					} else {
						$phone = new Telephone();
					}

					if ($number) {
						$phone->tel_cli    = $model->cli_id;
						$phone->tel_number = $number;
						$phone->tel_type   = $type;
						$phone->save();
					} else {
						if (!$phone->isNewRecord) {
							$phone->delete();
						}
					}
				}
				$model->saveAreas(isset($_POST['Client']['matchingPostcode']) ? $_POST['Client']['matchingPostcode'] : []);
				$model->saveFeatures(isset($_POST['Client']['feature']) ? $_POST['Client']['feature'] : []);

				if ($generalNoteIds) {
					Note::model()->saveNoteTypeIds($generalNoteIds, $model->cli_id);
				}
				if ($reqNoteIds) {
					Note::model()->saveNoteTypeIds($reqNoteIds, $model->cli_id);
				}
				if ($generalNote) {
					$generalNote['not_type'] = Note::TYPE_CLIENT_GENERAL;
					Note::model()->saveNote($generalNote, $model->cli_id);
				}
				if ($reqNote) {
					$reqNote['not_type'] = Note::TYPE_CLIENT_REQ;
					Note::model()->saveNote($reqNote, $model->cli_id);
				}

				if (isset($_POST['Client']['saveProceed']) && $_POST['Client']['saveProceed']) {
					$this->redirect([$this->createUrl('appointmentBuilder/clientSelected', ['clientId' => $model->cli_id])]);
				} else {
					Yii::app()->user->setFlash('client-update-success', 'Updated Successfully');
					$this->redirect(array(
											'Update',
											'id'        => $model->cli_id,
											'useClient' => (isset($_GET['useClient']) && $_GET['useClient'] ? 1 : 0)
									));
				}
			}
		}
	}

	public function actionUpdate($id)
	{

		$model = Client::model()->findByPk($id);
		$this->saveClient($model);
		$this->render('update', ['model' => $model]);
	}

	private function prepareModel(Client $model)
	{

		$model->cli_salebed = $model->cli_salebed ? : '';
		$model->cli_letbed  = $model->cli_letbed ? : '';
		if (!isset($model->notes[0])) {
			$model->notes = [new Note()];
		}
	}

	public function actionGetSourceType()
	{

		$result = [];
		if (isset($_GET['parentType']) && $_GET['parentType']) {
			$parentType = $_GET['parentType'];
			$sources    = Source::model()->getTypes($parentType);
			foreach ($sources as $source) {
				/** @var $source Source [ ] */
				$result[$source->sou_id] = $source->sou_title;
			}
		}
		echo json_encode($result);
	}

	public function actionInfo($id, $format = 'JSON')
	{

		/** @var $client Client */
		$client = Client::model()->findByPk($id);

		if (!$client) {
			echo json_encode(array());
			return true;
		}
		$data = $client->attributes;

		$data['fullName'] = $client->getFullName();

		echo json_encode($data);

	}

	public function actionClientsWithoutAddress()
	{

		$this->layout = '//layouts/adminDefault';

		$criteria = new CDbCriteria();
		$criteria->compare('addressID', 0);

		$dataProvider = new CActiveDataProvider('Client', CMap::mergeArray(array(
																				   'criteria' => $criteria,
																		   ), Yii::app()->params['CActiveDataProvider']));

		$this->render('clientsWithoutAddress', ['dataProvider' => $dataProvider]);

	}

	public function actionNewlyRegistered()
	{

		$model = new Client('search');
		$this->render('newlyRegistered', ['model' => $model]);
	}

	public function actionRegisteredStatistics()
	{

		$model = new RegisteredStatistics('search');

		if (!$model->startDate) {
			$model->startDate = date("d/m/Y", strtotime(date("Y-m-d") . " -1 year"));
		}
		$model->granulation = RegisteredStatistics::GRANULARITY_WEEK;
		$this->render('registeredStatistics', ['model' => $model]);

	}

	public function actionMatch($instructionId = 0)
	{

		$instructionModel = Deal::model()->findByPk($instructionId);
		if (!$instructionModel) {
			$instructionModel = new Deal();
		}
		$instructionModel->setScenario('search');
		$clientModel = new Client('search');
		$this->render('match', array(
				'instructionModel' => $instructionModel,
				'clientModel'      => $clientModel,
		));
	}

	public function getStatusIcon($value)
	{

		return ($value ? CHtml::image(Icon::GREEN_TICK_ICON) : CHtml::image(Icon::GRAY_CROSS_ICON));
	}

	public function actionAutocomplete($search)
	{

		$model = new Client('search');
		$model->setFullName($search);
		/** @var $data Client[] */
		$data   = $model->search()->getData();
		$result = [];
		foreach ($data as $value) {
			$result[] = [
					'label' => $value->getFullName() . ($value->cli_email ? '(' . $value->cli_email . ')' : ''),
					'value' => $value->getFullName(),
					'id'    => $value->cli_id
			];
		}

		echo json_encode($result);

	}

	public function actionSaveLastContacted()
	{

		$clientIds = isset($_GET['clientIds']) && $_GET['clientIds'] ? $_GET['clientIds'] : '';
		if (isset($_GET['contactDate']) && $clientIds) {
			$contactDate  = $_GET['contactDate'] ? Date::formatDate('Y-m-d H:i:s', $_GET['contactDate']) : "null";
			$clientUpdate = Client::model()->updateAll(
								  ['lastContacted' => $contactDate],
								  "cli_id in (" . $clientIds . ")"
			);
			if ($clientUpdate) {
				echo Date::formatDate('d/m/Y', $contactDate);
			}
		}
	}

	public function actionDetailSearch($instructionId = 0)
	{

		$model                      = new Client('search');
		$model->cli_created         = '';
		$model->searchNoBudget      = true;
		$model->searchNoMinimumBeds = true;
		$model->cli_saleemail       = Client::EMAIL_SALES_YES;
		$model->telephones          = array(new Telephone('search'));

		$clientAttributes = isset($_GET['Client']) && $_GET['Client'] ? $_GET['Client'] : [];
		$minPrices        = Util::getPropertyPrices("minimum");
		$maxPrices        = Util::getPropertyPrices("maximum");

		$title = '';
		if (!$clientAttributes && $instructionId) {
			/** @var Deal $instructionModel */
			$instructionModel = Deal::model()->with('address')->findByPk($instructionId);

			if ($address = $instructionModel->address) {
				$title = $address->getFullAddressString(', ') . " (" . Locale::formatCurrency($instructionModel->getPrice()) . ")";
				if ($address->postcode) {
					$thisPostcodeFirst = $address->getPostcodePart();
					$postcodeList      = LinkOfficeToPostcode::model()->getPostcodeList();
					if (in_array($thisPostcodeFirst, $postcodeList)) {
						$model->searchPostcodes[] = $thisPostcodeFirst;
					}
				}
			}

			$model->PropertyTypesIds = [$instructionModel->dea_ptype, $instructionModel->dea_psubtype];

			$model->cli_salebed = $instructionModel->dea_bedroom ? $instructionModel->dea_bedroom : '';
			$categories = [];
			foreach ($instructionModel->propertyCategories as $category) {
				$categories = $category->id;
			}
			$model->setPropertyCategoryIds($categories);

			/**
			 * round is required because numbers with floating point cannot be used as array keys
			 */
			$model->minPrice = (int)round($instructionModel->dea_marketprice - $instructionModel->dea_marketprice * Yii::app()->params['mailshot']['price_margin_min']);
			$model->maxPrice = (int)round($instructionModel->dea_marketprice + $instructionModel->dea_marketprice * Yii::app()->params['mailshot']['price_margin_max']);
			$minPrices[$model->minPrice] = Locale::formatCurrency($model->minPrice, true, false);
			$maxPrices[$model->maxPrice] = Locale::formatCurrency($model->maxPrice, true, false);
			ksort($minPrices);
			ksort($maxPrices);
		}
		if ($clientAttributes) {
			$model->attributes = $clientAttributes;
		}

		$this->render('detailSearch', compact('model', 'title', 'minPrices', 'maxPrices'));
	}

	public function actionTextConversation($clientId)
	{

		$this->layout  = '//layouts/fixed';
		$highlightText = null;
		if (isset($_GET['messageId']) && $_GET['messageId']) {
			$highlightText = Sms::model()->findByPk($_GET['messageId']);
			if (!$highlightText) {
				throw new CHttpException(404, 'message [id:' . $_GET['messageId'] . '] not found');
			}
			if (!$highlightText->isRead()) {
				$highlightText->markRead(Yii::app()->user->id);
			}
		}

		$client = Client::model()->findByPk($clientId);

		if (!$client) {
			throw new CHttpException(404, 'client [id : ' . $clientId . '] is not found');
		}
		$this->render('textConversation', array('client' => $client, 'highlightText' => $highlightText));
	}

	/**
	 * This got messy
	 *
	 * @param $instructionId
	 * @throws CHttpException
	 */
	public function actionSendMailshot($instructionId)
	{
		$model = new Client('search');

		if (isset($_POST['send']) && $_POST['send']) {

			$instruction       = Deal::model()->findByPk($instructionId);
			$type              = MailshotType::model()->findByPk($_POST['MailshotType']);
			$model->attributes = $_GET['Client'];

			$dataProvider = $model->search();
			$dataProvider->setPagination(['pageSize' => 100]);
			if (!$type) {
				throw new CHttpException(404, 'Mailshot Type [name: ' . $_POST['MailshotType'] . '] was not found');
			}
			if (!$instruction) {
				throw new CHttpException(404, 'Instruction [id: ' . $instructionId . '] was not found');
			}
			$mailshot                = new MandrillMailshot();
			$mailshot->instructionId = $instruction->dea_id;
			$mailshot->type          = $type->name;
			$mailshot->save();

			if ($type->templatePath && file_exists($type->templatePath)) {
				ob_start();
				include $type->templatePath;
				$htmlTemplate = ob_get_clean();
			} else {
				$htmlTemplate = $this->execTemplate($type->htmlTemplate);
			}
			$textTemplate = $this->execTemplate($type->textTemplate);

			$mandrillMessagePreset = new MandrillMessage();

			$mandrillMessagePreset->setFrom(Yii::app()->params['mailshot']['sender_email'], Yii::app()->params['mailshot']['sender_name']);
			$mandrillMessagePreset->setHtmlBody($htmlTemplate);
			$mandrillMessagePreset->setTextBody($textTemplate);
			$mandrillMessagePreset->setSubject($type->subject);
			$mandrillMessagePreset->setPreserveRecepients(false);
			$mandrillMessagePreset->setGlobalMergeVar('MAILSHOT_ID', $mailshot->id);
			$mandrillMessagePreset->setGlobalMergeVar('INSTRUCTION_ID', $instruction->dea_id);
			$mandrillMessagePreset->setGlobalMergeVar('INSTRUCTION_PRICE', Locale::formatCurrency($instruction->dea_marketprice));
			$mandrillMessagePreset->setGlobalMergeVar('INSTRUCTION_TITLE', $instruction->title);
			$mandrillMessagePreset->setGlobalMergeVar('INSTRUCTION_STRAPLINE', $instruction->dea_strapline);
			$mandrillMessagePreset->setGlobalMergeVar('OFFICE_TITLE', $instruction->branch->office->title);
			$mandrillMessagePreset->setGlobalMergeVar('OFFICE_NUMBER', $instruction->branch->bra_tel);

			if (isset($_POST['test']) && $_POST['test']) {
				$mandrillMessagePreset->enableTest();
			}

			$iterator = new DataProviderIterator($dataProvider);

			/** @var $mandrillMessage MandrillMessage */
			$mandrillMessage = null;
			$x               = 0;
			$staffEmails     = array_fill_keys(Yii::app()->params['mailshot']['alwaysSendTo'], '?');

			if (Yii::app()->params['mandrill']['test_run']) {
				$testEmails = Yii::app()->params['mandrill']['test_emails'];
			}
			/** @var $client Client */
			foreach ($iterator as $client) {
				$email = $client->cli_email;

				if (isset($testEmails)) {
					if (!$testEmails) break;
					$email = array_shift($testEmails);
				}

				if (!$email) continue;

				//we are sending emails to our staff in a different message
				if (array_key_exists($email, $staffEmails)) continue;

				if ($x % Yii::app()->params['mandrill']['mails_in_message'] === 0) {
					if ($mandrillMessage) {
						$mandrillMessage->send();
						$mailshot->addMessage($mandrillMessage);
					}
					$mandrillMessage = clone $mandrillMessagePreset;
				}
				$mandrillMessage->addTo($email, $client->getFullName());
				$mandrillMessage->setRecepientMergeVar($email, 'CLIENT_FULLNAME', $client->getFullName());
				$mandrillMessage->setRecepientMergeVar($email, 'CLIENT_SALUTATION', $client->cli_salutation);
				$mandrillMessage->setRecepientMergeVar($email, 'CLIENT_ID', $client->cli_id);
				$mandrillMessage->setRecepientMergeVar($email, 'CLIENT_EMAIL', $email);
				$mandrillMessage->setRecepientMetaData($email, 'clientId', $client->cli_id);
				$x++;
			}
			if (!$mandrillMessage->id) {
				$mandrillMessage->send();
				$mailshot->addMessage($mandrillMessage);
			}

			/**
			 * doing a trick. will send an email to everyone in staff list to avoid complains
			 */
			$staffMessage = clone $mandrillMessagePreset;
			$clients      = Client::model()->findAll('cli_email IN (' . implode(', ', $staffEmails) . ')', array_keys($staffEmails));
			foreach ($clients as $client) {
				$email = $client->cli_email;
				$staffMessage->addTo($email, $client->getFullName());
				$staffMessage->setRecepientMergeVar($email, 'CLIENT_FULLNAME', $client->getFullName());
				$staffMessage->setRecepientMergeVar($email, 'CLIENT_SALUTATION', $client->cli_salutation);
				$staffMessage->setRecepientMergeVar($email, 'CLIENT_ID', $client->cli_id);
				$staffMessage->setRecepientMergeVar($email, 'CLIENT_EMAIL', $email);
				$staffMessage->setRecepientMetaData($email, 'clientId', $client->cli_id);
			}
			$staffMessage->send();
			$mailshot->addMessage($staffMessage);

			Yii::app()->user->setFlash('mailshot-sent', 'Mailshot successfully sent');
			$this->redirect(['instruction/summary', 'id' => $instructionId]);
		}
		$this->render('sendMailshot');
	}

	/**
	 * PLEASE BE EXTREMELY CAREFUL WITH THIS METHOD
	 * @param $str
	 * @return mixed
	 */
	private function execTemplate($str)
	{

		$str = '?>' . $str;
		ob_start();
		eval($str);
		return ob_get_clean();
	}

	public function getBudgetValues(Client $client = null)
	{

		$prices = array_merge([125000, 150000], range(200000, 500000, 50000), range(600000, 1000000, 100 * 1000), range(1250000, 2000000, 250000), range(2500000, 6000000, 500000));
		$prices = array_merge($prices, range(75000, 150000, 25000), range(200000, 500000, 50000), range(600000, 1000000, 100 * 1000));
		if ($client && $client->budget) {
			$prices = array_merge($prices, [$client->budget]);
		}
		$prices = array_combine($prices, Locale::formatMoneyArray($prices));
		ksort($prices);
		return $prices;
	}

}
