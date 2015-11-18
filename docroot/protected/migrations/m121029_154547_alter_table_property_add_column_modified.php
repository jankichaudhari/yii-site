<?php

class m121029_154547_alter_table_property_add_column_modified extends CDbMigration
{
	public function up()
	{
		return $this->alterColumn('property','pro_timestamp','timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
	}

	public function down()
	{
		echo 'can not migrate down';
		return false;
	}
}