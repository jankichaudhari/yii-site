<?php

class m140113_160549_create_backup_deal_lettings extends CDbMigration
{
	public function up()
	{
		$sql = 'CREATE TABLE backup_lettings_deal LIKE deal';
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		echo "m140113_160549_create_backup_deal_lettings does not support migration down.\n";
		return false;
	}
}