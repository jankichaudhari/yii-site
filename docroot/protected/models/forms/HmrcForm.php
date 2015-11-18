<?php

class HmrcForm extends CFormModel
{
	public $dateFrom = '2012-01-01';
	public $dateTo = '2013-01-01';
	public $timeBetweenApps = 15;

	public function rules()
	{
		return array(
			['dateFrom, dateTo', 'date', 'format' => ['dd/mm/yy']],
			['timeBetweenApps', 'numerical', 'integerOnly' => true]
		);
	}
}
