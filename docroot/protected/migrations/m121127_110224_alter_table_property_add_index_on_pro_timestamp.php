<?php

class m121127_110224_alter_table_property_add_index_on_pro_timestamp extends CDbMigration
{
	public function up()
	{
		$sql = "CREATE INDEX pro_timestamp ON property (pro_timestamp)";
		if(Yii::app()->db->createCommand($sql)->query()){
			return true;
		}
	}

	public function down()
	{
		$sql = "DROP INDEX pro_timestamp ON property";
		if(Yii::app()->db->createCommand($sql)->query()){
			return true;
		}
	}
}