<?php

class m131009_160352_create_mandrill_email_table extends CDbMigration
{
	private $table = 'mandrillEmail';

	public function up()
	{
		$this->createTable($this->table, array(
											  'id'        => 'VARCHAR(64) NOT NULL DEFAULT ""',
											  'messageId' => 'int',
											  'status'    => "enum('queued', 'sent', 'rejected', 'bounced', 'delayed', 'open', 'spam') NOT NULL DEFAULT 'queued'",
											  'sent'      => 'DATETIME',
											  'opened'    => 'int UNSIGNED NOT NULL DEFAULT 0',
											  'clientId'  => 'int UNSIGNED NOT NULL DEFAULT 0',
											  'created'   => 'DATETIME',
											  "PRIMARY KEY  (id)"
										 ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}

}