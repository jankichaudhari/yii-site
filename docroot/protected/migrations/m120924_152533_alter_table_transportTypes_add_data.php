<?php

class m120924_152533_alter_table_transportTypes_add_data extends CDbMigration
{
	public function up()
	{
		$this->insert('transportTypes',['title'=>'Tube','info'=>'tube station', 'createdBy'=>'0', 'createdDt'=>date('Y-m-d H:i:s'), 'modifiedBy'=>'0', 'modifiedDt'=>date('Y-m-d H:i:s')]);
		$this->insert('transportTypes',['title'=>'National Rail','info'=>'national rail station', 'createdBy'=>'0', 'createdDt'=>date('Y-m-d H:i:s'), 'modifiedBy'=>'0', 'modifiedDt'=>date('Y-m-d H:i:s')]);
		$this->insert('transportTypes',['title'=>'DLR','info'=>'Docklands Light Railway station', 'createdBy'=>'0', 'createdDt'=>date('Y-m-d H:i:s'), 'modifiedBy'=>'0', 'modifiedDt'=>date('Y-m-d H:i:s')]);
		$this->insert('transportTypes',['title'=>'Overground','info'=>'overground station', 'createdBy'=>'0', 'createdDt'=>date('Y-m-d H:i:s'), 'modifiedBy'=>'0', 'modifiedDt'=>date('Y-m-d H:i:s')]);
		$this->insert('transportTypes',['title'=>'Tram','info'=>'tram station', 'createdBy'=>'0', 'createdDt'=>date('Y-m-d H:i:s'), 'modifiedBy'=>'0', 'modifiedDt'=>date('Y-m-d H:i:s')]);
		$this->insert('transportTypes',['title'=>'River','info'=>'river station', 'createdBy'=>'0', 'createdDt'=>date('Y-m-d H:i:s'), 'modifiedBy'=>'0', 'modifiedDt'=>date('Y-m-d H:i:s')]);
		return true;
	}

	public function down()
	{
		echo "m120924_152533_alter_table_transportTypes_add_data does not support migration down.\n";
		return true;
	}
}