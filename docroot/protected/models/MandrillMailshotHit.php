<?php

/**
 * Class MandrillMailshotHit
 * @property int              $id
 * @property int              $clientId
 * @property int              $mailshotId
 * @property String           $ip
 * @property String           $time
 * @property String           $userAgent
 *
 * @property Client           $client
 * @property MandrillMailshot $mailshot
 *
 */
class MandrillMailshotHit extends CActiveRecord
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return [];
	}

	public function relations()
	{
		return [
				'client'   => [self::BELONGS_TO, 'Client', 'clientId'],
				'mailshot' => [self::BELONGS_TO, 'MandrillMailshot', 'mailshotId'],
		];
	}

	/**
	 * @return string
	 */
	public function tableName()
	{
		// this is done just because phpStorm template cannot return filename with small first letter
		$tableName    = 'MandrillMailshotHit';
		$tableName[0] = strtolower($tableName[0]);
		return $tableName;
	}

	public function attributeLabels()
	{
		return array();
	}

	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->time = date('Y-m-d H:i:s');
		}
		return parent::beforeSave();
	}

}
