<?php

class BranchController extends BaseSuperAdminController
{
	public function actionUpdate($id)
	{

		$this->layout = '//layouts/new/popup';
		$model        = Branch::model()->findByPk($id);
		$this->edit($model);
	}

	public function edit(Branch $model)
	{

		if (isset($_POST['Branch']) && $_POST['Branch']) {
			$model->attributes = $_POST['Branch'];
			if ($model->save()) {

				$params          = ['id' => $model->bra_id];
				$params['close'] = false;

				if (isset($_POST['close'])) {
					$params['close'] = true;
				}

				if (isset($_GET['callback']) && $_GET['callback']) {
					Yii::app()->user->setFlash('branch-callback', true);
					$params['callback'] = $_GET['callback'];
				}

				Yii::app()->user->setFlash('branch-update-success', 'Branch info updated');
				$this->redirect($this->createUrl('Update', $params));
			}

		}

		if (Yii::app()->user->getFlash('branch-callback')) {
			$callback = new PopupCallback($_GET['callback']);
			$callback->run([$model->bra_id], isset($_GET['close']) && $_GET['close']);
		} elseif(isset($_GET['close']) && $_GET['close']) {
			echo '<script>window.close();</script>';
		}

		$this->render('edit', array(
								   'model' => $model,
							  ));
	}

	public function actionGetInfo($id)
	{

		$model = Branch::model()->findByPk($id);
		$this->renderPartial('info', array(
										  'model' => $model,
									 ));

	}
}

