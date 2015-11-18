<?php

class m120716_134557_add_addressID_column_to_Client_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('client', 'addressID', 'integer');

	}

	public function down()
	{
		$this->dropColumn('client', 'addressID');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}