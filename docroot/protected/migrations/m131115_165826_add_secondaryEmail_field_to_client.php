<?php

class m131115_165826_add_secondaryEmail_field_to_client extends CDbMigration
{
	private $table = 'client';

	private $column = 'secondaryEmail';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'string NOT NULL DEFAULT "" AFTER cli_email');
	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}

}