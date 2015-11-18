<?php

/**
 * This is the model class for table "link_client_to_feature".
 *
 * The followings are the available columns in table 'link_client_to_feature':
 * @property integer $id
 * @property integer $featureId
 * @property integer $clientId
 * @property string  $status
 */
class LinkClientToFeature extends CActiveRecord
{
	const STATUS_WOULD_LIKE    = "Would like";
	const STATUS_MUST_HAVE     = "Must have";
	const STATUS_MUST_NOT_HAVE = "Must not have";

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LinkClientToFeature the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'link_client_to_feature';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('featureId, clientId', 'numerical', 'integerOnly' => true),
			array('status', 'length', 'max' => 13),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, featureId, clientId, status', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'feature' => [self::BELONGS_TO, 'Feature', 'featureId', "together" => true]
		);
	}

	public function getStatuses()
	{

		return array_combine($t = array(
			self::STATUS_WOULD_LIKE,
			self::STATUS_MUST_HAVE,
			self::STATUS_MUST_NOT_HAVE,
		), $t);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'        => 'ID',
			'featureId' => 'Feature',
			'clientId'  => 'Client',
			'status'    => 'Status',
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
		$criteria->compare('featureId', $this->featureId);
		$criteria->compare('clientId', $this->clientId);
		$criteria->compare('status', $this->status, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}
}