<?php

class m130322_142846_outer_links extends CDbMigration
{
    private $tableName = "outerLinks";

    public function safeUp()
	{
        $this->createTable($this->tableName, array(
                                             'id' => 'pk',
                                             'title' => 'string NOT NULL',
                                             'description' => 'text',
                                             'link' => 'string NOT NULL',
                                             'image' => 'string',
                                        ));
	}

	public function safeDown()
	{
	    $this->dropTable($this->tableName);
	}

}