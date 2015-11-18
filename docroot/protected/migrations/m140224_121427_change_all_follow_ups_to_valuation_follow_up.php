<?php

class m140224_121427_change_all_follow_ups_to_valuation_follow_up extends CDbMigration
{
	public function up()
	{
		$sql = "UPDATE appointment SET app_type = 'Valuation Follow Up' WHERE app_type = 'Follow Up'";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		$sql = "UPDATE appointment SET app_type = 'Follow Up' WHERE app_type = 'Valuation Follow Up'";
		Yii::app()->db->createCommand($sql)->execute();
	}
}