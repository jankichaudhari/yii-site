<?php
include_once dirname(__FILE__) . '/bootstrap.php';

class
DealTest extends ActiveRecordTest
{

	/**
	 * @param string $scenario
	 * @return CActiveRecord
	 */
	protected function getModel($scenario = 'insert')
	{
		return new Deal($scenario);
	}

	public function testGetPrice()
	{
		$deal                  = new Deal();
		$deal->dea_marketprice = 400000;
		$this->assertEquals(400000, $deal->getPrice());
	}

	public function testGetQualifierReturnsEmptyStringIfNone()
	{
		$deal = new Deal();
		$this->assertEquals('', $deal->getQualifier());
		$this->assertEquals('', $deal->getQualifierText());
	}

	public function testGetQualifier()
	{
		$deal                = new Deal();
		$deal->dea_qualifier = Deal::QUALIFIER_OIEO;
		$this->assertEquals(Deal::QUALIFIER_OIEO, $deal->getQualifier());

		$deal->dea_qualifier = Deal::QUALIFIER_OIRO;
		$this->assertEquals(Deal::QUALIFIER_OIRO, $deal->getQualifier());

		$deal->dea_qualifier = Deal::QUALIFIER_POA;
		$this->assertEquals(Deal::QUALIFIER_POA, $deal->getQualifier());
	}

	/**
	 * No one knows what this method does or where it is used but lets test it just for fun
	 */
	public function testGetTotalChoices()
	{
		$model = new Deal();
		$this->assertEquals([0, 1, 2, 3], $model->getTotalChoices(0, 3));
	}

	public function testCompareStatuses()
	{

		$this->assertLessThan(0, Deal::compareStatuses(Deal::STATUS_VALUATION, Deal::STATUS_AVAILABLE));
		$this->assertLessThan(0, Deal::compareStatuses(Deal::STATUS_VALUATION, Deal::STATUS_PROOFING));
		$this->assertLessThan(0, Deal::compareStatuses(Deal::STATUS_VALUATION, Deal::STATUS_PRODUCTION));
		$this->assertLessThan(0, Deal::compareStatuses(Deal::STATUS_PRODUCTION, Deal::STATUS_PROOFING));

		$this->assertEquals(0, Deal::compareStatuses(Deal::STATUS_PRODUCTION, Deal::STATUS_PRODUCTION));
		$this->assertEquals(0, Deal::compareStatuses(Deal::STATUS_VALUATION, Deal::STATUS_VALUATION));

		$this->assertGreaterThan(0, Deal::compareStatuses(Deal::STATUS_AVAILABLE, Deal::STATUS_VALUATION));
		$this->assertGreaterThan(0, Deal::compareStatuses(Deal::STATUS_AVAILABLE, Deal::STATUS_PRODUCTION));
		$this->assertGreaterThan(0, Deal::compareStatuses(Deal::STATUS_AVAILABLE, Deal::STATUS_PROOFING));
		$this->assertGreaterThan(0, Deal::compareStatuses(Deal::STATUS_UNDER_OFFER_WITH_OTHER, Deal::STATUS_UNDER_OFFER));
		$this->assertGreaterThan(0, Deal::compareStatuses(Deal::STATUS_UNDER_OFFER_WITH_OTHER, Deal::STATUS_AVAILABLE));
	}

	public function testGetTenantsNamesReturnsAlistOfAllTenantNamesJoinedWithSeparator()
	{
		$tenant1 = $this->getMock('Client', ['getFullName']);
		$tenant1->expects($this->exactly(2))->method('getFullName')->will($this->returnValue('John Smith'));

		$tenant2 = $this->getMock('Client', ['getFullName']);
		$tenant2->expects($this->exactly(2))->method('getFullName')->will($this->returnValue('Jane Smith'));

		$model         = new Deal();
		$model->tenant = [$tenant1, $tenant2];

		$this->assertEquals('John Smith, Jane Smith', $model->getTenantNames());
		$this->assertEquals('John Smith<br>Jane Smith', $model->getTenantNames('<br>'));
	}

	public function testGetOwnersNamesReturnsAlistOfAllTenantNamesJoinedWithSeparator()
	{
		$owner1 = $this->getMock('Client', ['getFullName']);
		$owner1->expects($this->exactly(2))->method('getFullName')->will($this->returnValue('John Smith'));

		$owner2 = $this->getMock('Client', ['getFullName']);
		$owner2->expects($this->exactly(2))->method('getFullName')->will($this->returnValue('Jane Smith'));

		$model         = new Deal();
		$model->tenant = [$owner1, $owner2];

		$this->assertEquals('John Smith, Jane Smith', $model->getTenantNames());
		$this->assertEquals('John Smith<br>Jane Smith', $model->getTenantNames('<br>'));
	}

	/**
	 * @dataProvider roomProvider
	 */
	public function testGetPropertyRoomString($bedrooms, $receptions, $bathrooms, $result, $separator)
	{
		$model                = new Deal();
		$model->dea_bedroom   = $bedrooms;
		$model->dea_bathroom  = $bathrooms;
		$model->dea_reception = $receptions;

		$this->assertEquals($result, $model->getPropertyRoomString($separator));
	}

	public function roomProvider()
	{
		return array(
				[1, 0, 0, '1 bedroom', ' '],
				[0, 1, 0, '1 reception', ' '],
				[0, 0, 1, '1 bathroom', ' '],
				[1, 1, 0, '1 bedroom 1 reception', ' '],
				[1, 0, 1, '1 bedroom 1 bathroom', ' '],
				[0, 1, 1, '1 reception 1 bathroom', ' '],
				[1, 1, 1, '1 bedroom 1 reception 1 bathroom', ' '],
				[1, 1, 1, '1 bedroom, 1 reception, 1 bathroom', ', '],
				[2, 1, 1, '2 bedrooms, 1 reception, 1 bathroom', ', '],
				[2, 0, 0, '2 bedrooms', ' '],
				[0, 2, 0, '2 receptions', ' '],
				[0, 0, 2, '2 bathrooms', ' '],
		);
	}

	/**
	 * @dataProvider publicStatusProvider
	 */
	public function testIsPublic($status, $isPublic, $displayOnWebsite)
	{
		$model                   = new Deal();
		$model->dea_status       = $status;
		$model->displayOnWebsite = $displayOnWebsite;
		$this->assertEquals($isPublic, $model->isPublic(), 'Failed on status ' . $status . ' is supposed to be ' . (string)$isPublic);

	}

	public function publicStatusProvider()
	{
		return array(
				[Deal::STATUS_VALUATION, false, false],
				[Deal::STATUS_INSTRUCTED, false, false],
				[Deal::STATUS_COMPLETED, false, false],
				[Deal::STATUS_AVAILABLE, true, false],
				[Deal::STATUS_UNDER_OFFER, true, false],
				[Deal::STATUS_UNDER_OFFER_WITH_OTHER, true, false],
				[Deal::STATUS_PRODUCTION, false, false],
				[Deal::STATUS_PROOFING, false, false],
				[Deal::STATUS_EXCHANGED, true, false],
				[Deal::STATUS_COLLAPSED, false, false],
				[Deal::STATUS_NOT_INSTRUCTED, false, false],
				[Deal::STATUS_WITHDRAWN, false, false],
				[Deal::STATUS_DISINSTRUCTED, false, false],
				[Deal::STATUS_SOLD_BY_OTHER, false, false],
				[Deal::STATUS_ARCHIVED, false, false],
				[Deal::STATUS_COMPARABLE, false, false],
				[Deal::STATUS_CHAIN, false, false],
				[Deal::STATUS_UNKNOWN, false, false],
				[Deal::STATUS_VALUATION, true, true],
				[Deal::STATUS_INSTRUCTED, true, true],
				[Deal::STATUS_COMPLETED, true, true],
				[Deal::STATUS_AVAILABLE, true, true],
				[Deal::STATUS_UNDER_OFFER, true, true],
				[Deal::STATUS_UNDER_OFFER_WITH_OTHER, true, true],
				[Deal::STATUS_PRODUCTION, true, true],
				[Deal::STATUS_PROOFING, true, true],
				[Deal::STATUS_EXCHANGED, true, true],
				[Deal::STATUS_COLLAPSED, true, true],
				[Deal::STATUS_NOT_INSTRUCTED, true, true],
				[Deal::STATUS_WITHDRAWN, true, true],
				[Deal::STATUS_DISINSTRUCTED, true, true],
				[Deal::STATUS_SOLD_BY_OTHER, true, true],
				[Deal::STATUS_ARCHIVED, true, true],
				[Deal::STATUS_COMPARABLE, true, true],
				[Deal::STATUS_CHAIN, true, true],
				[Deal::STATUS_UNKNOWN, true, true],
		);
	}

	public function testSetCategories()
	{
		$model = new Deal();
	}
}