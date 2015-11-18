<?php

class BlogController extends PublicController
{
	public $layout = "//layouts/default";

	public function actionIndex()
	{
		$model         = new Blog('search');
		$model->status = Blog::STATUS_PUBLISHED;
		if (isset($_GET['preview']) && !Yii::app()->user->isGuest) {
			$model->status = null;
		}
		$this->render('index', compact('model'));
	}

	public function actionView($id)
	{
		$model = Blog::model();
		if (Yii::app()->user->isGuest) {
			$model->published();
		}
		$model = Blog::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Blog post [id: ' . $id . '] not found');
		}
		$this->render('view', compact('model'));
	}
}
