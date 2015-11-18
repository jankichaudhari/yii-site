<?php

class m130103_121335_alter_table_instructionVideo_add_column_featuredVideo extends CDbMigration
{
	private $tableName = "instructionVideo";
	private $columnName = "featuredVideo";

	public function up()
	{
		$this->addColumn($this->tableName, $this->columnName, "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
	}

	public function down()
	{
		$this->dropColumn($this->tableName, $this->columnName);
	}
}