<?php
include_once dirname(__FILE__) . '/../bootstrap.php';
Yii::import('application.migrations.*');
class populateCurrentPropertyOwnersTableMigrationTest extends CDbTestCase
{

	/**
	 * @var CDbMigration
	 */
	private $migration;

	/** @var Property */
	private $property;

	/**
	 * @var Client
	 */
	private $owner1;
	/**
	 * @var Client
	 */
	private $owner2;
	/** @var Client */
	private $owner3;
	/** @var Client */
	private $owner4;

	public function setUp()
	{

		Yii::app()->db->createCommand()->truncateTable('link_client_to_instruction');
		Yii::app()->db->createCommand()->truncateTable('property');
		Yii::app()->db->createCommand()->truncateTable('deal');
		Yii::app()->db->createCommand()->truncateTable('client');
		Yii::app()->db->createCommand()->truncateTable('currentPropertyOwner');
		Yii::app()->db->createCommand()->truncateTable('cli2off');
		Yii::app()->db->createCommand()->truncateTable('offer');

		$this->migration = new m121127_103823_populate_currentPropertyOwners_table();
		$this->migration->down();

		$this->property = new Property();
		$this->property->save(false);

		$this->assertInstanceOf('Property', $this->property);

		$this->owner1 = new Client();
		$this->owner1->save(false);

		$this->owner2 = new Client();
		$this->owner2->save(false);

		$this->owner3 = new Client();
		$this->owner3->save(false);

		$this->owner4 = new Client();
		$this->owner4->save(false);

	}

	/**
	 * tests that a deal that is added later overrides owners from previous deal
	 */
	public function testNewestInstructionOverridesFormerInstruction()
	{

		$instruction             = new Deal();
		$instruction->dea_prop   = $this->property->pro_id;
		$instruction->dea_status = 'Available';
		$instruction->save(false);

		$instruction2             = new Deal();
		$instruction2->dea_prop   = $this->property->pro_id;
		$instruction2->dea_status = 'Available';
		$instruction2->save(false);
		$sql = "INSERT INTO link_client_to_instruction (dealId, clientId, capacity)
		VALUES
		(" . $instruction->dea_id . ", " . $this->owner1->cli_id . ", 'Owner'),
		(" . $instruction->dea_id . ", " . $this->owner2->cli_id . ", 'Owner'),
		(" . $instruction2->dea_id . ", " . $this->owner3->cli_id . ", 'Owner'),
		(" . $instruction2->dea_id . ", " . $this->owner4->cli_id . ", 'Owner')";
		Yii::app()->db->createCommand($sql)->execute();

		$this->migration->up();

		$property = Property::model()->findByPk($this->property->pro_id);
		$this->assertCount(2, $property->owners);

		$ownerIds = [];

		foreach ($property->owners as $key => $value) {
			$ownerIds[] = $value->cli_id;
		}

		$this->assertContains($this->owner3->cli_id, $ownerIds, print_r($ownerIds, true));
		$this->assertContains($this->owner4->cli_id, $ownerIds);

	}

	public function testAcceptedOfferOverridesInstructionsOwner()
	{

		$instruction             = new Deal();
		$instruction->dea_prop   = $this->property->pro_id;
		$instruction->dea_status = 'Completed';
		$instruction->save(false);

		$sql = "INSERT INTO link_client_to_instruction (dealId, clientId, capacity)
				VALUES
				(" . $instruction->dea_id . ", " . $this->owner1->cli_id . ", 'Owner'),
				(" . $instruction->dea_id . ", " . $this->owner2->cli_id . ", 'Owner')";
		Yii::app()->db->createCommand($sql)->execute();

		$sql = "INSERT INTO offer SET
		off_deal = '" . $instruction->dea_id . "',
	 	off_status = 'Accepted'";

		Yii::app()->db->createCommand($sql)->execute();
		$offerId = Yii::app()->db->getLastInsertID();

		$sql = "INSERT INTO cli2off SET c2o_cli = '" . $this->owner3->cli_id . "', c2o_off='" . $offerId . "'";
		Yii::app()->db->createCommand($sql)->execute();
		$sql = "INSERT INTO cli2off SET c2o_cli = '" . $this->owner4->cli_id . "', c2o_off='" . $offerId . "'";
		Yii::app()->db->createCommand($sql)->execute();

		$this->migration->up();

		$property = Property::model()->findByPk($this->property->pro_id);
		$this->assertCount(2, $property->owners);

		$ownerIds = [];

		foreach ($property->owners as $key => $value) {
			$ownerIds[] = $value->cli_id;
		}

		$this->assertContains($this->owner3->cli_id, $ownerIds);
		$this->assertContains($this->owner4->cli_id, $ownerIds);
	}

	public function testAcceptedOfferIsOverridenByNewerInstruction()
	{

		$instruction             = new Deal();
		$instruction->dea_prop   = $this->property->pro_id;
		$instruction->dea_status = 'Completed';
		$instruction->save(false);

		$sql = "INSERT INTO link_client_to_instruction (dealId, clientId, capacity)
				VALUES
				(" . $instruction->dea_id . ", " . $this->owner1->cli_id . ", 'Owner'),
				(" . $instruction->dea_id . ", " . $this->owner2->cli_id . ", 'Owner')";
		Yii::app()->db->createCommand($sql)->execute();

		$sql = "INSERT INTO offer SET
		off_deal = '" . $instruction->dea_id . "',
	 	off_status = 'Accepted'";

		Yii::app()->db->createCommand($sql)->execute();
		$offerId = Yii::app()->db->getLastInsertID();

		$sql = "INSERT INTO cli2off SET c2o_cli = '" . $this->owner3->cli_id . "', c2o_off='" . $offerId . "'";
		Yii::app()->db->createCommand($sql)->execute();
		$sql = "INSERT INTO cli2off SET c2o_cli = '" . $this->owner4->cli_id . "', c2o_off='" . $offerId . "'";
		Yii::app()->db->createCommand($sql)->execute();

		// =================================================================================
		// <<< second instruction
		$instruction2             = new Deal();
		$instruction2->dea_prop   = $this->property->pro_id;
		$instruction2->dea_status = 'Available';
		$instruction2->save(false);

		$sql = "INSERT INTO link_client_to_instruction (dealId, clientId, capacity)
						VALUES
						(" . $instruction2->dea_id . ", " . $this->owner1->cli_id . ", 'Owner'),
						(" . $instruction2->dea_id . ", " . $this->owner2->cli_id . ", 'Owner')";

		Yii::app()->db->createCommand($sql)->execute();
		// second instruction >>>
		// =================================================================================

		$this->migration->up();

		$property = Property::model()->findByPk($this->property->pro_id);
		$this->assertCount(2, $property->owners);

		$ownerIds = [];

		foreach ($property->owners as $key => $value) {
			$ownerIds[] = $value->cli_id;
		}

		$this->assertContains($this->owner1->cli_id, $ownerIds);
		$this->assertContains($this->owner2->cli_id, $ownerIds);
	}

	public function tearDown()
	{

	}
}