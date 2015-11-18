<?php

class m131010_123945_add_email_and_reject_message_fields_to_mandrillEmail extends CDbMigration
{
	private $table = 'mandrillEmail';

	public function up()
	{
		$this->addColumn($this->table, 'email', 'string');
		$this->addColumn($this->table, 'rejectMessage', 'string');
	}

	public function down()
	{
		$this->dropColumn($this->table, 'email');
		$this->dropColumn($this->table, 'rejectMessage');

	}

}