<?php

class MailshotTypeController extends AdminController
{
	public function actionIndex()
	{
		$model = new MailshotType('search');
		$this->render('index', compact('model'));
	}

	public function actionCreate()
	{
		$model = new MailshotType();
		$this->edit($model);

	}

	public function actionUpdate($name)
	{
		$model = MailshotType::model()->findByPk($name);
		if (!$model) {
			throw new CHttpException(404, 'MailshotType [name: ' . $name . '] was not found');
		}
		$this->edit($model);
	}

	private function edit(MailshotType $model)
	{

		if (isset($_POST['MailshotType']) && $_POST['MailshotType']) {
			$model->attributes = $_POST['MailshotType'];
			if ($model->save()) {
				$this->redirect(['mailshotType/update', 'name' => $model->name]);
			}
		}
		$this->render('edit', ['model' => $model]);
	}

}
