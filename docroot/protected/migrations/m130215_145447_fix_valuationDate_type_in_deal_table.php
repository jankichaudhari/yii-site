<?php

class m130215_145447_fix_valuationDate_type_in_deal_table extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('deal', 'valuationDate', 'DATE NULL DEFAULT NULL');
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