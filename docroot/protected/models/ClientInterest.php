<?php

/**
 * Class ClientInterest
 * @property String $instructionId
 * @property String $clientId
 * @property String $text
 * @property String $email
 * @property String $created
 * @property String $createdBy
 *
 * @property Deal   $instruction
 * @property Client $client
 *
 */
class ClientInterest extends CActiveRecord
{
	/**
	 * @param string $className
	 * @return static
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return array();
	}

	public function relations()
	{
		return array(
				'client'      => [self::BELONGS_TO, 'Client', 'clientId'],
				'instruction' => [self::BELONGS_TO, 'Deal', 'instructionId'],
		);
	}

	public function tableName()
	{
		return 'clientInterest';
	}

	public function scopes()
	{
		return array();
	}

	public function attributeLabels()
	{
		return array(
				'instructionId' => 'Instruction ID',
				'clientId'      => 'client ID',
				'text'          => 'Text sent',
				'email'         => 'Email sent',
				'created'       => 'Created',
				'createdBy'     => 'Created By'
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria();

		return new CActiveDataProvider($this, array(
				'crtiteria' => $criteria,
		));
	}

}