<?php

class m130314_140144_update_appointments_set_null_where_zeroes extends CDbMigration
{
	public function up()
	{
		$this->update('appointment', ['app_end' => null], "app_end = '0000-00-00 00:00:00'");
	}

	public function down()
	{
		return true;
	}

}