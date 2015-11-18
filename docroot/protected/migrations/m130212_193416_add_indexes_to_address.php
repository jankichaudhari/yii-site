<?php

class m130212_193416_add_indexes_to_address extends CDbMigration
{
	public $table = 'address';

	public function up()
	{

		$this->createIndex('line1', $this->table, 'line1');
		$this->createIndex('line2', $this->table, 'line2');
		$this->createIndex('line3', $this->table, 'line3');
		$this->createIndex('line4', $this->table, 'line4');
		$this->createIndex('line5', $this->table, 'line5');
		$this->createIndex('searchString', $this->table, 'searchString(100)');
	}

	public function down()
	{

		$this->dropIndex('line1', $this->table);
		$this->dropIndex('line2', $this->table);
		$this->dropIndex('line3', $this->table);
		$this->dropIndex('line4', $this->table);
		$this->dropIndex('line5', $this->table);
		$this->dropIndex('searchString', $this->table);
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