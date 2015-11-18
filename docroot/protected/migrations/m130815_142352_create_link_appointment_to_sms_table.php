<?php

class m130815_142352_create_link_appointment_to_sms_table extends CDbMigration
{
	public $table = 'link_appointment_to_sms';

	public function up()
	{
		$this->createTable($this->table, array(
											  'appointmentId' => 'int',
											  'smsId'         => 'int',
											  'PRIMARY KEY (appointmentId, smsId)'
										 ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}
}