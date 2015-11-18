<?php

class m131112_143538_make_budget_null_by_default extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('client', 'budget', 'int unsigned null default null');
		$sql = "UPDATE client SET budget = null WHERE budget = 0";
		Yii::app()->db->createCommand($sql)->execute();

	}

	public function down()
	{
		$this->alterColumn('client', 'budget', 'int unsigned not null default 0');
	}

}