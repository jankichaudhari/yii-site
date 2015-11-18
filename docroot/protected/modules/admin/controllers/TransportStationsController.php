<?php

class TransportStationsController extends AdminController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin_big_table';



	public function actionViewRecord($id)
	{
		$thisRecord = TransportStations::model()->findByPk($id);
		echo $thisRecord->title;
	}


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$this->layout='';
		$model=new TransportStations;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$recordSave = false;
		$this->layout='';
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['TransportStations']))
		{
			$model->attributes=$_POST['TransportStations'];
			if($model->save()){
				LinkTransportStationsToTransportTypes::model()->deleteAll("transportStation=" . $id);
				if (isset($_POST['TransportStations']['type'])) {
					$types = $_POST['TransportStations']['type'];
					foreach ($types as $typeKey=> $typeValue) {
						if ($typeValue == 1) {
							$user2role = new LinkTransportStationsToTransportTypes();
							$user2role->unsetAttributes();
							$user2role->transportStation = $id;
							$user2role->transportType = $typeKey;
							$user2role->status = 1;
							$user2role->save();
						}
					}
				}
				$recordSave = true;
			}
		}

		$this->render('update',array(
				'model'=>$model,
				'recordSave' => $recordSave,
		   ));
	}

	/*
	 *
	 */
	public function actionSavePosition($position,$id)
	{
		$model=new TransportStations;
		if((!empty($id)) && ($id!=0)){
			$model =$this->loadModel($id);
		}
		if(isset($position)){
			$latLng = explode(",",str_replace(["(",")"],"",$position));
			$model->latitude = $latLng[0];
			$model->longitude = $latLng[1];
			if($model->save())
				echo $model->id;
		}
	}
	/*
	 *
	 */


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			if($this->loadModel($id)->delete()){
				echo true;
			}

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
//			if(!isset($_GET['ajax']))
//				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($recordsOnly=false)
	{
		$dataProvider= TransportStations::model()->findAll(['order'=>'title ASC']);
		$page = 'index';
		if($recordsOnly==true){
			$this->layout='';
			$page = '_mapMarkers';
		}
		if($recordsOnly==false){
			$dataProvider= TransportStations::model()->findAll();
		}

		$this->render($page,array(
								   'dataProvider'=>$dataProvider,
							  ));
	}

	/**
	 * Manages all models.
	 */

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=TransportStations::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='transport-stations-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
