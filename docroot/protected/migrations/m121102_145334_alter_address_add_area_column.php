<?php

class m121102_145334_alter_address_add_area_column extends CDbMigration
{
	private $tableName = 'address';
	private $columnName = 'areaId';

	public function up()
	{

		$this->addColumn($this->tableName, $this->columnName, 'int');
	}

	public function down()
	{

		$this->dropColumn($this->tableName, $this->columnName);
	}
}