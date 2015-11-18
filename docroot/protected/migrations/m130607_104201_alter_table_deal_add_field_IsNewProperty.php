<?php

class m130607_104201_alter_table_deal_add_field_IsNewProperty extends CDbMigration
{
	private $tableName = 'deal';
	public $fieldName = 'noNewProperty';

	public function up()
	{

		return $this->addColumn($this->tableName, $this->fieldName, "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
	}

	public function down()
	{

		return $this->dropColumn($this->tableName, $this->fieldName);
	}
}