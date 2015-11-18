<?php

class m121127_103823_populate_currentPropertyOwners_table extends CDbMigration
{
	private $tableName = 'currentPropertyOwner';

	public function up()
	{

		$sql = "SELECT DISTINCT
		d.dea_created,
		p.pro_id propertyId,
		d.dea_id dealId,
		c.cli_id ownerId,
		d.dea_status dealStatus,
		o.off_id offerId,
		o.off_status offerStatus,
		oc.cli_id offerClient FROM property p
		INNER JOIN deal d on d.dea_prop = p.pro_id
		INNER JOIN link_client_to_instruction l on l.dealId = d.dea_id
		INNER JOIN `client` c on l.clientId = c.cli_id AND l.capacity ='Owner'

		LEFT JOIN offer o on d.dea_id = o.off_deal
		LEFT JOIN cli2off l2 on l2.c2o_off = o.off_id
		LEFT JOIN `client` oc on l2.c2o_cli = oc.cli_id

		ORDER BY pro_id, d.dea_created, d.dea_id";
		$data = Yii::app()->db->createCommand($sql)->queryAll();

		$owners = [];

		$lastPropertyId = null;
		$lastDealId     = null;
		$lastOfferId    = null;

		foreach ($data as $value) {
			$propertyId     = $value['propertyId'];
			$dealId         = $value['dealId'];
			$ownerAfterDeal = $value['ownerId'];

			if (!isset($owners[$propertyId])) {
				$owners[$propertyId] = [];
			}

			if ($value['dealStatus'] == 'Completed' && $value['offerStatus'] && $value['offerStatus'] == 'Accepted' && $value['offerClient']) {
				$ownerAfterDeal = $value['offerClient'];
			}

			if ($lastPropertyId === null) {
				$lastPropertyId = $propertyId;
			}

			if ($lastDealId === null) {
				$lastDealId = $dealId;
			}

			if ($lastDealId === $dealId) { // it's the same deal as before
				$owners[$propertyId][$ownerAfterDeal] = $ownerAfterDeal . ', ' . $propertyId;
			} else {
				$owners[$propertyId]                  = [];
				$owners[$propertyId][$ownerAfterDeal] = $ownerAfterDeal . ', ' . $propertyId;
			}

			$lastDealId = $dealId;

		}

		$sql    = [];
		$owners = array_filter($owners);
		foreach ($owners as $propertyId => $ownerList) {
			$sql[] = '(' . implode('), (', $ownerList) . ')';
		}

		$sql = "REPLACE INTO " . $this->tableName . " (clientId, propertyId) VALUES " . implode(', ', $sql);
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{

		return $this->truncateTable($this->tableName);
	}
}