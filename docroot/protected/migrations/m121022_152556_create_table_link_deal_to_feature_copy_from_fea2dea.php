<?php

class m121022_152556_create_table_link_deal_to_feature_copy_from_fea2dea extends CDbMigration
{
	private $tableName = 'link_instruction_to_feature';
	private $copyFrom = 'fea2dea';

	public function up()
	{

		Yii::app()->db->createCommand("CREATE TABLE " . $this->tableName . " LIKE " . $this->copyFrom)->execute();
		Yii::app()->db->createCommand("INSERT " . $this->tableName . " SELECT * FROM " . $this->copyFrom)->execute();

	}

	public function down()
	{
		$this->dropTable($this->tableName);
		return true;
	}
}