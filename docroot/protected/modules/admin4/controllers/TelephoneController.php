<?php

class TelephoneController extends AdminController
{

	public function actionUpdateAttributes($id)
	{

		if (!Yii::app()->request->isPostRequest) {
			throw new CHttpException(400, 'must be a post request');
		}
		$model             = Telephone::model()->findByPk($id);
		$model->attributes = $_POST;
		if ($model->save()) {
			echo json_encode($model->attributes);
		} else {
			echo json_encode(['errors' => $model->getErrors()]);
		}
	}

	public function actionUpdate()
	{

		if (!Yii::app()->request->isPostRequest) {
			throw new CHttpException(400, 'must be a post request');
		}

		if (isset($_POST['tel_id']) && $_POST['tel_id']) {
			$model = Telephone::model()->findByPk($_POST['tel_id']);
		} else {
			$model = new Telephone();
		}

		$model->attributes = $_POST;

		if ($model->save()) {
			echo json_encode($model->attributes);
		} else {
			echo json_encode(['errors' => $model->getErrors()]);
		}
	}

}
