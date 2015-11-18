<?php

class m131115_165855_populate_secondary_email_field extends CDbMigration
{
	public function up()
	{
		$sql = "UPDATE client SET secondaryEmail = cli_web";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		$sql = "UPDATE client SET secondaryEmail = ''";
		Yii::app()->db->createCommand($sql)->execute();
	}

}