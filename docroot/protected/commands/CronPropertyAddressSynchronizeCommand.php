<?php
/**
 * Synchronizes addressId and pro_addr fields in property table
 */
class CronPropertyAddressSynchronizeCommand extends CConsoleCommand
{

	private $log = [];

	public function run($args)
	{

		parent::run($args);
	}

	/**
	 * After this script is running all pro_addr fields in property table become read only.
	 *
	 * @cron 5 * * * * php /var/www/ws-website-production/htdocs/admin4/protected/cron.php  CronPropertyAddressSynchronize
	 *
	 * @return bool
	 */
	public function actionIndex($period = 5)
	{

		// =================================================================================
		// <<< prepare queries
		$sql = "SELECT a.id addressId,
				p.pro_id as propertyId,
				p.pro_addr1,p.pro_addr2,p.pro_addr3,p.pro_addr4,p.pro_addr5,p.pro_postcode,
				a.line1,a.line2,a.line3,a.line4,a.line5,a.postcode,

				concat_ws(' ', p.pro_addr1, p.pro_addr2, p.pro_addr3, p.pro_addr4, p.pro_addr5, p.pro_postcode) as propertyRecordAddress,
				concat_ws(' ', a.line1,a.line2,a.line3,a.line4,a.line5,a.postcode) as AddressRecord

				FROM property p
				LEFT JOIN address a on p.addressId = a.id
				where
				TIMESTAMPDIFF(DAY,p.pro_timestamp,NOW()) <= " . $period . "
				AND
				replace(concat_ws('', p.pro_addr1, p.pro_addr2, p.pro_addr3, p.pro_addr4, p.pro_addr5, p.pro_postcode), ' ', '') != replace(concat_ws('', a.line1,a.line2,a.line3,a.line4,a.line5,a.postcode), ' ', '')
				OR p.addressId is null
				OR p.addressId = 0";

		$insertIntoAddress = "
				INSERT INTO address SET
				line1 = :pro_addr1,
				line2 = :pro_addr2,
				line3 = :pro_addr3,
				line4 = :pro_addr4,
				line5 = :pro_addr5,
				postcode = :pro_postcode";

		$updatePropertyAddress = "
								UPDATE property SET
								pro_addr1 = :line1,
								pro_addr2 = :line2,
								pro_addr3 = :line3,
								pro_addr4 = :line4,
								pro_addr5 = :line5,
								pro_postcode = :postcode
								WHERE pro_id = :propertyId";

		$updatePropertyAddressId = "UPDATE property SET addressId = :addressId WHERE pro_id = :propertyId";

		$updatePropertyAddressId = Yii::app()->db->createCommand($updatePropertyAddressId);
		$insertIntoAddress       = Yii::app()->db->createCommand($insertIntoAddress);
		$updatePropertyAddress   = Yii::app()->db->createCommand($updatePropertyAddress);
		// prepare queries >>>
		// =================================================================================

		$data = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($data as $value) {
			if ($value['addressId']) { // has address ID => address changed and needs to be synchronized with pro_address
				$this->log("property: " . $value['propertyId'] . " -- [inline address : " . $value['propertyRecordAddress'] . "] not equals to [address string: " . $value['AddressRecord'] . "]");
				$updatePropertyAddress->execute(array(
													 'line1'      => $value['line1'],
													 'line2'      => $value['line2'],
													 'line3'      => $value['line3'],
													 'line4'      => $value['line4'],
													 'line5'      => $value['line5'],
													 'postcode'   => $value['postcode'],
													 'propertyId' => $value['propertyId'],
												));
			} else {
				if (trim($value['propertyRecordAddress'])) { // has pro_address fields but no related address object.
					$this->log("property [propertyId:" . $value['propertyId'] . '] does not have related address object. creating it.');
					$insertIntoAddress->execute(array(
													 'pro_addr1'    => $value['pro_addr1'],
													 'pro_addr2'    => $value['pro_addr2'],
													 'pro_addr3'    => $value['pro_addr3'],
													 'pro_addr4'    => $value['pro_addr4'],
													 'pro_addr5'    => $value['pro_addr5'],
													 'pro_postcode' => $value['pro_postcode'],
												));
					// set addressID of property to correct value
					$updatePropertyAddressId->execute(array(
														   'addressId'  => Yii::app()->db->getLastInsertID(),
														   'propertyId' => $value['propertyId'],
													  ));
				} else { // that is just a problem with a property; it is not even a property without an address. just some rubbish data that should be removed from a db.
//					$this->log("property [propertyId:" . $value['propertyId'] . '] neither has pro_address nor related address object');
				}

			}
		}

		$this->saveLog();
		return true;
	}

	private function log($string)
	{

		$this->log[] = date("Y-m-d H:i:s") . " -- " . $string;
	}

	private function saveLog()
	{

//		$fileName = dirname(__FILE__) . "/../../../../logs/" . date("Y-m-d") . "_addressSyncronization.log";
		$fileName = Yii::app()->params['logDirPath'] . '/' . date("Y-m-d") . "_addressSyncronization.log";
		file_put_contents($fileName, implode("\n", $this->log) . "\n", FILE_APPEND);
	}

}
