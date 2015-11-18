<?php

class DevnoteController extends AdminController
{

	public function actionSave()
	{

		if (isset($_GET['id']) && $_GET['id']) {
			$note = Devnote::model()->findByPk($_GET['id']);
		}
		if(!isset($note)) {
			$note = new Devnote;
		}

		$note->attributes = $_GET['Devnote'];

		$note->save(false);
		echo $note->id;
		Yii::app()->end();
	}

}
