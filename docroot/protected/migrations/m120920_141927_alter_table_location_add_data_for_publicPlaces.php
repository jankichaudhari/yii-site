<?php

class m120920_141927_alter_table_location_add_data_for_publicPlaces extends CDbMigration
{
	public function up()
	{
		$placeAdds = Yii::app()->db->createCommand("SELECT addressId FROM publicPlaces" )->queryAll();
		if(count($placeAdds)!=0){
			foreach($placeAdds as $placeAdd){
				$addressId = $placeAdd['addressId'];
				$thisAddress = Yii::app()->db->createCommand("SELECT * FROM address WHERE id = '$addressId'" )->queryAll();
				if(count($thisAddress) != 0){
					$thisAdd = $thisAddress[0]['line1'] ? $thisAddress[0]['line1'] . ',' : '' .  $thisAddress[0]['line2'] ? $thisAddress[0]['line2'] . ',' : '' . $thisAddress[0]['line3'] ? $thisAddress[0]['line3'] . ',' : '' . $thisAddress[0]['line4'] ? $thisAddress[0]['line4'] : '';
					$this->insert('location',array('address'=>$thisAdd,'city'=>$thisAddress[0]['line5'],'postcode'=>$thisAddress[0]['postcode'],'latitude'=>$thisAddress[0]['lat'],'longitude'=>$thisAddress[0]['lng']));
					$this->update('publicPlaces',array('addressId'=>Yii::app()->db->lastInsertID),"addressId = '$addressId' ");
				}
			}
		}
	}

	public function down()
	{
		echo "m120910_135238_alter_table_location_add_data_for_publicPlaces does not support migration down.\n";
		return true;
	}
}