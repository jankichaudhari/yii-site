<?php

class m130613_153016_alter_table_deal_add_field_emailRandomString extends CDbMigration
{
	private $tableName = "deal";
	private $columnName = "emailLinkString";

	public function up()
	{

		return $this->addColumn($this->tableName, $this->columnName, "string");
	}

	public function down()
	{

		return $this->dropColumn($this->tableName, $this->columnName);
	}
}