<?php

class m131112_140911_add_field_budget_to_client_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('client', 'budget', 'int unsigned not null default 0');
	}

	public function down()
	{
		$this->dropColumn('client', 'budget');
	}

}