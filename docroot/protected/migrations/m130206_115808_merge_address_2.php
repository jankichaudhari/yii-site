<?php

class m130206_115808_merge_address_2 extends CDbMigration
{
	public function up()
	{
	/**
		 * Select all addresses that repeat.
		 */
		$sql = "SELECT concat_ws(' ', line1, line2, line3,line4,line5,postcode) address, count(*) `count`, GROUP_CONCAT(id) ids, id from address group by REPLACE(concat(line1, line2, line3,line4,line5,postcode), ' ', '') HAVING `count` > 1 ORDER BY `count` DESC";

		while ($data = Yii::app()->db->createCommand($sql)->queryAll()) {
//		$data = Yii::app()->db->createCommand($sql)->queryAll();

			$updateClient   = "UPDATE `client` set addressID = :addrID WHERE addressID";
			$updateProperty = "UPDATE `property` set addressId = :addrID WHERE addressId";
			$updateOffice   = "UPDATE `office` set addressId = :addrID WHERE addressId";

			$addSuspiciosAddress = Yii::app()->db->createCommand('REPLACE INTO suspiciosAddress SET id = :addrID');

			foreach ($data as $value) {

				$pieces = array_slice(explode(',', $value['ids']), 1);
//			$params = ['addressesToMerge' => implode(',', $pieces)];
				$params = ['addrID' => $value['id']];

				$addIn = " IN (" . implode(',', $pieces) . ")"; // stupid String escaping.

				Yii::app()->db->createCommand("DELETE FROM address WHERE id in (" . implode(',', $pieces) . ")")->execute();

				Yii::app()->db->createCommand($updateClient . $addIn)->execute($params);
				Yii::app()->db->createCommand($updateProperty . $addIn)->execute($params);
				Yii::app()->db->createCommand($updateOffice . $addIn)->execute($params);

				$addSuspiciosAddress->execute($params);

			}
		}

	}

	public function down()
	{

		return true;
	}
}