<?php

class m130923_140605_add_readBy_field_to_sms extends CDbMigration
{
	private $table = 'log_sms';

	private $column = 'readBy';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'int');
	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}
}