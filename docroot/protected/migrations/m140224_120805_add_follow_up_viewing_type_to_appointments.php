<?php

class m140224_120805_add_follow_up_viewing_type_to_appointments extends CDbMigration
{
	private $table = 'appointment';

	private $column = 'app_type';

	public function up()
	{
		$this->alterColumn($this->table, $this->column, "ENUM('Viewing','Valuation','Production','Inspection','Meeting','Note','Lunch', 'Follow Up', 'Viewing Follow Up', 'Valuation Follow Up') NOT NULL DEFAULT 'Viewing' COMMENT 'Follow Up is deprecated'");
	}

	public function down()
	{
		$this->alterColumn($this->table, $this->column, "ENUM('Viewing','Valuation','Production','Inspection','Meeting','Note','Lunch', 'Follow Up') NOT NULL DEFAULT 'Viewing'");
	}
}