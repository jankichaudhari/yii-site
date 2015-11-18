<?php

class m120801_120638_create_new_table_transportStations extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable("transportTypes", array(
														'id' => 'pk',
														'title' => 'string',
														'info' => 'text',
														'createdBy' => 'integer',
														'createdDt' => 'datetime',
														'modifiedBy' => 'integer',
														'modifiedDt' => 'datetime',
												   ));

		$this->createTable("transportStations", array(
													   'id' => 'pk',
													   'title' => 'string',
													   'description' => 'text' ,
													   'latitude' => 'double',
													   'longitude' => 'double',
													   'createdBy' => 'integer',
													   'createdDt' => 'datetime',
													   'modifiedBy' => 'integer',
													   'modifiedDt' => 'datetime',
													   'statusId' => 'integer',
												  ));
	}

	public function safeDown()
	{
		$this->dropTable('transportTypes');

		$this->dropTable('transportStations');
	}
}