<?php

class m131028_135951_add_indexes_to_client extends CDbMigration
{
	private $table = 'client';

	public function up()
	{
		$this->createIndex('cli_status', $this->table, 'cli_status');
		$this->createIndex('cli_created', $this->table, 'cli_created');
		$this->createIndex('cli_salemin', $this->table, 'cli_salemin');
		$this->createIndex('cli_salemax', $this->table, 'cli_salemax');
		$this->createIndex('addressID', $this->table, 'addressID');
		$this->createIndex('cli_saleemail', $this->table, 'cli_saleemail');
	}

	public function down()
	{
		$this->dropIndex('cli_status', $this->table);
		$this->dropIndex('cli_created', $this->table);
		$this->dropIndex('cli_salemin', $this->table);
		$this->dropIndex('cli_salemax', $this->table);
		$this->dropIndex('addressID', $this->table);
		$this->dropIndex('cli_saleemail', $this->table);
	}

}