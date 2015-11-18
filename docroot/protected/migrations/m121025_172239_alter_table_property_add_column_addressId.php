<?php

class m121025_172239_alter_table_property_add_column_addressId extends CDbMigration
{
	public function up()
	{
		return $this->addColumn('property','addressId','INT');
	}

	public function down()
	{
		return $this->dropColumn('property','addressId');
	}
}