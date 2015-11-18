<?php

class m121119_121438_create_table_instructionToPdfSettings extends CDbMigration
{
	private $tableName = 'instructionToPdfSettings';

	public function up()
	{

		$this->createTable($this->tableName, array(
												  'id'                   => 'pk',
												  'instructionId'        => 'int NOT NULL',
												  'displayLeaseExpires'  => 'tinyint(1) NOT NULL DEFAULT 1',
												  'displayServiceCharge' => 'tinyint(1) NOT NULL DEFAULT 1',
												  'displayGroundRent'    => 'tinyint(1) NOT NULL DEFAULT 1',
												  'additionalNotes'      => 'text',
											 ));
		$this->createIndex('instructionId', $this->tableName, 'instructionId', true);
	}

	public function down()
	{

		$this->dropTable($this->tableName);
	}
}