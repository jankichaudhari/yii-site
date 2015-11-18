<?php

class m131107_173221_populate_link_client_to_propertyType extends CDbMigration
{
	private $table = 'link_client_to_propertyType';

	public function up()
	{
		$sql  = "SELECT cli_id as id, cli_saleptype as types FROM client WHERE cli_saleptype != ''";
		$data = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($data as $value) {
			$types    = explode('|', $value['types']);
			$clientId = $value['id'];

			$sql = [];
			foreach ($types as $type) {
				if (!$type) continue;
				$sql[] = "({$clientId}, {$type})";
			}
			Yii::app()->db->createCommand("REPLACE INTO {$this->table}(clientId, typeId) VALUES " . implode(',', $sql))->execute();
		}

	}

	public function down()
	{
		$this->truncateTable($this->table);
	}

}