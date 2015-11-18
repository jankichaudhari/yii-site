<?php
class LocalEventController extends AdminController
{
	public $layout = '//layouts/admin_big_table';
	public $pathToImages;
	public $mainImageCropFactor = array();

	public function __construct($id, $module = null)
	{

		$this->pathToImages = Yii::app()->params['imgPath'] . "/LocalEvent";
		parent::__construct($id, $module);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{

		$this->render('view', array(
								   'model' => $this->loadModel($id),
							  ));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$this->deleteEmptyRecord();
		$model = new LocalEvent();
		$model->save(false);
		$this->redirect(array('update', 'id' => $model->id, 'newRecord' => true));
	}

	public function deleteEmptyRecord()
	{

		$localEventInfo = LocalEvent::model()->findAll('createdBy=' . Yii::app()->user->getId() . ' AND (heading is NULL || heading = "" || description is NULL || description = "")');
		if (count($localEventInfo) > 0) {
			foreach ($localEventInfo as $localEvent) {
				LocalEvent::model()->findByPk($localEvent->id)->delete();
			}
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{

		$model        = $this->loadModel($id);
		$this->layout = '//layouts/adminDefault';

		if (!$model->address) {
			$model->address = new Location();
		}

		$error = false;
		if (isset($_POST['Location'])) {
			$model->address->attributes = $_POST['Location'];
			if ($model->address->save()) {
				$model->addressID = $model->address->id;
			} else {
				$model->addressID = 0;
				$error            = true;
			}
		}

		if (!$error && isset($_POST['LocalEvent'])) {
			$model->attributes = $_POST['LocalEvent'];
			if ($model->save()) {
				Yii::app()->user->setFlash('success', 'Updated Successfully');
				$this->redirect(array('update', 'id' => $model->id));
			}
		}

		$this->render('_mainForm', array(
										'model'   => $model,
										'address' => $model->address,
										'images'  => $model->images,
								   ));

	}

	public function actionLocalEventPhotos($id)
	{

		$this->layout = '//layouts/new/main';
		$model        = $this->loadModel($id);

		if (isset($_POST['mainImage']) && !empty($_POST['mainImage'])) {
			$this->mainImageCropFactor = array(
				'width'      => $_POST['imageWidth'],
				'height'     => $_POST['imageHeight'],
				'cropWidth'  => $_POST['cropWidth'],
				'cropHeight' => $_POST['cropWidth'],
				'x'          => $_POST['cropX'],
				'y'          => $_POST['cropY'],
			);
			$this->saveImages($model, 'mainImage');
			$this->redirect(array('LocalEventPhotos', 'id' => $model->id));
		} else if (isset($_POST['images']) && !empty($_POST['images'])) {
			$this->saveImages($model, 'images');
			$this->redirect(array('LocalEventPhotos', 'id' => $model->id));
		}

		$this->render('_photos', array(
									  'model' => $model
								 ));
	}

	public function saveImages($model, $instanceName)
	{

		$recordType = 'LocalEvent';
		$images     = CUploadedFile::getInstancesByName($instanceName);
		if ($images) {
			foreach ($images as $num => $pic) {
				$img = new LocalEventImage();
				if ($instanceName == 'mainImage') {
					$recordType      = 'LocalEventMain';
					$img->cropFactor = $this->mainImageCropFactor;
				}
				$img->file       = $pic;
				$img->recordId   = $model->id;
				$img->recordType = $recordType;

				if ($img->save() && $instanceName == 'mainImage') {
					$model->mainImageID = $img->id;
					$model->update(array('mainImageID'));
				}
			}
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{

		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$delete = $this->loadModel($id)->delete();
			if ($delete) {
				$mainImages = LocalEventImage::model()->findAllByAttributes([
																			'recordId'   => $id,
																			'recordType' => 'LocalEventMain'
																			]);
				foreach ($mainImages as $mainImage) {
					$mainImage->delete();
				}
				$images = LocalEventImage::model()->findAllByAttributes([
																		'recordId' => $id, 'recordType' => 'LocalEvent'
																		]);
				foreach ($images as $image) {
					$image->delete();
				}
			}

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{

		$this->layout = '//layouts/adminDefault';
		$dataProvider = new CActiveDataProvider('LocalEvent');
		$this->render('index', array(
									'dataProvider' => $dataProvider,
							   ));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{

		$model = new LocalEvent('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['LocalEvent'])) {
			$model->attributes = $_GET['LocalEvent'];
		}

		$this->render('admin', array(
									'model' => $model,
							   ));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{

		$model = LocalEvent::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'local-event-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
