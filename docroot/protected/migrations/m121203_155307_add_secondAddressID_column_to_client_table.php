<?php

class m121203_155307_add_secondAddressID_column_to_client_table extends CDbMigration
{
	private $tableName = 'client';
	private $column = 'secondAddressID';

	public function up()
	{

		$this->addColumn($this->tableName, $this->column, 'int');
	}

	public function down()
	{

		return $this->dropColumn($this->tableName, $this->column);
	}

}