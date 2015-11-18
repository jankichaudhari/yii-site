<?php

class m130307_155122_alter_table_instructionVideo_add_column_displayOrder extends CDbMigration
{
	private $tableName = "instructionVideo";
	private $columnName = "displayOrder";

	public function up()
	{
		return $this->addColumn($this->tableName, $this->columnName,"INT");
	}

	public function down()
	{
		return $this->dropColumn($this->tableName, $this->columnName);
	}
}