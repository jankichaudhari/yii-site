<?php

class m140224_132317_add_follow_up_viewing_id_to_appointment_table extends CDbMigration
{
	private $table = 'appointment';

	private $column = 'followUpAppointmentId';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'int(10) UNSIGNED NOT NULL DEFAULT 0');

	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}
}