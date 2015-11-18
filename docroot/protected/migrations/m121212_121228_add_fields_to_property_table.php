<?php

class m121212_121228_add_fields_to_property_table extends CDbMigration
{
	public function up()
	{

		$this->addColumn('property', 'servicecharge' ,'string');
		$this->addColumn('property', 'groundrent' ,'string');

	}

	public function down()
	{

		$this->dropColumn('property', 'servicecharge');
		$this->dropColumn('property', 'groundrent');
	}
}