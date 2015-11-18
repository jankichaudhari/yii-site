<?php

/**
 * This is the model class for table "tel".
 *
 * The followings are the available columns in table 'tel':
 * @property integer $tel_id
 * @property string  $tel_type
 * @property string  $tel_number
 * @property integer $tel_cli
 * @property integer $tel_con
 * @property integer $tel_com
 * @property integer $tel_ord
 * @property string  $plainNumber
 * @property string  $plainNumberReversed used for index to search from right side
 */
class Telephone extends CActiveRecord
{
	const TYPE_MOBILE = 'Mobile';
	const TYPE_WORK   = 'Work';
	const TYPE_HOME   = 'Home';
	const TYPE_ABROAD = 'Abroad';
	const TYPE_OTHER  = 'Other';
	const TYPE_DX     = 'DX';
	const TYPE_FAX    = 'Fax';
	const TYPE_PAGER  = 'Pager';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Telephone the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getPlainNumber()
	{
		if ($this->plainNumber) {
			return $this->plainNumber;
		}
		return preg_replace('/[^0-9]/i', '', $this->tel_number);
	}

	public function getClientsWithSimilarPhone($minLength = 5)
	{
		if (!$this->getPlainNumber() || strlen($this->getPlainNumber()) < $minLength) {
			return false;
		}
		$sql = "SELECT tel_cli FROM " . $this->tableName() . " WHERE plainNumber LIKE :plainnumber";
		return Yii::app()->db->createCommand($sql)->queryColumn(['plainnumber' => '%' . $this->getPlainNumber() . '%']);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'tel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('tel_cli, tel_con, tel_com', 'numerical', 'integerOnly' => true),
			array('tel_number', 'required'),
			array('plainNumber, plainNumberReversed', 'unsafe'),
			array('tel_number', 'length', 'max' => 40, 'allowEmpty' => false),
			array('tel_type', 'in', 'range' => array_values(self::getTypes())),
			array('tel_type, tel_number, tel_con, tel_com', 'safe', 'on' => 'search'),
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
			'tel_id'     => 'Tel',
			'tel_type'   => 'Tel Type',
			'tel_number' => 'Tel Number',
			'tel_cli'    => 'Tel Cli',
			'tel_con'    => 'Tel Con',
			'tel_com'    => 'Tel Com',
			'tel_ord'    => 'Tel Ord',
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

		$criteria->compare('tel_id', $this->tel_id);
		$criteria->compare('tel_type', $this->tel_type, true);
		$criteria->compare('tel_number', $this->tel_number, true);
		$criteria->compare('tel_cli', $this->tel_cli);
		$criteria->compare('tel_con', $this->tel_con);
		$criteria->compare('tel_com', $this->tel_com);
		$criteria->compare('tel_ord', $this->tel_ord);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));

	}

	protected function beforeSave()
	{

		if ($this->isNewRecord && !$this->tel_ord) {

			if (!$this->tel_cli && !$this->tel_com && !$this->tel_con) {
				throw new CDbException('Cant save the telephone number if it does not belong to any record');
			}

			if ($this->tel_com) {
				$field = 'tel_com = ' . $this->tel_com;
			} elseif ($this->tel_con) {
				$field = 'tel_con = ' . $this->tel_con;
			} else {
				$field = 'tel_cli = ' . $this->tel_cli;
			}

			$sql           = "SELECT IF(MAX(tel_ord) IS NOT NULL, MAX(tel_ord), -1) as max_ord  FROM " . $this->tableName() . " WHERE " . $field;
			$command       = Yii::app()->db->createCommand($sql);
			$result        = $command->queryRow();
			$this->tel_ord = $result['max_ord'] + 1;

			$this->plainNumber         = preg_replace('/[^0-9+]/', '', $this->tel_number);
			$this->plainNumberReversed = strrev($this->plainNumber);
		}
		return parent::beforeSave();

	}

	public static function getTypes()
	{

		return array('Mobile' => 'Mobile', 'Home' => 'Home', 'Fax' => 'Fax', 'Abroad' => 'Abroad', 'DX' => 'DX', 'Other' => 'Other');
	}
}