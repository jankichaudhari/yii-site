<?php

class m130215_145708_set_valuationDate_to_null_if_zeroes extends CDbMigration
{
	public function up()
	{
		Yii::app()->db->createCommand("update deal set valuationDate = null where valuationDate = '0000-00-00'")->execute();
	}

	public function down()
	{
		return true;
	}
}