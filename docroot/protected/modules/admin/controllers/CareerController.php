<?php

/**
 *
 */
class CareerController extends AdminController
{
	/**
	 * @var string
	 */
	public $layout = '//layouts/adminDefault';

	/**
	 *
	 */
	public function actionCreate()
	{
		$model = new Career();

		$this->edit($model);
	}

	/**
	 * @param $id
	 */
	public function actionUpdate($id)
	{
		$model = Career::model()->findByPk($id);

		$this->edit($model);
	}

	/**
	 * @param Career $model
	 */
	private function edit(Career $model)
	{
		if (isset($_POST['Career']) && $_POST['Career']) {
			$model->attributes = $_POST['Career'];
			if ($model->save()) {
				$this->redirect(array('update',
									  'id'=> $model->id));
			}
		}
		$this->render('edit', array('model' => $model));
	}

	/**
	 *
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('Career', array('pagination' => array('pageSize' => 37)));
		$this->render('index', array(
									'dataProvider'=> $dataProvider,
							   ));
	}

}