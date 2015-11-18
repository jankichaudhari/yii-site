<?php

/**
 * This is the model class for table "hit".
 *
 * The followings are the available columns in table 'hit':
 * @property integer $hit_id
 * @property integer $hit_mailshot
 * @property integer $hit_deal
 * @property integer $hit_client
 * @property string $hit_date
 * @property string $hit_action
 */
class Hit extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Hit the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hit';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hit_date', 'required'),
			array('hit_mailshot, hit_deal, hit_client', 'numerical', 'integerOnly'=>true),
			array('hit_action', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('hit_id, hit_mailshot, hit_deal, hit_client, hit_date, hit_action', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'hit_id' => 'Hit',
			'hit_mailshot' => 'Hit Mailshot',
			'hit_deal' => 'Hit Deal',
			'hit_client' => 'Hit Client',
			'hit_date' => 'Hit Date',
			'hit_action' => 'Hit Action',
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

		$criteria=new CDbCriteria;

		$criteria->compare('hit_id',$this->hit_id);
		$criteria->compare('hit_mailshot',$this->hit_mailshot);
		$criteria->compare('hit_deal',$this->hit_deal);
		$criteria->compare('hit_client',$this->hit_client);
		$criteria->compare('hit_date',$this->hit_date,true);
		$criteria->compare('hit_action',$this->hit_action,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}