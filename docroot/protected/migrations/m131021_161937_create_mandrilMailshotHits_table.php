<?php

class m131021_161937_create_mandrilMailshotHits_table extends CDbMigration
{
	private $table = 'mandrillMailshotHit';

	public function up()
	{
		$this->createTable($this->table, array(
											  'id'         => 'pk',
											  'clientId'   => 'int unsigned not null',
											  'mailshotId' => 'int unsigned not null',
											  'ip'         => 'string',
											  'time'       => 'datetime not null',
											  'userAgent'  => 'text',
										 ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}

}