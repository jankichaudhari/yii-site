<?php

class FeedController extends AdminController
{

	public function filters()
	{

		return array(
			'adminOnly'
		);
	}

	public function filterAdminOnly(CFilterChain $filterChain)
	{
		$filterChain->run();
	}

	public function actionRun($feedname = null)
	{

		if (isset($_POST['feed']) && $_POST['feed']) {
			$feeds = FeedPortal::model()->findAllByPk($_POST['feed']);
			foreach ($feeds as $key => $feed) {
				$path = Yii::app()->params['feedPath'] . '/' . $feed->filename;
				if ($feed->filename && file_exists($path)) {
					passthru('php ' . $path . ' &');
				}
			}
		}

		$this->render('run', array('model' => new FeedPortal('search')));
	}

	public function actionUpdate($id)
	{

		$model = $this->loadModel($id);
		$this->edit($model);
	}

	/**
	 * @param $model
	 */
	private function edit(FeedPortal $model)
	{

		if (isset($_POST['FeedPortal']) && $_POST['FeedPortal']) {
			$model->attributes = $_POST['FeedPortal'];
			if ($model->save()) {
				Yii::app()->user->setFlash('success', 'Feed is updated');
				$this->redirect(['update', 'id' => $model->portal_id]);
			}
		}
		$this->render('edit', ['model' => $model]);
	}

	public function actionCreate()
	{

		$model = new FeedPortal();

		$this->edit($model);
	}

	/**
	 * @param $id
	 * @return FeedPortal
	 * @throws CHttpException
	 */
	private function loadModel($id)
	{

		/** @var $model FeedPortal */
		$model = FeedPortal::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Sorry, feed is not found');
		}
		return $model;
	}
}
