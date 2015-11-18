<?php

class m131018_155824_rename_link_deal_mandrillMessage_to_instruction extends CDbMigration
{
	private $newName = 'link_instruction_to_mandrillMessage';

	private $table = 'link_deal_to_mandrillMessage';

	public function up()
	{
		$this->renameTable($this->table, $this->newName);
	}

	public function down()
	{
		$this->renameTable($this->newName, $this->table);
	}

}