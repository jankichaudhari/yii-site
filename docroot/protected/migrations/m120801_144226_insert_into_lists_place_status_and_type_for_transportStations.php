<?php

class m120801_144226_insert_into_lists_place_status_and_type_for_transportStations extends CDbMigration
{
	public function up()
	{
		$this->insert('lists',array(
								   'ListName'=>'TransportStationsStatus',
								   'ListOrder'=>'1',
								   'ListItem'=>'Active',
								   'ListItemID'=>'1',
							  ));

		$this->insert('lists',array(
								   'ListName'=>'TransportStationsStatus',
								   'ListOrder'=>'2',
								   'ListItem'=>'Inactive',
								   'ListItemID'=>'2',
							  ));
	}

	public function down()
	{
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='TransportStationsStatus' AND  ListItem='Active' ")->execute();
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='TransportStationsStatus' AND  ListItem='Inactive' ")->execute();
	}
}