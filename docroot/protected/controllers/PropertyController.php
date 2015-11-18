<?php

class PropertyController extends PublicController
{
	/**
	 * @var string
	 */
	public $layout = "//layouts/default";

	/**
	 * @param $id
	 * @param $criteria
	 * @return CActiveRecord
	 * @throws CHttpException
	 */
	public function loadModel($id, $criteria = false)
	{

		if (!$id) {
			throw new CHttpException(404, 'Property not found');
		}

		$criteria       = $criteria ? : new CDbCriteria();
		$previewSession = new CHttpSession();
		$previewSession->open();
		$previewString = (isset($previewSession['preview']) && $previewSession['preview']) ? $previewSession['preview'] : null;
		$previewString = (isset($_GET['preview']) && $_GET['preview']) ? $_GET['preview'] : $previewString;

		if (Yii::app()->user->isGuest) {
			$criteria->scopes = ['notUnderTheRadar'];
			if ($previewString) {
				$criteria->compare('emailLinkString', $previewString);
			} else {
				$criteria->scopes = ['publicAvailable', 'notUnderTheRadar'];
			}
		}

		$model = Deal::model()->findByPk($id, $criteria);
		if (!$model) {
			throw new CHttpException(404, 'Property not found');
		} else {
			$previewSession['preview'] = $previewString;
			$previewSession->close();
		}

		return $model;
	}

	/**
	 * @param string $type
	 */
	public function actionIndex($type = 'sales')
	{

		$model           = new Deal('search');
		$model->property = new Property('search');
		$criteria        = new CDbCriteria();

		$criteria->scopes     = ['publicAvailable'];
		$_GET['Deal']['sort'] = isset($_GET['Deal']['sort']) && $_GET['Deal']['sort'] ? $_GET['Deal']['sort'] : 'price DESC';
		if (isset($_GET['studio'])) {
			$_GET['Deal']['min_bedrooms'] = 0;
			$_GET['Deal']['max_bedrooms'] = 0;
			$_GET['Deal']['dea_ptype']    = 2;
		}

		if (isset($_GET['Deal'])) {
			$model->attributes = $_GET['Deal'];

			// =================================================================================
			// <<< Reconsider that part of code
			if (isset($_GET['Deal']['priceMode']) && $_GET['Deal']['priceMode'] == 'pcm') {
				$criteria->compare('dea_marketprice * 4.3 * 1.1', '>=' . (isset($_GET['Deal']['min_price']) ? $_GET['Deal']['min_price'] : ""));
				$criteria->compare('dea_marketprice * 4.3 * 0.9', '<=' . (isset($_GET['Deal']['max_price']) ? $_GET['Deal']['max_price'] : ""));
			} else {
				$criteria->compare('dea_marketprice * 1.1', '>=' . (isset($_GET['Deal']['min_price']) ? $_GET['Deal']['min_price'] : ""));
				$criteria->compare('dea_marketprice * 0.9', '<=' . (isset($_GET['Deal']['max_price']) ? $_GET['Deal']['max_price'] : ""));
			}

			$criteria->compare('dea_bedroom', '>=' . (isset($_GET['Deal']['min_bedrooms']) ? $_GET['Deal']['min_bedrooms'] : ""));
			$criteria->compare('dea_bedroom', '<=' . (isset($_GET['Deal']['max_bedrooms']) ? $_GET['Deal']['max_bedrooms'] : ""));
			$criteria->compare('dea_ptype', (isset($_GET['Deal']['dea_ptype']) ? $_GET['Deal']['dea_ptype'] : ""));
			$criteria->compare('dea_psubtype', (isset($_GET['Deal']['dea_ptype']) ? $_GET['Deal']['dea_ptype'] : ""), false, 'OR');
			$criteria->compare('dea_branch', (isset($_GET['Deal']['dea_branch']) ? $_GET['Deal']['dea_branch'] : ""));

			list($field, $order) = explode(" ", $_GET['Deal']['sort']);
			$order      = $order == 'ASC' ? $order : 'DESC';
			$field      = in_array($field, ['price', 'date']) ? $field : 'price';
			$orderArray = ['price' => 'dea_marketprice', 'date' => 'dea_launchdate'];

			// Reconsider that part of code >>>
			// =================================================================================
			$criteria->order = $orderArray[$field] . ' ' . $order;

			if (isset($_GET['Deal']['showMode']) && $_GET['Deal']['showMode'] == 'available') {
				$criteria->scopes = ['available'];
			}

		}

		if (isset($_GET['Property']['fullAddressString'])) {
			$criteria->with = ['property', 'property.address', 'property.area'];
			if (isset($model->property->fullAddressString)) {
				$model->property->fullAddressString = $_GET['Property']['fullAddressString'];
			} else {
				$model->property->setFullAddressString($_GET['Property']['fullAddressString']);
			}

			$addressParts    = explode(' ', str_replace(",", "", $_GET['Property']['fullAddressString']));
			$addressCriteria = $areaCriteria = [];
			foreach ($addressParts as $key => $part) {
				$addressCriteria[] = "concat_ws(' ',address.line1,address.line2,address.line3,address.line4,address.line5, address.postcode) LIKE :part" . $key;
				$areaCriteria[]    = "area.are_title LIKE :part" . $key . "";

				$criteria->params[':part' . $key] = '%' . $part . '%';
			}
			$criteria->addCondition('((' . implode(') AND (', $addressCriteria) . ')) OR ((' . implode(') AND (', $areaCriteria) . '))');

		}

		$criteria->compare('dea_type', $type);

		array_push($criteria->scopes, "notUnderTheRadar");

		$dataProvider = $model->publicSearch($criteria);

		$this->render('index',
					  [
							  'dataProvider' => $dataProvider,
							  'type'         => $type,
							  'model'        => $model,
							  'isMobile'     => Yii::app()->device->isDevice('mobile')
					  ]
		);
	}

