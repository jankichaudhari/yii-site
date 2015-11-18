<?php

class m121115_115204_add_logUserAction_table extends CDbMigration
{
	private $table = 'logUserAction';

	public function up()
	{
		$this->createTable($this->table, array(
											  'id' => 'pk',
											  'userId' => 'int',
											  'method' => 'string',
											  'get_data' => 'text',
											  'post_data' => 'text',
											  'session' => 'text',
											  'request' => 'string',
											  'controller' => 'string',
											  'action' => 'string',
											  'previousActionId' => 'int',
											  'ip' => 'string',
											  'referer' => 'text',
											  'date' => 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP',
										 ));
		$this->createIndex('userId', $this->table, 'userId');
		$this->createIndex('controller', $this->table, 'controller');
		$this->createIndex('action', $this->table, 'action');
		$this->createIndex('date', $this->table, 'date');
		$this->createIndex('previousActionId', $this->table, 'previousActionId');
	}

	public function down()
	{
		$this->dropTable($this->table);
		return true;
	}
}