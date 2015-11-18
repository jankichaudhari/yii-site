<?php

class m130531_140129_alter_table_user_add_style_columns extends CDbMigration
{
	private $tableName = "propertyCategory";

	public function up()
	{

		$this->renameColumn($this->tableName, "displayName", "title");
		$this->addColumn($this->tableName, "displayName", "string");
		$this->addColumn($this->tableName, "bgColour", "string");
		$this->addColumn($this->tableName, "textColour", "string");
		$this->addColumn($this->tableName, "hoverBgColour", "string");
		$this->addColumn($this->tableName, "hoverTextColour", "string");
		$this->alterColumn($this->tableName, 'created', " DATETIME NULL DEFAULT NULL AFTER `hoverTextColour`");
		$this->alterColumn($this->tableName, 'modified', " DATETIME NULL DEFAULT NULL AFTER `created`");
	}

	public function down()
	{

		$this->dropColumn($this->tableName, "displayName");
		$this->dropColumn($this->tableName, "bgColour");
		$this->dropColumn($this->tableName, "textColour");
		$this->dropColumn($this->tableName, "hoverBgColour");
		$this->dropColumn($this->tableName, "hoverTextColour");
		$this->renameColumn($this->tableName, "title", "displayName");
	}
}