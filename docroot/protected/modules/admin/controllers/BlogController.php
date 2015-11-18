<?php

class BlogController extends AdminController
{

	public function actionIndex()
	{
		$model = new Blog('search');
		$this->render('index', compact('model'));
	}

	public function actionCreate()
	{
		$model = new Blog();
		$this->edit($model);
	}

	public function actionUpdate($id)
	{
		$model = Blog::model()->findByPk($id);
		$this->edit($model);

	}

	private function edit(Blog $model)
	{
		if (isset($_POST['Blog']) && $_POST['Blog']) {
			$model->attributes = $_POST['Blog'];

			$image             = CUploadedFile::getInstanceByName('upload');
			$imageModel        = new BlogImage();
			$imageModel->image = $image;
			if ($imageModel->save()) {
				$model->featuredImage = $imageModel->id;
			}
			if ($model->save()) {
				/** @var CWebUser $user */
				$user = Yii::app()->user;
				$user->setFlash('blog-created', $model->isNewRecord ? 'Post succesfully created' : 'Post updated');
				$this->redirect(['blog/update', 'id' => $model->id, 'preview' => isset($_POST['preview'])]);
			}
		}
		$this->render('edit', compact('model'));
	}

	public function actionUploadImage()
	{
		$file         = CUploadedFile::getInstanceByName('upload');
		$model        = new BlogImage();
		$model->image = $file;
		if ($model->save()) {
			$this->renderPartial('uploadImage', compact('model'));
		}

	}
}
