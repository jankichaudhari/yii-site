<?php

class m120920_141858_create_new_table_location extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable("location", array(
											'id' => 'pk',
											'address' => 'text',
											'city' => 'string',
											'postcode' => 'string',
											'latitude' => 'double',
											'longitude' => 'double'
									   ));
	}


	public function safeDown()
	{
		$this->dropTable('location');
	}
}