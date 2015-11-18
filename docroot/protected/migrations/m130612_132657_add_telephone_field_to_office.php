<?php

class m130612_132657_add_telephone_field_to_office extends CDbMigration
{
	public $table = 'office';

	public $column = 'phone';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'varchar(25) not null default "" AFTER email');
	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}
}