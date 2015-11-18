<?php

class m131019_151217_create_new_mailshot_table extends CDbMigration
{
	private $t = 'mandrillMailshot';

	public function up()
	{
		$this->createTable($this->t, array(
										  'id'            => 'pk',
										  'instructionId' => 'int unsigned not null',
										  'type'          => 'string',
									 ));
	}

	public function down()
	{
		$this->dropTable($this->t);
	}

}