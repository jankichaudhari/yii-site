<?php

class m121022_153112_alter_link_instruction_to_feature_change_indexes extends CDbMigration
{

	private $tableName = 'link_instruction_to_feature';
	public function up()
	{
		$sql = 'ALTER TABLE ' . $this->tableName . '
			DROP COLUMN `f2d_id`,
			DROP PRIMARY KEY,
			DROP INDEX `f2d_fea`,
			DROP INDEX `f2d_dea`,
			ADD PRIMARY KEY (`f2d_fea`, `f2d_dea`)';

		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		echo "m121022_153112_alter_link_instruction_to_feature_change_indexes does not support migration down.\n";
		return false;
	}
}