<?php

class m120727_143241_alter_user_table_modify_column_use_status_type_value extends CDbMigration
{
	public function safeUp()
	{
		return $this->alterColumn("user","use_status","ENUM('Active','Disabled')");
	}

	public function safeDown()
	{
		return $this->alterColumn("user","use_status","ENUM('Pending','Active','Disabled','Archived')");
	}
}
