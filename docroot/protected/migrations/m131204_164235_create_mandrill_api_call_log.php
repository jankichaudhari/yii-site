<?php

class m131204_164235_create_mandrill_api_call_log extends CDbMigration
{
	private $table = 'mandrillWebhookLog';

	public function up()
	{
		$this->createTable($this->table, array(
											  'id'        => 'pk',
											  'GET'       => 'text',
											  'POST'      => 'text',
											  'SERVER'    => 'text',
											  'headers'   => 'text',
											  'requested' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
										 ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}
}