<?php

class m140210_171725_create_link_client_to_propertyCategory extends CDbMigration
{
	private $table = 'link_client_to_propertyCategory';

	public function up()
	{
		$this->createTable($this->table, array(
				'clientId'   => 'int unsigned not null',
				'categoryId' => 'int unsigned not null',
				'PRIMARY KEY (clientId, categoryId)'
		));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}

}