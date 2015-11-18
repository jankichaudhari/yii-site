<?php

class m121022_153130_alter_link_instruction_to_feature_change_column_names extends CDbMigration
{

	private $tableName = 'link_instruction_to_feature';

	public function up()
	{

		$sql = "
			ALTER TABLE " . $this->tableName . "
			CHANGE COLUMN `f2d_fea` `featureId` INT(11) NOT NULL DEFAULT '0' FIRST,
			CHANGE COLUMN `f2d_dea` `dealId` INT(11) NOT NULL DEFAULT '0' AFTER `featureId`";

		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{

		echo "m121022_153130_alter_link_instruction_to_feature_change_column_names does not support migration down.\n";
		return false;
	}

}