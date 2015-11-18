<?php

class m130215_153042_set_compdate_to_null_if_zeroes extends CDbMigration
{
	public function up()
	{
		$sql = "UPDATE deal SET dea_exchdate = NULL WHERE dea_exchdate = '1970-01-01' OR dea_exchdate = '0000-00-00'";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}