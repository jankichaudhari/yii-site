<?php

class ParkController extends PublicController
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
	public function loadModel($id, $criteria = false)
	{

		if (!$id) {
			throw new CHttpException(404, 'Park not found');
		}
		if (is_numeric($id)) {
			$model = Place::model()->findByPk($id, $criteria);
		} else {
			$model = Place::model()->findByAttributes(['title' => urldecode($id)], $criteria);
		}
		return $model;
	}

	/**
	 *
	 */
	public function actionIndex()
	{

		$viewTypes = array('gallery', 'list', 'map');
		$view      = isset($_GET['view']) && in_array($_GET['view'], $viewTypes) ? $_GET['view'] : 'gallery';

		/** @var  $detector \Device */
		$mobile   = Yii::app()->device->isDevice('mobile');
		if ($mobile) {
			$view = 'list';
		}

		$model = new Place('search');

		if (isset($_GET['Place'])) {
			$criteria = null;
			if (isset($_GET['Place']['sortField']) && $_GET['Place']['sortField']) {
				$criteria        = new CDbCriteria();
				$criteria->order = $_GET['Place']['sortField'];
			}

			$model->attributes = $_GET['Place'];
			$model->statusId   = 3;
			$model->address    = $_GET['Place']['title'];
			$dataProvider      = $model->search($criteria);
		} else {
			$dataProvider = new CActiveDataProvider('Place', array(
					'pagination' => array('pageSize' => 18),
					'criteria'   => array(
							'scopes' => 'onlyActive'
					)
			));
		}

		$instructions = Deal::model()->available()->notUnderTheRadar()->findAll();
		$parks        = Place::model()->findAll(['scopes' => 'onlyActive']);

		$this->render("index", array(
				'dataProvider' => $dataProvider,
				'view'         => $view,
				'model'        => $model,
				'instructions' => $instructions,
				'parks'        => $parks
		));
	}

	/**
	 * @param      $id
	 * @param null $model
	 */
	public function actionView($id, $model = null)
	{

		$criteria = new CDbCriteria();
		if (Yii::app()->user->isGuest) {
			$criteria->scopes = ['onlyActive'];
		}
		$model = $this->loadModel($id, $criteria);

		if (isset($_POST['Place']) && $_POST['Place']) {
			unset($model->attributes);
			$model->attributes = $_POST['Place'];
			if (isset($_POST['Location'])) {
				if (!$model->location) {
					$model->location = new Location();
				}
				$model->location->attributes = $_POST['Location'];
			}
		}

		$instructions = Deal::model()->available()->notUnderTheRadar()->findAll();
		$allParks     = Place::model()->findAll(['scopes' => 'onlyActive']);

		/** @var  $device \Device */
		$device    = Yii::app()->device;
		$view        = $device->isDevice('mobile') ? 'mobileDetailsView' : 'detailsView';
		$smallDevice = $device->isDevice('smallDevice');

		$this->render(
			 $view,
			 [
					 'model'        => $model,
					 'instructions' => $instructions,
					 'allParks'     => $allParks,
					 'title'        => $model->title . ($model->location->postcode ? ' , ' . $model->location->postcode : ''),
					 'smallDevice'  => $smallDevice
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

		$model = $this->loadModel($id);
		$title = $model->title;

		$this->render('gallery', array(
				'model'          => $model,
				'title'          => $title,
				'currentPhotoId' => $photoId
		));
	}

	/**
	 * @param $id
	 */
	public function actionInfoBox($id)
	{

		$data = $this->loadModel($id);
		$this->renderPartial('infoBox', array(
				'data'          => $data,
				'detailPageUrl' => $this->detailPage($id)
		));
	}

	/**
	 * @param        $id
	 * @param string $mode
	 * @throws CHttpException
	 */
	public function actionShowMap($id, $mode = 'map')
	{

		$this->layout = '//layouts/popup-iframe';
		$park         = $this->loadModel($id);
		if (!$park->location->latitude || !$park->location->longitude) {
			throw new CHttpException(404, 'Park  map not defined');
		}
		$parks = Place::model()->findAll(['scopes' => 'onlyActive']);

		$this->render("//MapView/default", array(
				'id'               => $id,
				'latitude'         => $park->location->latitude,
				'longitude'        => $park->location->longitude,
				'type'             => 'park',
				'mode'             => $mode,
				'mapDim'           => ['w' => '80%', 'h' => ''],
				'parks'            => $parks,
				'nearestTransport' => true,
		));
	}

	/**
	 * @param $recordId
	 * @param $recordType
	 * @param $imageName
	 */
	public function actionGetParkImageInfo($recordId, $recordType, $imageName)
	{

		$newImageName = $imageName;
		if (strstr($imageName, "_medium")) {
			$newImageName = str_replace("_medium", "", $imageName);
		} else if (strstr($imageName, "_small")) {
			$newImageName = str_replace("_small", "", $imageName);
		}

		$recordRes = File::model()->findBySql("SELECT * FROM file WHERE recordId=" . $recordId . " AND recordType='" . $recordType . "' AND name='" . $newImageName . "'");
		echo json_encode(array('originalImageName' => $newImageName, 'captionText' => $recordRes->caption));
	}

	/**
	 * @param string $view
	 * @return string
	 */
	public function listingPage($view = 'gallery')
	{

		return '/parks/view/' . $view;
	}

	/**
	 * @param $id
	 * @return string
	 */
	public function detailPage($id)
	{

		return '/park/' . $id;
	}
}
