<?php

class m131203_160652_add_invalid_email_to_client extends CDbMigration
{
	private $table = 'client';

	private $column = 'invalidEmail';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'tinyint(1) NOT NULL default 0');
	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}

}