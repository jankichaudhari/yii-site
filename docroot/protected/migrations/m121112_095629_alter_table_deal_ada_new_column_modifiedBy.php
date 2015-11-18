<?php

class m121112_095629_alter_table_deal_ada_new_column_modifiedBy extends CDbMigration
{
	private $tableName = "deal";
	private $columnName = "modifiedBy";

	public function up()
	{
		$this->addColumn($this->tableName, $this->columnName, "int");
	}

	public function down()
	{
		$this->dropColumn($this->tableName, $this->columnName);
	}
}