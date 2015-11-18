<?php
Yii::import("application.components.migrationTemplates.*");

class m121113_143046_fix_deal_data_covert_to_proper_utf8 extends MigrationConvertToUTF8
{

	protected $table = "deal";
	protected $pk = "dea_id";

}