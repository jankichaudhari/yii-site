<?php

class m131112_140932_populate_budget_field_in_client_table extends CDbMigration
{
	public function up()
	{
		$sql = "UPDATE client SET budget = cli_salemax";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		$sql = "UPDATE client SET budget = 0";
		Yii::app()->db->createCommand($sql)->execute();
	}

}