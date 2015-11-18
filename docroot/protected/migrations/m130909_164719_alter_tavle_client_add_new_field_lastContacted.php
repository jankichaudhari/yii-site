<?php

class m130909_164719_alter_tavle_client_add_new_field_lastContacted extends CDbMigration
{
	public $tableName = "client";
	public $columnName = "lastContacted";
	public function up()
	{
		return $this->addColumn($this->tableName,$this->columnName,"datetime");
	}

	public function down()
	{
		return $this->dropColumn($this->tableName,$this->columnName);
	}
}