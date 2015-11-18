<?php

class m131112_141312_add_index_to_budget extends CDbMigration
{
	public function up()
	{
		$this->createIndex('budget', 'client', 'budget');
	}

	public function down()
	{
		$this->dropIndex('budget', 'client');
	}
}