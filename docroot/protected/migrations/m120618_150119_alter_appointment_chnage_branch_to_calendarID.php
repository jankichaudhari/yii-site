<?php

class m120618_150119_alter_appointment_chnage_branch_to_calendarID extends CDbMigration
{
	public function up()
	{
		$this->renameColumn("appointment", "app_branch", "calendarID");
	}

	public function down()
	{
		$this->renameColumn("appointment",  "calendarID", "app_branch");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}