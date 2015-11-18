<?php

class m130214_115641_add_new_note_type_valuation_followup extends CDbMigration
{
	public function up()
	{

		return $this->alterColumn('note', 'not_type', "ENUM('confirm','appointment','feedback','sot','viewing_arrangements','appointment_cancel','client_req','deal_general','client_general','deal_production','hip', 'valuation_followup') NOT NULL");
	}

	public function down()
	{

		return $this->alterColumn('note', 'not_type', "ENUM('confirm','appointment','feedback','sot','viewing_arrangements','appointment_cancel','client_req','deal_general','client_general','deal_production','hip') NULL DEFAULT NULL");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}