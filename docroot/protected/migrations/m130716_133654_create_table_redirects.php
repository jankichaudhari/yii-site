<?php

class m130716_133654_create_table_redirects extends CDbMigration
{
	public $table = 'redirect';

	public function up()
	{
		$this->createTable($this->table, array(
											  'id'         => 'pk',
											  'clientId'   => 'int not null default 0',
											  'url'        => 'string',
											  'redirected' => 'string',
											  'comment'    => 'string',
											  'created'    => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
										 ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}

}