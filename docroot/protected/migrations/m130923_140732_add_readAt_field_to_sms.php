<?php

class m130923_140732_add_readAt_field_to_sms extends CDbMigration
{
	public function up()
	{
		$this->addColumn('log_sms', 'readAt', 'datetime NULL DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('log_sms', 'readAt');
	}
}