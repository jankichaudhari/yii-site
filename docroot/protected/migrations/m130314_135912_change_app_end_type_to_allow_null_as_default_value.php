<?php

class m130314_135912_change_app_end_type_to_allow_null_as_default_value extends CDbMigration
{
	private $tableName = 'appointment';

	public function up()
	{
		$this->alterColumn($this->tableName, 'app_end', 'DATETIME NULL DEFAULT NULL');
	}

	public function down()
	{
		return $this->alterColumn($this->tableName, 'app_end', "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
	}
}