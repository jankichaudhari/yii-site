<?php

class m120727_144025_alter_file_table_add_column_caption extends CDbMigration
{
	public function safeUp()
	{
		return $this->addColumn("file","caption","TINYTEXT");
	}

	public function safeDown()
	{
		return $this->dropColumn("file","caption");
	}
}