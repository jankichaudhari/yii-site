<?php

class m130312_131506_add_followUp_type_to_appointment extends CDbMigration
{
	private $tableName = 'appointment';

	public function up()
	{

		return $this->alterColumn($this->tableName, 'app_type', "ENUM('Viewing','Valuation','Production','Inspection','Meeting','Note','Lunch', 'Follow Up') NOT NULL DEFAULT 'Viewing'");
	}

	public function down()
	{
		return $this->alterColumn($this->tableName, 'app_type', "ENUM('Viewing','Valuation','Production','Inspection','Meeting','Note','Lunch') NOT NULL DEFAULT 'Viewing'");
	}
}