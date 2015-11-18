<?php

class m130930_152459_add_marker_field_read_to_sms_log	 extends CDbMigration
{
	public function up()
	{
		$this->addColumn('log_sms', 'read', 'enum("unread", "read") default "unread"');
		$this->createIndex('read', 'log_sms', 'read');
	}

	public function down()
	{
		$this->dropIndex('read', 'log_sms');
		$this->dropColumn('log_sms', 'read');
	}
}