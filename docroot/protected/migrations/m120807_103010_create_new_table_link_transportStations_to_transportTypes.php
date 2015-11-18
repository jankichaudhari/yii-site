<?php

class m120807_103010_create_new_table_link_transportStations_to_transportTypes extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable("link_transportStations_to_transportTypes", array(
												  'id' => 'pk',
												  'transportStation' => 'integer',
												  'transportType' => 'integer',
												  'status' => 'BIT'
											 ));
	}

	public function safeDown()
	{
		$this->dropTable('link_transportStations_to_transportTypes');
	}
}