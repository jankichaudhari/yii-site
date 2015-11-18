<?php

class m121112_095616_alter_table_deal_ada_new_column_createdBy extends CDbMigration
{
	private $tableName = "deal";
	private $columnName = "createdBy";

	public function up()
	{
		$this->addColumn($this->tableName, $this->columnName, "int");
	}

	public function down()
	{
		$this->dropColumn($this->tableName, $this->columnName);
	}
}