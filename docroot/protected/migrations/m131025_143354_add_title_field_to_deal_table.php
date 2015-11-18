<?php

class m131025_143354_add_title_field_to_deal_table extends CDbMigration
{
	private $column = 'title';

	private $table = 'deal';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'string');
		$this->createIndex('title', $this->table, $this->column);
	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}
}