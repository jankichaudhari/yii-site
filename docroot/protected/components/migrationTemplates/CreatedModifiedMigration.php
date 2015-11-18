<?php
abstract class CreatedModifiedMigration extends CDbMigration
{
	protected $tableName;

	public function up()
	{

		$this->addColumn($this->tableName, 'created', 'datetime');
		$this->addColumn($this->tableName, 'createdBy', 'int');
		$this->addColumn($this->tableName, 'modified', 'datetime');
		$this->addColumn($this->tableName, 'modifiedBy', 'int');

	}

	public function down()
	{

		$this->dropColumn($this->tableName, 'created');
		$this->dropColumn($this->tableName, 'createdBy');
		$this->dropColumn($this->tableName, 'modified');
		$this->dropColumn($this->tableName, 'modifiedBy');
	}
}