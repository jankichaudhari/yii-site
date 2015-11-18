<?php

class m121212_122110_update_property_information_from_instruction extends CDbMigration
{
	public function up()
	{

		$sql = "SELECT p.pro_id,
		d.dea_id,
		d.dea_created,
		dea_psubtype,
		dea_ptype,
		dea_floors,
		dea_floor,
		dea_reception,
		dea_bedroom,
		dea_bathroom,
		dea_leaseend,
		dea_servicecharge,
		dea_groundrent,
		dea_tenure
		FROM property p INNER JOIN deal d ON d.dea_prop = p.pro_id
		ORDER by  pro_id, d.dea_created, d.dea_id
		";

		$data = Yii::app()->db->createCommand($sql)->queryAll();

		$updateProperty = "UPDATE property  p SET
		p.pro_psubtype 	= :psubtype,
		p.pro_ptype 	= :ptype,
		p.pro_floors 	= :floors,
		p.pro_floor 	= :floor,
		p.pro_bedroom 	= :bedroom,
		p.pro_reception = :reception,
		p.pro_bathroom 	= :bathroom,
		p.pro_leaseend 	= :leaseend,
		p.servicecharge = :servicecharge,
		p.groundrent 	= :groundrent,
		p.pro_tenure 	= :tenure
		WHERE p.pro_id = :id";

		$updateProperty = Yii::app()->db->createCommand($updateProperty);

		foreach ($data as $key => $value) {
			$data = array(
				'psubtype'      => $value['dea_psubtype'],
				'ptype'         => $value['dea_ptype'],
				'floors'        => $value['dea_floors'],
				'floor'         => $value['dea_floor'],
				'bedroom'       => $value['dea_bedroom'],
				'reception'     => $value['dea_reception'],
				'bathroom'      => $value['dea_bathroom'],
				'leaseend'      => $value['dea_leaseend'],
				'servicecharge' => $value['dea_servicecharge'],
				'groundrent'    => $value['dea_groundrent'],
				'tenure'        => $value['dea_tenure'],
				'id'            => $value['pro_id'],
			);
			$updateProperty->execute($data);
		}

	}

	public function down()
	{

		return true;
	}

}