<?php

class m130429_164115_alter_table_propertyCategory_add_fields_displayOnHome_and_displayInMenu extends CDbMigration
{
    private $tableName = "propertyCategory";
    private $column1 = "displayOnHome";
    private $column2 = "displayInMenu";

    public function up()
    {
        $this->addColumn($this->tableName, $this->column1,"TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
        $this->addColumn($this->tableName, $this->column2,"TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
    }

    public function down()
    {
        $this->dropColumn($this->tableName, $this->column1);
        $this->dropColumn($this->tableName, $this->column2);
    }
}