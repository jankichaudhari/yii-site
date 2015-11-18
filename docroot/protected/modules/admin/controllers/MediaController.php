<?php

class MediaController extends AdminController
{
	/**
	 * @deprecated
	 * @param        $instructionId
	 * @param string $type
	 */
	public function actionPhotoForm($instructionId, $type = 'images')
	{

		$this->layout     = '//layouts/new/main';
		$model        = new Media();
		$model->med_row    = $instructionId;
		$model->med_table  = 'deal';

		if (isset($_POST['Media']) && $_POST['Media']) {

			$model->attributes = $_POST['Media'];
			$model->file       = CUploadedFile::getInstance($model, 'file');

			$model->setCropFactor(array(
									   'width'  => $_POST['imageWidth'],
									   'height' => $_POST['imageHeight'],
									   'w'      => $_POST['cropWidth'],
									   'h'      => $_POST['cropWidth'],
									   'x'      => $_POST['cropX'],
									   'y'      => $_POST['cropY'],
								  ));
			if ($model->save()) {
				$this->redirect(array('Media/PhotoForm', 'instructionId' => $instructionId));
			}
		}
		$cr        = new CDbCriteria();
		$cr->order = 'med_order ASC';

		$files = Media::model()->findAllByAttributes(array(
														  'med_table' => 'deal',
														  'med_row'   => $instructionId,
														  'med_type'  => Media::TYPE_PHOTO,
													 ), $cr);
		$this->render('_photoForm', ['files' => $files, 'model' => $model, 'instructionId' => $instructionId]);
	}

	public function actionMediaForm($instructionId, $type = 'images')
	{

		$this->layout     = '//layouts/new/main';
		$model            = new Media();
		$model->med_row   = $instructionId;
		$model->med_table = 'deal';

		if (isset($_POST['Media']) && $_POST['Media']) {
			if (isset($_POST['floorplanUpload']) && isset($_POST['Media']['floorplan'])) {
				$model->med_dims   = $_POST['Media']['med_dims'];
				$model->file       = CUploadedFile::getInstance($model, 'floorplan');
				$model->med_title  = $_POST['Media']['med_title'];
				$model->otherMedia = 'floorplan';
			} elseif (isset($_POST['epcUpload']) && isset($_POST['Media']['epc'])) {
				$model->file       = CUploadedFile::getInstance($model, 'epc');
				list($thisWidth, $thisHeight, $type, $attr) = getimagesize($model->file->tempName);
				if(($model->file->extensionName=='gif' || $model->file->extensionName=='GIF') && (($thisWidth * $thisHeight) >= Media::MAX_AVAILABLE_SIZE) ){
					$flagDimension = false;
					Yii::app()->user->setFlash('error', 'Too big gif image!!');
				}
				$model->otherMedia = 'epc';
			}

			if ($model->save()) {
				$this->redirect(array('Media/MediaForm', 'instructionId' => $instructionId));
			}
		}

		$cr        = new CDbCriteria();
		$cr->order = 'med_order ASC';

		$floorPlans = Media::model()->findAllByAttributes(array(
															   'med_table' => 'deal',
															   'med_row'   => $instructionId,
															   'med_type'  => Media::TYPE_FLOORPLAN,
														  ), $cr);

		$epc = Media::model()->findAllByAttributes(array(
														'med_table' => 'deal',
														'med_row'   => $instructionId,
														'med_type'  => Media::TYPE_EPC,
												   ), $cr);

		$this->render('_mediaForm', ['floorPlans' => $floorPlans, 'epc' => $epc, 'model' => $model, 'instructionId' => $instructionId]);
	}

	public function actionRearrange()
	{

		Media::model()->rearrange($_POST['newSort'], $_POST['instructionId'], $_POST['type']);
		echo 'done';
	}

	public function actionDelete()
	{

		if (!isset($_POST['id'])) {
			return;
		}
		$model = Media::model()->findByPk($_POST['id']);

		if (!$model) {
			return;
		}

		if ($model->delete()) {
			echo 'done';
		} else {
			echo 'error';
		}

	}

	public function actionChangeTitle()
	{

		if (!isset($_POST['id'])) {
			return;
		}
		$model = Media::model()->findByPk($_POST['id']);
		if (!$model) {
			return;
		}
		$model->med_title = $_POST['value'];
		if ($model->save()) {
			echo 'done';
		} else {
			echo 'error';
		}
	}

	public function actionChangeArea()
	{

		if (!isset($_POST['id'])) {
			return;
		}
		$model = Media::model()->findByPk($_POST['id']);
		if (!$model) {
			return;
		}
		$model->med_dims = $_POST['value'];
		if ($model->save()) {
			echo 'done';
		} else {
			echo 'error';
		}
	}

}
