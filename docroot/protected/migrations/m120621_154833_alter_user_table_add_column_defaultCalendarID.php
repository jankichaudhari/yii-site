<?php

class m120621_154833_alter_user_table_add_column_defaultCalendarID extends CDbMigration
{
	public function safeUp()
	{
		return $this->addColumn("user","defaultCalendarID","INT");
	}

	public function safeDown()
	{
		return $this->dropColumn("user","defaultCalendarID");
	}
}