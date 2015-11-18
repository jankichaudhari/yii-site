<?php
class NoteController extends AdminController
{
	public function actionEdit($id)
	{

		/** @var $model Note */
		$model        = Note::model()->findByPk($id, array('with' => 'changes'));
		$this->layout = '//layouts/new/popup';
		if (isset($_POST['delete']) || isset($_POST['restore'])) {
			$model->not_status = isset($_POST['delete']) ? 'Deleted' : 'Active';
			if ($model->save()) {
				if (isset($_POST['delete'])) {
					Yii::app()->user->setFlash('note-deleted', 'Note is deleted!');
				} else {
					Yii::app()->user->setFlash('note-restored', 'Note is restored!');
				}

				Yii::app()->user->setFlash('note-callback', (isset($_GET['callback']) ? $_GET['callback'] : ""));
				$this->redirect(array(
									 'Edit',
									 'id'       => $id,
									 'callback' => (isset($_GET['callback']) ? $_GET['callback'] : ""),
									 'close'    => (isset($_POST['close']) ? true : false),
								));
			}

		} elseif (isset($_POST['Note'])) {
			$model->attributes = $_POST['Note'];
			if ($model->save()) {
				Yii::app()->user->setFlash('note-updated', 'Saved!');
				Yii::app()->user->setFlash('note-callback', (isset($_GET['callback']) ? $_GET['callback'] : ""));
				$this->redirect(array(
									 'Edit',
									 'id'       => $id,
									 'callback' => (isset($_GET['callback']) ? $_GET['callback'] : ""),
									 'close'    => (isset($_POST['close']) ? true : false),
								));
			}
		}

		if (Yii::app()->user->hasFlash('note-callback')) {
			$callback = Yii::app()->user->getFlash('note-callback');
			$callback = new PopupCallback($callback);
			$callback->run(array($model->not_id), isset($_GET['close']) && $_GET['close']);
		}

		$this->render('edit', ['model' => $model]);
	}

	public function actionDeleteNote()
	{

		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		if (empty($id)) {
			return false;
		}
		/** @var $model Note */
		$model             = Note::model()->findByPk($id, array('with' => 'changes'));
		$model->not_status = 'Deleted';
		if (!$model->save()) {
			return false;
		}
		return true;
	}

	public function actionShowNotesBlocksByType($noteTypeId, $noteType)
	{

		if (!$noteType) {
			return false;
		}

		if (!$noteTypeId) {
			Note::model()->deleteNotesHavingEmptyTypeId($noteType);
		} else {
			$criteria = new CDbCriteria();
			$criteria->compare('not_type', $noteType);
			$criteria->compare('not_row', $noteTypeId);
			$model = Note::model()->find($criteria);
			if ($model) {
				$this->actionShowNotesBlocksById($model->not_id);
			}
		}
	}

	public function actionShowNotesBlocksById($noteId)
	{

		$model = Note::model()->findByPk($noteId);
		if (!$model) {
			throw new CHttpException(404, "Note [id = " . $noteId . "] is not found");
		}
		$noteTypeId   = $model->not_row;
		$noteType     = $model->not_type;
		$noteCriteria = new CDbCriteria();
		$noteCriteria->compare('not_row', $noteTypeId, false, 'AND', false);
		$noteCriteria->compare('not_type', $noteType, false, 'AND', false);
		$noteCriteria->order = 'not_id DESC, not_status DESC';
		$notes               = Note::model()->findAll($noteCriteria);
		$this->renderPartial('notes', ['notes' => $notes, 'noteType' => $noteType]);
	}

	public function actionSaveNoteBlurb()
	{

		if (!isset($_POST['noteId'], $_POST['noteBlurb'])) {
			throw new CHttpException(400, 'Wrong request');
		}
		if ($_POST['noteId']) {
			$model = Note::model()->findByPk($_POST['noteId']);
			if (!$model) {
				throw new CHttpException(400, 'Wrong request');
			}
		} elseif (isset($_POST['noteType'], $_POST['noteTypeId'])) {
			$model           = new Note();
			$model->not_type = $_POST['noteType'];
			$model->not_row  = $_POST['noteTypeId'];
		} else {
			throw new CHttpException(400, 'Wrong request');
		}
		$model->not_blurb = $_POST['noteBlurb'];

		$model->save();
		echo $model->not_id;

	}

}