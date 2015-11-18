<?php

class m120923_123505_alter_table_office_add_addressID_and_migrate_data extends CDbMigration
{
	public $tableName = 'office';

	public $columnName = 'addressId';

	public function up()
	{

		$this->addColumn($this->tableName, $this->columnName, 'int');
		/** @var $data Office[] */
		$data              = Office::model()->findAll();
		$migratedAddresses = [];
		foreach ($data as $office) {
			$fullAddress = implode(' ', [
										$office->address1,
										$office->address2,
										$office->address3,
										$office->address4,
										]);

			if (isset($migratedAddresses[$fullAddress])) {
				$office->addressId = $migratedAddresses[$fullAddress]->id;
				$office->save(false);
				continue;
			}

			$parts          = explode(' ', $office->address1);
			$buildingNumber = array_shift($parts);
			$line2          = implode(' ', $parts);

			$address           = new Address();
			$address->line1    = $buildingNumber;
			$address->line2    = $line2;
			$address->line5    = $office->address3;
			$address->postcode = $office->postcode;
			$address->save();
			$migratedAddresses[$fullAddress] = $address;
			$office->addressId               = $migratedAddresses[$fullAddress]->id;
			$office->save(false);
			continue;
		}

	}

	public function down()
	{

		$this->dropColumn($this->tableName, $this->columnName);
		return false;
	}
}