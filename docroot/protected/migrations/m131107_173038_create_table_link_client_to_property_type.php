<?php

class m131107_173038_create_table_link_client_to_property_type extends CDbMigration
{
	private $table = 'link_client_to_propertyType';

	public function up()
	{
		$this->createTable($this->table, array(
															   'clientId' => 'int unsigned not null',
															   'typeId'   => 'int unsigned not null',
															   'primary key (clientId, typeId)',
														  ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}
}