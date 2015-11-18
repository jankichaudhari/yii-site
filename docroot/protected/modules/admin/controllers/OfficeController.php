<?php
/**
 * Only available for super admin
 */
class OfficeController extends BaseSuperAdminController
{

	public function actionIndex()
	{

		$model = new Office('search');

		$this->render('index', array(
									'model' => $model,
							   ));
	}

	public function actionCreate()
	{
		$model = new Office;
		$this->edit($model);
	}

	public function actionUpdate($id)
	{

		$model = Office::model()->findByPk($id);
		$this->edit($model);
	}

	public function edit(Office $model)
	{

		$this->backLink = $this->createUrl('index');

		if (isset($_POST['Office'])) {
			$model->attributes = $_POST['Office'];
			$model->addressId  = $_POST['Address']['id'];

			$model->setPostcodes($_POST['operatingPostcode']);

			if ($model->save()) {
				Yii::app()->user->setFlash('office-update-success', 'Office sucessfully updated');
				$this->redirect(array('update', 'id'=> $model->id));
			}
		}

		$this->render('edit', array(
								   'model' => $model,
							  ));
	}
}
