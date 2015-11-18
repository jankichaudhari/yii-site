<?php

/**
 * This is the model class for table "mailshot".
 *
 * The followings are the available columns in table 'mailshot':
 * @property integer $mai_id
 * @property integer $mai_deal
 * @property string  $mai_type
 * @property integer $mai_count
 * @property integer $mai_failed
 * @property integer $mai_unsub
 * @property integer $mai_user
 * @property string  $mai_date
 * @property string  $mai_status
 */
class Mailshot extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Mailshot the static model class
	 */
	const STATUS_PENDIG  = "Pending";
	const STATUS_SENDING = "Sending";
	const STATUS_SENT    = "Sent";

	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'mailshot';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mai_deal, mai_count, mai_failed, mai_unsub, mai_user', 'numerical', 'integerOnly'=> true),
			array('mai_type', 'length', 'max'=> 100),
			array('mai_status', 'length', 'max'=> 7),
			array('mai_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('mai_id, mai_deal, mai_type, mai_count, mai_failed, mai_unsub, mai_user, mai_date, mai_status', 'safe', 'on'=> 'search'),
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
			'deal'          => [self::BELONGS_TO, 'Deal', 'mai_deal'],
			'user'          => [self::BELONGS_TO, 'User', 'mai_user'],
			"hits"          => array(
				self::HAS_MANY, "Hit", "hit_mailshot",
				'order' => 'hits.hit_id DESC'
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'mai_id'     => 'Mai',
			'mai_deal'   => 'Mai Deal',
			'mai_type'   => 'Mai Type',
			'mai_count'  => 'Mai Count',
			'mai_failed' => 'Mai Failed',
			'mai_unsub'  => 'Mai Unsub',
			'mai_user'   => 'Mai User',
			'mai_date'   => 'Mai Date',
			'mai_status' => 'Mai Status',
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

		$criteria->compare('mai_id', $this->mai_id);
		$criteria->compare('mai_deal', $this->mai_deal);
		$criteria->compare('mai_type', $this->mai_type, true);
		$criteria->compare('mai_count', $this->mai_count);
		$criteria->compare('mai_failed', $this->mai_failed);
		$criteria->compare('mai_unsub', $this->mai_unsub);
		$criteria->compare('mai_user', $this->mai_user);
		$criteria->compare('mai_date', $this->mai_date, true);
		$criteria->compare('mai_status', $this->mai_status, true);

		return new CActiveDataProvider($this, array(
												   'criteria'=> $criteria,
											  ));
	}

	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->mai_date = date("Y-m-d H:i");
			$this->mai_user = Yii::app()->user->id;

		}
		return parent::beforeSave();
	}

}