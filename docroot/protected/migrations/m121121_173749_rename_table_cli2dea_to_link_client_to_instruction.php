<?php

class m121121_173749_rename_table_cli2dea_to_link_client_to_instruction extends CDbMigration
{
	private $tableName = 'link_client_to_instruction';
	private $copyFrom = 'cli2dea';

	public function up()
	{

		Yii::app()->db->createCommand("CREATE TABLE " . $this->tableName . " LIKE " . $this->copyFrom)->execute();
		Yii::app()->db->createCommand("INSERT " . $this->tableName . " SELECT * FROM " . $this->copyFrom)->execute();
		$this->renameColumn($this->tableName, 'c2d_id', 'id');
		$this->renameColumn($this->tableName, 'c2d_dea', 'dealId');
		$this->renameColumn($this->tableName, 'c2d_cli', 'clientId');
		$this->renameColumn($this->tableName, 'c2d_capacity', 'capacity');
	}

	public function down()
	{

		$this->dropTable($this->tableName);
		return true;
	}
}