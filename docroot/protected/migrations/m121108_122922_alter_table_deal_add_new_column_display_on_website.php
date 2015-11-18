<?php

class m121108_122922_alter_table_deal_add_new_column_display_on_website extends CDbMigration
{
	private $tableName = "deal";
	private $columnName = "displayOnWebsite";

	public function up()
	{
		$this->addColumn($this->tableName, $this->columnName, "ENUM('y','n') DEFAULT 'n'");
		$this->alterColumn($this->tableName,$this->columnName,"TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
	}

	public function down()
	{
		$this->dropColumn($this->tableName, $this->columnName);
	}
}