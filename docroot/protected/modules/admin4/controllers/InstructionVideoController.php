<?php
class InstructionVideoController extends AdminController
{

	public $layout = '//layouts/adminDefault';

	public function actionManageVideo(){

		$cr = new CDbCriteria();
		$cr->order = 'displayOrder ASC';
		$instructionVideos = InstructionVideo::model()->findAllByAttributes(['displayOnSite' => 1],$cr);
		$this->render('manageVideo', array(
												'instructionVideos' => $instructionVideos
										   ));
	}

	public function actionRearrange()
	{
		$newOrder = isset($_POST['newOrder']) ? $_POST['newOrder'] : '';
		if($newOrder){
			$cases = '';
			foreach ($newOrder as $orderNum => $id) {
				$cases .= " WHEN id = " . $id . " THEN " . ($orderNum + 1) . "";
			}

			$sql = "UPDATE " . InstructionVideo::model()->tableName() . "
				SET displayOrder = CASE " . $cases . " END";

			Yii::app()->db->createCommand($sql)->execute();
		}
	}

}