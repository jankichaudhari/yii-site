<?php

class m130214_120234_add_valuation_follow_up_fields_to_instruction extends CDbMigration
{
	private $tableName = 'deal';

	public function up()
	{

		$this->addColumn($this->tableName, 'valuationLetterSent', 'TINYINT(1) NOT NULL DEFAULT "0" AFTER displayOnWebsite');
		$this->addColumn($this->tableName, 'followUpDue', 'DATE NULL DEFAULT NULL AFTER valuationLetterSent');
		$this->addColumn($this->tableName, 'vendorFollowUp', 'TINYINT(1) NOT NULL DEFAULT "0" AFTER followUpDue');
		$this->addColumn($this->tableName, 'instructionLetterSent', 'TINYINT(1) NOT NULL DEFAULT "0" AFTER vendorFollowUp');

	}

	public function down()
	{

		$this->dropColumn($this->tableName, 'valuationLetterSent');
		$this->dropColumn($this->tableName, 'followUpDue');
		$this->dropColumn($this->tableName, 'vendorFollowUp');
		$this->dropColumn($this->tableName, 'instructionLetterSent');
	}
}