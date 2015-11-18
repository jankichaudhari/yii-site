<?php

class m130403_154442_alter_table__structure_propertyCategory extends CDbMigration
{
    private $tableName = "propertyCategory";
	public function up()
	{
        $this->dropColumn($this->tableName,'title');
        $this->addColumn($this->tableName,'status',"TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
        $this->addColumn($this->tableName,'created',"datetime");
        $this->addColumn($this->tableName,'modified',"timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

        $this->insert('lists',array(
            'ListName'=>'propertyCategoryStatus',
            'ListOrder'=>'1',
            'ListItem'=>'Active',
            'ListItemID'=>'1',
        ));

        $this->insert('lists',array(
            'ListName'=>'propertyCategoryStatus',
            'ListOrder'=>'2',
            'ListItem'=>'Inactive',
            'ListItemID'=>'2',
        ));
	}

	public function down()
	{
		echo "m130403_154442_alter_table__structure_propertyCategory does not support migration down.\n";
		return true;
	}
}