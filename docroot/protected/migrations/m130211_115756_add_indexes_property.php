<?php

class m130211_115756_add_indexes_property extends CDbMigration
{
	public $table = 'property';

	public function up()
	{

		$this->createIndex('addressId', $this->table, 'addressID');
	}

	public function down()
	{

		$this->dropIndex('addressId', $this->table);
		return true;
	}

}