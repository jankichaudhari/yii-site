<?php

class m130215_153056_set_exchangedate_to_null_if_zeroes_or_1970 extends CDbMigration
{
	public function up()
	{

		$sql = "UPDATE deal SET dea_compdate = NULL WHERE dea_compdate = '1970-01-01' OR dea_compdate = '0000-00-00'";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{

		return true;
	}
}