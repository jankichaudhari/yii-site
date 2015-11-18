<?php

class m130612_120842_add_active_field_to_office_table extends CDbMigration
{
	public $table = 'office';
	public $column = 'active';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'tinyint(0) unsigned not null default 1');
	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}

}