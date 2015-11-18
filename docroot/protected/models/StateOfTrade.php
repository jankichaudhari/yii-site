<?php

/**
 * This is the model class for table "sot".
 *
 * The followings are the available columns in table 'sot':
 * @property integer $sot_id
 * @property integer $sot_deal
 * @property string $sot_status
 * @property string $sot_date
 * @property string $sot_nextdate
 * @property integer $sot_user
 * @property string $sot_notes
 */
class StateOfTrade extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StateOfTrade the static model class
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
		return 'sot';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('sot_notes', 'required'),
			array('sot_deal, sot_user', 'numerical', 'integerOnly'=>true),
			array('sot_status', 'length', 'max'=>50),
			array('sot_deal, sot_user,sot_status, sot_date, sot_nextdate, sot_notes', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('sot_id, sot_deal, sot_status, sot_date, sot_nextdate, sot_user, sot_notes', 'safe', 'on'=>'search'),
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
			'creator' => [self::BELONGS_TO, 'User', 'sot_user', 'together' => true],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'sot_id' => 'Sot',
			'sot_deal' => 'Sot Deal',
			'sot_status' => 'Sot Status',
			'sot_date' => 'Sot Date',
			'sot_nextdate' => 'Sot Nextdate',
			'sot_user' => 'Sot User',
			'sot_notes' => 'Sot Notes',
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

		$criteria->compare('sot_id',$this->sot_id);
		$criteria->compare('sot_deal',$this->sot_deal);
		$criteria->compare('sot_status',$this->sot_status,true);
		$criteria->compare('sot_date',$this->sot_date,true);
		$criteria->compare('sot_nextdate',$this->sot_nextdate,true);
		$criteria->compare('sot_user',$this->sot_user);
		$criteria->compare('sot_notes',$this->sot_notes,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->sot_date = date("Y-m-d H:i:s");
			$this->sot_user = Yii::app()->user->getId();
		}
		return parent::beforeSave();
	}
}