<?php

class m131019_152450_link_mandrillMailshot_to_message extends CDbMigration
{
	private $t = 'link_mandrillMailshot_to_mandrillMessage';

	public function up()
	{
		$this->createTable($this->t, array(
										  'mailshotId' => 'int unsigned not null',
										  'messageId'  => 'int unsigned not null',
										  'PRIMARY KEY (mailshotId,messageId)'
									 ));
	}

	public function down()
	{
		$this->dropTable($this->t);
	}
}