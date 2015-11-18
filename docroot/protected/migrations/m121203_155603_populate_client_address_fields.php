<?php

class m121203_155603_populate_client_address_fields extends CDbMigration
{
	public function up()
	{

		$sql = "SELECT cli_id, cli_pro,
		p.pro_addr1, p.pro_addr2, p.pro_addr3,p.pro_addr4,p.pro_addr5,p.pro_postcode, p.pro_pcid, p.pro_latitude, p.pro_longitude,
		p2.pro_addr1 second_addr1, p2.pro_addr2 second_addr2, p2.pro_addr3 second_addr3,
		p2.pro_addr4 second_addr4, p2.pro_addr5 second_addr5, p2.pro_postcode second_postcode, p2.pro_pcid second_pcid, p2.pro_latitude second_latitude, p2.pro_longitude second_longitude
		FROM `client` c
		INNER JOIN property p on c.cli_pro = p.pro_id
		LEFT JOIN pro2cli l ON l.p2c_type = 'Home' AND l.p2c_cli = c.cli_id AND l.p2c_pro != c.cli_pro
		LEFT JOIN property p2 on p2.pro_id = l.p2c_pro
		WHERE c.addressID is null OR c.addressID = 0";

		$db   = Yii::app()->db;
		$data = $db->createCommand($sql)->queryAll();

		$existingAdresses = [];
		$processedClients = [];

		$createAddress = 'INSERT INTO address (line1, line2, line3, line4, line5, postcode, lat, lng, postcodeAnywhereID) VALUES
												(:line1, :line2, :line3, :line4, :line5, :postcode, :lat, :lng, :postcodeAnywhereID)';
		$createAddress = $db->createCommand($createAddress);

		$updateClient = 'UPDATE client SET addressID = :addressID, secondAddressID = :secondAddressID WHERE cli_id = :clientId';
		$updateClient = $db->createCommand($updateClient);

		foreach ($data as $key => $value) {
			if (isset($processedClients[$value['cli_id']])) {
				continue; // ignore all other records for client. we can only hold two addresses now
			}
			$processedClients[$value['cli_id']] = true;

			$addressId         = 0;
			$secondAddressId   = 0;
			$addressHash       = $value['pro_addr1'] . $value['pro_addr2'] . $value['pro_addr3'] . $value['pro_addr4'] . $value['pro_addr5'] . $value['pro_postcode'];
			$secondAddressHash = $value['second_addr1'] . $value['second_addr2'] . $value['second_addr3'] . $value['second_addr4'] . $value['second_addr5'] . $value['second_postcode'];

			if (isset($existingAdresses[$secondAddressHash])) {
				$secondAddressId = $existingAdresses[$secondAddressHash];
			}

			if (isset($existingAdresses[$addressHash])) {
				$addressId = $existingAdresses[$addressHash];
				$updateClient->execute(['addressID' => $addressId, 'secondAddressID' => $secondAddressId, 'clientId' => $value['cli_id']]);
				continue;
			}

			$createAddress->execute(array(
										 'line1'              => $value['pro_addr1'],
										 'line2'              => $value['pro_addr2'],
										 'line3'              => $value['pro_addr3'],
										 'line4'              => $value['pro_addr4'],
										 'line5'              => $value['pro_addr5'],
										 'lat'                => $value['pro_latitude'],
										 'lng'                => $value['pro_longitude'],
										 'postcodeAnywhereID' => $value['pro_pcid'],
										 'postcode'           => $value['pro_postcode'],
									));

			$addressId                      = $db->getLastInsertID();
			$existingAdresses[$addressHash] = $addressId;

			if (trim($secondAddressHash)) {
				$createAddress->execute(array(
											 'line1'              => $value['second_addr1'],
											 'line2'              => $value['second_addr2'],
											 'line3'              => $value['second_addr3'],
											 'line4'              => $value['second_addr4'],
											 'line5'              => $value['second_addr5'],
											 'lat'                => $value['second_latitude'],
											 'lng'                => $value['second_longitude'],
											 'postcodeAnywhereID' => $value['second_pcid'],
											 'postcode'           => $value['second_postcode'],
										));

				$secondAddressId                      = $db->getLastInsertID();
				$existingAdresses[$secondAddressHash] = $secondAddressId;
			}
			$updateClient->execute(['addressID' => $addressId, 'secondAddressID' => $secondAddressId, 'clientId' => $value['cli_id']]);
		}

	}

	public function down()
	{

		return true;
	}

}