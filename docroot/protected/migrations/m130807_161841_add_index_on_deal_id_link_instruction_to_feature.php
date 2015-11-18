<?php

class m130807_161841_add_index_on_deal_id_link_instruction_to_feature extends CDbMigration
{
	public $table = "link_instruction_to_feature";

	public $name = 'dealId';

	public function up()
	{
		$this->createIndex($this->name, $this->table, $this->name);
	}

	public function down()
	{
		$this->dropIndex($this->name, $this->table);
	}

}