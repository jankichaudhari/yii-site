<?php

class m121220_171548_create_table_currentPropertyTenants extends CDbMigration
{
	private $tableName = 'currentPropertyTenant';

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