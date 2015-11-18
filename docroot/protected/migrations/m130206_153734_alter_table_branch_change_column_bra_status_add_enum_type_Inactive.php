<?php

class m130206_153734_alter_table_branch_change_column_bra_status_add_enum_type_Inactive extends CDbMigration
{
	private $tableName = "branch";
	private $columnName = "bra_status";

	public function up()
	{
		return $this->alterColumn($this->tableName, $this->columnName,"ENUM('Pending','Active','Archived','Inactive')");
	}

	public function down()
	{
		return $this->alterColumn($this->tableName, $this->columnName,"ENUM('Pending','Active','Archived')");
	}
}
