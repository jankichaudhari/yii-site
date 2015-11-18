<?php

class m130920_113251_rename_toClientId_to_clientId_log_sms extends CDbMigration
{
	public function up()
	{
		$this->renameColumn('log_sms', 'toClientId', 'clientId');
	}

	public function down()
	{
		$this->renameColumn('log_sms', 'clientId', 'toClientId');

	}

}