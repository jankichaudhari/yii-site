<?php

class m140122_161936_return_lettings_back extends CDbMigration
{
	public function up()
	{
		$sql = 'INSERT INTO deal SELECT * FROM backup_lettings_deal';
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		echo "m140122_161936_return_lettings_back does not support migration down.\n";
		return false;
	}

}