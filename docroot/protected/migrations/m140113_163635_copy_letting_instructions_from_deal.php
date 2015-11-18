<?php

class m140113_163635_copy_letting_instructions_from_deal extends CDbMigration
{
	public function up()
	{
		$sql = 'INSERT INTO backup_lettings_deal SELECT * FROM deal WHERE dea_type = "Lettings"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = "DELETE FROM deal WHERE dea_type = 'Lettings'";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		$sql = 'INSERT INTO deal SELECT * FROM backup_lettings_deal';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = "DELETE FROM backup_lettings_deal";
		Yii::app()->db->createCommand($sql)->execute();
	}

}