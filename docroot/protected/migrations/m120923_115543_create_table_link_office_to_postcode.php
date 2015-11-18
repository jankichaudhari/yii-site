<?php

class m120923_115543_create_table_link_office_to_postcode extends CDbMigration
{
	public $tableName = 'link_office_to_postcode';

	public function up()
	{
		$this->createTable($this->tableName, array(
														   'officeId' => 'int',
														   'postcode' => 'varchar(10)',
														   'PRIMARY KEY (`officeId`, `postcode`)',
													  ));
	}

	public function down()
	{
		$this->dropTable($this->tableName);
	}
}