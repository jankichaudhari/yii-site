<?php

class m130326_124604_add_new_table_propertyCategory_and_add_new_table_link_instruction_to_propertyCategory extends CDbMigration
{
    private $tableName1 = "propertyCategory";
    private $tableName2 = "link_instruction_to_propertyCategory";
	public function up()
	{
        $this->createTable($this->tableName1, array(
            'id' => 'pk',
            'title' => 'string NOT NULL',
            'displayName' => 'string',
            'description' => 'text',
        ));

        $this->createTable($this->tableName2, array(
            'id' => 'pk',
            'instructionId' => 'int',
            'categoryId' => 'int',
        ));
 	}

	public function down()
	{
        $this->dropTable($this->tableName1);
        $this->dropTable($this->tableName2);
	}

}