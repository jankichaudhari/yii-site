<?php

class m130813_122958_create_sms_message_log_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('log_sms', array(
										   'id'         => 'pk',
										   'fromNumber' => 'varchar(20)',
										   'toNumber'   => 'varchar(20)',
										   'toClientId' => 'int',
										   'text'       => 'text',
										   'createdBy'  => 'int',
										   'created'    => 'datetime',
										   'sid'        => 'text',
										   'price'      => 'double',
										   'price_unit' => 'string',
										   'uri'        => 'text',
										   'status'     => 'string',
									  ));
	}

	public function down()
	{
		$this->dropTable('log_sms');
	}
}