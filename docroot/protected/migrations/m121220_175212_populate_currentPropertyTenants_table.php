<?php

class m121220_175212_populate_currentPropertyTenants_table extends CDbMigration
{
	private $tableName = 'currentPropertyTenant';

	public function up()
	{

		$sql = "SELECT DISTINCT
		d.dea_created,
		p.pro_id propertyId,
		d.dea_id dealId,
		c.cli_id tenantId,
		d.dea_status dealStatus,
		o.off_id offerId,
		o.off_status offerStatus,
		oc.cli_id offerClient FROM property p
		INNER JOIN deal d on d.dea_prop = p.pro_id
		INNER JOIN link_client_to_instruction l on l.dealId = d.dea_id
		INNER JOIN `client` c on l.clientId = c.cli_id AND l.capacity ='Tenant'

		LEFT JOIN offer o on d.dea_id = o.off_deal
		LEFT JOIN cli2off l2 on l2.c2o_off = o.off_id
		LEFT JOIN `client` oc on l2.c2o_cli = oc.cli_id

		ORDER BY pro_id, d.dea_created, d.dea_id";
		$data = Yii::app()->db->createCommand($sql)->queryAll();

		$tenants = [];

		$lastPropertyId = null;
		$lastDealId     = null;
		$lastOfferId    = null;

		foreach ($data as $value) {
			$propertyId     = $value['propertyId'];
			$dealId         = $value['dealId'];
			$tenantAfterDeal = $value['tenantId'];

			if (!isset($tenants[$propertyId])) {
				$tenants[$propertyId] = [];
			}

			if ($value['dealStatus'] == 'Completed' && $value['offerStatus'] && $value['offerStatus'] == 'Accepted' && $value['offerClient']) {
				$tenantAfterDeal = $value['offerClient'];
			}

			if ($lastPropertyId === null) {
				$lastPropertyId = $propertyId;
			}

			if ($lastDealId === null) {
				$lastDealId = $dealId;
			}

			if ($lastDealId === $dealId) { // it's the same deal as before
				$tenants[$propertyId][$tenantAfterDeal] = $tenantAfterDeal . ', ' . $propertyId;
			} else {
				$tenants[$propertyId]                  = [];
				$tenants[$propertyId][$tenantAfterDeal] = $tenantAfterDeal . ', ' . $propertyId;
			}

			$lastDealId = $dealId;

		}

		$sql    = [];
		$tenants = array_filter($tenants);
		foreach ($tenants as $propertyId => $tenantList) {
			$sql[] = '(' . implode('), (', $tenantList) . ')';
		}

		$sql = "REPLACE INTO " . $this->tableName . " (clientId, propertyId) VALUES " . implode(', ', $sql);
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{

		return $this->truncateTable($this->tableName);
	}
}