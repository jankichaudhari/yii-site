<?php

class m140206_151700_create_table_to_register_interest extends CDbMigration
{

	private $table = 'clientInterest';

	public function up()
	{
		$this->createTable($this->table, array(
				'instructionId' => 'int unsigned not null',
				'clientId'      => 'int unsigned not null',
				'created'       => 'datetime not null',
				'createdBy'     => 'int unsigned not null',
				'PRIMARY KEY (instructionId, clientId)'
		));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}

}