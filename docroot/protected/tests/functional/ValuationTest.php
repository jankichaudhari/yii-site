<?php
include_once __DIR__ . '/../bootstrap.php';
class ValuationTest extends WebTestCase2
{
	public function testBookValuation()
	{
		$this->url('/valuations');
		$this->byId('bookValuation_email')->value('vitaly.suhanov@woosterstock.co.uk');
		$this->byId('bookValuation_email')->value('vitalijs.suhanovs@woosterstock.co.uk');
		$this->byId('bookValuation_date')->value('preffered date and time' . date('Y-m-d h:i:s'));
		$this->byId('bookValuation_name')->value('Vitaly Suhanov');
		$this->byId('bookValuation_phone')->value('020 7708 6700');
		$this->byId('bookValuation_address')->value("line 1\nline2\nSE15 3QQ");
		$this->byName('bookValuation[send]')->click();
		$this->assertRegExp('/Your message has been sent, thank you/i', $this->source());
	}
}
