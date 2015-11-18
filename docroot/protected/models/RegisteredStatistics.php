<?php
class RegisteredStatistics extends CModel
{

	public $startDate = '';
	public $endDate = '';
	public $granulation = '';

	const GRANULARITY_WEEK  = 'Week';
	const GRANULARITY_DAY   = 'day';
	const GRANULARITY_MONTH = 'month';

	public static function getGranularity()
	{

		return array_combine($t = array(
			self::GRANULARITY_WEEK,
			self::GRANULARITY_DAY,
			self::GRANULARITY_MONTH,
		), $t);
	}

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames()
	{

		return array(
			'startDate'   => 'Starting date',
			'endDate'     => 'Starting date',
			'granulation' => 'granulation',
		);
	}

	public function search()
	{

		$criteria = new CDbCriteria();

		if ($this->startDate) {
			$criteria->compare('cli_created', '>=' . Date::formatDate('Y-m-d', $this->startDate));
		}
		if ($this->endDate) {
			$criteria->compare('cli_created', '<=' . Date::formatDate('Y-m-d', $this->endDate));
		}

		$data = Yii::app()->db->createCommand()

				->from('client');

		switch ($this->granulation) {
			case self::GRANULARITY_WEEK :
				$data = $data->select('YEAR(cli_created) year, WEEK(cli_created) period, COUNT(*) count');
				$data = $data->group('YEAR(cli_created), WEEK(cli_created)');
				break;
			case self::GRANULARITY_MONTH :
				$data = $data->select('YEAR(cli_created) year, MONTH(cli_created) period, COUNT(*) count');
				$data = $data->group('YEAR(cli_created), MONTH(cli_created)');
				break;
			case self::GRANULARITY_DAY :
				$data = $data->select('YEAR(cli_created) year, DAYOFYEAR(cli_created) period, COUNT(*) count');
				$data = $data->group('YEAR(cli_created), DAYOFYEAR(cli_created)');
				break;
			default :
				$data = $data->select('YEAR(cli_created) year, WEEK(cli_created) period, COUNT(*) count');
				$data = $data->group('YEAR(cli_created), WEEK(cli_created)');
				break;
		}

		if ($criteria->toArray()['condition']) {
			$data->where($criteria->toArray()['condition']);
			$data->params = $criteria->params;
		}
		$data = $data->queryAll();
		return $data;
	}

	public function rules()
	{

		return array(
			['startDate, endDate, granulation', 'safe', 'on' => 'search'],
		);
	}
}