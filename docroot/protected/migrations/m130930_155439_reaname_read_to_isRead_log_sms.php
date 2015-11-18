<?php

class m130930_155439_reaname_read_to_isRead_log_sms extends CDbMigration
{
	public function up()
	{

		$this->renameColumn('log_sms', 'read', 'isRead');
	}

	public function down()
	{
		$this->renameColumn('log_sms', 'isRead', 'read');
	}
}