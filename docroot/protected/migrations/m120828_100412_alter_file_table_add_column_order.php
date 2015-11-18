<?php

class m120828_100412_alter_file_table_add_column_order extends CDbMigration
{
	public function safeUp()
	{
		return $this->addColumn("file","displayOrder","INTEGER");
	}

	public function safeDown()
	{
		return $this->dropColumn("file","displayOrder");
	}
}