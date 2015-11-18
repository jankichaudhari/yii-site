<?php

class m120925_115659_create_table_instruction_video extends CDbMigration
{
	public $tableName = 'instructionVideo';

	public function up()
	{
		$this->createTable($this->tableName, array(
			'id' => 'pk',
			'instructionId' => 'int',
			'videoId' => 'string',
			'host' => 'string',
			'videoData' => 'text',
			));
	}

	public function down()
	{
		$this->dropTable($this->tableName);
	}
}