<?php

class m130111_111134_alter_table_deal_add_new_field_valuationDate extends CDbMigration
{
	private $tableName = "deal";
	private $columnName = "valuationDate";

	public function up()
	{
			$this->addColumn($this->tableName, $this->columnName, "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");
	}

	public function down()
	{
			$this->dropColumn($this->tableName, $this->columnName);
	}
}
