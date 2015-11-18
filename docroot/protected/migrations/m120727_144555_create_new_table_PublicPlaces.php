<?php

class m120727_144555_create_new_table_PublicPlaces extends CDbMigration
{
	public function up()
	{
		return $this->createTable("publicPlaces", array(
					'id' => 'pk',
					'title' => 'VARCHAR(100) NULL',
					'addressId' => 'INT(10) NULL',
					'strapline' => 'MEDIUMTEXT NULL' ,
					'description' => 'LONGTEXT NULL' ,
					'mainGalleryImageId' => 'INT(10) NULL',
					'mainViewImageId' => 'INT(10) NULL',
					'createdByUserId' => 'INT(10) NULL',
					'createdDT' => 'DATETIME NULL',
					'modifiedByUserId' => 'INT(10) NULL',
					'modifiedDT' => 'DATETIME NULL',
					'statusId' => 'INT(10) NULL',
					'typeId' => 'INT(10) NULL'
	   ));
	}

	public function down()
	{
		return $this->dropTable('publicPlaces');
	}
}