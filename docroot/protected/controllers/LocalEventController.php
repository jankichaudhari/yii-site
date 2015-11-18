<?php

class LocalEventController extends PublicController
{
	/**
	 * @var string
	 */
	public $layout = "/layouts/default";

	/**
	 * @param $id
	 * @return CActiveRecord
	 * @throws CHttpException
	 */
	public function loadModel($id, $c = false)
	{

		if (!$id) {
			throw new CHttpException(404, 'Event not found');
		}

		$criteria = $c ? $c : new CDbCriteria();
		if (is_numeric($id)) {
			$model = LocalEvent::model()->findByPk($id, $criteria);
		} else {
			$model = LocalEvent::model()->findByAttributes(['linkId' => $id], $criteria);
		}

		if (!$model) {
			throw new CHttpException(404, 'Event not found');
		}

		return $model;
	}

	/**
	 *
	 */
	public function actionIndex()
	{

		$dataProvider = new CActiveDataProvider('LocalEvent', array(
				'criteria' => array(
						'order'  => 'dateFrom',
						'scopes' => ['onlyActive', 'published']
				)
		));
		$this->render("index", array('dataProvider' => $dataProvider));
	}

	/**
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionView($id)
	{

		$criteria = new CDbCriteria();
		if (Yii::app()->user->isGuest) {
			$criteria->scopes = ['onlyActive', 'published'];
		}
		$model = $this->loadModel($id, $criteria);

		if (isset($_POST['LocalEvent']) && $_POST['LocalEvent']) {
			unset($model->attributes);
			$model->attributes = $_POST['LocalEvent'];
			if (isset($_POST['Location'])) {
				if (!$model->address) {
					$model->address = new Location();
				}
				$model->address->attributes = $_POST['Location'];
			}
		}

		/** @var  $device \Device */
		$device    = Yii::app()->device;
		$view        = $device->isDevice('mobile') ? 'mobileDetailsView' : 'detailsView';
		$smallDevice = $device->isDevice('smallDevice');

		$this->render($view, [
								   'model'       => $model,
								   'smallDevice' => $smallDevice
						   ]
		);
	}

	/**
	 * @param     $id
	 * @param int $photoId
	 */
	public function actionGallery($id, $photoId = 0)
	{

		$this->layout = '//layouts/small-device-iframe';

		$criteria         = new CDbCriteria();
		$criteria->scopes = ['onlyActive', 'published'];
		$model            = $this->loadModel($id, $criteria);

		$this->render('gallery', array(
				'model'          => $model,
				'title'          => $model->heading,
				'currentPhotoId' => $photoId
		));
	}

	/**
	 * @param $id
	 */
	public function actionInfoBox($id)
	{

		$model = $this->loadModel($id);
		$this->renderPartial('infoBox', array(
											  'model'         => $model,
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
		$localEvent   = $this->loadModel($id);

		if (!$localEvent->address->latitude || !$localEvent->address->longitude) {
			throw new CHttpException(404, 'Event map not defined');
		}

		$criteria = new CDbCriteria();
//		$criteria->condition = "dateTo >= '" . date("Y-m-d") . "' OR (dateTo is NULL AND dateFrom >= '" . date("Y-m-d") . "')";
		$criteria->order  = 'dateFrom';
		$criteria->scopes = ['onlyActive', 'published'];
		$localEvents      = LocalEvent::model()->findAll($criteria);

		$properties = Deal::model()->publicAvailable()->notUnderTheRadar()->with('property')->findAll();

		$this->render("//MapView/default", array(
				'id'               => $id,
				'latitude'         => $localEvent->address->latitude,
				'longitude'        => $localEvent->address->longitude,
				'type'             => 'localEvent',
				'mode'             => $mode,
				'mapDim'           => ['w' => '80%', 'h' => ''],
				'properties'       => $properties,
				'localEvents'      => $localEvents,
				'nearestTransport' => true,
		));
	}

	/**
	 * @return string
	 */
	public function listingPage()
	{

		return '/local-events';
	}

	/**
	 * @param $id
	 * @return string
	 */
	public function detailPage($id)
	{

		return '/local-event/' . $id;
	}

}
