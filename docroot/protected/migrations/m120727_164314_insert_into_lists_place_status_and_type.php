<?php

class m120727_164314_insert_into_lists_place_status_and_type extends CDbMigration
{
	public function up()
	{
		$this->insert('lists',array(
								   'ListName'=>'PublicPlacesStatus',
								   'ListOrder'=>'1',
								   'ListItem'=>'Pending',
								   'ListItemID'=>'1',
							  ));

		$this->insert('lists',array(
								   'ListName'=>'PublicPlacesStatus',
								   'ListOrder'=>'2',
								   'ListItem'=>'Proofing',
								   'ListItemID'=>'2',
							  ));

		$this->insert('lists',array(
								   'ListName'=>'PublicPlacesStatus',
								   'ListOrder'=>'3',
								   'ListItem'=>'Active',
								   'ListItemID'=>'3',
							  ));

		$this->insert('lists',array(
								   'ListName'=>'PublicPlacesStatus',
								   'ListOrder'=>'4',
								   'ListItem'=>'Inactive',
								   'ListItemID'=>'4',
							  ));

		$this->insert('lists',array(
								   'ListName'=>'PublicPlacesParkType',
								   'ListOrder'=>'1',
								   'ListItem'=>'None',
								   'ListItemID'=>'1',
							  ));

		$this->insert('lists',array(
								   'ListName'=>'PublicPlacesParkType',
								   'ListOrder'=>'2',
								   'ListItem'=>'Secret Spaces',
								   'ListItemID'=>'2',
							  ));

		$this->insert('lists',array(
								   'ListName'=>'PublicPlacesParkType',
								   'ListOrder'=>'3',
								   'ListItem'=>'Major Parks',
								   'ListItemID'=>'3',
							  ));
	}

	public function down()
	{
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='PublicPlacesStatus' AND  ListItem='Pending' ")->execute();
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='PublicPlacesStatus' AND  ListItem='Proofing' ")->execute();
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='PublicPlacesStatus' AND  ListItem='Active' ")->execute();
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='PublicPlacesStatus' AND  ListItem='Inactive' ")->execute();
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='PublicPlacesParkType' AND  ListItem='None' ")->execute();
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='PublicPlacesParkType' AND  ListItem='Secret Spaces' ")->execute();
		Yii::app()->db->createCommand("DELETE FROM lists WHERE  ListName='PublicPlacesParkType' AND  ListItem='Major Parks' ")->execute();
	}
}