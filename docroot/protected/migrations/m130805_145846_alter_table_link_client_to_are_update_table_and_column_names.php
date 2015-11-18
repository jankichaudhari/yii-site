<?php

class m130805_145846_alter_table_link_client_to_are_update_table_and_column_names extends CDbMigration
{
	private $table = 'link_client_to_area';
	private $newTable = 'link_client_to_postcode';
	private $column1 = 'client_to_area_postcode';
	private $newColumn1 = 'postcode';
	private $column2 = 'client_to_area_clientId';
	private $newColumn2 = 'clientId';

	public function up()
	{

		$this->renameColumn($this->table, $this->column1, $this->newColumn1);
		$this->renameColumn($this->table, $this->column2, $this->newColumn2);
		$this->renameTable($this->table, $this->newTable);
		return true;
	}

	public function down()
	{

		$this->renameColumn($this->newTable, $this->newColumn1, $this->column1);
		$this->renameColumn($this->newTable, $this->newColumn2, $this->column2);
		$this->renameTable($this->newTable, $this->table);
		return true;
	}
}