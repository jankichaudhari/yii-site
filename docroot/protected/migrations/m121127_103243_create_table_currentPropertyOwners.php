<?php

class m121127_103243_create_table_currentPropertyOwners extends CDbMigration
{
	private $tableName = 'currentPropertyOwner';

	public function up()
	{
		$this->createTable($this->tableName, array(
												  'clientId'   => 'int',
												  'propertyId' => 'int',
												  'PRIMARY KEY (`clientId`, `propertyId`)',
											 ));
	}

	public function down()
	{

		$this->dropTable($this->tableName);

		return true;
	}

}