	/**
	 *
	 */
	public function actionTopProperties()
	{

		$this->render('topProperties', array(
											 'instructions' => Deal::model()->getMostViewed(20, date("Y-m-d H:i:s", strtotime('-1 week')), 'sales'),
											 'isMobile'     => Yii::app()->device->isDevice('mobile')
									 )
		);
	}

	public function actionPropertyCategory($id)
	{

		if (!$id) {
			throw new CHttpException(404, 'Property Category not found');
		}
		$propCatModel = PropertyCategory::model()->active()->findByPk($id);
		if (!$propCatModel) {
			throw new CHttpException(404, 'Property Category not found');
		}
		$this->render('propertyCategory', array(
												'instructions' => Deal::model()->getCategorized($id, 'sales', null),
												'propCatModel' => $propCatModel
										)
		);
	}

	/**
	 * @param $id
	 */
	public function actionView($id)
	{

		$preview = false;

		$model = $this->loadModel($id);
		$this->countPropertyHit($id);
		$this->substituteStatus($model);
		$area = 0;
		foreach ($model->floorplans as $floorplan) {
			if ($floorplan->med_dims) {
				$area += $floorplan->med_dims;
			}
		}

		/** @var  $device \Device */
		$device    = Yii::app()->device;
		$view        = $device->isDevice('mobile') ? 'mobileDetailsView' : 'detailsView';
		$smallDevice = $device->isDevice('smallDevice');

		$this->render($view, array(
				'model'       => $model,
				'area'        => $area,
				'title'       => $model->property->getShortAddressString(', '),
				'price'       => Locale::formatPrice($model->dea_marketprice, $model->dea_type == 'Sales' ? false : true),
				'smallDevice' => $smallDevice
		));
	}

	public function actionGallery($id, $photoId = 0)
	{

		$this->layout = '//layouts/small-device-iframe';
		$model        = $this->loadModel($id);
		$price        = Locale::formatPrice($model->dea_marketprice, $model->dea_type == 'Sales' ? false : true);

		$this->render('gallery', array(
				'model'          => $model,
				'title'          => $model->property->getShortAddressString(', ') . ' - ' . $price,
				'currentPhotoId' => $photoId
		));
	}

	/**
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionPdf($id)
	{

		Yii::import('application.extensions.less.*');

		$lessc = new lessc();
		$lessc->checkedCompile(Yii::getPathOfAlias('webroot.less') . '/' . 'html2pdf.less', Yii::getPathOfAlias('webroot.css') . '/' . 'html2pdf.css');

		$model = $this->loadModel($id);

		if (!$model) {
			throw new CHttpException(404, 'Property not found');
		}
		$settings = InstructionToPdfSettings::model()->findByAttributes(['instructionId' => $id]);

		if (!$settings) {
			$settings = new InstructionToPdfSettings();
		}

		$pdf = new WKPDF();
		$pdf->setMargins(['top' => '40']);
		$pdf->addResource('css', Yii::getPathOfAlias('webroot.css') . '/' . 'html2pdf.css');
		$cssFiles = ['html2pdf.css'];

		/** @var $browser Browser */
		$browser = Yii::app()->browser;
		if ($browser->getBrowser() == Browser::BROWSER_FIREFOX && $browser->getVersion() >= 19) {
			$pdf->addResource('css', Yii::getPathOfAlias('webroot.css') . '/' . 'helvetica_html2pdf.css');
			$cssFiles[] = 'helvetica_html2pdf.css';
		}

