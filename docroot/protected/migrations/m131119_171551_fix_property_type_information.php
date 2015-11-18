<?php

class m131119_171551_fix_property_type_information extends CDbMigration
{
	public function up()
	{
		$sql = "SELECT DISTINCT c.cli_id as clientId,  pty_type as typeId FROM client c
			INNER JOIN link_client_to_propertyType l1 ON l1.clientId = c.cli_id
			INNER JOIN ptype p ON l1.typeId = p.pty_id
			WHERE p.pty_type IS NOT NULL
			GROUP BY c.cli_id, pty_type";

		$data = Yii::app()->db->createCommand($sql)->queryAll();
		$sql  = [];
		foreach ($data as $key => $value) {
			$sql[] = '(' . $value['clientId'] . ', ' . $value['typeId'] . ')';
		}

		$sql = "REPLACE INTO link_client_to_propertyType(clientId, typeId) VALUES " . implode(', ', $sql);
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		echo "m131119_171551_fix_property_type_information does not support migration down.\n";
		return false;
	}
}