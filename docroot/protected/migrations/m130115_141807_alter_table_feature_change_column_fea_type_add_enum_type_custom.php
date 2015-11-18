<?php

class m130115_141807_alter_table_feature_change_column_fea_type_add_enum_type_custom extends CDbMigration
{
	private $tableName = "feature";
	private $columnName = "fea_type";

	public function up()
	{
		return $this->alterColumn($this->tableName, $this->columnName,"ENUM('External','Internal','Locality','Lettings','Custom')");
	}

	public function down()
	{
		return $this->alterColumn($this->tableName, $this->columnName,"ENUM('External','Internal','Locality','Lettings')");
	}
}