		$pdf->set_html($this->renderPartial('instructionToPDF', [
				'model'    => $model,
				'settings' => $settings,
				'pdf'      => $pdf,
				'cssFiles' => $cssFiles,
		], true));
		$pdf->set_htmlHeader($this->renderPartial('instructionToPDF/header', [
				'model'    => $model,
				'settings' => $settings,
				'pdf'      => $pdf,
				'cssFiles' => $cssFiles,
		], true));
		$pdf->set_htmlFooter($this->renderPartial('instructionToPDF/footer', [
				'model'    => $model,
				'settings' => $settings,
				'pdf'      => $pdf,
				'cssFiles' => $cssFiles,
				'offices'  => Office::model()->active()->findAll(),
		], true));
		$pdf->set_orientation(WKPDF::$PDF_PORTRAIT);
		$pdf->render();
		$pdf->output(WKPDF::$PDF_EMBEDDED, null);
	}

	/**
	 * @param $id
	 */
	public function actionInfoBox($id)
	{

		$model = $this->loadModel($id);
		$this->renderPartial('infoBox', array(
											  'model'         => $model,
											  'title'         => $model->property->getShortAddressString(),
											  'price'         => Locale::formatPrice($model->dea_marketprice, $model->dea_type == 'Sales' ? false : true),
											  'detailPageUrl' => $this->detailPage($id)
									  )
		);
	}

	/**
	 * @param        $id
	 * @param string $mode
	 * @throws CHttpException
	 */
	public function actionShowMap($id, $mode = 'map')
	{

		$this->layout = '//layouts/popup-iframe';
		$instruction  = $this->loadModel($id);

		if (!$instruction->property->getLat() || !$instruction->property->getLng()) {
			throw new CHttpException(404, 'Property map not defined');
		}
		$properties = Deal::model()->publicAvailable()->notUnderTheRadar()->with('property')->findAll();

		$this->render("//MapView/default", array(
				'id'               => $id,
				'latitude'         => $instruction->property->getLat(),
				'longitude'        => $instruction->property->getLng(),
				'type'             => 'instruction',
				'mode'             => $mode,
				'mapDim'           => ['w' => '80%', 'h' => ''],
				'properties'       => $properties,
				'nearestTransport' => true,
		));
	}

	/**
	 * @param $status
	 * @param $type
	 * @return mixed
	 */
	public function getStatusString($status, $type)
	{

		$statusStringsSales    = array(
				Deal::STATUS_UNDER_OFFER            => 'Under Offer',
				Deal::STATUS_UNDER_OFFER_WITH_OTHER => 'Under Offer',
				Deal::STATUS_EXCHANGED              => 'Sold',
				Deal::STATUS_COMPLETED              => 'Sold',
				Deal::STATUS_AVAILABLE              => 'For Sale',
		);
		$statusStringsLettings = [
				'Under Offer' => 'Under Offer', 'Under Offer With Other' => 'Under Offer', 'Exchanged' => 'Under Offer',
				'Available'   => 'To Let'
		];

		if (strtolower($type) == 'sales') {
			return (isset($statusStringsSales[$status]) ? $statusStringsSales[$status] : $statusStringsSales['Under Offer']);
		} else {
			return (isset($statusStringsLettings[$status]) ? $statusStringsLettings[$status] : $statusStringsLettings['Under Offer']);
		}
	}

	/**
	 * @param $id
	 */
	public function actionSendToFriend($id)
	{

		$this->layout = '/layouts/popup-iframe';
		$message      = null;
		$renderEmail  = null;
		$errorMessage = null;
		$model        = new Deal();

		if ($id) {
			$model = $this->loadModel($id);
			$price = Locale::formatPrice($model->dea_marketprice, $model->dea_type == 'Sales' ? false : true);
			if ($model->dea_type == 'Lettings') {
				$price .= ' - ' . Locale::formatPrice($model->getPrice('pcm'), true, true);
			}

			$fullUrl = 'http://' . Yii::app()->request->getServerName() . '/details/' . $model->dea_id . '.html';

			$renderEmail = $model->dea_strapline . "\n" . $model->property->getShortAddressString(', ', true) . ' - ' . $price;
			$renderEmail .= "\n" . '' . $fullUrl;
		}

		if (isset($_POST['SendToFriend']) && $_POST['SendToFriend']) {
			$name = 'SendToFriend';

			$errors = array();
			if (!$_POST[$name]['friendEmail'] || !filter_var($_POST[$name]['friendEmail'], FILTER_VALIDATE_EMAIL)) {
				$errors[$name]['friendEmail'] = 'Your Friend\'s valid email address';
			}

			if ($errors) {
				$errorMessage = 'In order for us to deal with your enquiry please provide the following info...
					<ul><li>' . implode("</li><li>", $errors[$name]) . "</li></ul>";

			} else {
				try {
					include_once("Zend/Mail.php");

					$contactEmail = strip_tags($_POST[$name]['email']);
					if (!isset($contactEmail)) {
						$contactEmail = 'post@woosterstock.co.uk';
					}
					$friendEmail = strip_tags($_POST[$name]['friendEmail']);

					$fullMessage = "From:\t" . $contactEmail . "\n\n";
					$fullMessage .= "I'm visiting the Wooster and Stock Web Site and I thought this property might be of interest to you:\n\n";
					$fullMessage .= str_replace('&pound;', 'GBP ', $renderEmail) . "\n\n";
					if (isset($_POST[$name]['comment'])) {
						$fullMessage .= $_POST[$name]['comment'] . "\n\n";
					}
					$fullMessage .= "Sent:\t" . date("d/m/Y H:i");

					$mailToFriend = new Zend_Mail("UTF-8");
					$mailToFriend->addTo($friendEmail);
					$mailToFriend->setFrom($contactEmail);
					$mailToFriend->setSubject("Web Site recommendation from your friend or colleague");
					$mailToFriend->setBodyText($fullMessage);
					$mailToFriend->send();
					$message = 'Your message has been sent, thank you';

				} catch (Exception $e) {
					$errorMessage = 'Error!! Your message has not been sent';
				}
				unset($_POST[$name]);
			}

		}
		$this->render('detailsView/sendToFriend', [
				'model' => $model, 'message' => $message, 'errorMessage' => $errorMessage
		]);
	}

	public function actionFloorplans($id)
	{
		$model = $this->loadModel($id);
		$area  = 0;
		foreach ($model->floorplans as $floorplan) {
			if ($floorplan->med_dims) {
				$area += $floorplan->med_dims;
			}
		}

		$this->render('detailsView/floorplans', array(
				'model' => $model,
				'area'  => $area,
		));
	}

	/**
	 * @return string
	 */
	public function listingPage()
	{

		return '/property';
	}

	/**
	 * @param $id
	 * @return string
	 */
	public function detailPage($id)
	{

		return '/details/' . $id;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	private function countPropertyHit($id)
	{

		if (isset($_SESSION['visitedProperty'][$id])) {
			return true;
		}
		$sql = "REPLACE INTO propertyviews SET dea_id = '" . $id . "',
		session = '" . session_id() . "',
		ip = '" . $_SERVER['REMOTE_ADDR'] . "',
		datetime = '" . date('Y-m-d H:i:s') . "'";
		Yii::app()->db->createCommand($sql)->execute();

		$_SESSION['visitedProperty'][$id] = $id;

		return true;
	}

	/**
	 * @param Deal $instruction
	 */
	public function substituteStatus(Deal $instruction)
	{

		if (!$instruction->statusCompare(Deal::STATUS_EXCHANGED) || !$instruction->statusCompare(Deal::STATUS_SOLD_BY_OTHER) || !$instruction->statusCompare(Deal::STATUS_COMPLETED)) {
			$instruction->dea_status = Deal::STATUS_EXCHANGED;
		} elseif ($instruction->statusCompare(Deal::STATUS_UNDER_OFFER) >= 0) {
			$instruction->dea_status = Deal::STATUS_UNDER_OFFER;
		} elseif ($instruction->statusCompare(Deal::STATUS_AVAILABLE) <= 0) {
			$instruction->dea_status = Deal::STATUS_AVAILABLE;
		}
	}

	public function getPriceWithQualifier(Deal $model)
	{

		$price = Locale::formatPrice($model->dea_marketprice);
		if ($model->dea_qualifier === Deal::QUALIFIER_POA) {
			$price = "POA" . ($model->dea_tenure ? ',' : '');
		} elseif ($model->dea_qualifier !== Deal::QUALIFIER_NONE) {
			$price .= ' ' . $model->dea_qualifier . ($model->dea_tenure ? ',' : '');
		}
		$price .= ($model->dea_tenure ? " " . $model->dea_tenure : "");
		return $price;
	}

}
