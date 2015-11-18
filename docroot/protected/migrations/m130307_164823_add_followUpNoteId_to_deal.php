<?php

class m130307_164823_add_followUpNoteId_to_deal extends CDbMigration
{
	private $tableName = 'deal';

	public function up()
	{

		$this->addColumn($this->tableName, 'followUpAppointmentId', "INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `followUpDue`");
	}

	public function down()
	{

		return $this->dropColumn($this->tableName, 'followUpAppointmentId');
	}

}