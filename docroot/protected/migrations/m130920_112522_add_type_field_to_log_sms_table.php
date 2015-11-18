<?php

class m130920_112522_add_type_field_to_log_sms_table extends CDbMigration
{
	private $table = 'log_sms';

	private $column = 'type';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'ENUM("outgoing", "incoming") NOT NULL');
		$this->createIndex($this->column, $this->table, $this->column);
	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}

}