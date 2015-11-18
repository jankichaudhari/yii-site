<?php

/**
 * This is the model class for table "link_client_to_instruction".
 *
 * The followings are the available columns in table 'link_client_to_instruction':
 * @property integer $id
 * @property integer $clientId
 * @property integer $dealId
 * @property string  $capacity
 */
class LinkClientToInstruction extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LinkClientToInstruction the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	public static function copyRecords($from, $to)
	{

		$sql = "REPLACE INTO link_client_to_instruction (clientId, dealId, capacity)
						SELECT clientId, :to, capacity FROM link_client_to_instruction WHERE dealId=:from";
		return Yii::app()->db->createCommand($sql)->execute(array(
																 ':to'   => $to,
																 ':from' => $from,
															));
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'link_client_to_instruction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('clientId, dealId', 'numerical', 'integerOnly' => true),
			array('capacity', 'length', 'max' => 6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, clientId, dealId, capacity', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'       => 'ID',
			'clientId' => 'Client',
			'dealId'   => 'Deal',
			'capacity' => 'Capacity',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('clientId', $this->clientId);
		$criteria->compare('dealId', $this->dealId);
		$criteria->compare('capacity', $this->capacity, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}
}