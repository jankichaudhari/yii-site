<?php

class m120920_141957_alter_table_location_add_data_from_localEvent extends CDbMigration
{
	public function up()
	{
		$placeAdds = Yii::app()->db->createCommand("SELECT addressID FROM localEvent" )->queryAll();
		if(count($placeAdds)!=0){
			foreach($placeAdds as $placeAdd){
				$addressID = $placeAdd['addressID'];
				$thisAddress = Yii::app()->db->createCommand("SELECT * FROM address WHERE id = '$addressID'" )->queryAll();
				if(count($thisAddress) != 0){
					$thisAdd = $thisAddress[0]['line1'] ? $thisAddress[0]['line1'] . ',' : '' .  $thisAddress[0]['line2'] ? $thisAddress[0]['line2'] . ',' : '' . $thisAddress[0]['line3'] ? $thisAddress[0]['line3'] . ',' : '' . $thisAddress[0]['line4'] ? $thisAddress[0]['line4'] : '';
					$this->insert('location',array('address'=>$thisAdd,'city'=>$thisAddress[0]['line5'],'postcode'=>$thisAddress[0]['postcode'],'latitude'=>$thisAddress[0]['lat'],'longitude'=>$thisAddress[0]['lng']));
					$this->update('localEvent',array('addressID'=>Yii::app()->db->lastInsertID),"addressID = '$addressID' ");
				}
			}
		}
	}


	public function down()
	{
		echo "m120910_133124_alter_table_location_add_data_from_localEvent does not support migration down.\n";
		return true;
	}
}