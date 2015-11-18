<?php

class m121108_160228_fix_deal_table_displayOnWebsite_column_change_to_zero extends CDbMigration
{

	private $tableName = 'deal';
	private $fieldName = 'displayOnWebsite';

	public function up()
	{

		$sql = 'UPDATE ' . $this->tableName . '  SET ' . $this->fieldName . ' = 0';
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{

		$sql = 'UPDATE ' . $this->tableName . '  SET ' . $this->fieldName . ' = 2';
		Yii::app()->db->createCommand($sql)->execute();
	}

}