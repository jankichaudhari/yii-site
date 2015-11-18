<?php

class SuperAdminController extends BaseSuperAdminController
{

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionSiteErrorLog()
	{

		$files = glob(Yii::app()->params['logDirPath'] . '/site-error*.log');
		sort($files);
		$dataProvider = new CArrayDataProvider($files);
		$this->render('siteErrorLog', compact('dataProvider', 'files'));

	}

	public function actionSiteLogDetails($file)
	{
		$filePath = Yii::app()->params['logDirPath'] . '/' . $file;
		if (!file_exists($filePath)) {
			throw new CHttpException(404, 'file [' . $file . '] not found under log directory');
		}
		$this->render('siteLogDetails', array('fileContent' => file_get_contents($filePath)));
	}

	public function actionViewRecord($model, $pk)
	{
		$modelClass = $model;
		if (!class_exists($modelClass) || !is_subclass_of($modelClass, 'CActiveRecord')) {
			throw new CHttpException(401, 'model ' . $modelClass . ' does not exist or not an activeRecord');
		}
		/** @var CActiveRecord $model */
		$model = $modelClass::model()->findByPk($pk);
		$this->render('viewRecord', compact('model'));

	}

	public function actionLoadRelatedIds($model, $pk, $related)
	{
		$modelClass = $model;
		if (!class_exists($modelClass) || !is_subclass_of($modelClass, 'CActiveRecord')) {
			throw new CHttpException(401, 'model ' . $modelClass . ' does not exist or not an activeRecord');
		}
		/** @var CActiveRecord $model */
		$model = $modelClass::model()->findByPk($pk);
		$ids   = [];
		if (is_array($model->$related)) {
			foreach ($model->$related as $key => $value) {
				$ids[] = $value->getPrimaryKey();
			}
		} elseif ($model->$related instanceof CActiveRecord) {
			$ids[] = $model->$related->getPrimaryKey();
		}

		header('Content-Type: text/json');
		echo json_encode($ids);
	}

}
