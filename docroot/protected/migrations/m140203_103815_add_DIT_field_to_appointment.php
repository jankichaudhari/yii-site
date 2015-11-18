<?php

class m140203_103815_add_DIT_field_to_appointment extends CDbMigration
{
	private $table = 'appointment';

	public function up()
	{
		$this->addColumn($this->table, 'DIT', "tinyint(1) NOT NULL DEFAULT 0 COMMENT 'DO IT TOGETHER'");
	}

	public function down()
	{
		$this->dropColumn($this->table, 'DIT');
	}

}