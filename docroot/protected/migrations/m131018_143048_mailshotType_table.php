<?php

class m131018_143048_mailshotType_table extends CDbMigration
{
	private $table = 'mailshotType';

	public function up()
	{
		$this->createTable($this->table, array(
											  'name'         => 'string',
											  'subject'      => 'text',
											  'htmlTemplate' => 'text',
											  'textTemplate' => 'text',
											  'description'  => 'text',
											  'created'      => 'datetime',
											  'createdBy'    => 'int unsigned NOT NULL default 0',
											  'PRIMARY KEY (name)'
										 ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}

}