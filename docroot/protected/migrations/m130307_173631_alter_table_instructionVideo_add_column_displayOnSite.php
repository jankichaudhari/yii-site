<?php

class m130307_173631_alter_table_instructionVideo_add_column_displayOnSite extends CDbMigration
{
	private $tableName = "instructionVideo";
	private $columnName = "displayOnSite";

	public function up()
	{
		return $this->addColumn($this->tableName, $this->columnName,"TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
	}

	public function down()
	{
		return $this->dropColumn($this->tableName, $this->columnName);
	}
}