<?php

class m140107_160121_add_index_to_client_on_neg_and_reg extends CDbMigration
{
	public function up()
	{
		$this->createIndex('cli_regd', 'client', 'cli_regd');
		$this->createIndex('cli_neg', 'client', 'cli_neg');
	}

	public function down()
	{
		$this->dropIndex('cli_regd', 'client');
		$this->dropIndex('cli_neg', 'client');
	}
}