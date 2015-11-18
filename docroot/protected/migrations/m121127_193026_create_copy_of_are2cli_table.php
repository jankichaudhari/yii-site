<?php

class m121127_193026_create_copy_of_are2cli_table extends CDbMigration
{
	private $tableName = 'link_client_to_area';
	private $copyFrom = 'are2cli';

	public function up()
	{
		Yii::app()->db->createCommand("CREATE TABLE " . $this->tableName . " LIKE " . $this->copyFrom)->execute();
		Yii::app()->db->createCommand("INSERT " . $this->tableName . " (a2c_are, a2c_cli) SELECT DISTINCT a2c_are, a2c_cli FROM " . $this->copyFrom)->execute();

		$sql = 'ALTER TABLE ' . $this->tableName . '
					DROP INDEX `a2c_are`,
					DROP INDEX `a2c_cli`';
		Yii::app()->db->createCommand($sql)->execute();

		$this->dropColumn($this->tableName, 'a2c_id');
		$this->renameColumn($this->tableName, 'a2c_are', 'client_to_area_postcode');
		$this->renameColumn($this->tableName, 'a2c_cli', 'client_to_area_clientId');
		$this->alterColumn($this->tableName, 'client_to_area_postcode', 'string');
		$sql = 'ALTER TABLE ' . $this->tableName . '
							ADD PRIMARY KEY (`client_to_area_clientId`, `client_to_area_postcode`)';
		Yii::app()->db->createCommand($sql)->execute();
		$sql = "SELECT t.*, a.* FROM " . $this->tableName . " t INNER JOIN area a on a.are_id = t.client_to_area_postcode";

		$data = Yii::app()->db->createCommand($sql)->queryAll($sql);
		$sql = [];
		foreach ($data as $key => $value) {
			$sql[] = '(' . $value['client_to_area_clientId'] . ', "' . $value['are_postcode'] . '")';
		}
		$this->truncateTable($this->tableName);
		$sql = "REPLACE INTO " . $this->tableName . " (`client_to_area_clientId`, `client_to_area_postcode`) VALUES
							" . implode(", ", $sql) . "";
		Yii::app()->db->createCommand($sql)->execute();



	}

	public function down()
	{
		return $this->dropTable($this->tableName);
	}

}