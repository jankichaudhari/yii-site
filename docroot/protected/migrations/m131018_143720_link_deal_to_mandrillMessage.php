<?php

class m131018_143720_link_deal_to_mandrillMessage extends CDbMigration
{
	private $table = 'link_deal_to_mandrillMessage';

	public function up()
	{
		$this->createTable('' . $this->table . '', array(
																'instructionId'     => 'int unsigned NOT NULL',
																'mandrillMessageId' => 'int unsigned NOT NULL',
																'mailshotType'      => 'text',
																'PRIMARY KEY  (instructionId, mandrillMessageId)',
														   ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}
}