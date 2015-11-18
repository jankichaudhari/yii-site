<?php

class m121127_190143_create_copy_of_fea2cli_table extends CDbMigration
{
	private $tableName = 'link_client_to_feature';
	private $copyFrom = 'fea2cli';

	public function up()
	{

		Yii::app()->db->createCommand("CREATE TABLE " . $this->tableName . " LIKE " . $this->copyFrom)->execute();
		Yii::app()->db->createCommand("INSERT " . $this->tableName . " SELECT * FROM " . $this->copyFrom)->execute();
		$this->renameColumn($this->tableName, 'f2c_id', 'id');
		$this->renameColumn($this->tableName, 'f2c_fea', 'featureId');
		$this->renameColumn($this->tableName, 'f2c_cli', 'clientId');
		$this->renameColumn($this->tableName, 'f2c_status', 'status');
	}

	public function down()
	{

		return $this->dropTable($this->tableName);
	}

}