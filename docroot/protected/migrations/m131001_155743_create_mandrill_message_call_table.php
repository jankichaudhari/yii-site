<?php

class m131001_155743_create_mandrill_message_call_table extends CDbMigration
{
	private $table = 'mandrillMessage';

	public function up()
	{
		$this->createTable('' . $this->table . '', array(
														'id'        => 'pk',
														'text'      => 'text',
														'from'      => 'string',
														'subject'   => 'text',
														'message'   => 'text',
														'created'   => 'datetime',
														'createdBy' => 'int',
														'type'      => 'int',
												   ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}

}