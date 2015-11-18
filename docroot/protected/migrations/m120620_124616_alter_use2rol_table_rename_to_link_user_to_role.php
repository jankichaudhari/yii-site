<?php

class m120620_124616_alter_use2rol_table_rename_to_link_user_to_role extends CDbMigration
{
	public function safeUp()
	{
		return $this->renameTable("use2rol","link_user_to_role");
	}

	public function safeDown()
	{
		return $this->renameTable("link_user_to_role","use2rol");
	}
}